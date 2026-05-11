-- Update bac_types with Arabic names
ALTER TABLE `bac_types` ADD COLUMN IF NOT EXISTS `nom_ar` varchar(100) DEFAULT NULL;

UPDATE `bac_types` SET `nom_ar` = 'علوم رياضية أ' WHERE `code` = 'SMA';
UPDATE `bac_types` SET `nom_ar` = 'علوم رياضية ب' WHERE `code` = 'SMB';
UPDATE `bac_types` SET `nom_ar` = 'علوم فيزيائية' WHERE `code` = 'PC';
UPDATE `bac_types` SET `nom_ar` = 'علوم الحياة والأرض' WHERE `code` = 'SVT';
UPDATE `bac_types` SET `nom_ar` = 'علوم اقتصادية' WHERE `code` = 'ECO';
UPDATE `bac_types` SET `nom_ar` = 'تسيير محاسباتي' WHERE `code` = 'GEST';
UPDATE `bac_types` SET `nom_ar` = 'علوم وتقنيات' WHERE `code` = 'TECH';
UPDATE `bac_types` SET `nom_ar` = 'آداب' WHERE `code` = 'LET';
UPDATE `bac_types` SET `nom_ar` = 'علوم إنسانية' WHERE `code` = 'SH';
UPDATE `bac_types` SET `nom_ar` = 'باكالوريا مهنية' WHERE `code` = 'PROF';
