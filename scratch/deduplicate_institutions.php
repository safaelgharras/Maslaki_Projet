<?php
require 'config/DataBase.php';

// 1. Find duplicate names
$dupes = $pdo->query("SELECT name FROM institutions GROUP BY name HAVING COUNT(*) > 1")->fetchAll(PDO::FETCH_COLUMN);

echo "Found " . count($dupes) . " duplicate names.\n";

$tablesToUpdate = [
    'institution_filieres' => 'institution_id',
    'institution_bac_types' => 'institution_id',
    'institution_images' => 'institution_id',
    'saved_schools' => 'institution_id',
    'reviews' => 'institution_id',
    'deadlines' => 'institution_id',
    'contests' => 'institution_id'
];

foreach ($dupes as $name) {
    // Get all IDs for this name
    $stmt = $pdo->prepare("SELECT id FROM institutions WHERE name = ? ORDER BY id ASC");
    $stmt->execute([$name]);
    $ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    $masterId = array_shift($ids); // Keep the first one
    $idsToDelete = $ids; // The rest are duplicates
    
    echo "Processing '$name': Keeping ID $masterId, Deleting IDs: " . implode(', ', $idsToDelete) . "\n";
    
    foreach ($idsToDelete as $oldId) {
        // Reassign foreign keys
        foreach ($tablesToUpdate as $table => $column) {
            $stmt = $pdo->prepare("UPDATE IGNORE $table SET $column = ? WHERE $column = ?");
            $stmt->execute([$masterId, $oldId]);
            
            // Delete if IGNORE didn't work (e.g. unique constraint violation)
            $stmt = $pdo->prepare("DELETE FROM $table WHERE $column = ?");
            $stmt->execute([$oldId]);
        }
        
        // Delete the duplicate institution
        $stmt = $pdo->prepare("DELETE FROM institutions WHERE id = ?");
        $stmt->execute([$oldId]);
    }
}

echo "Deduplication complete.\n";
