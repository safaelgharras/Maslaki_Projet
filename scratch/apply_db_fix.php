<?php
require "config/DataBase.php";

try {
    $sql = file_get_contents("database/fix_missing_tables.sql");
    $pdo->exec($sql);
    echo "Database fix applied successfully!\n";
} catch (Exception $e) {
    echo "Error applying fix: " . $e->getMessage() . "\n";
}
?>
