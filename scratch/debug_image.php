<?php
require 'config/DataBase.php';
$name = 'ENCG Casablanca';
$res = $pdo->prepare("SELECT image FROM institutions WHERE name = ?");
$res->execute([$name]);
$img = $res->fetchColumn();
echo "Image in DB: [$img]\n";

$fullPath = __DIR__ . '/../assets/images/institutions/' . $img;
echo "Full Path: [$fullPath]\n";
echo "File Exists: " . (file_exists($fullPath) ? 'YES' : 'NO') . "\n";
