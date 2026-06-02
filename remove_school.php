<?php
session_start();
require "config/DataBase.php";
require_once "includes/csrf.php";

$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

function respondDelete($isAjax, $status, $message, $redirectParam = 'error') {
    if ($isAjax) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['status' => $status, 'message' => $message]);
        exit();
    }

    header("Location: views/saved_schools.php?{$redirectParam}=" . urlencode($message));
    exit();
}

if (!isset($_SESSION["user_id"])) {
    if ($isAjax) {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['status' => 'error', 'message' => 'Not logged in']);
        exit();
    }
    header("Location: views/login.php");
    exit();
}

$requestMethod = $_SERVER["REQUEST_METHOD"] ?? 'GET';
$rawId = null;

if ($requestMethod !== "POST" || !verify_csrf_token($_POST["csrf_token"] ?? null)) {
    respondDelete($isAjax, 'error', 'Invalid request', 'error');
}

if (isset($_POST["id"])) {
    $rawId = $_POST["id"];
}

if ($rawId === null || !is_numeric($rawId)) {
    respondDelete($isAjax, 'error', 'Invalid school', 'error');
}

$student_id = $_SESSION["user_id"];
$institution_id = (int) $rawId;

$sql = "DELETE FROM saved_schools WHERE student_id = ? AND institution_id = ?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$student_id, $institution_id]);

if ($stmt->rowCount() > 0) {
    respondDelete($isAjax, 'success', 'Ecole supprimee avec succes', 'success');
}

respondDelete($isAjax, 'error', 'Ecole introuvable ou deja supprimee', 'error');
?>
