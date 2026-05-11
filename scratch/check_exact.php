<?php
require 'config/DataBase.php';
$categories = $pdo->query("SELECT * FROM categories")->fetchAll(PDO::FETCH_ASSOC);
foreach ($categories as $cat) {
    echo "ID: " . $cat['id'] . " | Nom: '" . $cat['nom'] . "' | AR: '" . ($cat['nom_ar'] ?? 'NULL') . "' | EN: '" . ($cat['nom_en'] ?? 'NULL') . "'\n";
}
