<?php
/**
 * csrf.php — CSRF (Cross-Site Request Forgery) protection utilities.
 *
 * Provides three functions:
 *   csrf_token()        — Returns the current session token (generates one if missing).
 *   csrf_input()        — Returns a hidden <input> HTML tag with the token for forms.
 *   verify_csrf_token() — Checks whether a submitted token matches the session token
 *                         using a timing-safe comparison (hash_equals).
 */

// Ensure a session exists before working with $_SESSION
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Generate (if needed) and return the CSRF token stored in the session.
 */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Return an HTML hidden input containing the CSRF token (for embedding in forms).
 */
function csrf_input(): string
{
    return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
}

/**
 * Verify a submitted CSRF token against the session token (timing-safe).
 */
function verify_csrf_token(?string $token): bool
{
    return isset($_SESSION['csrf_token'])
        && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}
?>
