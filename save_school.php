<?php
require_once "includes/lang_helper.php";
require_once "includes/helpers.php";
require "config/DataBase.php";
require_once "includes/csrf.php";

// Validate authentication and request method
if (!is_logged_in()) {
    if (is_ajax_request()) {
        json_error('Not logged in', 401);
    }
    header("Location: views/login.php");
    exit();
}

if (!require_method('POST', false) || !verify_csrf_token($_POST["csrf_token"] ?? null)) {
    if (is_ajax_request()) {
        json_error('Invalid request', 403);
    }
    header("Location: views/institutions.php?error=" . urlencode(__('error_invalid_request')));
    exit();
}

if (!isset($_POST["id"]) || !is_numeric($_POST["id"])) {
    if (is_ajax_request()) {
        json_error('Invalid ID');
    }
    header("Location: views/institutions.php?error=" . urlencode(__('error_invalid_school')));
    exit();
}

$student_id = current_user_id();
$institution_id = (int) $_POST["id"];

// Check if already saved
$check = $pdo->prepare("SELECT id FROM saved_schools WHERE student_id = ? AND institution_id = ?");
$check->execute([$student_id, $institution_id]);
$existing = $check->fetch();

if ($existing) {
    // If it exists, remove it (toggle behavior)
    $pdo->prepare("DELETE FROM saved_schools WHERE id = ?")->execute([$existing['id']]);
    if (is_ajax_request()) {
        json_success('School removed', ['action' => 'removed']);
    }
    header("Location: views/institutions.php?success=" . urlencode(__('success_school_removed')));
    exit();
}

// Save the school
$sql = "INSERT INTO saved_schools (student_id, institution_id) VALUES (?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$student_id, $institution_id]);

if (is_ajax_request()) {
    json_success('School saved', ['action' => 'saved']);
}

header("Location: views/institutions.php?success=" . urlencode(__('success_school_saved')));
exit();
?>
