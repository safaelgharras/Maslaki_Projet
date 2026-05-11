<?php
require 'config/DataBase.php';
$res = $pdo->query('SELECT id, nom, nom_ar, nom_en FROM categories')->fetchAll(PDO::FETCH_ASSOC);
print_r($res);
