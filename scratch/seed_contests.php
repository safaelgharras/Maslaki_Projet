<?php
require "config/DataBase.php";

try {
    $sql = file_get_contents("database/seed_real_contests.sql");
    $pdo->exec($sql);
    echo "Realistic contest data seeded successfully!\n";
} catch (Exception $e) {
    echo "Error seeding contests: " . $e->getMessage() . "\n";
}
?>
