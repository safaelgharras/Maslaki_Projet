<?php
require "config/DataBase.php";

try {
    // Get ENSA Tanger ID
    $ensaId = $pdo->query("SELECT id FROM institutions WHERE name = 'ENSA Tanger'")->fetchColumn();
    // Get Bac types
    $bacTypes = $pdo->query("SELECT id, code FROM bac_types")->fetchAll(PDO::FETCH_KEY_PAIR);

    if ($ensaId) {
        // Add Bac requirements for ENSA Tanger
        $stmt = $pdo->prepare("INSERT IGNORE INTO institution_bac_types (institution_id, bac_type_id, min_grade) VALUES (?, ?, ?)");
        if (isset($bacTypes['SMA'])) $stmt->execute([$ensaId, array_search('SMA', $bacTypes), 12.0]);
        if (isset($bacTypes['SMB'])) $stmt->execute([$ensaId, array_search('SMB', $bacTypes), 12.0]);
        if (isset($bacTypes['PC'])) $stmt->execute([$ensaId, array_search('PC', $bacTypes), 14.5]);
        if (isset($bacTypes['SVT'])) $stmt->execute([$ensaId, array_search('SVT', $bacTypes), 15.5]);

        // Add some images for ENSA Tanger
        $stmt = $pdo->prepare("INSERT INTO institution_images (institution_id, image_path, is_main) VALUES (?, ?, ?)");
        $stmt->execute([$ensaId, 'ENSA Tanger.png', 1]);
        $stmt->execute([$ensaId, 'default_school.jpg', 0]);
    }

    echo "TestData for ENSA Tanger added.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
