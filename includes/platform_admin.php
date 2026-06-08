<?php
/**
 * Platform organizer (staff) — users who run Maslaki, not regular students.
 * Grant access with: UPDATE students SET role = 'admin' WHERE id = ...
 * @see database/notifications_setup.sql (column students.role)
 */

/**
 * @return string|null role string or null if missing/error
 */
function platform_admin_role(PDO $pdo, int $userId): ?string
{
    try {
        $stmt = $pdo->prepare("SELECT role FROM students WHERE id = ?");
        $stmt->execute([$userId]);
        $role = $stmt->fetchColumn();
        if ($role === false || $role === null) {
            return null;
        }
        return (string) $role;
    } catch (Exception $e) {
        return null;
    }
}

function is_platform_admin(PDO $pdo): bool
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        return false;
    }
    return platform_admin_role($pdo, (int) $_SESSION['user_id']) === 'admin';
}

/**
 * Require login + role admin. Otherwise redirect to login or show access denied.
 */
function require_platform_admin(PDO $pdo): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
    if (!is_platform_admin($pdo)) {
        http_response_code(403);
        $pageTitle = function_exists('__') ? __('platform_admin_nav') : 'Administration';
        require __DIR__ . '/header.php';
        echo '<div class="container" style="margin-top:40px;"><div class="msg msg-error">'
            . htmlspecialchars(function_exists('__') ? __('platform_admin_access_denied') : 'Access denied.')
            . '</div></div>';
        require __DIR__ . '/footer.php';
        exit();
    }
}
