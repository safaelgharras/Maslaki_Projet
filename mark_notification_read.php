<?php
session_start();
require "config/DataBase.php";

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit();
}

$userId = $_SESSION["user_id"];
$notifId = isset($_GET["id"]) ? (int)$_GET["id"] : 0;
$all = isset($_GET["all"]) ? (int)$_GET["all"] : 0;

try {
    if ($all) {
        // Mark all as read
        // First find all relevant notifications for this user that aren't read yet
        $sql = "SELECT n.id FROM notifications n
                LEFT JOIN user_notifications un ON n.id = un.notification_id AND un.user_id = ?
                WHERE (n.is_global = 1 OR n.target_user_id = ?)
                AND (un.is_read IS NULL OR un.is_read = 0)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$userId, $userId]);
        $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);

        if (!empty($ids)) {
            foreach ($ids as $id) {
                $pdo->prepare("INSERT INTO user_notifications (user_id, notification_id, is_read, read_at) 
                               VALUES (?, ?, 1, NOW()) 
                               ON DUPLICATE KEY UPDATE is_read = 1, read_at = NOW()")
                    ->execute([$userId, $id]);
            }
        }
        echo json_encode(["status" => "success"]);
    } elseif ($notifId > 0) {
        // Mark specific as read
        $stmt = $pdo->prepare("INSERT INTO user_notifications (user_id, notification_id, is_read, read_at) 
                               VALUES (?, ?, 1, NOW()) 
                               ON DUPLICATE KEY UPDATE is_read = 1, read_at = NOW()");
        $stmt->execute([$userId, $notifId]);
        echo json_encode(["status" => "success"]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid ID"]);
    }
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
