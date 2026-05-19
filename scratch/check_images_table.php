<?php
require __DIR__ . '/../config/DataBase.php';

$stmt = $pdo->query("SHOW CREATE TABLE institution_images");
$res = $stmt->fetch(PDO::FETCH_ASSOC);
echo "=== institution_images ===\n";
echo $res['Create Table'] . "\n\n";

$stmt = $pdo->query("SELECT * FROM institution_images LIMIT 10");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
