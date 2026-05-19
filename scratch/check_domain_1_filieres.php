<?php
require __DIR__ . '/../config/DataBase.php';

$stmt = $pdo->query("SELECT id, nom, nom_ar, nom_en FROM filieres WHERE domain_id = 1");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
