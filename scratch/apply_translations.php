<?php
require "config/DataBase.php";

try {
    $sql = file_get_contents("database/translate_institution_details.sql");
    $pdo->exec($sql);
    echo "Institution translations applied successfully!\n";
} catch (Exception $e) {
    echo "Error applying translations: " . $e->getMessage() . "\n";
}
?>
