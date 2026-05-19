<?php
require __DIR__ . '/../config/DataBase.php';

$stmt = $pdo->query("SELECT * FROM bac_types");
$bac_types = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "=== bac_types ===\n";
print_r($bac_types);

$stmt = $pdo->query("SELECT * FROM filieres WHERE nom LIKE '%dev%' OR nom LIKE '%info%' OR nom LIKE '%web%' OR nom LIKE '%réseau%' OR nom LIKE '%digital%' OR nom LIKE '%data%' LIMIT 30");
$filieres = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "=== filieres related to IT/digital ===\n";
print_r($filieres);

// Check if Solicode already exists in DB
$stmt = $pdo->query("SELECT * FROM institutions WHERE name LIKE '%Solicode%' OR name_ar LIKE '%Solicode%' OR name_en LIKE '%Solicode%'");
$solicode = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "=== existing Solicode rows ===\n";
print_r($solicode);
?>
