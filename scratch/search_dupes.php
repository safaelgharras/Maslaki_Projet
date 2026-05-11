<?php
require 'config/DataBase.php';
echo "--- Categories ---\n";
$stmt = $pdo->query("SELECT * FROM categories WHERE nom LIKE '%Education%' OR nom LIKE '%Éducation%'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

echo "\n--- Domains ---\n";
$stmt = $pdo->query("SELECT * FROM domains WHERE nom LIKE '%Education%' OR nom LIKE '%Éducation%'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
