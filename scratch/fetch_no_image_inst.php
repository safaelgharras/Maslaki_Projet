<?php
require 'config/DataBase.php';
$res = $pdo->query("SELECT name FROM institutions WHERE image IS NULL OR image = '' OR image = 'default_school.jpg'")->fetchAll(PDO::FETCH_COLUMN);
foreach($res as $name) {
    echo $name . "\n";
}
