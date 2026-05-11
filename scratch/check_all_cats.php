<?php
require 'config/DataBase.php';
$stmt = $pdo->query("SELECT * FROM categories");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
