<?php
require 'config/DataBase.php';
$stmt = $pdo->query('SHOW COLUMNS FROM categories');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
