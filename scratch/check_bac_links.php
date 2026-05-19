<?php
require __DIR__ . '/../config/DataBase.php';

$stmt = $pdo->query("SELECT COUNT(*) FROM institution_bac_types");
echo "Total rows in institution_bac_types: " . $stmt->fetchColumn() . "\n";

$stmt = $pdo->query("SELECT * FROM institution_bac_types LIMIT 20");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
?>
