<?php
require 'config/DataBase.php';
$stmt = $pdo->query('DESCRIBE domains');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
