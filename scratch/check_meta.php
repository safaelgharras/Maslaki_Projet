<?php
require __DIR__ . '/../config/DataBase.php';

$stmt = $pdo->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
echo "All Tables in maslaki:\n";
print_r($tables);

// Let's also check distinct types and sectors in institutions
$stmt = $pdo->query("SELECT DISTINCT type, sector_type FROM institutions");
$types = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "\nDistinct Types & Sector Types in institutions:\n";
print_r($types);

// Let's check how many cities (villes) exist and get the ID for Tanger
$stmt = $pdo->query("SELECT * FROM villes");
$villes = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "\nAll Cities (villes):\n";
print_r($villes);
?>
