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
    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }
    if (!is_platform_admin($pdo)) {
        echo '<div class="container" style="margin-top:40px;"><div class="msg msg-error">'
            . htmlspecialchars(__('platform_admin_access_denied'))
            . '</div></div>';
        require __DIR__ . '/footer.php';
        exit();
    }
}
