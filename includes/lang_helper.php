<?php
/**
 * lang_helper.php — Internationalization (i18n) and localization engine.
 *
 * Manages the current language selection and provides translation functions.
 *
 * Language priority:
 * 1. ?lang= query parameter (persisted in session + cookie for 30 days)
 * 2. Session variable
 * 3. Cookie value
 * 4. Default: French ('fr')
 *
 * Supported languages: French (fr), English (en), Arabic (ar)
 *
 * Functions provided:
 * - __($key) — Translate a key to the current language
 * - getLang() — Get current language code
 * - isRTL() — Check if current language is right-to-left (Arabic)
 * - getLocalizedDbField($row, $field) — Get localized DB column value (e.g., name_ar, name_en)
 * - formatLocalizedDate($dateStr) — Format a date with localized month name
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$allowed_langs = ['fr', 'en', 'ar'];

// Set language
if (isset($_GET['lang'])) {
    $lang = $_GET['lang'];
    if (in_array($lang, $allowed_langs)) {
        $_SESSION['lang'] = $lang;
        setcookie('lang', $lang, time() + (86400 * 30), "/"); // 30 days
    }
}

$currentLang = $_SESSION['lang'] ?? $_COOKIE['lang'] ?? 'fr';
if (!in_array($currentLang, $allowed_langs)) {
    $currentLang = 'fr';
}

$lang_file = __DIR__ . '/../lang/' . $currentLang . '.php';
if (file_exists($lang_file)) {
    $translations = require $lang_file;
} else {
    $translations = require __DIR__ . '/../lang/fr.php';
}

function __($key) {
    global $translations;
    return $translations[$key] ?? $key;
}

function getLang() {
    global $currentLang;
    return $currentLang;
}

function isRTL() {
    return getLang() === 'ar';
}

function getLocalizedDbField($row, $field) {
    global $currentLang;
    if ($currentLang === 'ar' && !empty($row[$field . '_ar'])) {
        return $row[$field . '_ar'];
    }
    if ($currentLang === 'en' && !empty($row[$field . '_en'])) {
        return $row[$field . '_en'];
    }
    return $row[$field] ?? '';
}

/**
 * Formats a date string into a localized format (e.g., 22 Juillet 2026 or 22 يوليوز 2026)
 */
function formatLocalizedDate($dateStr) {
    if (!$dateStr || $dateStr === '0000-00-00') return '--';
    
    $time = strtotime($dateStr);
    $day = date('d', $time);
    $monthNum = date('n', $time);
    $year = date('Y', $time);
    
    $monthName = __('month_' . $monthNum);
    
    if (getLang() === 'ar') {
        return $day . ' ' . $monthName . ' ' . $year;
    }
    
    return $day . ' ' . $monthName . ' ' . $year;
}
?>
