<?php
require 'config/DataBase.php';
$stmt = $pdo->query('DESCRIBE reviews');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
