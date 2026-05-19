<?php
require __DIR__ . '/../config/DataBase.php';

try {
    $stmt = $pdo->prepare("UPDATE institutions SET site_web = 'https://solicode.co/' WHERE id = 131");
    $stmt->execute();
    echo "Updated SoliCode website link to https://solicode.co/ !\n";
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
?>
