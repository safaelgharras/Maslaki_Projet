<?php
require "config/DataBase.php";

try {
    $sql = file_get_contents("database/populate_orientation_data.sql");
    $pdo->exec($sql);
    echo "Orientation data populated successfully!\n";
} catch (Exception $e) {
    echo "Error populating data: " . $e->getMessage() . "\n";
}
?>
