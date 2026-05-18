<?php
require __DIR__ . '/../config/DataBase.php';

$pdo->exec("UPDATE institutions SET name='ENSET Mohammedia', city='Mohammedia' WHERE name='ENS' AND city='ET Mohammedia'");
$pdo->exec("UPDATE institutions SET name='ENSAM Meknes', city='Meknes' WHERE name='ENSA' AND city='M'");
$pdo->exec("UPDATE institutions SET name='ENSIAS Rabat', city='Rabat' WHERE name='ENS' AND city='IAS Rabat'");

echo "Fixed manual entries.\n";
