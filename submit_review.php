<?php
/**
 * submit_review.php — Submit a new review for an institution.
 *
 * Handles POST from the review form on institution_detail.php.
 * Validates authentication, CSRF token, content, and duplicate check.
 * Inserts review with 'pending' status for admin moderation.
 * Supports optional star rating (1-5) with fallback if column is missing.
 */
require_once "includes/lang_helper.php";
require_once "includes/helpers.php";
require "config/DataBase.php";
require_once "includes/csrf.php";

// Must be logged in
require_auth('views/login.php');

// Must be POST — redirect-safe (no json_error)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: views/institutions.php");
    exit();
}

// CSRF check
$institution_id = (int) ($_POST['institution_id'] ?? 0);

if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
    $back = $institution_id > 0
        ? "views/institution_detail.php?id={$institution_id}&review_error=" . urlencode(__('error_csrf_retry'))
        : "views/institutions.php";
    header("Location: $back");
    exit();
}

if ($institution_id <= 0) {
    header("Location: views/institutions.php");
    exit();
}

$student_id = current_user_id();
$content    = trim($_POST['content'] ?? '');
$rating     = isset($_POST['rating']) ? max(1, min(5, (int) $_POST['rating'])) : null;

if (empty($content)) {
    header("Location: views/institution_detail.php?id={$institution_id}&review_error=" . urlencode(__('error_review_empty')));
    exit();
}

// One review per user per institution
$check = $pdo->prepare("SELECT id FROM reviews WHERE student_id = ? AND institution_id = ?");
$check->execute([$student_id, $institution_id]);
if ($check->fetch()) {
    header("Location: views/institution_detail.php?id={$institution_id}&review_error=" . urlencode(__('error_review_duplicate')));
    exit();
}

// Insert — try with rating first, fall back if the column doesn't exist yet
try {
    $stmt = $pdo->prepare("INSERT INTO reviews (student_id, institution_id, content, rating, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt->execute([$student_id, $institution_id, $content, $rating]);
} catch (PDOException $e) {
    // rating column not present in this environment — insert without it
    $stmt = $pdo->prepare("INSERT INTO reviews (student_id, institution_id, content, status) VALUES (?, ?, ?, 'pending')");
    $stmt->execute([$student_id, $institution_id, $content]);
}

header("Location: views/institution_detail.php?id={$institution_id}&review_success=1#reviews");
exit();
?>
