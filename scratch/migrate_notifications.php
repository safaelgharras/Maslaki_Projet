<?php
require_once __DIR__ . '/../config/DataBase.php';

$sqlFile = __DIR__ . '/../database/notifications_setup.sql';

if (!file_exists($sqlFile)) {
    die("SQL file not found: $sqlFile\n");
}

$sql = file_get_contents($sqlFile);

try {
    // We need to execute the SQL. Since it might contain multiple statements, 
    // and PDO::exec doesn't always handle that well depending on the driver,
    // we'll split by semicolon if needed, but the setup file uses START TRANSACTION/COMMIT.
    
    echo "Starting migration...\n";
    $pdo->exec($sql);
    echo "Migration successful! Notifications tables created.\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
}
