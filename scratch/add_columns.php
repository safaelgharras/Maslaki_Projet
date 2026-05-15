<?php
require "config/DataBase.php";

try {
    $sql = file_get_contents("database/ensure_translations_columns.sql");
    $pdo->exec($sql);
    echo "Columns added successfully!\n";
} catch (Exception $e) {
    echo "Error adding columns: " . $e->getMessage() . "\n";
}
?>
