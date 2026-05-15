<?php
require "config/DataBase.php";

echo "--- INSTITUTIONS ---\n";
$insts = $pdo->query("SELECT id, name FROM institutions")->fetchAll(PDO::FETCH_KEY_PAIR);
print_r($insts);

echo "\n--- FILIERES ---\n";
$fils = $pdo->query("SELECT id, nom FROM filieres")->fetchAll(PDO::FETCH_KEY_PAIR);
print_r($fils);
?>
