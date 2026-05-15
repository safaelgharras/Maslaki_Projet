<?php
require "config/DataBase.php";

try {
    $sql = file_get_contents("database/setup_logic_relationships.sql");
    $pdo->exec($sql);
    echo "Logic relationships and institutions setup successfully!\n";
} catch (Exception $e) {
    echo "Error setting up logic: " . $e->getMessage() . "\n";
}
?>
