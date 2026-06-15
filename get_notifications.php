<?php
/**
 * get_notifications.php — JSON API to fetch the logged-in user's notifications.
 *
 * Returns up to 50 notifications (global + targeted) with read status,
 * relative timestamps ("2 hours ago"), localized title/message, and
 * an emoji icon based on the notification type.
 *
 * Used by the header polling script to refresh the notification dropdown.
 */

require_once "includes/helpers.php";
require "config/DataBase.php";
require_once "includes/lang_helper.php";

// Return empty array for unauthenticated users
if (!is_logged_in()) {
    json_response([]);
}

$userId = current_user_id();

/**
 * Convert a timestamp to a human-readable relative string (e.g. "3 hours ago").
 * Uses localized translation keys for multilingual support.
 */
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
    // Fetch notifications: global ones + those targeted to this user,
    // excluding soft-deleted ones. GROUP BY prevents duplicates from the LEFT JOIN.
    $sql = "SELECT n.*, 
                   COALESCE(un.is_read, 0) as is_read
            FROM notifications n
            LEFT JOIN user_notifications un ON n.id = un.notification_id AND un.user_id = ?
            WHERE (n.is_global = 1 OR n.target_user_id = ?)
            AND (un.is_deleted IS NULL OR un.is_deleted = 0)
            GROUP BY n.id
            ORDER BY n.created_at DESC LIMIT 50";
            
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$userId, $userId]);
    $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Enrich each notification with time_ago, localized text, and an emoji icon
    foreach ($notifications as &$n) {
        $n['time_ago'] = time_ago($n['created_at']);
        $n['title'] = __(localized_db_field($n, 'title'));
        $n['message'] = __(localized_db_field($n, 'message'));
        
        // Map notification type to an emoji for the dropdown UI
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
