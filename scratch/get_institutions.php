<?php
require __DIR__ . '/../config/DataBase.php';

$stmt = $pdo->query("DESCRIBE institutions");
echo "TABLE STRUCTURE:\n";
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));

$stmt2 = $pdo->query("SELECT id, name, city, ville_id, image, seuil, diplome FROM institutions LIMIT 10");
echo "\nDATA SAMPLE:\n";
print_r($stmt2->fetchAll(PDO::FETCH_ASSOC));

// Also let's output a summary of current names in the database
$stmt3 = $pdo->query("SELECT id, name, city FROM institutions");
echo "\nALL NAMES:\n";
print_r($stmt3->fetchAll(PDO::FETCH_ASSOC));
