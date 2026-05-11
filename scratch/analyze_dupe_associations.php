<?php
require 'config/DataBase.php';
$name = 'FST Mohammedia';
$stmt = $pdo->prepare('SELECT id FROM institutions WHERE name = ?');
$stmt->execute([$name]);
$ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
foreach($ids as $id) {
    $count = $pdo->query('SELECT COUNT(*) FROM institution_filieres WHERE institution_id = '.$id)->fetchColumn();
    echo "ID: $id has $count filieres\n";
}
