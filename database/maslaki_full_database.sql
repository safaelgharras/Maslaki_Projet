-- Maslaki Full Database Reconstruction (Complete with all Institutions & Filieres)
-- Generated: 2026-05-15
-- Compatibility: MySQL / MariaDB
-- Character Set: UTF-8 (Arabic, French, English support)

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET FOREIGN_KEY_CHECKS = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Table structure for `admin_users`
--
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','manager') DEFAULT 'manager',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admin_users` (`username`, `email`, `password`, `role`) VALUES
('admin', 'admin@maslaki.ma', '$2y$10$8W3Y6H8V8W3Y6H8V8W3Y6u', 'superadmin');

--
-- Table structure for `students`
--
CREATE TABLE `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `bac_branch` varchar(50) DEFAULT NULL,
  `average` float DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `role` varchar(20) DEFAULT 'student',
  `is_premium` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `students` (`id`, `name`, `email`, `password`, `bac_branch`, `average`, `city`, `role`, `is_premium`) VALUES
(1, 'Safa El', 'safa@example.com', '$2y$10$4zxaFDvq6GD5jhY1hHazB.3rGadltTVNg70YPO/EvyufsUG8Q9Aii', 'SVT', 15.64, 'Tanger', 'student', 0),
(2, 'Ahmed Al', 'ahmed@test.ma', '$2y$10$4zxaFDvq6GD5jhY1hHazB.3rGadltTVNg70YPO/EvyufsUG8Q9Aii', 'Math', 17.5, 'Casablanca', 'student', 1);

--
-- Table structure for `villes`
--
CREATE TABLE `villes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `nom_ar` varchar(100) DEFAULT NULL,
  `nom_en` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `villes` (`id`, `nom`, `nom_ar`, `nom_en`) VALUES
(1, 'Casablanca', 'الدار البيضاء', 'Casablanca'),
(2, 'Rabat', 'الرباط', 'Rabat'),
(3, 'Marrakech', 'مراكش', 'Marrakech'),
(4, 'Fes', 'فاس', 'Fez'),
(5, 'Tanger', 'طنجة', 'Tangier'),
(6, 'Agadir', 'أكادير', 'Agadir'),
(7, 'Oujda', 'وجدة', 'Oujda'),
(8, 'Kenitra', 'القنيطرة', 'Kenitra'),
(9, 'Settat', 'سطات', 'Settat'),
(10, 'Meknes', 'مكناس', 'Meknes'),
(11, 'Errachidia', 'الرشيدية', 'Errachidia'),
(12, 'Al Hoceima', 'الحسيمة', 'Al Hoceima'),
(13, 'Laayoune', 'العيون', 'Laayoune'),
(14, 'Mohammedia', 'المحمدية', 'Mohammedia'),
(15, 'Beni Mellal', 'بني ملال', 'Beni Mellal');

--
-- Table structure for `categories`
--
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `nom_ar` varchar(100) DEFAULT NULL,
  `nom_en` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `categories` (`id`, `nom`, `nom_ar`, `nom_en`) VALUES
(1, 'Sciences', 'العلوم', 'Sciences'),
(2, 'Économie & Gestion', 'الاقتصاد والتسيير', 'Economics & Management'),
(3, 'Lettres', 'الآداب', 'Literature'),
(4, 'Sciences Humaines', 'العلوم الإنسانية', 'Human Sciences'),
(5, 'Informatique', 'المعلوميات', 'Computer Science'),
(6, 'Santé', 'الصحة', 'Health'),
(7, 'Droit', 'القانون', 'Law'),
(8, 'Arts', 'الفنون', 'Arts'),
(9, 'Technologie', 'التكنولوجيا', 'Technology');

--
-- Table structure for `bac_types`
--
CREATE TABLE `bac_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `nom_ar` varchar(100) DEFAULT NULL,
  `nom_en` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `bac_types` (`code`, `nom`, `nom_ar`, `nom_en`) VALUES
('SMA', 'Sciences Math A', 'علوم رياضية أ', 'Mathematical Sciences A'),
('SMB', 'Sciences Math B', 'علوم رياضية ب', 'Mathematical Sciences B'),
('PC', 'Physique-Chimie', 'علوم فيزيائية', 'Physics-Chemistry'),
('SVT', 'Sciences Vie et Terre', 'علوم الحياة والأرض', 'Life and Earth Sciences'),
('ECO', 'Sciences Économiques', 'علوم اقتصادية', 'Economic Sciences'),
('GEST', 'Gestion Comptable', 'تسيير محاسباتي', 'Accounting Management'),
('TECH', 'Sciences et Tech', 'علوم وتقنيات', 'Sciences and Technologies'),
('LET', 'Lettres', 'آداب', 'Literature'),
('SH', 'Sciences Humaines', 'علوم إنسانية', 'Human Sciences'),
('PROF', 'Bac Professionnel', 'باكالوريا مهنية', 'Vocational Baccalaureate');

--
-- Table structure for `institutions`
--
CREATE TABLE `institutions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `name_ar` varchar(150) DEFAULT NULL,
  `name_en` varchar(150) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `city_ar` varchar(100) DEFAULT NULL,
  `city_en` varchar(100) DEFAULT NULL,
  `ville_id` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `min_average` float DEFAULT NULL,
  `seuil` float DEFAULT NULL,
  `description` text DEFAULT NULL,
  `description_ar` text DEFAULT NULL,
  `description_en` text DEFAULT NULL,
  `requirements` text DEFAULT NULL,
  `requirements_ar` text DEFAULT NULL,
  `requirements_en` text DEFAULT NULL,
  `diplome` varchar(150) DEFAULT NULL,
  `diplome_ar` varchar(150) DEFAULT NULL,
  `diplome_en` varchar(150) DEFAULT NULL,
  `image` varchar(255) DEFAULT 'default_school.jpg',
  `site_web` varchar(255) DEFAULT NULL,
  `duree_etudes` varchar(50) DEFAULT NULL,
  `is_popular` boolean DEFAULT false,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_inst_ville` FOREIGN KEY (`ville_id`) REFERENCES `villes` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `institutions` (`id`, `name`, `city`, `type`, `min_average`, `description`, `requirements`) VALUES
(1, 'ENSA Casablanca', 'Casablanca', 'Engineering', 12, 'école d\'ingénieur publique', 'Bac Sciences Math / Physique + concours'),
(2, 'ENCG Casablanca', 'Casablanca', 'Business', 11, 'école de commerce publique', 'Bac Eco / Math + sélection dossier'),
(3, 'FST Settat', 'Settat', 'Science', 10, 'faculté des sciences et techniques', 'Bac Sciences + moyenne >= 10'),
(4, 'EST Casablanca', 'Casablanca', 'Technical', 10, 'école supérieure de technologie', 'Bac Sciences / Tech'),
(5, 'ISCAE Casablanca', 'Casablanca', 'Business', 14, 'institut supérieur de commerce', 'Bac mention + concours écrit + oral'),
(6, 'ENSA Marrakech', 'Marrakech', 'Engineering', 12, 'école nationale des sciences appliquées', 'Bac Sciences + concours'),
(7, 'ENCG Agadir', 'Agadir', 'Business', 11, 'école nationale de commerce', 'Bac Eco / Math + dossier'),
(8, 'FST Tanger', 'Tanger', 'Science', 10, 'faculté scientifique', 'Bac Sciences + 10+'),
(9, 'EST Fes', 'Fes', 'Technical', 10, 'école technologie', 'Bac Tech / Sciences'),
(10, 'EMI Rabat', 'Rabat', 'Engineering', 15, 'école Mohammadia', 'Bac Sciences Math + très haut niveau + concours'),
(11, 'ENSA Fes', 'Fes', 'Engineering', 12, 'école ingénieur', 'Bac Sciences + concours'),
(12, 'ENSA Tanger', 'Tanger', 'Engineering', 12, 'école ingénieur', 'Bac Sciences + concours'),
(13, 'ENCG Settat', 'Settat', 'Business', 11, 'école commerce', 'Bac Eco / Math'),
(14, 'ENCG Marrakech', 'Marrakech', 'Business', 11, 'école gestion', 'Bac Eco / Math'),
(15, 'FST Mohammedia', 'Mohammedia', 'Science', 10, 'faculté technique', 'Bac Sciences'),
(16, 'FST Beni Mellal', 'Beni Mellal', 'Science', 10, 'faculté régionale', 'Bac Sciences'),
(17, 'EST Agadir', 'Agadir', 'Technical', 10, 'école technique', 'Bac Tech'),
(18, 'EST Oujda', 'Oujda', 'Technical', 10, 'école technique', 'Bac Tech'),
(19, 'FS Casablanca', 'Casablanca', 'Science', 10, 'faculté sciences', 'Bac Sciences'),
(20, 'FS Rabat', 'Rabat', 'Science', 10, 'faculté sciences', 'Bac Sciences'),
(21, 'CPGE Casablanca', 'Casablanca', 'Preparatory', 14, 'classes préparatoires', 'Bac mention Bien ou Très Bien'),
(22, 'CPGE Rabat', 'Rabat', 'Preparatory', 14, 'prépa scientifique', 'Bac mention Bien + excellent niveau'),
(23, 'UIR Rabat', 'Rabat', 'Private', 12, 'université privée', 'Bac + dossier + entretien'),
(24, 'EMSI Casablanca', 'Casablanca', 'Private', 10, 'école ingénieur privée', 'Bac Sciences / Tech'),
(25, 'SUPMTI Rabat', 'Rabat', 'Private', 10, 'école IT', 'Bac + dossier'),
(26, 'IGA Casablanca', 'Casablanca', 'Private', 10, 'école privée', 'Bac + entretien'),
(27, 'HEM Casablanca', 'Casablanca', 'Private', 12, 'école management', 'Bac + sélection'),
(28, 'ISGA Marrakech', 'Marrakech', 'Private', 10, 'école privée', 'Bac + dossier'),
(29, 'ENS Rabat', 'Rabat', 'Education', 11, 'école normale', 'Bac + concours'),
(30, 'ENSET Mohammedia', 'Mohammedia', 'Education', 11, 'école technique', 'Bac + concours'),
(31, 'ENSA Oujda', 'Oujda', 'Engineering', 12, 'école ingénieur', 'Bac Sciences + concours'),
(32, 'ENSA Kenitra', 'Kenitra', 'Engineering', 12, 'école ingénieur', 'Bac Sciences + concours'),
(33, 'ENCG Oujda', 'Oujda', 'Business', 11, 'école commerce', 'Bac Eco / Math'),
(34, 'ENCG Kenitra', 'Kenitra', 'Business', 11, 'école gestion', 'Bac Eco / Math'),
(35, 'FST Errachidia', 'Errachidia', 'Science', 10, 'faculté sciences', 'Bac Sciences'),
(36, 'FST Al Hoceima', 'Al Hoceima', 'Science', 10, 'faculté sciences', 'Bac Sciences'),
(37, 'EST Kenitra', 'Kenitra', 'Technical', 10, 'école technique', 'Bac Tech'),
(38, 'EST Laayoune', 'Laayoune', 'Technical', 10, 'école technique', 'Bac Tech'),
(39, 'FS Meknes', 'Meknes', 'Science', 10, 'faculté sciences', 'Bac Sciences'),
(40, 'FS Oujda', 'Oujda', 'Science', 10, 'faculté sciences', 'Bac Sciences'),
(41, 'CPGE Marrakech', 'Marrakech', 'Preparatory', 14, 'prépa', 'Bac mention Bien'),
(42, 'CPGE Fes', 'Fes', 'Preparatory', 14, 'prépa', 'Bac mention Bien'),
(43, 'Université Cadi Ayyad', 'Marrakech', 'University', 10, 'université publique', 'Bac + inscription'),
(44, 'Université Ibn Tofail', 'Kenitra', 'University', 10, 'université publique', 'Bac + inscription'),
(45, 'Université Abdelmalek Essaadi', 'Tanger', 'University', 10, 'université publique', 'Bac + inscription'),
(46, 'SUPINFO Casablanca', 'Casablanca', 'Private', 10, 'école IT', 'Bac + dossier'),
(47, 'EIGSI Casablanca', 'Casablanca', 'Private', 12, 'école ingénieur privée', 'Bac Sciences'),
(48, 'HECI Casablanca', 'Casablanca', 'Private', 10, 'école commerce', 'Bac'),
(49, 'ESCA Ecole de Management', 'Casablanca', 'Private', 13, 'école management', 'Bac + concours');

-- Update institutions with extra details and mappings
UPDATE institutions SET ville_id = 1 WHERE city = 'Casablanca';
UPDATE institutions SET ville_id = 2 WHERE city = 'Rabat';
UPDATE institutions SET ville_id = 3 WHERE city = 'Marrakech';
UPDATE institutions SET ville_id = 4 WHERE city = 'Fes';
UPDATE institutions SET ville_id = 5 WHERE city = 'Tanger';
UPDATE institutions SET ville_id = 6 WHERE city = 'Agadir';
UPDATE institutions SET ville_id = 7 WHERE city = 'Oujda';
UPDATE institutions SET ville_id = 8 WHERE city = 'Kenitra';
UPDATE institutions SET ville_id = 9 WHERE city = 'Settat';
UPDATE institutions SET ville_id = 10 WHERE city = 'Meknes';
UPDATE institutions SET ville_id = 11 WHERE city = 'Errachidia';
UPDATE institutions SET ville_id = 12 WHERE city = 'Al Hoceima';
UPDATE institutions SET ville_id = 13 WHERE city = 'Laayoune';
UPDATE institutions SET ville_id = 14 WHERE city = 'Mohammedia';
UPDATE institutions SET ville_id = 15 WHERE city = 'Beni Mellal';

-- Apply specific data from migrations
UPDATE `institutions` SET `image` = 'ensa_casa.jpg', `seuil` = 12, `duree_etudes` = '5 ans', `diplome` = 'Diplôme d\'Ingénieur', `is_popular` = true, `name_ar` = 'المدرسة الوطنية للعلوم التطبيقية بالدار البيضاء', `name_en` = 'National School of Applied Sciences Casablanca' WHERE `id` = 1;
UPDATE `institutions` SET `image` = 'encg_casa.jpg', `seuil` = 11, `duree_etudes` = '5 ans', `diplome` = 'Diplôme ENCG', `is_popular` = true, `name_ar` = 'المدرسة الوطنية للتجارة والتسيير بالدار البيضاء', `name_en` = 'National School of Business and Management Casablanca' WHERE `id` = 2;
UPDATE `institutions` SET `image` = 'emi_rabat.jpg', `seuil` = 15, `duree_etudes` = '3 ans', `diplome` = 'Diplôme d\'Ingénieur', `name_ar` = 'المدرسة المحمدية للمهندسين', `name_en` = 'Mohammadia School of Engineers' WHERE `id` = 10;
UPDATE `institutions` SET `image` = 'uir_rabat.jpg', `seuil` = 12, `duree_etudes` = '5 ans', `diplome` = 'Master / Ingénieur' WHERE `id` = 23;

-- Fill missing info automatically
UPDATE institutions SET diplome = 'Ingénieur d\'État', duree_etudes = '5 ans' WHERE (type = 'Engineering' OR name LIKE '%ENSA%' OR name LIKE '%EMI%') AND (diplome IS NULL OR diplome = '');
UPDATE institutions SET diplome = 'Master en Management', duree_etudes = '5 ans' WHERE (type = 'Business' OR name LIKE '%ENCG%' OR name LIKE '%ISCAE%') AND (diplome IS NULL OR diplome = '');
UPDATE institutions SET diplome = 'DUT / Licence Pro', duree_etudes = '2 ans' WHERE (type = 'Technical' OR name LIKE '%EST%') AND (diplome IS NULL OR diplome = '');
UPDATE institutions SET diplome = 'Licence / Master', duree_etudes = '3-5 ans' WHERE (type IN ('Science', 'University', 'Education') OR name LIKE '%FST%' OR name LIKE '%Faculté%' OR name LIKE '%ENS%') AND (diplome IS NULL OR diplome = '');
UPDATE institutions SET diplome = 'Attestation CPGE', duree_etudes = '2 ans' WHERE (type = 'Preparatory' OR name LIKE '%CPGE%') AND (diplome IS NULL OR diplome = '');
UPDATE institutions SET diplome = 'Bachelor / Master', duree_etudes = '3-5 ans' WHERE type = 'Private' AND (diplome IS NULL OR diplome = '');
UPDATE institutions SET diplome = 'Diplôme National' WHERE diplome IS NULL OR diplome = '';
UPDATE institutions SET duree_etudes = '3-5 ans' WHERE duree_etudes IS NULL OR duree_etudes = '';

--
-- Table structure for `filieres`
--
CREATE TABLE `filieres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(150) NOT NULL,
  `nom_ar` varchar(150) DEFAULT NULL,
  `nom_en` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `description_ar` text DEFAULT NULL,
  `description_en` text DEFAULT NULL,
  `categorie_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_filiere_category` FOREIGN KEY (`categorie_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `filieres` (`id`, `nom`, `description`, `categorie_id`) VALUES
(1, 'Génie Informatique', 'Conception et développement de systèmes logiciels', 5),
(2, 'Finance', 'Gestion financière et marchés de capitaux', 2),
(3, 'Marketing', 'Stratégies commerciales et communication', 2),
(4, 'Droit Français', 'Étude du système juridique francophone', 7),
(5, 'Médecine', 'Études médicales générales', 6),
(6, 'Architecture', 'Conception architecturale et urbanisme', 8),
(7, 'Data Science', 'Analyse de données et intelligence artificielle', 5),
(8, 'Gestion des Entreprises', 'Management et administration', 2);

-- Update filieres translations
UPDATE `filieres` SET `nom_ar` = 'الهندسة المعلوماتية', `nom_en` = 'Computer Engineering' WHERE `id` = 1;
UPDATE `filieres` SET `nom_ar` = 'المالية', `nom_en` = 'Finance' WHERE `id` = 2;
UPDATE `filieres` SET `nom_ar` = 'التسويق', `nom_en` = 'Marketing' WHERE `id` = 3;
UPDATE `filieres` SET `nom_ar` = 'القانون الفرنسي', `nom_en` = 'French Law' WHERE `id` = 4;
UPDATE `filieres` SET `nom_ar` = 'الطب', `nom_en` = 'Medicine' WHERE `id` = 5;
UPDATE `filieres` SET `nom_ar` = 'الهندسة المعمارية', `nom_en` = 'Architecture' WHERE `id` = 6;
UPDATE `filieres` SET `nom_ar` = 'علم البيانات', `nom_en` = 'Data Science' WHERE `id` = 7;
UPDATE `filieres` SET `nom_ar` = 'تسيير المقاولات', `nom_en` = 'Business Management' WHERE `id` = 8;

--
-- Table structure for `institution_filieres`
--
CREATE TABLE `institution_filieres` (
  `institution_id` int(11) NOT NULL,
  `filiere_id` int(11) NOT NULL,
  PRIMARY KEY (`institution_id`,`filiere_id`),
  CONSTRAINT `fk_pivot_institution` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_pivot_filiere` FOREIGN KEY (`filiere_id`) REFERENCES `filieres` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `institution_filieres` (`institution_id`, `filiere_id`) VALUES (1, 1), (1, 7), (2, 2), (2, 3), (10, 1), (23, 1), (23, 2), (5, 2), (5, 3);

--
-- Table structure for `reviews`
--
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `institution_id` int(11) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `status` enum('pending','approved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_review_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_review_inst` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for `saved_schools`
--
CREATE TABLE `saved_schools` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `institution_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_saved_student` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_saved_inst` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for `notifications`
--
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` enum('system','school','filiere','announcement','maintenance','orientation','deadline') NOT NULL DEFAULT 'system',
  `related_link` varchar(255) DEFAULT NULL,
  `is_global` tinyint(1) DEFAULT 1,
  `target_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_notif_target_user` FOREIGN KEY (`target_user_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO `notifications` (`title`, `message`, `type`, `is_global`) VALUES
('Bienvenue sur Maslaki !', 'Découvrez notre nouveau système d\'orientation personnalisé.', 'system', 1),
('Nouvelle École : ENSA Tanger', 'L\'ENSA Tanger a été ajoutée à la plateforme.', 'school', 1);

--
-- Table structure for `user_notifications`
--
CREATE TABLE `user_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `is_deleted` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_notif` (`user_id`,`notification_id`),
  CONSTRAINT `fk_un_user` FOREIGN KEY (`user_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_un_notif` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Table structure for `user_requests`
--
CREATE TABLE `user_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `type` enum('suggestion','report','support','other') DEFAULT 'suggestion',
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','seen','resolved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_req_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for `admin_notifications`
--
CREATE TABLE `admin_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for `ai_recommendations`
--
CREATE TABLE `ai_recommendations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `result` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_ai_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for `appointments`
--
CREATE TABLE `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_appointment_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Table structure for `contests`
--
CREATE TABLE `contests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text,
  `exam_date` date DEFAULT NULL,
  `registration_deadline` date DEFAULT NULL,
  `status` enum('open', 'closed', 'soon') DEFAULT 'soon',
  `is_featured` boolean DEFAULT false,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_contest_institution_id` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `contests` (`institution_id`, `title`, `status`, `is_featured`) VALUES
(1, 'Concours ENSA 2026', 'open', true),
(2, 'Test d\'Aptitude ENCG (TAFEM)', 'soon', true);

--
-- Table structure for `deadlines`
--
CREATE TABLE `deadlines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) DEFAULT NULL,
  `deadline_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_dead_inst_id` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `deadlines` (`id`, `institution_id`, `deadline_date`) VALUES
(1, 1, '2026-06-15'),
(2, 2, '2026-06-20'),
(3, 3, '2026-07-01'),
(4, 4, '2026-06-30'),
(5, 5, '2026-05-30'),
(6, 6, '2026-06-15'),
(7, 7, '2026-06-20'),
(8, 8, '2026-07-01'),
(9, 9, '2026-06-30'),
(10, 10, '2026-05-15');

--
-- Table structure for `translations`
--
CREATE TABLE `translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` varchar(5) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_lang_key` (`lang`,`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `translations` (`lang`, `key`, `value`) VALUES
('fr', 'welcome', 'Bienvenue sur Maslaki'),
('ar', 'welcome', 'مرحباً بكم في مسلكي'),
('en', 'welcome', 'Welcome to Maslaki');

--
-- Table structure for `premium_plans`
--
CREATE TABLE `premium_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `price` float NOT NULL,
  `duration_days` int(11) NOT NULL,
  `features` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `premium_plans` (`name`, `price`, `duration_days`, `features`) VALUES
('Mensuel', 50, 30, 'IA Illimitée, Coaching, Accès Anticipé'),
('Annuel', 450, 365, 'IA Illimitée, Coaching, Accès Anticipé, Réduction 25%');

--
-- Table structure for `student_subscriptions`
--
CREATE TABLE `student_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `start_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_date` timestamp NULL DEFAULT NULL,
  `status` enum('active','expired','cancelled') DEFAULT 'active',
  PRIMARY KEY (`id`),
  CONSTRAINT `fk_sub_student_id` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_sub_plan_id` FOREIGN KEY (`plan_id`) REFERENCES `premium_plans` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS = 1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
