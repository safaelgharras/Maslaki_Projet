<?php
require 'config/DataBase.php';
$res = $pdo->query('SELECT id, nom FROM domains')->fetchAll(PDO::FETCH_ASSOC);
foreach($res as $r) {
    echo $r['id'] . " => " . $r['nom'] . "\n";
}
