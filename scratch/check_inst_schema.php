<?php
require 'config/DataBase.php';
$stmt = $pdo->query('DESCRIBE institutions');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
