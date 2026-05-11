-- Maslaki English Localization Update
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- 1. Update Villes
ALTER TABLE `villes` ADD COLUMN IF NOT EXISTS `nom_en` varchar(100) DEFAULT NULL;
UPDATE `villes` SET `nom_en` = 'Casablanca' WHERE `nom` = 'Casablanca';
UPDATE `villes` SET `nom_en` = 'Rabat' WHERE `nom` = 'Rabat';
UPDATE `villes` SET `nom_en` = 'Marrakech' WHERE `nom` = 'Marrakech';
UPDATE `villes` SET `nom_en` = 'Fez' WHERE `nom` = 'Fes';
UPDATE `villes` SET `nom_en` = 'Tangier' WHERE `nom` = 'Tanger';
UPDATE `villes` SET `nom_en` = 'Agadir' WHERE `nom` = 'Agadir';
UPDATE `villes` SET `nom_en` = 'Oujda' WHERE `nom` = 'Oujda';
UPDATE `villes` SET `nom_en` = 'Kenitra' WHERE `nom` = 'Kenitra';
UPDATE `villes` SET `nom_en` = 'Settat' WHERE `nom` = 'Settat';

-- 2. Update Categories
ALTER TABLE `categories` ADD COLUMN IF NOT EXISTS `nom_en` varchar(100) DEFAULT NULL;
UPDATE `categories` SET `nom_en` = 'Exact Sciences & Technologies' WHERE `nom` LIKE 'Sciences Exactes%';
UPDATE `categories` SET `nom_en` = 'Engineering & Industry' WHERE `nom` LIKE 'Ingénierie%';
UPDATE `categories` SET `nom_en` = 'Health & Life Sciences' WHERE `nom` LIKE 'Santé%';
UPDATE `categories` SET `nom_en` = 'Agriculture & Environment' WHERE `nom` LIKE 'Agriculture%';
UPDATE `categories` SET `nom_en` = 'Business, Management & Finance' WHERE `nom` LIKE 'Business%';
UPDATE `categories` SET `nom_en` = 'Law, Politics & Society' WHERE `nom` LIKE 'Droit%';
UPDATE `categories` SET `nom_en` = 'Arts, Design & Media' WHERE `nom` LIKE 'Arts%';
UPDATE `categories` SET `nom_en` = 'Services, Tourism & Transport' WHERE `nom` LIKE 'Services%';
UPDATE `categories` SET `nom_en` = 'Education & Human Sciences' WHERE `nom` LIKE 'Éducation%';
UPDATE `categories` SET `nom_en` = 'Vocational Training & Crafts' WHERE `nom` LIKE 'Formation%';

-- 3. Update Institutions
ALTER TABLE `institutions` 
ADD COLUMN IF NOT EXISTS `name_en` varchar(150) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `city_en` varchar(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `description_en` text DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `requirements_en` text DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `diplome_en` varchar(150) DEFAULT NULL;

-- Sample English data for main institutions
UPDATE `institutions` SET 
`name_en` = 'National School of Applied Sciences Casablanca', 
`city_en` = 'Casablanca',
`description_en` = 'A public engineering school affiliated with Hassan II University.',
`diplome_en` = 'State Engineering Diploma'
WHERE `name` LIKE '%ENSA Casablanca%';

UPDATE `institutions` SET 
`name_en` = 'National School of Business and Management Casablanca', 
`city_en` = 'Casablanca',
`description_en` = 'A high school specialized in business and management sciences.',
`diplome_en` = 'ENCG Diploma'
WHERE `name` LIKE '%ENCG Casablanca%';

-- 4. Update Domains & Filieres
ALTER TABLE `domains` ADD COLUMN IF NOT EXISTS `nom_en` varchar(150) DEFAULT NULL;
ALTER TABLE `filieres` ADD COLUMN IF NOT EXISTS `nom_en` varchar(150) DEFAULT NULL;

-- 5. Update Bac Types
ALTER TABLE `bac_types` ADD COLUMN IF NOT EXISTS `nom_en` varchar(100) DEFAULT NULL;
UPDATE `bac_types` SET `nom_en` = 'Mathematical Sciences A' WHERE `code` = 'SMA';
UPDATE `bac_types` SET `nom_en` = 'Mathematical Sciences B' WHERE `code` = 'SMB';
UPDATE `bac_types` SET `nom_en` = 'Physics-Chemistry' WHERE `code` = 'PC';
UPDATE `bac_types` SET `nom_en` = 'Life and Earth Sciences' WHERE `code` = 'SVT';
UPDATE `bac_types` SET `nom_en` = 'Economic Sciences' WHERE `code` = 'ECO';
UPDATE `bac_types` SET `nom_en` = 'Accounting Management' WHERE `code` = 'GEST';
UPDATE `bac_types` SET `nom_en` = 'Sciences and Technologies' WHERE `code` = 'TECH';
UPDATE `bac_types` SET `nom_en` = 'Literature' WHERE `code` = 'LET';
UPDATE `bac_types` SET `nom_en` = 'Human Sciences' WHERE `code` = 'SH';
UPDATE `bac_types` SET `nom_en` = 'Vocational Baccalaureate' WHERE `code` = 'PROF';

COMMIT;
