<?php
require "config/DataBase.php";
echo "CATEGORIES:\n";
$stmt = $pdo->query("SELECT * FROM categories");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) echo $row['id'] . ': ' . $row['nom'] . "\n";
echo "\nFILIERES:\n";
$stmt = $pdo->query("SELECT * FROM filieres");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) echo $row['id'] . ': ' . $row['nom'] . " (cat: " . $row['categorie_id'] . ")\n";
?>
