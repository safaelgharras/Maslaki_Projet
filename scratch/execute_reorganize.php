<?php
require_once __DIR__ . '/../config/DataBase.php';
try {
    echo "Starting reorganization migration...\n";
    $sqlFile = __DIR__ . '/../database/reorganize_domains.sql';
    if (!file_exists($sqlFile)) {
        die("Error: SQL file not found at $sqlFile\n");
    }
    $sql = file_get_contents($sqlFile);
    
    // Execute the SQL multi-query
    $pdo->exec($sql);
    echo "Reorganization migration completed successfully! All tables, columns, domains, and institutions are updated.\n";
} catch (PDOException $e) {
    die("Migration failed: " . $e->getMessage() . "\n");
}
?>
