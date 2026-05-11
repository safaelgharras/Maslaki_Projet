<?php
require "config/DataBase.php";
$stmt = $pdo->prepare("SELECT email FROM students WHERE id = 3");
$stmt->execute();
echo $stmt->fetchColumn();
?>
