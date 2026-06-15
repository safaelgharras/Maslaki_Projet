<?php
/**
 * check_new_notifications.php — JSON API for polling unread notification count.
 *
 * Called every 30 seconds by the header JS to update the bell badge.
 * Returns: { unread_count: int, latest: {title, message, ...} | null }
 *
 * Excludes soft-deleted and already-read notifications.
 */

session_start();
require "config/DataBase.php";

// Return zero count for unauthenticated users
if (!isset($_SESSION["user_id"])) {
    echo json_encode(["unread_count" => 0]);
    exit();
}

$userId = $_SESSION["user_id"];

try {
    // Count unread notifications (global + targeted to this user, not deleted, not read)
    $sql = "SELECT COUNT(DISTINCT n.id) FROM notifications n
            LEFT JOIN user_notifications un ON n.id = un.notification_id AND un.user_id = ?
            WHERE (n.is_global = 1 OR n.target_user_id = ?)
            AND (un.is_read IS NULL OR un.is_read = 0)
            AND (un.is_deleted IS NULL OR un.is_deleted = 0)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $userId]);
    $unreadCount = $stmt->fetchColumn();

    // Get the most recent unread notification (for toast popup display)
    $sqlLatest = "SELECT n.* FROM notifications n
                  LEFT JOIN user_notifications un ON n.id = un.notification_id AND un.user_id = ?
                  WHERE (n.is_global = 1 OR n.target_user_id = ?)
                  AND (un.is_read IS NULL OR un.is_read = 0)
                  AND (un.is_deleted IS NULL OR un.is_deleted = 0)
                  GROUP BY n.id
                  ORDER BY n.id DESC LIMIT 1";
    $stmtLatest = $pdo->prepare($sqlLatest);
    $stmtLatest->execute([$userId, $userId]);
    $latest = $stmtLatest->fetch(PDO::FETCH_ASSOC);

    // Translate title/message if a notification was found
    if ($latest) {
        require_once "includes/lang_helper.php";
        $latest['title'] = __($latest['title']);
        $latest['message'] = __($latest['message']);
    }

    header('Content-Type: application/json');
    echo json_encode([
        "unread_count" => (int)$unreadCount,
        "latest" => $latest
    ]);
} catch (Exception $e) {
    echo json_encode(["unread_count" => 0]);
}
?>
