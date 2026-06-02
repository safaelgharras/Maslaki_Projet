<?php
require_once "includes/helpers.php";
require "config/DataBase.php";
require_once "includes/csrf.php";

// Validate authentication
if (!is_logged_in()) {
    if (is_ajax_request()) {
        json_error('Not logged in', 401);
    }
    header("Location: views/login.php");
    exit();
}

// Validate request method and CSRF
if (!require_method('POST', false) || !verify_csrf_token($_POST["csrf_token"] ?? null)) {
    if (is_ajax_request()) {
        json_error('Invalid request', 403);
    }
    header("Location: views/saved_schools.php?error=" . urlencode('Invalid request'));
    exit();
}

// Validate ID parameter
$rawId = $_POST["id"] ?? null;
if ($rawId === null || !is_numeric($rawId)) {
    if (is_ajax_request()) {
        json_error('Invalid school');
    }
    header("Location: views/saved_schools.php?error=" . urlencode('Invalid school'));
    exit();
}

$student_id = current_user_id();
$institution_id = (int) $rawId;

// Delete the saved school
$sql = "DELETE FROM saved_schools WHERE student_id = ? AND institution_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$student_id, $institution_id]);

if ($stmt->rowCount() > 0) {
    if (is_ajax_request()) {
        json_success('Ecole supprimee avec succes');
    }
    header("Location: views/saved_schools.php?success=" . urlencode('Ecole supprimee avec succes'));
    exit();
}

// No rows affected
if (is_ajax_request()) {
    json_error('Ecole introuvable ou deja supprimee', 404);
}
header("Location: views/saved_schools.php?error=" . urlencode('Ecole introuvable ou deja supprimee'));
exit();
?>
