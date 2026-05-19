<?php
require __DIR__ . '/../config/DataBase.php';

$stmt = $pdo->query("SELECT id, nom FROM filieres WHERE id < 50");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
