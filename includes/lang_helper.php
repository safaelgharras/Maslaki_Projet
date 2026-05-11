<?php
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
?>
