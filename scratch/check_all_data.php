<?php
require 'config/DataBase.php';
echo "--- Categories ---\n";
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
foreach ($categories as $cat) {
    echo $cat['id'] . ": " . $cat['nom'] . "\n";
}

echo "\n--- Domains ---\n";
$domains = $pdo->query("SELECT * FROM domains")->fetchAll(PDO::FETCH_ASSOC);
foreach ($domains as $d) {
    echo $d['id'] . ": " . $d['nom'] . " (Cat: " . $d['categorie_id'] . ")\n";
}
