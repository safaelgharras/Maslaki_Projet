<?php
require 'config/DataBase.php';
$tables = [
    'institution_filieres' => 'institution_id',
    'institution_bac_types' => 'institution_id',
    'institution_images' => 'institution_id',
    'saved_schools' => 'school_id',
    'reviews' => 'institution_id',
    'deadlines' => 'institution_id',
    'contests' => 'institution_id',
    'appointments' => 'institution_id'
];

foreach ($tables as $table => $column) {
    try {
        $stmt = $pdo->query("DESCRIBE $table");
        $cols = $stmt->fetchAll(PDO::FETCH_COLUMN);
        if (in_array($column, $cols)) {
            echo "$table has $column\n";
        } else {
            echo "WARNING: $table DOES NOT have $column. Available: " . implode(', ', $cols) . "\n";
        }
    } catch (Exception $e) {
        echo "Error checking $table: " . $e->getMessage() . "\n";
    }
}
