<?php
/**
 * Platform admin / superadmin middleware.
 *
 * Role hierarchy (stored in students.role):
 *   'student'    – regular user, no admin access
 *   'admin'      – staff: can moderate reviews & send notifications, but CANNOT manage roles
 *   'superadmin' – platform owner: all admin powers + role management
 *
 * To promote someone: UPDATE students SET role = 'superadmin' WHERE id = ...;
 */

// ──────────────────────────────────────────────
// Low-level helpers
// ──────────────────────────────────────────────

/**
 * Return the role string of a user, or null on error/missing.
 */
function platform_admin_role(PDO $pdo, int $userId): ?string
{
    try {
        $stmt = $pdo->prepare("SELECT role FROM students WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        $role = $stmt->fetchColumn();
        return ($role === false || $role === null) ? null : (string) $role;
    } catch (Exception $e) {
        return null;
    }
}

/**
 * Returns true if the logged-in user has AT LEAST 'admin' privileges
 * (i.e. role is 'admin' OR 'superadmin').
 */
function is_platform_admin(PDO $pdo): bool
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) return false;
    $role = platform_admin_role($pdo, (int) $_SESSION['user_id']);
    return in_array($role, ['admin', 'superadmin'], true);
}

/**
 * Returns true ONLY for the superadmin (platform owner).
 */
function is_superadmin(PDO $pdo): bool
{
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) return false;
    return platform_admin_role($pdo, (int) $_SESSION['user_id']) === 'superadmin';
}

// ──────────────────────────────────────────────
// Access guards (redirect / 403 on failure)
// ──────────────────────────────────────────────

/**
 * Require AT LEAST admin role. Used by reviews, notifications, dashboard.
 */
function require_platform_admin(PDO $pdo): void
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    if (!is_platform_admin($pdo)) {
        http_response_code(403);
        $pageTitle = function_exists('__') ? __('platform_admin_nav') : 'Administration';
        require __DIR__ . '/header.php';
        echo '<div class="main-content"><div class="msg msg-error" style="margin-top:40px;">'
            . htmlspecialchars(function_exists('__') ? __('platform_admin_access_denied') : 'Accès refusé. Cette page est réservée aux administrateurs.')
            . '</div></div>';
        require __DIR__ . '/footer.php';
        exit();
    }
}

/**
 * Require superadmin role. Used by the user role-management page only.
 */
function require_superadmin(PDO $pdo): void
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (!isset($_SESSION['user_id'])) {
        header('Location: login.php');
        exit();
    }

    // If they are any kind of admin but NOT superadmin → show specific denial
    if (is_platform_admin($pdo) && !is_superadmin($pdo)) {
        http_response_code(403);
        $pageTitle = function_exists('__') ? __('platform_admin_nav') : 'Administration';
        require __DIR__ . '/header.php';
        echo '<div class="main-content"><div style="max-width:600px;margin:60px auto;padding:0 20px;">'
            . '<div class="msg msg-error" style="border-radius:16px;padding:25px 30px;">'
            . '🔒 <strong>' . (function_exists('__') ? __('platform_admin_access_denied') : 'Access denied.') . '</strong><br><br>'
            . (function_exists('__') ? __('admin_role_restricted_msg') : 'Role management is reserved for the Superadmin.') . '<br>'
            . '</div>'
            . '<div style="text-align:center;margin-top:20px;"><a href="admin_dashboard.php" class="btn btn-primary">' . (function_exists('__') ? __('back_to_dashboard') : '← Back to Dashboard') . '</a></div>'
            . '</div></div>';
        require __DIR__ . '/footer.php';
        exit();
    }

    // Not an admin at all → access denied
    if (!is_superadmin($pdo)) {
        http_response_code(403);
        $pageTitle = function_exists('__') ? __('platform_admin_nav') : 'Administration';
        require __DIR__ . '/header.php';
        echo '<div class="main-content"><div class="msg msg-error" style="margin-top:40px;">'
            . htmlspecialchars(function_exists('__') ? __('platform_admin_access_denied') : 'Accès refusé.')
            . '</div></div>';
        require __DIR__ . '/footer.php';
        exit();
    }
}
