<?php
require 'config/DataBase.php';
$res = $pdo->query("SELECT name, COUNT(*) as count FROM institutions GROUP BY name HAVING count > 1")->fetchAll(PDO::FETCH_ASSOC);
print_r($res);
