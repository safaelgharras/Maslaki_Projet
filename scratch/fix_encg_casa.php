<?php
require 'config/DataBase.php';
$pdo->query("UPDATE institutions SET image = 'default_school.jpg' WHERE image = 'encg_casa.jpg'");
echo "Fixed ENCG Casablanca record.\n";
