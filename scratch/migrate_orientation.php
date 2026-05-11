<?php
require "config/DataBase.php";

try {
    // 1. Create domains table
    $pdo->exec("CREATE TABLE IF NOT EXISTS domains (
        id INT AUTO_INCREMENT PRIMARY KEY,
        categorie_id INT,
        nom VARCHAR(150) NOT NULL,
        description TEXT,
        FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 2. Modify filieres table to link to domains
    // First, check if domain_id exists
    $stmt = $pdo->query("SHOW COLUMNS FROM filieres LIKE 'domain_id'");
    if (!$stmt->fetch()) {
        $pdo->exec("ALTER TABLE filieres ADD COLUMN domain_id INT AFTER nom");
        $pdo->exec("ALTER TABLE filieres ADD CONSTRAINT fk_filiere_domain FOREIGN KEY (domain_id) REFERENCES domains(id) ON DELETE SET NULL");
    }

    // 3. Create bac_types table
    $pdo->exec("CREATE TABLE IF NOT EXISTS bac_types (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nom VARCHAR(100) NOT NULL,
        code VARCHAR(20) UNIQUE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 4. Create institution_bac_types relationship
    $pdo->exec("CREATE TABLE IF NOT EXISTS institution_bac_types (
        institution_id INT,
        bac_type_id INT,
        min_grade FLOAT,
        PRIMARY KEY (institution_id, bac_type_id),
        FOREIGN KEY (institution_id) REFERENCES institutions(id) ON DELETE CASCADE,
        FOREIGN KEY (bac_type_id) REFERENCES bac_types(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    // 5. Create institution_images table
    $pdo->exec("CREATE TABLE IF NOT EXISTS institution_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        institution_id INT,
        image_path VARCHAR(255) NOT NULL,
        is_main TINYINT(1) DEFAULT 0,
        FOREIGN KEY (institution_id) REFERENCES institutions(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

    echo "Database schema updated successfully.\n";
} catch (Exception $e) {
    die("Migration failed: " . $e->getMessage());
}
?>
