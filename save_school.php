<?php
session_start();
require "config/DataBase.php";
require_once "includes/csrf.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if (!isset($_SESSION["user_id"])) {
    if ($isAjax) {
        echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
        exit();
    }
    header("Location: views/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST" || !verify_csrf_token($_POST["csrf_token"] ?? null)) {
    if ($isAjax) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid request']);
        exit();
    }
    header("Location: views/institutions.php?error=Invalid request");
    exit();
}

if (!isset($_POST["id"]) || !is_numeric($_POST["id"])) {
    if ($isAjax) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid ID']);
        exit();
    }
    header("Location: views/institutions.php?error=Invalid school");
    exit();
}

$student_id = $_SESSION["user_id"];
$institution_id = (int) $_POST["id"];

// Check if already saved
$check = $pdo->prepare("SELECT id FROM saved_schools WHERE student_id = ? AND institution_id = ?");
$check->execute([$student_id, $institution_id]);
$existing = $check->fetch();

if ($existing) {
    // If it exists, we REMOVE it (toggle behavior for AJAX)
    $pdo->prepare("DELETE FROM saved_schools WHERE id = ?")->execute([$existing['id']]);
    if ($isAjax) {
        echo json_encode(['status' => 'success', 'action' => 'removed']);
        exit();
    }
    header("Location: views/institutions.php?success=School removed");
    exit();
}

$sql = "INSERT INTO saved_schools (student_id, institution_id) VALUES (?, ?)";
$stmt = $pdo->prepare($sql);
$stmt->execute([$student_id, $institution_id]);

if ($isAjax) {
    echo json_encode(['status' => 'success', 'action' => 'saved']);
    exit();
}

header("Location: views/institutions.php?success=School saved successfully!");
exit();
?>
