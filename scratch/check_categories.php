<?php
require __DIR__ . '/../config/DataBase.php';

$stmt = $pdo->query("SELECT id, nom, nom_ar, nom_en FROM categories");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
