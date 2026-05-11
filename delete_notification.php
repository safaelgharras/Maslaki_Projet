<?php
session_start();
require "config/DataBase.php";

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit();
}

$userId = $_SESSION["user_id"];
$notifId = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

if ($notifId <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid ID"]);
    exit();
}

try {
    $stmt = $pdo->prepare("INSERT INTO user_notifications (user_id, notification_id, is_deleted) 
                           VALUES (?, ?, 1) 
                           ON DUPLICATE KEY UPDATE is_deleted = 1");
    $stmt->execute([$userId, $notifId]);
    echo json_encode(["status" => "success"]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
