<?php
/**
 * Shared helper functions.
 *
 * These are extracted from repeated patterns across the application to reduce
 * duplication and ensure consistent behavior.
 */

// ══════════════════════════════════════════════════════════════════════════════
// SESSION & AUTH
// ══════════════════════════════════════════════════════════════════════════════

/**
 * Ensure a session is started. Safe to call multiple times.
 */
function ensure_session(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Check if the current user is authenticated.
 *
 * @return bool True if logged in, false otherwise.
 */
function is_logged_in(): bool
{
    ensure_session();
    return isset($_SESSION['user_id']);
}

/**
 * Require authentication. Redirect to login if not authenticated.
 *
 * @param string $redirectTo URL to redirect to if not logged in (default: login.php)
 */
function require_auth(string $redirectTo = 'login.php'): void
{
    if (!is_logged_in()) {
        header("Location: $redirectTo");
        exit();
    }
}

/**
 * Get the currently logged-in user ID, or null if not logged in.
 *
 * @return int|null
 */
function current_user_id(): ?int
{
    ensure_session();
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

// ══════════════════════════════════════════════════════════════════════════════
// JSON RESPONSES
// ══════════════════════════════════════════════════════════════════════════════

/**
 * Send a JSON response and halt execution.
 *
 * @param mixed $data The data to encode as JSON.
 * @param int $statusCode HTTP status code (default: 200).
 */
function json_response($data, int $statusCode = 200): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

/**
 * Send a success JSON response.
 *
 * @param string $message Success message.
 * @param array $data Additional data to include.
 */
function json_success(string $message = 'Success', array $data = []): void
{
    json_response(array_merge(['status' => 'success', 'message' => $message], $data));
}

/**
 * Send an error JSON response.
 *
 * @param string $message Error message.
 * @param int $statusCode HTTP status code (default: 400).
 * @param array $data Additional data to include.
 */
function json_error(string $message, int $statusCode = 400, array $data = []): void
{
    json_response(
        array_merge(['status' => 'error', 'message' => $message], $data),
        $statusCode
    );
}

// ══════════════════════════════════════════════════════════════════════════════
// DATABASE LOCALIZATION
// ══════════════════════════════════════════════════════════════════════════════

/**
 * Get a localized database field value.
 *
 * Looks for a field with the current language suffix (e.g., `name_ar` when
 * current language is Arabic). Falls back to the base field if not found.
 *
 * This function delegates to the existing `getLocalizedDbField` in lang_helper.php
 * if it exists, or provides a minimal implementation if not.
 *
 * @param array $row Database row.
 * @param string $field Base field name (e.g., 'name').
 * @return string Localized field value.
 */
function localized_db_field(array $row, string $field): string
{
    // Delegate to existing function if available
    if (function_exists('getLocalizedDbField')) {
        return getLocalizedDbField($row, $field);
    }

    // Minimal fallback implementation
    global $currentLang;
    $lang = $currentLang ?? ($_SESSION['lang'] ?? 'fr');

    if ($lang === 'ar' && !empty($row[$field . '_ar'])) {
        return $row[$field . '_ar'];
    }

    return $row[$field] ?? '';
}

/**
 * Localize all specified fields in a database row array.
 *
 * @param array $row Database row.
 * @param array $fields Field names to localize.
 * @return array Row with localized fields.
 */
function localize_row(array $row, array $fields): array
{
    foreach ($fields as $field) {
        $row[$field] = localized_db_field($row, $field);
    }
    return $row;
}

/**
 * Localize fields across multiple rows.
 *
 * @param array $rows Array of database rows.
 * @param array $fields Field names to localize.
 * @return array Rows with localized fields.
 */
function localize_rows(array $rows, array $fields): array
{
    foreach ($rows as &$row) {
        $row = localize_row($row, $fields);
    }
    return $rows;
}

// ══════════════════════════════════════════════════════════════════════════════
// IMAGE RESOLUTION
// ══════════════════════════════════════════════════════════════════════════════

/**
 * Resolve an institution image path.
 *
 * Attempts to find an image for the given institution in the following order:
 * 1. The value in `$dbImage` (if provided and not the default)
 * 2. Special-case mappings for known institutions
 * 3. Standard filename patterns: name.webp, name.png, name.jpg
 * 4. Default fallback image
 *
 * Checks both `assets/images/` and `assets/images/institutions/`.
 *
 * @param string $institutionName Institution name.
 * @param string|null $dbImage Image filename from database (optional).
 * @param string $baseDir Base directory to resolve paths from (default: one level up from includes/).
 * @return string Relative URL to the image.
 */
function resolve_institution_image(string $institutionName, ?string $dbImage = null, string $baseDir = null): string
{
    // Auto-detect base directory if not provided
    if ($baseDir === null) {
        // Assumes this file is in includes/ and assets/ is a sibling
        $baseDir = dirname(__DIR__);
    }

    $name = trim($institutionName);
    $normalizedName = strtolower($name);

    $candidates = [];

    // Use DB image if provided and not the default
    if (!empty($dbImage) && $dbImage !== 'default_school.jpg') {
        $candidates[] = (string) $dbImage;
    }

    // Special cases (known naming issues)
    $specialCases = [
        'cpge fes'                  => 'CPGE Fez.jpg',
        'cpge fez'                  => 'CPGE Fez.jpg',
        'emsi casablanca'           => 'EMSI Casablanca.webp',
        'eigsi casablanca'          => 'EIGSI Casablanca.webp',
        'esca ecole de management'  => 'ESCA Ecole de Management Casablanca.webp',
    ];

    if (isset($specialCases[$normalizedName])) {
        $candidates[] = $specialCases[$normalizedName];
    }

    // Standard patterns
    $candidates[] = $name . '.webp';
    $candidates[] = $name . '.png';
    $candidates[] = $name . '.jpg';
    $candidates[] = 'default_school.jpg';

    $folders = ['assets/images/', 'assets/images/institutions/'];

    foreach ($candidates as $candidate) {
        $candidate = trim($candidate);
        if ($candidate === '') {
            continue;
        }

        foreach ($folders as $folder) {
            $fullPath = $baseDir . '/' . $folder . $candidate;
            if (file_exists($fullPath)) {
                // Return relative path with URL-encoded spaces
                $relativePath = $folder . str_replace(' ', '%20', $candidate);
                // Adjust for caller's context (assume caller is in views/ or root)
                return (str_starts_with($folder, 'assets/') ? '../' : '') . $relativePath;
            }
        }
    }

    // Ultimate fallback
    return '../assets/images/default_school.jpg';
}

// ══════════════════════════════════════════════════════════════════════════════
// TYPE TRANSLATION
// ══════════════════════════════════════════════════════════════════════════════

/**
 * Translate an institution type using the translation system, with fallback.
 *
 * @param string $type Type key (e.g., 'Engineering', 'Business').
 * @return string Translated type.
 */
function translate_type(string $type): string
{
    $key = 'type_' . strtolower($type);
    $translated = function_exists('__') ? __($key) : $key;

    // If translation not found, use hardcoded map
    if ($translated === $key) {
        $map = [
            'Engineering'   => 'Ingénierie',
            'Business'      => 'Commerce',
            'Science'       => 'Sciences',
            'Technical'     => 'Technique',
            'Preparatory'   => 'Classes Prépa',
            'Private'       => 'Privé',
            'Public'        => 'Public',
            'Education'     => 'Éducation',
            'University'    => 'Université',
            'Digital'       => 'Numérique',
            'Art'           => 'Art',
            'Management'    => 'Gestion',
            'Medical'       => 'Médical',
        ];
        return $map[$type] ?? $type;
    }

    return $translated;
}

// ══════════════════════════════════════════════════════════════════════════════
// REQUEST VALIDATION
// ══════════════════════════════════════════════════════════════════════════════

/**
 * Check if the current request is an AJAX request.
 *
 * @return bool
 */
function is_ajax_request(): bool
{
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Require that the current request uses a specific HTTP method.
 *
 * @param string $method HTTP method (e.g., 'POST', 'GET').
 * @param bool $exitOnFail If true, sends JSON error and exits (default: true).
 * @return bool True if method matches, false otherwise.
 */
function require_method(string $method, bool $exitOnFail = true): bool
{
    $match = $_SERVER['REQUEST_METHOD'] === strtoupper($method);
    if (!$match && $exitOnFail) {
        json_error('Invalid request method', 405);
    }
    return $match;
}
?>
