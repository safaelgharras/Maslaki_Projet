<?php
require_once "includes/helpers.php";
require "config/DataBase.php";
require_once "includes/lang_helper.php";

if (!is_logged_in()) {
    json_response([]);
}

$userId = current_user_id();

function time_ago($timestamp) {
    $time_ago = strtotime($timestamp);
    $current_time = time();
    $time_difference = $current_time - $time_ago;
    $seconds = $time_difference;
    $minutes = round($seconds / 60);
    $hours   = round($seconds / 3600);
    $days    = round($seconds / 84600);
    $weeks   = round($seconds / 604800);
    $months  = round($seconds / 2629440);
    $years   = round($seconds / 31553280);

    if ($seconds <= 60) {
        return __('just_now');
    } else if ($minutes <= 60) {
        return $minutes == 1 ? __('one_minute_ago') : sprintf(__('minutes_ago'), $minutes);
    } else if ($hours <= 24) {
        return $hours == 1 ? __('one_hour_ago') : sprintf(__('hours_ago'), $hours);
    } else if ($days <= 7) {
        return $days == 1 ? __('yesterday') : sprintf(__('days_ago'), $days);
    } else if ($weeks <= 4.3) {
        return $weeks == 1 ? __('one_week_ago') : sprintf(__('weeks_ago'), $weeks);
    } else if ($months <= 12) {
        return $months == 1 ? __('one_month_ago') : sprintf(__('months_ago'), $months);
    } else {
        return $years == 1 ? __('one_year_ago') : sprintf(__('years_ago'), $years);
    }
}

try {
    $sql = "SELECT n.*, 
                   COALESCE(un.is_read, 0) as is_read
            FROM notifications n
            LEFT JOIN user_notifications un ON n.id = un.notification_id AND un.user_id = ?
            WHERE (n.is_global = 1 OR n.target_user_id = ?)
            AND (un.is_deleted IS NULL OR un.is_deleted = 0)
            ORDER BY n.created_at DESC LIMIT 50";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $userId]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($notifications as &$n) {
        $n['time_ago'] = time_ago($n['created_at']);
        $n['title'] = __(localized_db_field($n, 'title'));
        $n['message'] = __(localized_db_field($n, 'message'));
        
        // Add icon based on type
        $iconMap = [
            'system'       => '⚙️',
            'school'       => '🏫',
            'filiere'      => '🎓',
            'announcement' => '📢',
            'maintenance'  => '🛠️',
            'orientation'  => '🧭',
            'deadline'     => '⏰',
        ];
        $n['icon'] = $iconMap[$n['type']] ?? '🔔';
    }

    json_response($notifications);
} catch (Exception $e) {
    json_error($e->getMessage(), 500);
}
?>
