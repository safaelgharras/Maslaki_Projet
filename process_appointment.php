<?php
session_start();
require "config/DataBase.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: views/login.php");
    exit();
}

$userId = $_SESSION["user_id"];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $date = $_POST['date'];
    $time = $_POST['time'];

    try {
        $stmt = $pdo->prepare("INSERT INTO appointments (student_id, title, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, 'pending')");
        $stmt->execute([$userId, $title, $date, $time]);
        
        // Add a notification for the student
        $notifStmt = $pdo->prepare("INSERT INTO notifications (target_user_id, title, message, type, is_global) VALUES (?, 'Rendez-vous Confirmé', ?, 'system', 0)");
        $notifStmt->execute([$userId, "Votre rendez-vous pour '$title' a été enregistré avec succès."]);

        header("Location: views/appointments.php?success=1");
    } catch (Exception $e) {
        header("Location: views/appointments.php?error=1");
    }
    exit();
}

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ? AND student_id = ?");
        $stmt->execute([$id, $userId]);
        header("Location: views/appointments.php?deleted=1");
    } catch (Exception $e) {
        header("Location: views/appointments.php?error=1");
    }
    exit();
}
?>
