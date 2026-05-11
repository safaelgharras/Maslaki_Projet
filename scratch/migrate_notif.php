<?php
require "config/DataBase.php";
$sql = file_get_contents("database/notifications_setup.sql");
try {
    $pdo->exec($sql);
    echo "Migration successful!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
