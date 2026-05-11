<?php
require 'config/DataBase.php';
echo "--- Categories English ---\n";
$stmt = $pdo->query("SELECT nom_en, COUNT(*) as count FROM categories GROUP BY nom_en");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo $row['nom_en'] . ": " . $row['count'] . "\n";
}
echo "\n--- Categories Arabic ---\n";
$stmt = $pdo->query("SELECT nom_ar, COUNT(*) as count FROM categories GROUP BY nom_ar");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo $row['nom_ar'] . ": " . $row['count'] . "\n";
}
