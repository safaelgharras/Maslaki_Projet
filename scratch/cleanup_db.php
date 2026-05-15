<?php
require "config/DataBase.php";

try {
    $sql = file_get_contents("database/cleanup_duplicates.sql");
    $pdo->exec($sql);
    echo "Database cleanup completed successfully!\n";
} catch (Exception $e) {
    echo "Error cleaning up: " . $e->getMessage() . "\n";
}
?>
