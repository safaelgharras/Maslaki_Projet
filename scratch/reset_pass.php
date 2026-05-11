<?php
require "config/DataBase.php";
$pass = password_hash('123456', PASSWORD_DEFAULT);
$pdo->prepare("UPDATE students SET password = ? WHERE id = 3")->execute([$pass]);
echo "Password reset to 123456 for user 3\n";
?>
