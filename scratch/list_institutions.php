<?php
require "config/DataBase.php";
$stmt = $pdo->query("SELECT * FROM institutions");
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($results, JSON_PRETTY_PRINT);
?>
