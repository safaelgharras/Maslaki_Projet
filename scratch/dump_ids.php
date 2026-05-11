<?php
require 'config/DataBase.php';
$stmt = $pdo->query("SELECT id FROM categories");
print_r($stmt->fetchAll(PDO::FETCH_COLUMN));
