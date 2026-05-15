-- Fix for missing tables in Maslaki database

-- 1. Create domains table
CREATE TABLE IF NOT EXISTS domains (
    id INT AUTO_INCREMENT PRIMARY KEY,
    categorie_id INT,
    nom VARCHAR(150) NOT NULL,
    nom_ar VARCHAR(150) DEFAULT NULL,
    nom_en VARCHAR(150) DEFAULT NULL,
    description TEXT,
    FOREIGN KEY (categorie_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2. Modify filieres table to link to domains
ALTER TABLE filieres ADD COLUMN IF NOT EXISTS domain_id INT AFTER nom;
-- Only add constraint if it doesn't exist
SET @dbname = DATABASE();
SET @tablename = "filieres";
SET @columnname = "domain_id";
SET @preparedStatement = (SELECT IF(
  (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS
   WHERE TABLE_SCHEMA = @dbname AND TABLE_NAME = @tablename AND COLUMN_NAME = @columnname) > 0,
  "SELECT 1",
  "ALTER TABLE filieres ADD COLUMN domain_id INT AFTER nom"
));
PREPARE stmt FROM @preparedStatement;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 3. Create institution_bac_types relationship
CREATE TABLE IF NOT EXISTS institution_bac_types (
    institution_id INT,
    bac_type_id INT,
    min_grade FLOAT,
    PRIMARY KEY (institution_id, bac_type_id),
    FOREIGN KEY (institution_id) REFERENCES institutions(id) ON DELETE CASCADE,
    FOREIGN KEY (bac_type_id) REFERENCES bac_types(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Create institution_images table
CREATE TABLE IF NOT EXISTS institution_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    institution_id INT,
    image_path VARCHAR(255) NOT NULL,
    is_main TINYINT(1) DEFAULT 0,
    FOREIGN KEY (institution_id) REFERENCES institutions(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5. Seed some domains if empty
INSERT IGNORE INTO domains (id, categorie_id, nom, nom_ar, nom_en) VALUES
(1, 5, 'Développement Web', 'تطوير الويب', 'Web Development'),
(2, 5, 'Data Science', 'علم البيانات', 'Data Science'),
(3, 2, 'Finance d\'Entreprise', 'مالية المقاولات', 'Corporate Finance'),
(4, 2, 'Audit & Contrôle', 'التدقيق والمراقبة', 'Audit & Control'),
(5, 1, 'Physique Appliquée', 'الفيزياء التطبيقية', 'Applied Physics'),
(6, 6, 'Médecine Générale', 'الطب العام', 'General Medicine');

-- 6. Link some filieres to domains
UPDATE filieres SET domain_id = 1 WHERE nom LIKE '%Informatique%' OR nom LIKE '%Web%';
UPDATE filieres SET domain_id = 2 WHERE nom LIKE '%Data%';
UPDATE filieres SET domain_id = 3 WHERE nom LIKE '%Finance%';
