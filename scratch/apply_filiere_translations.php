<?php
require "config/DataBase.php";

try {
    $sql = file_get_contents("database/translate_filieres.sql");
    $pdo->exec($sql);
    echo "Filiere translations applied successfully!\n";
} catch (Exception $e) {
    echo "Error applying translations: " . $e->getMessage() . "\n";
}
?>
