<?php
require __DIR__ . '/../config/DataBase.php';

$sql = file_get_contents(__DIR__ . '/../database/fix_missing_images.sql');
$pdo->exec($sql);

echo "Missing images fixed!\n";
?>
