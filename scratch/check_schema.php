<?php
require __DIR__ . '/../config/DataBase.php';

$tables = ['institutions', 'filieres', 'institution_filieres', 'reviews', 'saved_institutions', 'favorites', 'users', 'students'];

foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SHOW CREATE TABLE `$table`");
        $res = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "=== $table ===\n";
        echo $res['Create Table'] . "\n\n";
    } catch (PDOException $e) {
        echo "=== $table ===\n";
        echo "Table does not exist or error: " . $e->getMessage() . "\n\n";
    }
}

// Let's also check a sample row in institutions to see what values they have
try {
    $stmt = $pdo->query("SELECT * FROM institutions LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo "=== Sample Institution Row ===\n";
    print_r($row);
} catch (PDOException $e) {
    echo "Error fetching sample institution row: " . $e->getMessage() . "\n";
}
?>
