-- Maslaki Localization Update - Database Translation Support
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;

-- 1. Update Villes
ALTER TABLE `villes` ADD COLUMN IF NOT EXISTS `nom_ar` varchar(100) DEFAULT NULL;
UPDATE `villes` SET `nom_ar` = 'الدار البيضاء' WHERE `nom` = 'Casablanca';
UPDATE `villes` SET `nom_ar` = 'الرباط' WHERE `nom` = 'Rabat';
UPDATE `villes` SET `nom_ar` = 'مراكش' WHERE `nom` = 'Marrakech';
UPDATE `villes` SET `nom_ar` = 'فاس' WHERE `nom` = 'Fes';
UPDATE `villes` SET `nom_ar` = 'طنجة' WHERE `nom` = 'Tanger';
UPDATE `villes` SET `nom_ar` = 'أكادير' WHERE `nom` = 'Agadir';
UPDATE `villes` SET `nom_ar` = 'وجدة' WHERE `nom` = 'Oujda';
UPDATE `villes` SET `nom_ar` = 'القنيطرة' WHERE `nom` = 'Kenitra';
UPDATE `villes` SET `nom_ar` = 'سطات' WHERE `nom` = 'Settat';
UPDATE `villes` SET `nom_ar` = 'مكناس' WHERE `nom` = 'Meknes';

-- 2. Update Categories
ALTER TABLE `categories` ADD COLUMN IF NOT EXISTS `nom_ar` varchar(100) DEFAULT NULL;
UPDATE `categories` SET `nom_ar` = 'العلوم الدقيقة والتكنولوجيات' WHERE `nom` LIKE 'Sciences Exactes%';
UPDATE `categories` SET `nom_ar` = 'الهندسة والصناعة' WHERE `nom` LIKE 'Ingénierie%';
UPDATE `categories` SET `nom_ar` = 'الصحة وعلوم الحياة' WHERE `nom` LIKE 'Santé%';
UPDATE `categories` SET `nom_ar` = 'الفلاحة والبيئة' WHERE `nom` LIKE 'Agriculture%';
UPDATE `categories` SET `nom_ar` = 'الأعمال، التسيير والمالية' WHERE `nom` LIKE 'Business%';
UPDATE `categories` SET `nom_ar` = 'القانون، السياسة والمجتمع' WHERE `nom` LIKE 'Droit%';
UPDATE `categories` SET `nom_ar` = 'الفنون، التصميم والإعلام' WHERE `nom` LIKE 'Arts%';
UPDATE `categories` SET `nom_ar` = 'الخدمات، السياحة والنقل' WHERE `nom` LIKE 'Services%';
UPDATE `categories` SET `nom_ar` = 'التربية والعلوم الإنسانية' WHERE `nom` LIKE 'Éducation%';
UPDATE `categories` SET `nom_ar` = 'التكوين المهني والحرف' WHERE `nom` LIKE 'Formation%';

-- 3. Update Institutions
ALTER TABLE `institutions` 
ADD COLUMN IF NOT EXISTS `name_ar` varchar(150) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `city_ar` varchar(100) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `description_ar` text DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `requirements_ar` text DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `diplome_ar` varchar(150) DEFAULT NULL;

-- Sample Arabic data for main institutions
UPDATE `institutions` SET 
`name_ar` = 'المدرسة الوطنية للعلوم التطبيقية بالدار البيضاء', 
`city_ar` = 'الدار البيضاء',
`description_ar` = 'مدرسة وطنية لتكوين المهندسين تابعة لجامعة الحسن الثاني.',
`diplome_ar` = 'دبلوم مهندس دولة'
WHERE `name` LIKE '%ENSA Casablanca%';

UPDATE `institutions` SET 
`name_ar` = 'المدرسة الوطنية للتجارة والتسيير بالدار البيضاء', 
`city_ar` = 'الدار البيضاء',
`description_ar` = 'مدرسة عليا متخصصة في علوم التجارة والتدبير.',
`diplome_ar` = 'دبلوم المدارس الوطنية للتجارة والتسيير'
WHERE `name` LIKE '%ENCG Casablanca%';

-- Update City names for all institutions based on their current text city
UPDATE `institutions` SET `city_ar` = 'الدار البيضاء' WHERE `city` = 'Casablanca';
UPDATE `institutions` SET `city_ar` = 'الرباط' WHERE `city` = 'Rabat';
UPDATE `institutions` SET `city_ar` = 'مراكش' WHERE `city` = 'Marrakech';
UPDATE `institutions` SET `city_ar` = 'فاس' WHERE `city` = 'Fes';
UPDATE `institutions` SET `city_ar` = 'طنجة' WHERE `city` = 'Tanger';
UPDATE `institutions` SET `city_ar` = 'أكادير' WHERE `city` = 'Agadir';
UPDATE `institutions` SET `city_ar` = 'وجدة' WHERE `city` = 'Oujda';
UPDATE `institutions` SET `city_ar` = 'القنيطرة' WHERE `city` = 'Kenitra';
UPDATE `institutions` SET `city_ar` = 'سطات' WHERE `city` = 'Settat';

-- 4. Update Domains
ALTER TABLE `domains` 
ADD COLUMN IF NOT EXISTS `nom_ar` varchar(150) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `description_ar` text DEFAULT NULL;

-- 5. Update Filieres
ALTER TABLE `filieres` 
ADD COLUMN IF NOT EXISTS `nom_ar` varchar(150) DEFAULT NULL,
ADD COLUMN IF NOT EXISTS `description_ar` text DEFAULT NULL;

COMMIT;
