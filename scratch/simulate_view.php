<?php
require 'config/DataBase.php';
require 'includes/lang_helper.php';
$_SESSION['lang'] = 'en';

$categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();
foreach ($categories as &$cat) {
    $cat['nom'] = getLocalizedDbField($cat, 'nom');
}

foreach ($categories as $index => $cat) {
    echo "Index: $index | ID: " . $cat['id'] . " | Name: " . $cat['nom'] . "\n";
}
