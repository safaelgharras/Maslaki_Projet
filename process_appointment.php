<?php
require_once "includes/lang_helper.php";
require_once "includes/helpers.php";
require "config/DataBase.php";
require_once "includes/csrf.php";

require_auth('views/login.php');

$userId = current_user_id();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $action = $_POST['action'] ?? null;

    // Handle appointment creation
    if ($action === 'create') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            header("Location: views/appointments.php?error=" . urlencode(__('error_invalid_request')));
            exit();
        }

        $title = $_POST['title'] ?? '';
        $date = $_POST['date'] ?? '';
        $time = $_POST['time'] ?? '';

        if (empty($title) || empty($date) || empty($time)) {
            header("Location: views/appointments.php?error=" . urlencode(__('error_all_fields_required')));
            exit();
        }

        try {
            $stmt = $pdo->prepare("INSERT INTO appointments (student_id, title, appointment_date, appointment_time, status) VALUES (?, ?, ?, ?, 'pending')");
            $stmt->execute([$userId, $title, $date, $time]);
            
            // Add a notification for the student
            $notifStmt = $pdo->prepare("INSERT INTO notifications (target_user_id, title, message, type, is_global) VALUES (?, ?, ?, 'system', 0)");
            $notifStmt->execute([$userId, __('notif_appointment_confirmed'), sprintf(__('notif_appointment_msg'), $title)]);

            header("Location: views/appointments.php?success=1");
        } catch (Exception $e) {
            error_log("Appointment creation failed: " . $e->getMessage());
            header("Location: views/appointments.php?error=1");
        }
        exit();
    }

    // Handle appointment deletion
    if ($action === 'delete') {
        if (!verify_csrf_token($_POST['csrf_token'] ?? null)) {
            header("Location: views/appointments.php?error=" . urlencode(__('error_invalid_request')));
            exit();
        }

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        if ($id <= 0) {
            header("Location: views/appointments.php?error=" . urlencode(__('error_invalid_id')));
            exit();
        }

        try {
            // Verify ownership before deletion
            $stmt = $pdo->prepare("DELETE FROM appointments WHERE id = ? AND student_id = ?");
            $stmt->execute([$id, $userId]);
            
            if ($stmt->rowCount() > 0) {
                header("Location: views/appointments.php?deleted=1");
            } else {
                header("Location: views/appointments.php?error=" . urlencode(__('error_appointment_not_found')));
            }
        } catch (Exception $e) {
            error_log("Appointment deletion failed: " . $e->getMessage());
            header("Location: views/appointments.php?error=1");
        }
        exit();
    }
}

// Invalid request
header("Location: views/appointments.php?error=" . urlencode(__('error_invalid_request')));
exit();
?>
