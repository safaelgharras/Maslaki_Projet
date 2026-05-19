<?php
require __DIR__ . '/../config/DataBase.php';

$stmt = $pdo->query("SHOW CREATE TABLE translations");
$res = $stmt->fetch(PDO::FETCH_ASSOC);
echo "=== translations ===\n";
echo $res['Create Table'] . "\n\n";

$stmt = $pdo->query("SELECT * FROM translations LIMIT 10");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
