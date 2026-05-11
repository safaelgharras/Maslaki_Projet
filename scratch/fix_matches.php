<?php
require 'config/DataBase.php';

// Fix Hassan I
$stmt = $pdo->prepare("UPDATE institutions SET image = 'Université Hassan I Setat.PNG' WHERE name LIKE 'Université Hassan I' OR name LIKE 'Université Hassan I %'");
$stmt->execute();

// Fix Hassan II
$stmt = $pdo->prepare("UPDATE institutions SET image = 'Université Hassan II Casablanca.PNG' WHERE name LIKE 'Université Hassan II' OR name LIKE 'Université Hassan II %'");
$stmt->execute();

// Fix FST Settat (The automated one matched FST Settat - Hassan 1er.png which is fine but let's be sure)
$stmt = $pdo->prepare("UPDATE institutions SET image = 'FST Settat - Hassan 1er.png' WHERE name LIKE 'FST Settat'");
$stmt->execute();

echo "Specific matches fixed.\n";
