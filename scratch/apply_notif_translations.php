<?php
require "config/DataBase.php";

try {
    $sql = file_get_contents("database/translate_notifications.sql");
    $pdo->exec($sql);
    echo "Notifications translated successfully!\n";
} catch (Exception $e) {
    echo "Error applying translations: " . $e->getMessage() . "\n";
}
?>
