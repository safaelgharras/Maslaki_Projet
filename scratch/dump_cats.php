<?php
require 'config/DataBase.php';
$stmt = $pdo->query("SELECT * FROM categories");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "ID: " . $row['id'] . "\n";
    echo "  NOM: " . $row['nom'] . "\n";
    echo "  AR:  " . $row['nom_ar'] . "\n";
    echo "  EN:  " . $row['nom_en'] . "\n";
    echo "------------------\n";
}
