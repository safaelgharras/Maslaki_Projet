<?php
require __DIR__ . '/../config/DataBase.php';

try {
    $stmt = $pdo->prepare("UPDATE institutions SET type = 'Digital' WHERE id = 131");
    $stmt->execute();
    echo "Updated SoliCode type to Digital!\n";
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
?>
