<?php
require __DIR__ . '/../config/DataBase.php';

$stmt = $pdo->query("SELECT id, nom FROM filieres WHERE nom LIKE '%Web%' OR nom LIKE '%Développement%' OR nom LIKE '%Informatique%'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
