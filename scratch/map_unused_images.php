<?php
require __DIR__ . '/../config/DataBase.php';

$pdo->exec("UPDATE institutions SET image = 'FST - Beni mellal.png' WHERE id = 16");
$pdo->exec("UPDATE institutions SET image = 'UIR - Rabat.png' WHERE id = 23");
$pdo->exec("UPDATE institutions SET image = 'FST - ERRACHIDIA.png' WHERE id = 35");
$pdo->exec("UPDATE institutions SET image = 'YouCode - Youssoufia.png' WHERE id = 55");
$pdo->exec("UPDATE institutions SET image = 'OFPPT - National.png' WHERE id = 57");
$pdo->exec("UPDATE institutions SET image = 'UM6SS - Casablanca.png' WHERE id = 63");
$pdo->exec("UPDATE institutions SET image = 'FST OUJDA.png' WHERE id = 103");
$pdo->exec("UPDATE institutions SET image = 'OFPPT CASABLANCA.png' WHERE id = 128");

echo "Missing unused images mapped successfully!\n";
?>
