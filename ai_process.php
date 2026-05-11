<?php
session_start();
require "config/DataBase.php";

$pageTitle = "Résultats IA";
require "includes/header.php";

if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: views/ai_form.php");
    exit();
}

$bac_branch = trim($_POST["bac_branch"]);
$average = floatval($_POST["average"]);
$city = trim($_POST["city"]);

// Map bac branches to institution types
$branchToTypes = [
    'SVT'     => ['Engineering', 'Science', 'Technical', 'University', 'Preparatory', 'Private', 'Education'],
    'PC'      => ['Engineering', 'Science', 'Technical', 'University', 'Preparatory', 'Private', 'Education'],
    'Math'    => ['Engineering', 'Science', 'Technical', 'Business', 'University', 'Preparatory', 'Private', 'Education'],
    'Eco'     => ['Business', 'University', 'Private', 'Education'],
    'Tech'    => ['Technical', 'Science', 'University', 'Private', 'Education'],
    'Lettres' => ['University', 'Education', 'Private'],
];

$allowedTypes = isset($branchToTypes[$bac_branch]) ? $branchToTypes[$bac_branch] : ['University', 'Private'];

$placeholders = implode(',', array_fill(0, count($allowedTypes), '?'));

$sql = "SELECT * FROM institutions 
        WHERE min_average <= ? 
        AND type IN ($placeholders)";

$params = [$average];
$params = array_merge($params, $allowedTypes);

if (!empty($city)) {
    $sql .= " ORDER BY 
                CASE WHEN city = ? THEN 0 ELSE 1 END,
                min_average DESC";
    $params[] = $city;
} else {
    $sql .= " ORDER BY min_average DESC";
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll();

// Save recommendation
if (isset($_SESSION['user_id'])) {
    $resultText = count($results) . " écoles trouvées pour $bac_branch avec $average de moyenne";
    $saveSql = "INSERT INTO ai_recommendations (student_id, result) VALUES (?, ?)";
    $saveStmt = $pdo->prepare($saveSql);
    $saveStmt->execute([$_SESSION['user_id'], $resultText]);
}

function resolveInstitutionImagePath($institutionName, $dbImage = null) {
    $name = trim((string) ($institutionName ?? ''));
    $normalizedName = strtolower($name);

    $candidates = [];
    if (!empty($dbImage)) {
        $candidates[] = (string) $dbImage;
    }

    // Specific mappings
    $specifics = [
        'cpge fes' => 'CPGE Fez.jpg',
        'cpge fez' => 'CPGE Fez.jpg',
        'emsi casablanca' => 'EMSI Casablanca.webp',
        'eigsi casablanca' => 'EIGSI Casablanca.webp',
        'esca ecole de management' => 'ESCA Ecole de Management Casablanca.webp'
    ];

    if (isset($specifics[$normalizedName])) {
        $candidates[] = $specifics[$normalizedName];
    }

    $candidates[] = $name . '.webp';
    $candidates[] = $name . '.png';
    $candidates[] = $name . '.jpg';
    $candidates[] = 'default_school.jpg';

    foreach ($candidates as $candidate) {
        $candidate = trim((string) $candidate);
        if ($candidate === '') continue;

        if (file_exists('assets/images/' . $candidate)) {
            return 'assets/images/' . $candidate;
        }
        if (file_exists('assets/images/institutions/' . $candidate)) {
            return 'assets/images/institutions/' . $candidate;
        }
    }

    return 'assets/images/default_school.jpg';
}

function translateType($type) {
    $map = [
        'Engineering' => 'Ingénierie',
        'Business' => 'Commerce',
        'Science' => 'Sciences',
        'Technical' => 'Technique',
        'Preparatory' => 'Classes Prépa',
        'Private' => 'Privé',
        'Education' => 'Éducation',
        'University' => 'Université'
    ];
    return $map[$type] ?? $type;
}
?>

<h1 class="page-title">Recommandations IA</h1>

<div class="ai-summary-bar">
    <div class="ai-summary-icon">🤖</div>
    <div class="ai-summary-content">
        <h2>Recommandations Personnalisées</h2>
        <p>
            Nous avons trouvé <strong><?php echo count($results); ?></strong> école(s) pour ton profil 
            <strong><?php echo htmlspecialchars($bac_branch); ?></strong> avec une moyenne de 
            <strong><?php echo htmlspecialchars($average); ?>/20</strong>
            <?php if (!empty($city)): ?>
                à <strong><?php echo htmlspecialchars($city); ?></strong>
            <?php endif; ?>.
        </p>
    </div>
</div>

<?php if (count($results) == 0): ?>
    <div class="empty-state">
        <div class="icon">😔</div>
        <p>Aucune école ne correspond à tes critères. Essaie d'ajuster ta moyenne ou ta ville.</p>
        <a href="views/ai_form.php" class="btn btn-primary btn-lg" style="margin-top:15px;">Réessayer</a>
    </div>
<?php else: ?>
    <div class="cards-grid">
        <?php foreach($results as $index => $r): ?>
            <?php $imagePath = resolveInstitutionImagePath($r['name'], $r['image'] ?? null); ?>
            <div class="card ai-card stagger-<?php echo ($index % 5) + 1; ?>">
                <img src="<?php echo htmlspecialchars($imagePath); ?>" class="card-img" alt="<?php echo htmlspecialchars($r['name']); ?>">
                <div class="card-body">
                    <div class="card-header-flex">
                        <span class="premium-badge"><?php echo htmlspecialchars(translateType($r["type"])); ?></span>
                        <?php if (!empty($city) && strtolower($r["city"]) === strtolower($city)): ?>
                            <span class="location-badge city-match">📍 Ta ville</span>
                        <?php endif; ?>
                    </div>
                    
                    <h3 style="margin-top: 5px;"><?php echo htmlspecialchars($r["name"]); ?></h3>
                    
                    <p class="school-location" style="margin: 5px 0 10px 0;">📍 <?php echo htmlspecialchars($r["city"]); ?></p>

                    <p style="font-size: 0.85rem; color: var(--text-muted); min-height: 40px; margin-bottom: 12px;"><?php echo htmlspecialchars($r["description"]); ?></p>
                    
                    <div class="card-info-row" style="display: flex; gap: 15px; margin-bottom: 15px; font-size: 0.85rem; color: var(--text-muted);">
                        <span>🎓 <?php echo htmlspecialchars($r['diplome'] ?? 'Diplôme'); ?></span>
                        <span>⏳ <?php echo htmlspecialchars($r['duree_etudes'] ?? '--'); ?></span>
                    </div>

                    <div class="requirements" style="margin-bottom: 15px;">
                        <?php 
                            $reqs = explode(',', $r["requirements"] ?? '');
                            foreach($reqs as $req) {
                                if(!empty(trim($req))) {
                                    echo '<span class="requirement-tag">' . htmlspecialchars(trim($req)) . '</span>';
                                }
                            }
                        ?>
                    </div>

                    <div class="card-footer" style="margin-top: auto; padding-top: 15px; border-top: 1px solid var(--border-color); display: flex; justify-content: space-between; align-items: center;">
                        <span class="seuil">Moyenne min: <strong><?php echo htmlspecialchars($r["min_average"]); ?>/20</strong></span>
                        <div class="card-actions">
                            <?php if (isset($_SESSION['user_id'])): ?>
                                <a href="save_school.php?id=<?php echo $r['id']; ?>" class="btn btn-save" style="margin: 0; padding: 6px 12px;">Sauvegarder</a>
                            <?php endif; ?>
                            <a href="views/institution_detail.php?id=<?php echo $r['id']; ?>" class="btn-link" style="font-size: 0.85rem;">Détails →</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div style="text-align:center; margin-top:40px;">
    <a href="views/ai_form.php" class="btn btn-primary btn-lg">Nouvelle Recherche</a>
</div>

<?php require "includes/footer.php"; ?>