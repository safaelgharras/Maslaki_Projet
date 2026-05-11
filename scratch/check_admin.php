<?php
require "config/DataBase.php";
$stmt = $pdo->query("SELECT id, name, role FROM students LIMIT 1");
$row = $stmt->fetch(PDO::FETCH_ASSOC);
print_r($row);
?>
