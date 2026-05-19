<?php
require __DIR__ . '/../config/DataBase.php';

try {
    // Just a dummy query to test where it fails
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $userId = 1;
    $title = "Test";
    $date = "2026-05-20";
    $time = "10:00:00";
    
    $stmt = $pdo->prepare("INSERT INTO appointments (student_id, title, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, 'pending')");
    $stmt->execute([$userId, $title, $date, $time]);
    echo "Appointments insert successful.\n";
    
    $notifStmt = $pdo->prepare("INSERT INTO notifications (student_id, message, type) VALUES (?, ?, 'success')");
    $notifStmt->execute([$userId, "Test notif"]);
    echo "Notifications insert successful.\n";
    
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
?>
