<?php
require __DIR__ . '/../config/DataBase.php';

$stmt = $pdo->query("SELECT name, city, type FROM institutions LIMIT 30");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
