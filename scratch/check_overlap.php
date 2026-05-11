<?php
require 'config/DataBase.php';
echo "--- Category Names ---\n";
$stmt = $pdo->query("SELECT nom FROM categories");
print_r($stmt->fetchAll(PDO::FETCH_COLUMN));

echo "\n--- Domain Names ---\n";
$stmt = $pdo->query("SELECT nom FROM domains");
print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
