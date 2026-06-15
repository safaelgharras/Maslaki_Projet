<?php
/**
 * delete_notification.php — Soft-delete a notification for the logged-in user.
 *
 * Called via AJAX (GET with ?id=N). Marks the notification as deleted
 * in the user_notifications pivot table using INSERT … ON DUPLICATE KEY UPDATE
 * so it works whether or not a user_notifications row already exists.
 * Returns a JSON response.
 */

session_start();
require "config/DataBase.php";

// Reject unauthenticated requests
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => "error", "message" => "Not logged in"]);
    exit();
}

$userId  = $_SESSION["user_id"];
$notifId = isset($_GET["id"]) ? (int)$_GET["id"] : 0;

// Validate the notification ID
if ($notifId <= 0) {
    echo json_encode(["status" => "error", "message" => "Invalid ID"]);
    exit();
}

try {
    // Upsert: create the row if missing, otherwise just flip is_deleted to 1
    $stmt = $pdo->prepare("INSERT INTO user_notifications (user_id, notification_id, is_deleted) 
                           VALUES (?, ?, 1) 
                           ON DUPLICATE KEY UPDATE is_deleted = 1");
    $stmt->execute([$userId, $notifId]);
    echo json_encode(["status" => "success"]);
} catch (Exception $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
