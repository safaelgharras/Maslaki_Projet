<?php
/**
 * Chatbot AJAX Endpoint — Hybrid AI Assistant for Maslaki.
 *
 * Priority: DATABASE FIRST → then GEMINI.
 *
 * Accepts POST with { question: string, action?: string }
 * Returns JSON with { success, reply, source, timestamp }
 */

session_start();
require_once __DIR__ . '/config/DataBase.php';
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/services/GeminiService.php';

// ── CORS & Headers ──────────────────────────────────────────────
header('Content-Type: application/json; charset=UTF-8');
header('X-Content-Type-Options: nosniff');

// ── Only accept POST ────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Method not allowed', 405);
}

// ── Rate Limiting ───────────────────────────────────────────────
$ip           = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$rateLimitKey = 'chatbot_rate_' . $ip;
$rateLimitMax = 20; // max requests
$rateLimitWin = 60; // per 60 seconds

if (!isset($_SESSION[$rateLimitKey])) {
    $_SESSION[$rateLimitKey] = ['count' => 0, 'start' => time()];
}

$rl = &$_SESSION[$rateLimitKey];
if (time() - $rl['start'] > $rateLimitWin) {
    $rl['count'] = 0;
    $rl['start'] = time();
}
$rl['count']++;

if ($rl['count'] > $rateLimitMax) {
    json_error('Trop de requêtes. Veuillez patienter quelques secondes.', 429);
}

// ── Input Validation ────────────────────────────────────────────
$rawInput = file_get_contents('php://input');
$input    = json_decode($rawInput, true);

if (!$input) {
    json_error('Requête invalide.', 400);
}

$action   = $input['action'] ?? 'ask';
$userId   = $_SESSION['user_id'] ?? null;
$context  = $input['context'] ?? 'profile'; // 'profile' or 'orientation'

// ── Handle Actions (route BEFORE question validation) ──────────
switch ($action) {
    case 'history':
        handleHistory($pdo, $userId);
        break;
    case 'clear':
        handleClear($pdo, $userId);
        break;
    case 'ask':
    default:
        // Validate question only for 'ask' action
        if (empty($input['question'])) {
            json_error('Question manquante.', 400);
        }

        $question = trim($input['question']);

        // Limit question length
        if (mb_strlen($question) > 1000) {
            json_error('Question trop longue (max 1000 caractères).', 400);
        }

        $question = htmlspecialchars($question, ENT_QUOTES, 'UTF-8');
        handleAsk($pdo, $userId, $question, $context);
        break;
}

// ═══════════════════════════════════════════════════════════════
// HANDLERS
// ═══════════════════════════════════════════════════════════════

function handleAsk(PDO $pdo, ?int $userId, string $question, string $context = 'profile'): void
{
    $source = 'database';
    $reply  = null;

    // ── 1. Try Database First (always works — no external dependency) ──
    try {
        $reply = searchDatabase($pdo, $question, $userId);
    } catch (\Throwable $e) {
        error_log('[Chatbot] Database search error: ' . $e->getMessage());
        $reply = null; // Fall through to Gemini
    }

    // ── 2. Fallback to Gemini (wrapped to never crash) ───────────
    if ($reply === null) {
        $source = 'gemini';

        try {
            $gemini = new GeminiService();
            $system = buildSystemPrompt($pdo, $userId, $context);
            $result = $gemini->ask($question, $system);

            if ($result['success']) {
                $reply = $result['reply'];
            } else {
                // Gemini returned a controlled error (503, auth, etc.)
                $reply = $result['error'] ?? 'Désolé, je ne peux pas répondre pour le moment. Veuillez réessayer.';
            }
        } catch (\Throwable $e) {
            // Catch anything unexpected — never crash the chatbot
            error_log('[Chatbot] Gemini unexpected error: ' . $e->getMessage());
            $reply = '🤖 Maslaki AI est temporairement indisponible. Veuillez réessayer dans quelques secondes.';
        }
    }

    // ── 3. Save to Log (never let a log failure break the response) ──
    saveLog($pdo, $userId, $question, $reply, $source);

    // ── 4. Return Response ─────────────────────────────────────
    json_response([
        'success'  => true,
        'reply'    => $reply,
        'source'   => $source,
        'timestamp' => date('H:i'),
    ]);
}

function handleHistory(PDO $pdo, ?int $userId): void
{
    if (!$userId) {
        json_response(['success' => true, 'messages' => []]);
    }

    try {
        $stmt = $pdo->prepare(
            "SELECT question, answer, source, created_at 
             FROM chatbot_logs 
             WHERE user_id = ? 
             ORDER BY created_at DESC 
             LIMIT 50"
        );
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (\Throwable $e) {
        // Table might not exist yet — return empty history
        error_log('[Chatbot] History load error: ' . $e->getMessage());
        json_response(['success' => true, 'messages' => []]);
    }

    // Reverse to chronological order
    $rows = array_reverse($rows);

    $messages = [];
    foreach ($rows as $row) {
        $messages[] = [
            'question'  => $row['question'],
            'answer'    => $row['answer'],
            'source'    => $row['source'],
            'timestamp' => date('H:i', strtotime($row['created_at'])),
        ];
    }

    json_response(['success' => true, 'messages' => $messages]);
}

function handleClear(PDO $pdo, ?int $userId): void
{
    if ($userId) {
        try {
            $stmt = $pdo->prepare("DELETE FROM chatbot_logs WHERE user_id = ?");
            $stmt->execute([$userId]);
        } catch (\Throwable $e) {
            error_log('[Chatbot] History clear error: ' . $e->getMessage());
        }
    }

    json_response(['success' => true, 'message' => 'Historique supprimé.']);
}

// ═══════════════════════════════════════════════════════════════
// DATABASE SEARCH
// ═══════════════════════════════════════════════════════════════

function searchDatabase(PDO $pdo, string $question, ?int $userId): ?string
{
    $q = strtolower($question);

    // ── Schools in a city ──────────────────────────────────────
    if (preg_match('/(?:écoles?|schools?|institutions?)\s+(?:à|dans|en|in)\s+(.+)/iu', $question, $m)) {
        return searchByCity($pdo, trim($m[1]));
    }

    // ── Average-based search ───────────────────────────────────
    if (preg_match('/(?:moyenne|average|note)\s+(?:est|de|=|:)?\s*(\d{1,2}(?:[.,]\d+)?)/iu', $question, $m)) {
        $avg = floatval(str_replace(',', '.', $m[1]));
        return searchByAverage($pdo, $avg);
    }

    // ── Deadlines ──────────────────────────────────────────────
    if (preg_match('/(?:date?s?\s*limites?|deadlines?|prochaines?\s*dates?|concours|inscriptions?)/iu', $question)) {
        return searchDeadlines($pdo);
    }

    // ── Saved schools ──────────────────────────────────────────
    if (preg_match('/(?:écoles?\s+sauvegardées?|saved\s+schools?|mes\s+écoles?|favoris)/iu', $question)) {
        return searchSavedSchools($pdo, $userId);
    }

    // ── Institution detail ─────────────────────────────────────
    if (preg_match('/(?:parle?z?\s+(?:moi\s+)?de|tell\s+me\s+about|info(?:rmation)?s?\s+sur|détails?\s+sur)\s+(.+)/iu', $question, $m)) {
        return searchInstitutionDetail($pdo, trim($m[1]));
    }

    // ── Quick keyword: ENSA, ENCG, EST, etc. ──────────────────
    if (preg_match('/\b(ENSA|ENCG|EST|FST|FS|CPGE|EMI|ISCAE|ENSET|ENS|UIR|EMSI|HEM)\b/i', $question)) {
        return searchInstitutionByName($pdo, $question);
    }

    // ── Type-based search ──────────────────────────────────────
    if (preg_match('/(?:ingénieur|engineering|commerce|business|science|technique|technical|prépa|preparatory|privé|private|public|université|university|médecin|medical)/iu', $question)) {
        return searchByType($pdo, $question);
    }

    return null;
}

// ── Search Implementations ──────────────────────────────────────

function searchByCity(PDO $pdo, string $city): string
{
    $stmt = $pdo->prepare(
        "SELECT name, type, min_average, city FROM institutions 
         WHERE LOWER(city) = LOWER(?) 
         ORDER BY min_average DESC 
         LIMIT 10"
    );
    $stmt->execute([$city]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) {
        return "Je n'ai pas trouvé d'établissements à **" . htmlspecialchars($city) . "**. Essayez une autre ville comme Casablanca, Rabat, Tanger, Marrakech, Fès...";
    }

    $lines = ["📍 **Établissements à " . htmlspecialchars($city) . ":**\n"];
    foreach ($rows as $r) {
        $lines[] = "• **{$r['name']}** — {$r['type']} (moyenne min: {$r['min_average']}/20)";
    }
    $lines[] = "\n_Pour plus de détails, consultez la page Institutions._";

    return implode("\n", $lines);
}

function searchByAverage(PDO $pdo, float $average): string
{
    $stmt = $pdo->prepare(
        "SELECT name, type, min_average, city FROM institutions 
         WHERE min_average <= ? 
         ORDER BY min_average DESC 
         LIMIT 15"
    );
    $stmt->execute([$average]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) {
        return "Aucun établissement trouvé pour une moyenne de **{$average}/20**. Essayez d'élargir vos critères.";
    }

    $lines = ["📊 **Établissements accessibles avec {$average}/20:**\n"];
    foreach ($rows as $r) {
        $lines[] = "• **{$r['name']}** ({$r['city']}) — {$r['type']} (min: {$r['min_average']}/20)";
    }
    $lines[] = "\n_Utilisez notre outil d'orientation IA pour des recommandations personnalisées !_";

    return implode("\n", $lines);
}

function searchDeadlines(PDO $pdo): string
{
    $stmt = $pdo->prepare(
        "SELECT d.deadline_date, i.name, i.city 
         FROM deadlines d 
         JOIN institutions i ON d.institution_id = i.id 
         WHERE d.deadline_date >= CURDATE() 
         ORDER BY d.deadline_date ASC 
         LIMIT 10"
    );
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) {
        return "📅 Aucune date limite à venir dans la base de données. Consultez régulièrement la page Concours pour les mises à jour.";
    }

    $lines = ["📅 **Prochaines dates limites:**\n"];
    foreach ($rows as $r) {
        $date = date('d/m/Y', strtotime($r['deadline_date']));
        $lines[] = "• **{$r['name']}** ({$r['city']}) — Limite: **{$date}**";
    }

    return implode("\n", $lines);
}

function searchSavedSchools(PDO $pdo, ?int $userId): string
{
    if (!$userId) {
        return "🔒 Connectez-vous pour voir vos écoles sauvegardées.";
    }

    $stmt = $pdo->prepare(
        "SELECT i.name, i.type, i.city, i.min_average 
         FROM saved_schools ss 
         JOIN institutions i ON ss.institution_id = i.id 
         WHERE ss.student_id = ? 
         ORDER BY ss.created_at DESC"
    );
    $stmt->execute([$userId]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) {
        return "⭐ Vous n'avez pas encore sauvegardé d'écoles. Parcourez les institutions et cliquez sur 'Sauvegarder' !";
    }

    $lines = ["⭐ **Vos écoles sauvegardées:**\n"];
    foreach ($rows as $r) {
        $lines[] = "• **{$r['name']}** ({$r['city']}) — {$r['type']}";
    }

    return implode("\n", $lines);
}

function searchInstitutionDetail(PDO $pdo, string $name): string
{
    $searchTerm = '%' . $name . '%';
    $stmt = $pdo->prepare(
        "SELECT * FROM institutions WHERE LOWER(name) LIKE LOWER(?) LIMIT 1"
    );
    $stmt->execute([$searchTerm]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        return null; // Let Gemini handle it
    }

    $lines = [
        "🏫 **{$row['name']}**",
        "",
        "📍 Ville: **{$row['city']}**",
        "🏷️ Type: **{$row['type']}**",
        "📊 Moyenne minimum: **{$row['min_average']}/20**",
    ];

    if (!empty($row['description'])) {
        $lines[] = "📝 Description: {$row['description']}";
    }

    if (!empty($row['requirements'])) {
        $lines[] = "📋 Conditions: {$row['requirements']}";
    }

    return implode("\n", $lines);
}

function searchInstitutionByName(PDO $pdo, string $question): string
{
    // Extract institution acronym/keyword
    preg_match('/\b(ENSA|ENCG|EST|FST|FS|CPGE|EMI|ISCAE|ENSET|ENS|UIR|EMSI|HEM)\b/i', $question, $m);
    $keyword = $m[1] ?? '';

    if (empty($keyword)) {
        return null;
    }

    $stmt = $pdo->prepare(
        "SELECT name, type, city, min_average FROM institutions 
         WHERE name LIKE ? 
         ORDER BY min_average DESC 
         LIMIT 10"
    );
    $stmt->execute([$keyword . '%']);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) {
        return null;
    }

    if (count($rows) === 1) {
        $r = $rows[0];
        return "🏫 **{$r['name']}** — {$r['type']} à {$r['city']} (moyenne min: {$r['min_average']}/20)";
    }

    $lines = ["🏫 **Résultats pour {$keyword}:**\n"];
    foreach ($rows as $r) {
        $lines[] = "• **{$r['name']}** ({$r['city']}) — {$r['type']} (min: {$r['min_average']}/20)";
    }

    return implode("\n", $lines);
}

function searchByType(PDO $pdo, string $question): string
{
    $typeMap = [
        'ingénieur'    => 'Engineering',
        'engineering'  => 'Engineering',
        'commerce'     => 'Business',
        'business'     => 'Business',
        'gestion'      => 'Business',
        'science'      => 'Science',
        'technique'    => 'Technical',
        'technical'    => 'Technical',
        'prépa'        => 'Preparatory',
        'preparatory'  => 'Preparatory',
        'privé'        => 'Private',
        'private'      => 'Private',
        'public'       => 'Public',
        'université'   => 'University',
        'university'   => 'University',
        'médecin'      => 'Medical',
        'medical'      => 'Medical',
        'éducation'    => 'Education',
        'education'    => 'Education',
    ];

    $type = null;
    foreach ($typeMap as $fr => $en) {
        if (stripos($question, $fr) !== false) {
            $type = $en;
            break;
        }
    }

    if (!$type) {
        return null;
    }

    $stmt = $pdo->prepare(
        "SELECT name, city, min_average FROM institutions 
         WHERE type = ? 
         ORDER BY min_average DESC 
         LIMIT 10"
    );
    $stmt->execute([$type]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) {
        return null;
    }

    $lines = ["🏷️ **Établissements de type {$type}:**\n"];
    foreach ($rows as $r) {
        $lines[] = "• **{$r['name']}** ({$r['city']}) — min: {$r['min_average']}/20";
    }

    return implode("\n", $lines);
}

// ═══════════════════════════════════════════════════════════════
// HELPERS
// ═══════════════════════════════════════════════════════════════

function buildSystemPrompt(PDO $pdo, ?int $userId, string $context = 'profile'): string
{
    $userContext = '';
    if ($userId) {
        $stmt = $pdo->prepare("SELECT name, bac_branch, average, city FROM students WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user) {
            $userContext = "\n\nProfil de l'utilisateur connecté:\n";
            $userContext .= "- Nom: {$user['name']}\n";
            $userContext .= "- Branche Bac: " . ($user['bac_branch'] ?: 'Non renseigné') . "\n";
            $userContext .= "- Moyenne: " . ($user['average'] ? $user['average'] . '/20' : 'Non renseignée') . "\n";
            $userContext .= "- Ville: " . ($user['city'] ?: 'Non renseignée') . "\n";
        }
    }

    // Context-specific system prompts
    $basePrompt = "Tu es Maslaki AI, l'assistant intelligent de la plateforme d'orientation éducative Maslaki (Maroc). \nTu aides les étudiants marocains à choisir leur orientation post-bac.\n\nRègles:\n- Réponds en français par défaut, sauf si l'utilisateur écrit dans une autre langue.\n- Sois concis, amical et utile.\n- Concentre-toi sur l'orientation scolaire au Maroc.\n- Si on te demande des infos sur des écoles, villes, moyennes ou concours au Maroc, donne des réponses précises.\n- Ne invente jamais d'informations. Si tu n'es pas sûr, dis-le.\n- Tu peux mentionner les types d'établissements: ENSA, ENCG, EST, FST, CPGE, facultés, écoles privées, etc.\n- Les moyennes sont sur 20 au Maroc.";

    if ($context === 'orientation') {
        $basePrompt .= "\n\nContexte actuel: L'utilisateur est sur la page d'orientation IA. Tu es son conseiller d'orientation intelligent. Ton rôle principal est de:\n- Analyser son profil étudiant (branche bac, moyenne, ville)\n- Expliquer les recommandations d'établissements\n- Comparer les écoles entre elles\n- Donner des conseils personnalisés d'orientation\n- Aider à comprendre les critères d'admission";
    } else {
        $basePrompt .= "\n\nContexte actuel: L'utilisateur est sur son profil/dashboard. Tu es son assistant personnel. Ton rôle principal est de:\n- Expliquer les recommandations d'écoles\n- Suggérer des écoles adaptées à son profil\n- Répondre aux questions basées sur son profil\n- L'aider à gérer ses écoles sauvegardées";
    }

    return $basePrompt . $userContext;
}

function saveLog(PDO $pdo, ?int $userId, string $question, string $answer, string $source): void
{
    try {
        $stmt = $pdo->prepare(
            "INSERT INTO chatbot_logs (user_id, question, answer, source) VALUES (?, ?, ?, ?)"
        );
        $stmt->execute([$userId, $question, $answer, $source]);
    } catch (PDOException $e) {
        error_log('Chatbot log save failed: ' . $e->getMessage());
    }
}
