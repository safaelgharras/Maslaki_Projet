<?php
require "config/DataBase.php";
$pdo->exec("UPDATE students SET role='admin' WHERE id=3");
echo "Role updated to admin for user 3\n";
?>
