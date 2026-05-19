<?php
require __DIR__ . '/../config/DataBase.php';

$stmt = $pdo->query("SHOW CREATE TABLE institution_domain");
$res = $stmt->fetch(PDO::FETCH_ASSOC);
echo "=== institution_domain ===\n";
echo $res['Create Table'] . "\n\n";

$stmt = $pdo->query("SELECT * FROM institution_domain LIMIT 10");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "=== sample rows ===\n";
print_r($rows);

// Let's also check if there is an institution_bac_types schema
$stmt = $pdo->query("SHOW CREATE TABLE institution_bac_types");
$res = $stmt->fetch(PDO::FETCH_ASSOC);
echo "\n=== institution_bac_types ===\n";
echo $res['Create Table'] . "\n\n";

$stmt = $pdo->query("SELECT * FROM institution_bac_types LIMIT 10");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "=== sample rows ===\n";
print_r($rows);

// Let's see what categories exist in categories table
$stmt = $pdo->query("SELECT * FROM categories");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "\n=== categories ===\n";
print_r($rows);
?>
