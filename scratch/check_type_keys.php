<?php
$fr = require __DIR__ . '/../lang/fr.php';
$en = require __DIR__ . '/../lang/en.php';
$ar = require __DIR__ . '/../lang/ar.php';

foreach (['fr', 'en', 'ar'] as $lang) {
    $dict = $$lang;
    echo "=== $lang 'type_' keys ===\n";
    foreach ($dict as $k => $v) {
        if (strpos($k, 'type_') === 0) {
            echo "  '$k' => '$v'\n";
        }
    }
}
?>
