<?php
require __DIR__ . '/../config/DataBase.php';

$stmt = $pdo->query("SELECT id, nom, nom_ar, nom_en FROM filieres");
$filieres = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "=== All filieres ===\n";
print_r($filieres);
?>
