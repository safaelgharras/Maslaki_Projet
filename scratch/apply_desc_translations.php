<?php
require "config/DataBase.php";

try {
    $sql = file_get_contents("database/translate_descriptions.sql");
    $pdo->exec($sql);
    echo "Institution descriptions translated successfully!\n";
} catch (Exception $e) {
    echo "Error applying translations: " . $e->getMessage() . "\n";
}
?>
