<?php
require "config/DataBase.php";
$pdo->exec("UPDATE institutions SET seuil = min_average WHERE seuil IS NULL OR seuil = ''");
echo "Seuils updated based on min_average.\n";
?>
