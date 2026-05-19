<?php
require __DIR__ . '/../config/DataBase.php';

$stmt = $pdo->query("SHOW CREATE TABLE filieres");
$res = $stmt->fetch(PDO::FETCH_ASSOC);
echo "=== filieres ===\n";
echo $res['Create Table'] . "\n\n";
?>
