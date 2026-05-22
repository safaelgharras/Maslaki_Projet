<?php
/**
 * google_callback.php
 * Handles both:
 *   1. Real Google OAuth 2.0 authorization code exchange
 *   2. Dev-mode simulator POST (dev_mode=1)
 */

session_start();
require_once "config/DataBase.php";
require_once "config/google_config.php";

// ──────────────────────────────────────────────
// Helper: ensure `avatar` column exists
// ──────────────────────────────────────────────
try {
    $pdo->exec("ALTER TABLE students ADD COLUMN IF NOT EXISTS avatar VARCHAR(512) DEFAULT NULL");
} catch (PDOException $e) {
    // Column may already exist – safe to ignore
}

// ──────────────────────────────────────────────
// Route A: Dev-mode simulator
// ──────────────────────────────────────────────
if (isset($_POST['dev_mode']) && $_POST['dev_mode'] === '1') {
    $name   = trim($_POST['name']   ?? 'Google User');
    $email  = trim($_POST['email']  ?? '');
    $avatar = trim($_POST['avatar'] ?? '');

    if (empty($email)) {
        header("Location: views/login.php?error=" . urlencode("Email manquant dans le simulateur."));
        exit();
    }

    processGoogleUser($pdo, $name, $email, $avatar);
}

// ──────────────────────────────────────────────
// Route B: Real Google OAuth 2.0 callback
// ──────────────────────────────────────────────
if (isset($_GET['code'])) {
    $code = $_GET['code'];

    // 1. Exchange authorization code for tokens
    $tokenResponse = curlPost('https://oauth2.googleapis.com/token', [
        'code'          => $code,
        'client_id'     => GOOGLE_CLIENT_ID,
        'client_secret' => GOOGLE_CLIENT_SECRET,
        'redirect_uri'  => GOOGLE_REDIRECT_URI,
        'grant_type'    => 'authorization_code',
    ]);

    $tokenData = json_decode($tokenResponse, true);

    if (empty($tokenData['access_token'])) {
        header("Location: views/login.php?error=" . urlencode("Échec de l'authentification Google. Veuillez réessayer."));
        exit();
    }

    $accessToken = $tokenData['access_token'];

    // 2. Fetch user profile from Google
    $userInfoJson = curlGet('https://www.googleapis.com/oauth2/v3/userinfo', $accessToken);
    $userInfo     = json_decode($userInfoJson, true);

    if (empty($userInfo['email'])) {
        header("Location: views/login.php?error=" . urlencode("Impossible de récupérer votre profil Google."));
        exit();
    }

    $name   = trim($userInfo['name']    ?? ($userInfo['given_name'] ?? 'Google User'));
    $email  = trim($userInfo['email']   ?? '');
    $avatar = trim($userInfo['picture'] ?? '');

    processGoogleUser($pdo, $name, $email, $avatar);
}

// ──────────────────────────────────────────────
// Fallback – bad request
// ──────────────────────────────────────────────
header("Location: views/login.php?error=" . urlencode("Requête invalide."));
exit();


// ══════════════════════════════════════════════
// Core logic: find or create user, start session
// ══════════════════════════════════════════════
function processGoogleUser(PDO $pdo, string $name, string $email, string $avatar): void
{
    // Check if user already exists
    $stmt = $pdo->prepare("SELECT id, name, avatar FROM students WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Existing user — update avatar if changed
        if (!empty($avatar) && $avatar !== $user['avatar']) {
            $pdo->prepare("UPDATE students SET avatar = ? WHERE id = ?")
                ->execute([$avatar, $user['id']]);
        }
        $_SESSION['user_id']     = $user['id'];
        $_SESSION['user_name']   = $user['name'];
        $_SESSION['user_avatar'] = $avatar ?: $user['avatar'];

        header("Location: views/dashboard.php");
        exit();
    }

    // New user — register with Google data
    $hashedPassword = password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT);
    $insert = $pdo->prepare(
        "INSERT INTO students (name, email, password, bac_branch, average, city, avatar)
         VALUES (?, ?, ?, ?, ?, ?, ?)"
    );
    $insert->execute([$name, $email, $hashedPassword, 'Non défini', 0, 'Non définie', $avatar]);
    $newId = $pdo->lastInsertId();

    $_SESSION['user_id']     = $newId;
    $_SESSION['user_name']   = $name;
    $_SESSION['user_avatar'] = $avatar;

    header("Location: views/dashboard.php?welcome_google=1");
    exit();
}

// ══════════════════════════════════════════════
// cURL helpers
// ══════════════════════════════════════════════
function curlPost(string $url, array $fields): string
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($fields),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_HTTPHEADER     => ['Content-Type: application/x-www-form-urlencoded'],
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response ?: '';
}

function curlGet(string $url, string $accessToken): string
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_HTTPHEADER     => ["Authorization: Bearer $accessToken"],
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return $response ?: '';
}
?>
