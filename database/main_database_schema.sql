
-- ============================================================
-- MASLAKI - Main Database Schema (Single Source of Truth)
-- Generated: 2026-06-14
-- Tables: 25 | UNIQUE Indexes: 7 | FK Constraints: 18
-- ============================================================
-- USAGE:
--   mysql -u root < database/main_database_schema.sql
-- ============================================================

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

CREATE DATABASE IF NOT EXISTS `Maslaki` CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `Maslaki`;

DROP TABLE IF EXISTS `admin_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_notifications` (
  `id` int(11) NOT NULL,
  `type` varchar(50) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `admin_notifications` WRITE;
/*!40000 ALTER TABLE `admin_notifications` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_notifications` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `admin_users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `admin_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('superadmin','manager') DEFAULT 'manager',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `admin_users` WRITE;
/*!40000 ALTER TABLE `admin_users` DISABLE KEYS */;
/*!40000 ALTER TABLE `admin_users` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `ai_recommendations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ai_recommendations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `result` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `ai_recommendations` WRITE;
/*!40000 ALTER TABLE `ai_recommendations` DISABLE KEYS */;
/*!40000 ALTER TABLE `ai_recommendations` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `appointments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `appointments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `appointment_date` date NOT NULL,
  `appointment_time` time NOT NULL,
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `appointments` WRITE;
/*!40000 ALTER TABLE `appointments` DISABLE KEYS */;
INSERT INTO `appointments` VALUES (4,3,'ensa','2026-05-31','23:44:00','pending','2026-05-19 16:44:39');
/*!40000 ALTER TABLE `appointments` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `bac_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bac_types` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `nom` varchar(100) NOT NULL,
  `nom_ar` varchar(100) DEFAULT NULL,
  `nom_en` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `bac_types` WRITE;
/*!40000 ALTER TABLE `bac_types` DISABLE KEYS */;
INSERT INTO `bac_types` VALUES (1,'SMA','Sciences Math A','علوم رياضية أ','Mathematical Sciences A'),(2,'SMB','Sciences Math B','علوم رياضية ب','Mathematical Sciences B'),(3,'PC','Physique-Chimie','علوم فيزيائية','Physics-Chemistry'),(4,'SVT','Sciences Vie et Terre','علوم الحياة والأرض','Life and Earth Sciences'),(5,'ECO','Sciences Économiques','علوم اقتصادية','Economic Sciences'),(6,'GEST','Gestion Comptable','تسيير محاسباتي','Accounting Management'),(7,'TECH','Sciences et Tech','علوم وتقنيات','Sciences and Technologies'),(8,'LET','Lettres','آداب','Literature'),(9,'SH','Sciences Humaines','علوم إنسانية','Human Sciences'),(10,'PROF','Bac Professionnel','باكالوريا مهنية','Vocational Baccalaureate');
/*!40000 ALTER TABLE `bac_types` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `nom_ar` varchar(100) DEFAULT NULL,
  `nom_en` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_category_nom` (`nom`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (1,'Sciences Exactes & Technologies','العلوم الدقيقة والتكنولوجيات','Exact Sciences & Technologies'),(2,'Ingénierie & Industrie','الهندسة والصناعة','Engineering & Industry'),(3,'Santé & Sciences de la Vie','الصحة وعلوم الحياة','Health & Life Sciences'),(4,'Agriculture & Environnement','الفلاحة والبيئة','Agriculture & Environment'),(5,'Business, Gestion & Finance','الأعمال، التسيير والمالية','Business, Management & Finance'),(6,'Droit, Politique & Société','القانون، السياسة والمجتمع','Law, Politics & Society'),(7,'Arts, Design & Médias','الفنون، التصميم والإعلام','Arts, Design & Media'),(8,'Services, Tourisme & Transport','الخدمات، السياحة والنقل','Services, Tourism & Transportation'),(9,'Éducation & Sciences Humaines','التربية والعلوم الإنسانية','Education & Human Sciences'),(10,'Formation Professionnelle & Métiers','التكوين المهني والحرف','Vocational Training & Trades');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `contests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `contests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `title_ar` varchar(255) DEFAULT NULL,
  `title_en` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `description_ar` text DEFAULT NULL,
  `description_en` text DEFAULT NULL,
  `exam_date` date DEFAULT NULL,
  `registration_deadline` date DEFAULT NULL,
  `status` enum('open','closed','soon') DEFAULT 'soon',
  `is_featured` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `contests` WRITE;
/*!40000 ALTER TABLE `contests` DISABLE KEYS */;
INSERT INTO `contests` VALUES (1,1,'Concours Commun ENSA Maroc 2026','المباراة المشتركة لولوج المدارس الوطنية للعلوم التطبيقية 2026','ENSA Morocco Common Contest 2026','Concours d\'accès en 1ère année du cycle préparatoire des ENSA Maroc.','مباراة الولوج للسنة الأولى من السلك التحضيري للمدارس الوطنية للعلوم التطبيقية بالمغرب.','Entrance contest for the 1st year of the preparatory cycle of ENSA Morocco.','2026-07-20','2026-07-10','soon',1,'2026-05-15 11:43:44'),(2,2,'Concours TAFEM - ENCG 2026','اختبار القبول للتكوين في التدبير - المدارس الوطنية للتجارة والتسيير 2026','TAFEM Contest - ENCG 2026','Test d\'Aptitude à la Formation en Management pour l\'accès aux ENCG.','اختبار القدرات للتكوين في التسيير لولوج المدارس الوطنية للتجارة والتسيير.','Aptitude test for management training for access to ENCG.','2026-07-22','2026-07-12','soon',1,'2026-05-15 11:43:44'),(3,10,'Concours National Commun (CNC) 2026','المباراة الوطنية المشتركة 2026','National Common Contest (CNC) 2026','Concours national pour l\'accès aux grandes écoles d\'ingénieurs marocaines (CPGE).','المباراة الوطنية لولوج مؤسسات تكوين المهندسين الكبرى والمؤسسات التي في حكمها.','National contest for access to major Moroccan engineering schools.','2026-05-15','2026-04-30','open',1,'2026-05-15 11:43:44'),(4,5,'Concours d\'accès à l\'ISCAE 2026','مباراة ولوج المعهد العالي للتجارة وإدارة المقاولات 2026','ISCAE Entrance Contest 2026','Concours d\'accès en 1ère année de Licence à l\'ISCAE Casablanca.','مباراة الولوج للسنة الأولى إجازة بالمعهد العالي للتجارة وإدارة المقاولات بالدار البيضاء.','Entrance contest for the 1st year of Bachelor at ISCAE Casablanca.','2026-06-10','2026-05-25','soon',1,'2026-05-15 11:43:44'),(5,3,'Sélection FST Settat 2026','انتقاء كلية العلوم والتقنيات بسطات 2026','FST Settat Selection 2026','Sélection basée sur la moyenne du baccalauréat pour l\'accès aux FST.','انتقاء بناءً على معدل البكالوريا لولوج كليات العلوم والتقنيات.','Selection based on baccalaureate average for access to FST.','2026-08-05','2026-07-31','soon',0,'2026-05-15 11:43:44'),(6,4,'Inscription EST Casablanca 2026','تسجيل المدرسة العليا للتكنولوجيا بالدار البيضاء 2026','EST Casablanca Registration 2026','Candidature pour le Diplôme Universitaire de Technologie (DUT).','الترشيح للدبلوم الجامعي للتكنولوجيا.','Application for the University Technology Diploma (DUT).','2026-08-10','2026-07-31','soon',0,'2026-05-15 11:43:44');
/*!40000 ALTER TABLE `contests` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `deadlines`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `deadlines` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) DEFAULT NULL,
  `deadline_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `deadlines` WRITE;
/*!40000 ALTER TABLE `deadlines` DISABLE KEYS */;
INSERT INTO `deadlines` VALUES (1,1,'2026-06-15'),(2,2,'2026-06-20'),(3,3,'2026-07-01'),(4,4,'2026-06-30'),(5,5,'2026-05-30'),(6,6,'2026-06-15'),(7,7,'2026-06-20'),(8,8,'2026-07-01'),(9,9,'2026-06-30'),(10,10,'2026-05-15');
/*!40000 ALTER TABLE `deadlines` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `domains`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `domains` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `categorie_id` int(11) DEFAULT NULL,
  `nom` varchar(150) NOT NULL,
  `nom_ar` varchar(150) DEFAULT NULL,
  `nom_en` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_domain_nom_cat` (`nom`(100),`categorie_id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `domains` WRITE;
/*!40000 ALTER TABLE `domains` DISABLE KEYS */;
INSERT INTO `domains` VALUES (1,1,'Sciences Exactes & Technologies','العلوم الدقيقة والتكنولوجيات','Exact Sciences & Technologies','Mathématiques, Physique, Informatique, Développement, Big Data et IA'),(2,2,'Ingénierie & Industrie','الهندسة والصناعة','Engineering & Industry','Génie Civil, Électrique, Mécanique, Industriel, Procédés et Infrastructures'),(3,3,'Santé & Sciences de la Vie','الصحة وعلوم الحياة','Health & Life Sciences','Médecine, Pharmacie, Odontologie, Professions Infirmières et Biologie Médicale'),(4,4,'Agriculture & Environnement','الفلاحة والبيئة','Agriculture & Environment','Sciences Agronomiques, Agroalimentaire, Foresterie et Environnement'),(5,5,'Business, Gestion & Finance','الأعمال، التسيير والمالية','Business, Management & Finance','Management, Audit, Marketing, Commerce International, Supply Chain'),(6,6,'Droit, Politique & Société','القانون، السياسة والمجتمع','Law, Politics & Society','Droit Public, Privé, Sciences Politiques et Relations Internationales'),(7,7,'Arts, Design & Médias','الفنون، التصميم والإعلام','Arts, Design & Media','Architecture, Beaux-Arts, Cinéma, Journalisme et Communication'),(8,8,'Services, Tourisme & Transport','الخدمات، السياحة والنقل','Services, Tourism & Transportation','Management Hôtelier, Restauration, Transport et Logistique du Service'),(9,9,'Éducation & Sciences Humaines','التربية والعلوم الإنسانية','Education & Human Sciences','Lettres, Langues, Psychologie, Enseignement et Histoire-Géographie'),(10,10,'Formation Professionnelle & Métiers','التكوين المهني والحرف','Vocational Training & Trades','Filières professionnalisantes courtes de l\'OFPPT et métiers techniques');
/*!40000 ALTER TABLE `domains` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `filieres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `filieres` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(150) NOT NULL,
  `domain_id` int(11) DEFAULT NULL,
  `nom_ar` varchar(150) DEFAULT NULL,
  `nom_en` varchar(150) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `description_ar` text DEFAULT NULL,
  `description_en` text DEFAULT NULL,
  `categorie_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_filiere_nom_cat` (`nom`(100),`categorie_id`)
) ENGINE=InnoDB AUTO_INCREMENT=103 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `filieres` WRITE;
/*!40000 ALTER TABLE `filieres` DISABLE KEYS */;
INSERT INTO `filieres` VALUES (1,'Génie Informatique',1,'الهندسة المعلوماتية','Computer Engineering','Conception et développement de systèmes logiciels','تصميم وتطوير أنظمة البرمجيات','Design and development of software systems',1),(2,'Finance',5,'المالية','Finance','Gestion financière et marchés de capitaux','التدبير المالي وأسواق الرساميل','Financial management and capital markets',5),(3,'Marketing',5,'التسويق','Marketing','Stratégies commerciales et communication','الاستراتيجيات التجارية والتواصل','Commercial strategies and communication',5),(4,'Droit Français',9,'القانون الفرنسي','French Law','Étude du système juridique francophone','دراسة النظام القانوني الفرانكفوني','Study of the French-speaking legal system',9),(5,'Médecine',3,'الطب','Medicine','Études médicales générales','الدراسات الطبية العامة','General medical studies',3),(6,'Architecture',7,'الهندسة المعمارية','Architecture','Conception architecturale et urbanisme','التصميم المعماري والتعمير','Architectural design and urban planning',7),(7,'Data Science',1,'علم البيانات','Data Science','Analyse de données et intelligence artificielle','تحليل البيانات والذكاء الاصطناعي','Data analysis and artificial intelligence',1),(8,'Gestion des Entreprises',5,'تسيير المقاولات','Business Management','Management et administration','التدبير والإدارة','Management and administration',5),(9,'Sciences Mathématiques A',1,'العلوم الرياضية أ','Mathematical Sciences A',NULL,NULL,NULL,1),(10,'Sciences Mathématiques B',1,'العلوم الرياضية ب','Mathematical Sciences B',NULL,NULL,NULL,1),(11,'Sciences Physiques',1,'العلوم الفيزيائية','Physical Sciences',NULL,NULL,NULL,1),(12,'Sciences de la Vie et de la Terre (SVT)',NULL,'علوم الحياة والأرض','Life and Earth Sciences',NULL,NULL,NULL,1),(13,'Sciences Agronomiques',4,'العلوم الزراعية','Agronomic Sciences',NULL,NULL,NULL,4),(15,'Génie Civil',2,'الهندسة المدنية','Civil Engineering',NULL,NULL,NULL,2),(16,'Génie Industriel',2,'الهندسة الصناعية','Industrial Engineering',NULL,NULL,NULL,2),(17,'Génie Électrique',2,'الهندسة الكهربائية','Electrical Engineering',NULL,NULL,NULL,2),(18,'Génie Mécanique',2,'الهندسة الميكانيكية','Mechanical Engineering',NULL,NULL,NULL,2),(19,'Réseaux & Télécommunications',1,'الشبكات والاتصالات','Networks & Telecommunications',NULL,NULL,NULL,1),(20,'Intelligence Artificielle',1,'الذكاء الاصطناعي','Artificial Intelligence',NULL,NULL,NULL,1),(21,'Cybersécurité',1,'الأمن السيبراني','Cybersecurity',NULL,NULL,NULL,1),(22,'Big Data',1,'البيانات الضخمة','Big Data',NULL,NULL,NULL,1),(23,'Économie',5,'الاقتصاد','Economics',NULL,NULL,NULL,5),(24,'Gestion',5,'التسيير','Management',NULL,NULL,NULL,5),(25,'Comptabilité',5,'المحاسبة','Accounting',NULL,NULL,NULL,5),(28,'Commerce International',5,'التجارة الدولية','International Trade',NULL,NULL,NULL,5),(29,'Management',5,'الإدارة','Management',NULL,NULL,NULL,5),(30,'Audit & Contrôle de Gestion',5,'التدقيق ومراقبة التسيير','Audit & Management Control',NULL,NULL,NULL,5),(31,'Logistique',5,'اللوجستيك','Logistics',NULL,NULL,NULL,5),(33,'Droit Arabe',9,'القانون العربي','Arabic Law',NULL,NULL,NULL,9),(34,'Sciences Politiques',6,'العلوم السياسية','Political Science',NULL,NULL,NULL,6),(35,'Relations Internationales',6,'العلاقات الدولية','International Relations',NULL,NULL,NULL,6),(36,'Sociologie',9,'علم الاجتماع','Sociology',NULL,NULL,NULL,9),(37,'Psychologie',9,'علم النفس','Psychology',NULL,NULL,NULL,9),(38,'Philosophie',9,'الفلسفة','Philosophy',NULL,NULL,NULL,9),(39,'Géographie',9,'الجغرافيا','Geography',NULL,NULL,NULL,9),(40,'Histoire',9,'التاريخ','History',NULL,NULL,NULL,9),(41,'Études Islamiques',NULL,'الدراسات الإسلامية','Islamic Studies',NULL,NULL,NULL,4),(42,'Travail Social',NULL,'العمل الاجتماعي','Social Work',NULL,NULL,NULL,4),(43,'Études Françaises',9,'الدراسات الفرنسية','French Studies',NULL,NULL,NULL,9),(44,'Études Anglaises',9,'الدراسات الإنجليزية','English Studies',NULL,NULL,NULL,9),(45,'Études Hispaniques',NULL,'الدراسات الإسبانية','Hispanic Studies',NULL,NULL,NULL,3),(46,'Littérature Arabe',9,'الأدب العربي','Arabic Literature',NULL,NULL,NULL,9),(47,'Traduction',9,'الترجمة','Translation',NULL,NULL,NULL,9),(48,'Linguistique',9,'اللسانيات','Linguistics',NULL,NULL,NULL,9),(50,'Pharmacie',3,'الصيدلة','Pharmacy',NULL,NULL,NULL,3),(51,'Médecine Dentaire',3,'طب الأسنان','Dentistry',NULL,NULL,NULL,3),(52,'Infirmier',3,'التمريض','Nursing',NULL,NULL,NULL,3),(53,'Kinésithérapie',3,'الترويض الطبي','Physiotherapy',NULL,NULL,NULL,3),(54,'Biologie Médicale',3,'البيولوجيا الطبية','Medical Biology',NULL,NULL,NULL,3),(56,'Développement Mobile',1,'تطوير المحمول','Mobile Development',NULL,NULL,NULL,1),(58,'Systèmes Informatiques',1,'الأنظمة المعلوماتية','Information Systems',NULL,NULL,NULL,1),(59,'Génie Logiciel',1,'هندسة البرمجيات','Software Engineering',NULL,NULL,NULL,1),(60,'Design Graphique',7,'التصميم الغرافيكي','Graphic Design',NULL,NULL,NULL,7),(62,'Cinéma',7,'السينما','Cinema',NULL,NULL,NULL,7),(63,'Audiovisuel',7,'السمعي البصري','Audiovisual',NULL,NULL,NULL,7),(64,'Mode',7,'الموضة','Fashion',NULL,NULL,NULL,7),(65,'Gestion Hôtelière',8,'التسيير الفندقي','Hotel Management',NULL,NULL,NULL,8),(66,'Tourisme',8,'السياحة','Tourism',NULL,NULL,NULL,8),(67,'Restauration',8,'المطاعم','Catering',NULL,NULL,NULL,8),(100,'Technicien Spécialisé en Diagnostic Automobile',10,'تقني متخصص في تشخيص السيارات','Specialized Technician in Automotive Diagnostics','Diagnostic et réparation des pannes complexes de l\'automobile.',NULL,NULL,10),(101,'Technicien en Électricité de Bâtiment',10,'تقني في كهرباء البناء','Technician in Building Electricity','Installation et maintenance électrique dans le secteur de la construction.',NULL,NULL,10),(102,'Développement Web',1,'تطوير الويب','Web Development','Formation axée sur la création et l\'intégration de sites internet et d\'applications web (front-end et back-end).','تكوين يركز على إنشاء ودمj مواقع الإنترنت وتطبيقات الويب (الواجهة الأمامية والخلفية).','Training focused on creating and integrating websites and web applications (front-end and back-end).',1);
/*!40000 ALTER TABLE `filieres` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `institution_bac_types`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `institution_bac_types` (
  `institution_id` int(11) NOT NULL,
  `bac_type_id` int(11) NOT NULL,
  `min_grade` float DEFAULT NULL,
  PRIMARY KEY (`institution_id`,`bac_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `institution_bac_types` WRITE;
/*!40000 ALTER TABLE `institution_bac_types` DISABLE KEYS */;
/*!40000 ALTER TABLE `institution_bac_types` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `institution_domain`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `institution_domain` (
  `institution_id` int(11) NOT NULL,
  `domain_id` int(11) NOT NULL,
  PRIMARY KEY (`institution_id`,`domain_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `institution_domain` WRITE;
/*!40000 ALTER TABLE `institution_domain` DISABLE KEYS */;
INSERT INTO `institution_domain` VALUES (1,2),(2,5),(3,1),(4,1),(5,5),(6,2),(7,5),(8,1),(9,1),(10,2),(11,2),(12,2),(13,5),(14,5),(16,1),(17,1),(18,1),(19,1),(20,1),(21,9),(22,9),(24,1),(24,2),(24,5),(29,9),(30,9),(31,2),(32,2),(33,5),(34,5),(35,1),(36,1),(37,1),(38,1),(39,1),(40,1),(41,9),(42,9),(43,6),(44,6),(45,6),(58,6),(59,9),(60,7),(61,7),(62,7),(63,3),(64,3),(100,2),(101,2),(102,2),(103,1),(104,1),(105,1),(106,1),(107,1),(108,1),(109,1),(110,2),(111,3),(112,4),(113,5),(114,5),(115,1),(115,2),(115,5),(116,1),(116,2),(116,5),(117,2),(117,5),(118,7),(119,7),(120,6),(121,6),(122,6),(123,6),(124,6),(125,8),(126,9),(127,9),(128,10),(130,10),(131,1),(131,10);
/*!40000 ALTER TABLE `institution_domain` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `institution_filieres`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `institution_filieres` (
  `institution_id` int(11) NOT NULL,
  `filiere_id` int(11) NOT NULL,
  PRIMARY KEY (`institution_id`,`filiere_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `institution_filieres` WRITE;
/*!40000 ALTER TABLE `institution_filieres` DISABLE KEYS */;
INSERT INTO `institution_filieres` VALUES (1,1),(1,7),(1,15),(1,16),(1,19),(1,20),(2,2),(2,3),(3,7),(5,2),(5,3),(10,1),(10,15),(23,1),(23,2),(23,4),(23,5),(24,1),(24,15),(24,16),(24,19),(24,20),(27,2),(27,3),(49,2),(49,3),(51,1),(51,7),(51,19),(51,20),(52,16),(52,19),(53,15),(54,16),(58,4),(59,37),(60,6),(61,6),(62,6),(63,5),(64,5),(100,1),(101,1),(102,1),(103,11),(104,11),(110,1),(111,52),(112,13),(113,2),(114,2),(118,62),(119,63),(125,65),(128,100),(128,101),(130,100),(130,101),(131,56),(131,102);
/*!40000 ALTER TABLE `institution_filieres` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `institution_images`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `institution_images` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_id` int(11) DEFAULT NULL,
  `image_path` varchar(255) NOT NULL,
  `is_main` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `institution_images` WRITE;
/*!40000 ALTER TABLE `institution_images` DISABLE KEYS */;
/*!40000 ALTER TABLE `institution_images` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `institutions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `institutions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(150) DEFAULT NULL,
  `name_ar` varchar(150) DEFAULT NULL,
  `name_en` varchar(150) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `city_ar` varchar(100) DEFAULT NULL,
  `city_en` varchar(100) DEFAULT NULL,
  `ville_id` int(11) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `type` varchar(50) DEFAULT NULL,
  `sector_type` enum('public','private','semi-public','alternative') DEFAULT 'public',
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
  `duree_etudes_ar` varchar(50) DEFAULT NULL,
  `duree_etudes_en` varchar(50) DEFAULT NULL,
  `is_popular` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_inst_name_city` (`name`(100),`city`(50))
) ENGINE=InnoDB AUTO_INCREMENT=132 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `institutions` WRITE;
/*!40000 ALTER TABLE `institutions` DISABLE KEYS */;
INSERT INTO `institutions` VALUES (1,'ENSA','المدرسة الوطنية للعلوم التطبيقية','National School of Applied Sciences','Casablanca',NULL,NULL,1,NULL,'Engineering','public',12,14,'école d\'ingénieur publique','مدرسة عمومية للمهندسين','Public engineering school','Bac Sciences Math / Physique + concours','باكالوريا علوم رياضية / فيزيائية + مباراة','Bac Sciences Math / Physics + contest','Diplôme d\'Ingénieur d\'État','دبلوم مهندس','Engineering Degree','ENSA Casablanca.png',NULL,'5 ans','5 سنوات','5 years',1,'2026-05-15 10:49:42'),(2,'ENCG','المدرسة الوطنية للتجارة والتسيير','National School of Commerce and Management','Casablanca',NULL,NULL,1,NULL,'Business','public',11,14.5,'école de commerce publique','مدرسة عمومية للتجارة والتسيير','Public business and management school','Bac Eco / Math + sélection dossier','باكالوريا اقتصاد / رياضيات + انتقاء','Bac Eco / Math + selection','Diplôme Grade Master (Bac+5)','دبلوم المدارس الوطنية للتجارة والتسيير','ENCG Diploma','ENCG casablanca.png',NULL,'5 ans','5 سنوات','5 years',1,'2026-05-15 10:49:42'),(3,'FST','كلية العلوم والتقنيات','Faculty of Sciences and Technologies','Settat',NULL,NULL,9,121,'Science','public',10,13.5,'faculté des sciences et techniques','كلية العلوم والتقنيات','Faculty of Science and Technology','Bac Sciences + moyenne >= 10',NULL,NULL,'Licence en Sciences et Techniques (LST)','إجازة / ماستر','Bachelor / Master','FST Settat - Hassan 1er.png',NULL,'3-5 ans',NULL,NULL,0,'2026-05-15 10:49:42'),(4,'EST','المدرسة العليا للتكنولوجيا','Higher School of Technology','Casablanca',NULL,NULL,1,NULL,'Technical','public',10,13,'école supérieure de technologie','المدرسة العليا للتكنولوجيا','Higher School of Technology','Bac Sciences / Tech',NULL,NULL,'Diplôme Universitaire de Technologie (DUT)','الدبلوم الجامعي للتكنولوجيا','University Technology Diploma','EST Casablanca.png',NULL,'2 ans','سنتان','2 years',0,'2026-05-15 10:49:42'),(5,'ISCAE Casablanca','ISCAE Casablanca','ISCAE Casablanca','Casablanca',NULL,NULL,1,NULL,'Business','public',14,12,'institut supérieur de commerce','المعهد العالي للتجارة وإدارة المقاولات','Higher Institute of Commerce and Business Administration','Bac mention + concours écrit + oral',NULL,NULL,'Diplôme de l\'établissement','دبلوم المدارس الوطنية للتجارة والتسيير','ENCG Diploma','ISCAE Casablanca.png',NULL,'5 ans','5 سنوات','5 years',0,'2026-05-15 10:49:42'),(6,'ENSA','المدرسة الوطنية للعلوم التطبيقية','National School of Applied Sciences','Marrakech',NULL,NULL,3,43,'Engineering','public',12,14,'école nationale des sciences appliquées','école nationale des sciences appliquées','école nationale des sciences appliquées','Bac Sciences + concours',NULL,NULL,'Diplôme d\'Ingénieur d\'État','دبلوم مهندس','Engineering Degree','ENSA Marrakech.png',NULL,'5 ans','5 سنوات','5 years',0,'2026-05-15 10:49:42'),(7,'ENCG','المدرسة الوطنية للتجارة والتسيير','National School of Commerce and Management','Agadir',NULL,NULL,6,NULL,'Business','public',11,14.5,'école nationale de commerce','مدرسة عمومية للتجارة والتسيير','Public business and management school','Bac Eco / Math + dossier','باكالوريا اقتصاد / رياضيات + انتقاء','Bac Eco / Math + selection','Diplôme Grade Master (Bac+5)','دبلوم المدارس الوطنية للتجارة والتسيير','ENCG Diploma','ENCG Agadir.webp',NULL,'5 ans','5 سنوات','5 years',0,'2026-05-15 10:49:42'),(8,'FST','كلية العلوم والتقنيات','Faculty of Sciences and Technologies','Tanger',NULL,NULL,5,45,'Science','public',10,13.5,'faculté scientifique','كلية العلوم','Faculty of Science','Bac Sciences + 10+',NULL,NULL,'Licence en Sciences et Techniques (LST)','إجازة / ماستر','Bachelor / Master','FST Tanger.png',NULL,'3-5 ans',NULL,NULL,0,'2026-05-15 10:49:42'),(9,'EST','المدرسة العليا للتكنولوجيا','Higher School of Technology','Fes',NULL,NULL,4,123,'Technical','public',10,13,'école technologie','المدرسة العليا للتكنولوجيا','Higher School of Technology','Bac Tech / Sciences',NULL,NULL,'Diplôme Universitaire de Technologie (DUT)','الدبلوم الجامعي للتكنولوجيا','University Technology Diploma','EST Fes.jpg',NULL,'2 ans','سنتان','2 years',0,'2026-05-15 10:49:42'),(10,'EMI','EMI','EMI','Rabat',NULL,NULL,2,120,'Engineering','public',15,14,'école Mohammadia','المدرسة المحمدية للمهندسين','Mohammadia School of Engineers','Bac Sciences Math + très haut niveau + concours','باكالوريا علوم رياضية / فيزيائية + مباراة','Bac Sciences Math / Physics + contest','Diplôme d\'Ingénieur d\'État','دبلوم مهندس','Engineering Degree','EMI Rabat.webp',NULL,'3 ans','3 سنوات','3 years',0,'2026-05-15 10:49:42'),(11,'ENSA','المدرسة الوطنية للعلوم التطبيقية','National School of Applied Sciences','Fes',NULL,NULL,4,123,'Engineering','public',12,14,'école ingénieur','مدرسة عمومية للمهندسين','Public engineering school','Bac Sciences + concours',NULL,NULL,'Diplôme d\'Ingénieur d\'État','دبلوم مهندس','Engineering Degree','ENSA Fes.png',NULL,'5 ans','5 سنوات','5 years',0,'2026-05-15 10:49:42'),(12,'ENSA','المدرسة الوطنية للعلوم التطبيقية','National School of Applied Sciences','Tanger',NULL,NULL,5,45,'Engineering','public',12,14,'école ingénieur','مدرسة عمومية للمهندسين','Public engineering school','Bac Sciences + concours',NULL,NULL,'Diplôme d\'Ingénieur d\'État','دبلوم مهندس','Engineering Degree','ENSA Tanger.png',NULL,'5 ans','5 سنوات','5 years',0,'2026-05-15 10:49:42'),(13,'ENCG','المدرسة الوطنية للتجارة والتسيير','National School of Commerce and Management','Settat',NULL,NULL,9,121,'Business','public',11,14.5,'école commerce','مدرسة عمومية للتجارة والتسيير','Public business and management school','Bac Eco / Math','باكالوريا اقتصاد / رياضيات + انتقاء','Bac Eco / Math + selection','Diplôme Grade Master (Bac+5)','دبلوم المدارس الوطنية للتجارة والتسيير','ENCG Diploma','ENCG Settat.webp',NULL,'5 ans','5 سنوات','5 years',0,'2026-05-15 10:49:42'),(14,'ENCG','المدرسة الوطنية للتجارة والتسيير','National School of Commerce and Management','Marrakech',NULL,NULL,3,43,'Business','public',11,14.5,'école gestion','école gestion','école gestion','Bac Eco / Math','باكالوريا اقتصاد / رياضيات + انتقاء','Bac Eco / Math + selection','Diplôme Grade Master (Bac+5)','دبلوم المدارس الوطنية للتجارة والتسيير','ENCG Diploma','ENCG Marrakech.webp',NULL,'5 ans','5 سنوات','5 years',0,'2026-05-15 10:49:42'),(16,'FST','كلية العلوم والتقنيات','Faculty of Sciences and Technologies','Beni Mellal',NULL,NULL,15,122,'Science','public',10,13.5,'faculté régionale','faculté régionale','faculté régionale','Bac Sciences',NULL,NULL,'Licence en Sciences et Techniques (LST)','إجازة / ماستر','Bachelor / Master','FST - Beni mellal.png',NULL,'3-5 ans',NULL,NULL,0,'2026-05-15 10:49:42'),(17,'EST','المدرسة العليا للتكنولوجيا','Higher School of Technology','Agadir',NULL,NULL,6,NULL,'Technical','public',10,13,'école technique','école technique','école technique','Bac Tech',NULL,NULL,'Diplôme Universitaire de Technologie (DUT)','الدبلوم الجامعي للتكنولوجيا','University Technology Diploma','EST Agadir.png',NULL,'2 ans','سنتان','2 years',0,'2026-05-15 10:49:42'),(18,'EST','المدرسة العليا للتكنولوجيا','Higher School of Technology','Oujda',NULL,NULL,7,NULL,'Technical','public',10,13,'école technique','école technique','école technique','Bac Tech',NULL,NULL,'Diplôme Universitaire de Technologie (DUT)','الدبلوم الجامعي للتكنولوجيا','University Technology Diploma','EST Oujda.webp',NULL,'2 ans','سنتان','2 years',0,'2026-05-15 10:49:42'),(19,'FS','كلية العلوم','Faculty of Sciences','Casablanca',NULL,NULL,1,NULL,'Science','public',10,10,'faculté sciences','كلية العلوم','Faculty of Science','Bac Sciences',NULL,NULL,'Licence Fondamentale','إجازة / ماستر','Bachelor / Master','FS Casablanca.png',NULL,'3-5 ans',NULL,NULL,0,'2026-05-15 10:49:42'),(20,'FS','كلية العلوم','Faculty of Sciences','Rabat',NULL,NULL,2,120,'Science','public',10,10,'faculté sciences','كلية العلوم','Faculty of Science','Bac Sciences',NULL,NULL,'Licence Fondamentale','إجازة / ماستر','Bachelor / Master','FS Rabat.png',NULL,'3-5 ans',NULL,NULL,0,'2026-05-15 10:49:42'),(21,'CPGE','الأقسام التحضيرية للمدارس العليا','Preparatory Classes for Grandes Ecoles','Casablanca',NULL,NULL,1,NULL,'Preparatory','public',14,16,'classes préparatoires','الأقسام التحضيرية للمدارس العليا','Preparatory Classes for Higher Schools','Bac mention Bien ou Très Bien',NULL,NULL,'Attestation de réussite CPGE',NULL,NULL,'CPGE-Casablanca.png',NULL,'2 ans','سنتان','2 years',0,'2026-05-15 10:49:42'),(22,'CPGE','الأقسام التحضيرية للمدارس العليا','Preparatory Classes for Grandes Ecoles','Rabat',NULL,NULL,2,120,'Preparatory','public',14,16,'prépa scientifique','الأقسام التحضيرية للمدارس العليا','Preparatory Classes for Higher Schools','Bac mention Bien + excellent niveau',NULL,NULL,'Attestation de réussite CPGE',NULL,NULL,'CPGE -Rabat.png',NULL,'2 ans','سنتان','2 years',0,'2026-05-15 10:49:42'),(23,'UIR Rabat','UIR Rabat','UIR Rabat','Rabat',NULL,NULL,2,NULL,'Private','private',12,12,'université privée','جامعة خاصة','Private university','Bac + dossier + entretien',NULL,NULL,'Diplôme de l\'établissement',NULL,NULL,'UIR - Rabat.png',NULL,'5 ans','5 سنوات','5 years',0,'2026-05-15 10:49:42'),(24,'EMSI','EMSI','EMSI','Casablanca',NULL,NULL,1,NULL,'Private','private',10,12,'école ingénieur privée','مدرسة خاصة للمهندسين','Private engineering school','Bac Sciences / Tech',NULL,NULL,'Diplôme de l\'établissement','إجازة / ماستر','Bachelor / Master','EMSI Casablanca.webp',NULL,'3-5 ans',NULL,NULL,0,'2026-05-15 10:49:42'),(26,'IGA','IGA','IGA','Casablanca',NULL,NULL,1,NULL,'Private','private',10,12,'école privée','école privée','école privée','Bac + entretien',NULL,NULL,'Diplôme de l\'établissement','إجازة / ماستر','Bachelor / Master','IGA Casablanca.png',NULL,'3-5 ans',NULL,NULL,0,'2026-05-15 10:49:42'),(27,'HEM','HEM','HEM','Casablanca',NULL,NULL,1,NULL,'Private','private',12,14.5,'école management','مدرسة خاصة للإدارة','Private management school','Bac + sélection',NULL,NULL,'Diplôme Grade Master (Bac+5)','إجازة / ماستر','Bachelor / Master','HEM Casablanca.png',NULL,'3-5 ans',NULL,NULL,0,'2026-05-15 10:49:42'),(28,'ISGA','ISGA','ISGA','Marrakech',NULL,NULL,3,NULL,'Private','private',10,12,'école privée','école privée','école privée','Bac + dossier',NULL,NULL,'Diplôme de l\'établissement','إجازة / ماستر','Bachelor / Master','ISGA Marrakech.png',NULL,'3-5 ans',NULL,NULL,0,'2026-05-15 10:49:42'),(29,'ENS','المدرسة العليا للأساتذة','Higher Normal School','Rabat',NULL,NULL,2,120,'Education','public',11,10,'école normale','école normale','école normale','Bac + concours',NULL,NULL,'Licence Fondamentale','إجازة / ماستر','Bachelor / Master','ENS Rabat.png',NULL,'3-5 ans',NULL,NULL,0,'2026-05-15 10:49:42'),(30,'ENSET','ENSET','ENSET','Mohammedia',NULL,NULL,14,NULL,'Education','public',11,12,'école technique','école technique','école technique','Bac + concours',NULL,NULL,'Diplôme de l\'établissement','إجازة / ماستر','Bachelor / Master','ENSET Mohammedia.webp',NULL,'3-5 ans',NULL,NULL,0,'2026-05-15 10:49:42'),(31,'ENSA','المدرسة الوطنية للعلوم التطبيقية','National School of Applied Sciences','Oujda',NULL,NULL,7,NULL,'Engineering','public',12,14,'école ingénieur','مدرسة عمومية للمهندسين','Public engineering school','Bac Sciences + concours',NULL,NULL,'Diplôme d\'Ingénieur d\'État','دبلوم مهندس','Engineering Degree','ENSA Oujda.png',NULL,'5 ans','5 سنوات','5 years',0,'2026-05-15 10:49:42'),(32,'ENSA','المدرسة الوطنية للعلوم التطبيقية','National School of Applied Sciences','Kenitra',NULL,NULL,8,44,'Engineering','public',12,14,'école ingénieur','مدرسة عمومية للمهندسين','Public engineering school','Bac Sciences + concours',NULL,NULL,'Diplôme d\'Ingénieur d\'État','دبلوم مهندس','Engineering Degree','ENSA Kenitra.png',NULL,'5 ans','5 سنوات','5 years',0,'2026-05-15 10:49:42'),(33,'ENCG','المدرسة الوطنية للتجارة والتسيير','National School of Commerce and Management','Oujda',NULL,NULL,7,NULL,'Business','public',11,14.5,'école commerce','مدرسة عمومية للتجارة والتسيير','Public business and management school','Bac Eco / Math','باكالوريا اقتصاد / رياضيات + انتقاء','Bac Eco / Math + selection','Diplôme Grade Master (Bac+5)','دبلوم المدارس الوطنية للتجارة والتسيير','ENCG Diploma','ENCG Oujda.webp',NULL,'5 ans','5 سنوات','5 years',0,'2026-05-15 10:49:42'),(34,'ENCG','المدرسة الوطنية للتجارة والتسيير','National School of Commerce and Management','Kenitra',NULL,NULL,8,44,'Business','public',11,14.5,'école gestion','école gestion','école gestion','Bac Eco / Math','باكالوريا اقتصاد / رياضيات + انتقاء','Bac Eco / Math + selection','Diplôme Grade Master (Bac+5)','دبلوم المدارس الوطنية للتجارة والتسيير','ENCG Diploma','ENCG Kenitra.png',NULL,'5 ans','5 سنوات','5 years',0,'2026-05-15 10:49:42'),(35,'FST','كلية العلوم والتقنيات','Faculty of Sciences and Technologies','Errachidia',NULL,NULL,11,NULL,'Science','public',10,13.5,'faculté sciences','كلية العلوم','Faculty of Science','Bac Sciences',NULL,NULL,'Licence en Sciences et Techniques (LST)','إجازة / ماستر','Bachelor / Master','FST - ERRACHIDIA.png',NULL,'3-5 ans',NULL,NULL,0,'2026-05-15 10:49:42'),(36,'FST','كلية العلوم والتقنيات','Faculty of Sciences and Technologies','Al Hoceima',NULL,NULL,12,NULL,'Science','public',10,13.5,'faculté sciences','كلية العلوم','Faculty of Science','Bac Sciences',NULL,NULL,'Licence en Sciences et Techniques (LST)','إجازة / ماستر','Bachelor / Master','FST Al Hoceima.jpg',NULL,'3-5 ans',NULL,NULL,0,'2026-05-15 10:49:42'),(37,'EST','المدرسة العليا للتكنولوجيا','Higher School of Technology','Kenitra',NULL,NULL,8,44,'Technical','public',10,13,'école technique','école technique','école technique','Bac Tech',NULL,NULL,'Diplôme Universitaire de Technologie (DUT)','الدبلوم الجامعي للتكنولوجيا','University Technology Diploma','EST Kenitra.webp',NULL,'2 ans','سنتان','2 years',0,'2026-05-15 10:49:42'),(38,'EST','المدرسة العليا للتكنولوجيا','Higher School of Technology','Laayoune',NULL,NULL,13,NULL,'Technical','public',10,13,'école technique','école technique','école technique','Bac Tech',NULL,NULL,'Diplôme Universitaire de Technologie (DUT)','الدبلوم الجامعي للتكنولوجيا','University Technology Diploma','EST Laayoune.png',NULL,'2 ans','سنتان','2 years',0,'2026-05-15 10:49:42'),(39,'FS','كلية العلوم','Faculty of Sciences','Meknes',NULL,NULL,10,NULL,'Science','public',10,10,'faculté sciences','كلية العلوم','Faculty of Science','Bac Sciences',NULL,NULL,'Licence Fondamentale','إجازة / ماستر','Bachelor / Master','FS Meknes.png',NULL,'3-5 ans',NULL,NULL,0,'2026-05-15 10:49:42'),(40,'FS','كلية العلوم','Faculty of Sciences','Oujda',NULL,NULL,7,NULL,'Science','public',10,10,'faculté sciences','كلية العلوم','Faculty of Science','Bac Sciences',NULL,NULL,'Licence Fondamentale','إجازة / ماستر','Bachelor / Master','FS Oujda.png',NULL,'3-5 ans',NULL,NULL,0,'2026-05-15 10:49:42'),(41,'CPGE','الأقسام التحضيرية للمدارس العليا','Preparatory Classes for Grandes Ecoles','Marrakech',NULL,NULL,3,43,'Preparatory','public',14,16,'prépa','الأقسام التحضيرية للمدارس العليا','Preparatory Classes for Higher Schools','Bac mention Bien',NULL,NULL,'Attestation de réussite CPGE',NULL,NULL,'CPGE Marrakech.WEBP',NULL,'2 ans','سنتان','2 years',0,'2026-05-15 10:49:42'),(42,'CPGE','الأقسام التحضيرية للمدارس العليا','Preparatory Classes for Grandes Ecoles','Fes',NULL,NULL,4,123,'Preparatory','public',14,16,'prépa','الأقسام التحضيرية للمدارس العليا','Preparatory Classes for Higher Schools','Bac mention Bien',NULL,NULL,'Attestation de réussite CPGE',NULL,NULL,'CPGE Fez.jpg',NULL,'2 ans','سنتان','2 years',0,'2026-05-15 10:49:42'),(43,'Université Cadi Ayyad','جامعة القاضي عياض','Cadi Ayyad University','Marrakech',NULL,NULL,3,NULL,'University','public',10,10,'université publique','جامعة عمومية','Public university','Bac + inscription',NULL,NULL,'Licence / Master / Doctorat','إجازة / ماستر','Bachelor / Master','Université Cadi Ayyad Marrakech.png',NULL,'3-5 ans',NULL,NULL,0,'2026-05-15 10:49:42'),(44,'Université Ibn Tofail','جامعة ابن طفيل','Ibn Tofail University','Kenitra',NULL,NULL,8,NULL,'University','public',10,10,'université publique','جامعة عمومية','Public university','Bac + inscription',NULL,NULL,'Licence / Master / Doctorat','إجازة / ماستر','Bachelor / Master','Université Ibn Tofail Knitra.PNG',NULL,'3-5 ans',NULL,NULL,0,'2026-05-15 10:49:42'),(45,'Université Abdelmalek Essaadi','جامعة عبد المالك السعدي','Abdelmalek Essaadi University','Tanger',NULL,NULL,5,NULL,'University','public',10,10,'université publique','جامعة عمومية','Public university','Bac + inscription',NULL,NULL,'Licence / Master / Doctorat','إجازة / ماستر','Bachelor / Master','Université Abdelmalek Essaadi Tetouan.PNG',NULL,'3-5 ans',NULL,NULL,0,'2026-05-15 10:49:42'),(46,'SUPINFO','SUPINFO','SUPINFO','Casablanca',NULL,NULL,1,NULL,'Private','private',10,12,'école IT','école IT','école IT','Bac + dossier',NULL,NULL,'Diplôme de l\'établissement','إجازة / ماستر','Bachelor / Master','SUPINFO Casablanca.PNG',NULL,'3-5 ans',NULL,NULL,0,'2026-05-15 10:49:42'),(47,'EIGSI','EIGSI','EIGSI','Casablanca',NULL,NULL,1,NULL,'Private','private',12,14,'école ingénieur privée','مدرسة خاصة للمهندسين','Private engineering school','Bac Sciences',NULL,NULL,'Diplôme d\'Ingénieur d\'État','إجازة / ماستر','Bachelor / Master','EIGSI Casablanca.webp',NULL,'3-5 ans',NULL,NULL,0,'2026-05-15 10:49:42'),(48,'HECI','HECI','HECI','Casablanca',NULL,NULL,1,NULL,'Private','private',10,12,'école commerce','مدرسة خاصة للإدارة','Private management school','Bac',NULL,NULL,'Diplôme de l\'établissement','إجازة / ماستر','Bachelor / Master','HECI Casablanca.png',NULL,'3-5 ans',NULL,NULL,0,'2026-05-15 10:49:42'),(49,'ESCA','ESCA','ESCA','Ecole de Management',NULL,NULL,1,NULL,'Private','private',13,14.5,'école management','مدرسة خاصة للإدارة','Private management school','Bac + concours',NULL,NULL,'Diplôme Grade Master (Bac+5)','إجازة / ماستر','Bachelor / Master','ESCA Ecole de Management Casablanca.jpg',NULL,'3-5 ans',NULL,NULL,0,'2026-05-15 10:49:42'),(51,'INPT','INPT','INPT','Rabat','الرباط','Rabat',NULL,NULL,'Public','public',NULL,14,'Grande école d\'ingénieurs en TIC.','مدرسة كبرى للمهندسين في تكنولوجيا المعلومات والاتصالات.','Elite engineering school in ICT.',NULL,NULL,NULL,'Diplôme d\'Ingénieur d\'État',NULL,NULL,'INPT-Rabat.png',NULL,NULL,NULL,NULL,0,'2026-05-15 12:36:21'),(52,'ENSEM','المدرسة الوطنية العليا للكهرباء والميكانيك','National Higher School of Electricity and Mechanics','Casablanca','الدار البيضاء','Casablanca',NULL,NULL,'Public','public',NULL,14,'École d\'ingénieurs pluridisciplinaire.','مدرسة للمهندسين متعددة التخصصات.','Multidisciplinary engineering school.',NULL,NULL,NULL,'Diplôme d\'Ingénieur d\'État',NULL,NULL,'ENSEM - Casablanca.png',NULL,NULL,NULL,NULL,0,'2026-05-15 12:36:21'),(53,'EHTP','EHTP','EHTP','Casablanca','الدار البيضاء','Casablanca',NULL,NULL,'Public','public',NULL,14,'Référence en génie civil et infrastructures.','مرجع في الهندسة المدنية والبنية التحتية.','Reference in civil engineering and infrastructure.',NULL,NULL,NULL,'Diplôme d\'Ingénieur d\'État',NULL,NULL,'EHTP -Casablanca.png',NULL,NULL,NULL,NULL,0,'2026-05-15 12:36:21'),(54,'ENSAM','المدرسة الوطنية العليا للفنون والمهن','National Higher School of Arts and Crafts','Meknes','مكناس','Meknes',NULL,NULL,'Public','public',NULL,14,'Formation d\'ingénieurs Arts & Métiers.','تكوين مهندسي الفنون والمهن.','Arts & Crafts engineering training.',NULL,NULL,NULL,'Diplôme d\'Ingénieur d\'État',NULL,NULL,'ENSAM - Meknes.png',NULL,NULL,NULL,NULL,0,'2026-05-15 12:36:21'),(55,'YouCode','YouCode','YouCode','Youssoufia','اليوسفية','Youssoufia',NULL,NULL,'Public','alternative',NULL,0,'École de programmation inclusive.','مدرسة البرمجة الشاملة.','Inclusive programming school.',NULL,NULL,NULL,'Certificat de formation',NULL,NULL,'YouCode - Youssoufia.png',NULL,NULL,NULL,NULL,0,'2026-05-15 12:36:21'),(56,'1337','1337','1337','Khouribga','خريبكة','Khouribga',NULL,NULL,'Public','alternative',NULL,0,'Le futur de l\'éducation informatique.','مستقبل التعليم المعلوماتي.','The future of IT education.',NULL,NULL,NULL,'Certificat de formation',NULL,NULL,'1337-Khouribga.png',NULL,NULL,NULL,NULL,0,'2026-05-15 12:36:21'),(57,'OFPPT','مكتب التكوين المهني وإنعاش الشغل','Office of Vocational Training and Employment Promotion','National','وطني','National',NULL,NULL,'Public','public',NULL,10,'Formation professionnelle et technique.','التكوين المهني وإنعاش الشغل.','Vocational and technical training.',NULL,NULL,NULL,'Technicien / Technicien Spécialisé',NULL,NULL,'OFPPT - National.png',NULL,NULL,NULL,NULL,0,'2026-05-15 12:36:21'),(58,'FSJES','كلية العلوم القانونية والاقتصادية والاجتماعية','Faculty of Legal, Economic and Social Sciences','Casablanca','الدار البيضاء','Casablanca',NULL,NULL,'Public','public',NULL,10,'Études juridiques et économiques.','الدراسات القانونية والاقتصادية.','Legal and economic studies.',NULL,NULL,NULL,'Licence Fondamentale',NULL,NULL,'FSJES - Casablanca.png',NULL,NULL,NULL,NULL,0,'2026-05-15 12:36:21'),(59,'FLSH','كلية الآداب والعلوم الإنسانية','Faculty of Letters and Human Sciences','Rabat','الرباط','Rabat',NULL,NULL,'Public','public',NULL,10,'Études littéraires et humaines.','الدراسات الأدبية والإنسانية.','Literary and human studies.',NULL,NULL,NULL,'Licence Fondamentale',NULL,NULL,'FLSH - Rabat.png',NULL,NULL,NULL,NULL,0,'2026-05-15 12:36:21'),(60,'ENA','ENA','ENA','Rabat','الرباط','Rabat',NULL,120,'Public','public',NULL,12,'Formation d\'architectes d\'excellence.','تكوين المهندسين المعماريين المتميزين.','Excellence in architectural training.',NULL,NULL,NULL,'Diplôme de l\'établissement',NULL,NULL,'ENA- Rabat.png',NULL,NULL,NULL,NULL,0,'2026-05-15 12:36:21'),(61,'EAC','EAC','EAC','Casablanca','الدار البيضاء','Casablanca',NULL,NULL,'Private','private',NULL,12,'Architecture et urbanisme.','الهندسة المعمارية والتعمير.','Architecture and urbanism.',NULL,NULL,NULL,'Diplôme de l\'établissement',NULL,NULL,'EAC -Casablanca.png',NULL,NULL,NULL,NULL,0,'2026-05-15 12:36:21'),(62,'Art\'Com Sup','Art\'Com Sup','Art\'Com Sup','Casablanca','الدار البيضاء','Casablanca',NULL,NULL,'Private','private',NULL,12,'Design graphique et digital.','التصميم الغرافيكي والرقمي.','Graphic and digital design.',NULL,NULL,NULL,'Diplôme de l\'établissement',NULL,NULL,'Art\'Com Sup-Casablanca.png',NULL,NULL,NULL,NULL,0,'2026-05-15 12:36:21'),(63,'UM6SS','UM6SS','UM6SS','Casablanca','الدار البيضاء','Casablanca',NULL,NULL,'Private','private',NULL,10,'Pôle d\'excellence en santé.','قطب التميز في الصحة.','Health excellence hub.',NULL,NULL,NULL,'Licence / Master / Doctorat',NULL,NULL,'UM6SS - Casablanca.png',NULL,NULL,NULL,NULL,0,'2026-05-15 12:36:21'),(64,'FMP','كلية الطب والصيدلة','Faculty of Medicine and Pharmacy','Rabat','الرباط','Rabat',NULL,120,'Public','public',NULL,14,'Formation médicale de référence.','تكوين طبي مرجعي.','Reference medical training.',NULL,NULL,NULL,'Doctorat en Médecine / Diplôme d\'État',NULL,NULL,'FMP- Rabat.png',NULL,NULL,NULL,NULL,0,'2026-05-15 12:36:21'),(100,'ENSA','المدرسة الوطنية للعلوم التطبيقية','National School of Applied Sciences','Safi','آسفي','Safi',16,43,'Engineering','public',12,14,'École d\'ingénieurs publique.','المدرسة الوطنية للعلوم التطبيقية بآسفي مدرسة مهندسين عمومية.','National School of Applied Sciences Safi is a public engineering school.','Bac Sciences + Concours',NULL,NULL,'Diplôme d\'Ingénieur d\'État',NULL,NULL,'ENSA-Safi.png','http://www.ensas.uca.ma','5 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(101,'ENSA','المدرسة الوطنية للعلوم التطبيقية','National School of Applied Sciences','El Jadida','الجديدة','El Jadida',17,NULL,'Engineering','public',12,14,'École nationale publique d\'ingénieurs.','مدرسة عمومية لتكوين المهندسين.','Public national engineering school.','Bac Sciences + Concours',NULL,NULL,'Diplôme d\'Ingénieur d\'État',NULL,NULL,'ENSA - El Jadida.png','http://www.ensaj.ac.ma','5 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(102,'ENSA','المدرسة الوطنية للعلوم التطبيقية','National School of Applied Sciences','Khouribga','خريبكة','Khouribga',18,122,'Engineering','public',12,14,'École publique prestigieuse d\'ingénieurs.','مدرسة مهندسين حكومية بخريبكة.','Prestigious public engineering school.','Bac Sciences + Concours',NULL,NULL,'Diplôme d\'Ingénieur d\'État',NULL,NULL,'ENSA - Khouribga.png','http://ensa.usms.ac.ma','5 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(103,'FST','كلية العلوم والتقنيات','Faculty of Sciences and Technologies','Oujda','وجدة','Oujda',7,NULL,'Science','public',10,13.5,'Faculté des sciences et techniques publique.','كلية العلوم والتقنيات بوجدة مؤسسة جامعية عمومية.','Faculty of Sciences and Technologies Oujda is a public university institution.','Bac Sciences',NULL,NULL,'Licence en Sciences et Techniques (LST)',NULL,NULL,'FST OUJDA.png','http://fsto.ump.ma','3-5 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(104,'FST','كلية العلوم والتقنيات','Faculty of Sciences and Technologies','Al Jadida','الجديدة','El Jadida',17,NULL,'Science','public',10,13.5,'Faculté universitaire de sciences.','كلية العلوم والتقنيات بالجديدة.','University faculty of sciences.','Bac Sciences',NULL,NULL,'Licence en Sciences et Techniques (LST)',NULL,NULL,'FST - Al jadida.png','http://www.fsth.ac.ma','3-5 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(105,'EST','المدرسة العليا للتكنولوجيا','Higher School of Technology','Safi','آسفي','Safi',16,43,'Technical','public',10,13,'École technique publique pour diplômes courts.','المدرسة العليا للتكنولوجيا بآسفي تكوين تقني قصير.','Higher School of Technology Safi public technical school.','Bac Sciences / Tech',NULL,NULL,'Diplôme Universitaire de Technologie (DUT)',NULL,NULL,'EST - Safi.png','http://www.ests.uca.ma','2 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(106,'EST','المدرسة العليا للتكنولوجيا','Higher School of Technology','Essaouira','الصويرة','Essaouira',19,43,'Technical','public',10,13,'École de technologie à Essaouira.','المدرسة العليا للتكنولوجيا بالصويرة.','Higher School of Technology in Essaouira.','Bac Sciences / Tech',NULL,NULL,'Diplôme Universitaire de Technologie (DUT)',NULL,NULL,'EST -Essaouira.png','http://www.este.uca.ma','2 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(107,'FS','كلية العلوم','Faculty of Sciences','Agadir','أكادير','Agadir',6,NULL,'Science','public',10,10,'Faculté des sciences d\'Agadir.','كلية العلوم بأكادير.','Faculty of Sciences in Agadir.','Bac Sciences',NULL,NULL,'Licence Fondamentale',NULL,NULL,'FS - Agadir.png','http://www.fsa.ac.ma','3-5 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(108,'FS','كلية العلوم','Faculty of Sciences','Marrakech','مراكش','Marrakech',3,43,'Science','public',10,10,'Faculté scientifique prestigieuse.','كلية العلوم السملالية بمراكش.','Prestigious scientific faculty.','Bac Sciences',NULL,NULL,'Licence Fondamentale',NULL,NULL,'fs merrakch.jpg','http://www.fssm.uca.ma','3-5 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(109,'FS','كلية العلوم','Faculty of Sciences','Tanger','طنجة','Tangier',5,45,'Science','public',10,10,'Faculté des sciences de Tanger.','كلية العلوم بطنجة.','Faculty of Sciences in Tangier.','Bac Sciences',NULL,NULL,'Licence Fondamentale',NULL,NULL,'FS-Tanger.png','http://www.fst.ac.ma','3-5 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(110,'ENSIAS','ENSIAS','ENSIAS','Rabat','الرباط','Rabat',2,120,'Engineering','public',15.5,14,'La plus prestigieuse grande école en informatique au Maroc.','المدرسة الوطنية العليا لتحليل النظم بالرباط مدرسة متميزة في الهندسة المعلوماتية.','Prestigous elite IT engineering school in Rabat.','Concours National Commun (CNC) après CPGE',NULL,NULL,'Diplôme d\'Ingénieur d\'État',NULL,NULL,'ENSIAS Rabat.png','http://ensias.um5.ac.ma','3 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(111,'ISPITS','ISPITS','ISPITS','Casablanca','الدار البيضاء','Casablanca',1,NULL,'Medical','public',12.5,14,'Institut public leader dans les professions de santé au Maroc.','المعهد العالي للمهن التمريضية وتقنيات الصحة بالدار البيضاء.','Leading public institute in nursing and health sciences.','Bac SVT / PC + Sélection + Concours',NULL,NULL,'Doctorat en Médecine / Diplôme d\'État',NULL,NULL,'ISPITS - Casablanca.png','http://ispits.sante.gov.ma','3 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(112,'IAV Hassan II','IAV Hassan II','IAV Hassan II','Rabat','الرباط','Rabat',2,120,'Science','public',14,12,'Le pôle national d\'excellence pour l\'agriculture et la médecine vétérinaire.','معهد الحسن الثاني للزراعة والبيطرة بالرباط مرجع الهندسة الزراعية.','National agricultural and veterinary center of excellence.','Bac SVT/PC/Math + Sélection + Concours',NULL,NULL,'Diplôme de l\'établissement',NULL,NULL,'IAV Hassan II.png','http://www.iav.ac.ma','5-6 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(113,'ENCG','المدرسة الوطنية للتجارة والتسيير','National School of Commerce and Management','El Jadida','الجديدة','El Jadida',17,NULL,'Business','public',11,14.5,'École publique de commerce et management de l\'Université Chouaib Doukkali.','المدرسة الوطنية للتجارة والتسيير بالجديدة.','Public school of business and management at Chouaib Doukkali University.','Bac Éco / Math + Sélection dossier',NULL,NULL,'Diplôme Grade Master (Bac+5)',NULL,NULL,'ENCG El Jadida.png','http://www.encg.ac.ma','5 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(114,'ENCG','المدرسة الوطنية للتجارة والتسيير','National School of Commerce and Management','Tanger','طنجة','Tangier',5,45,'Business','public',11,14.5,'École supérieure de commerce et de gestion dynamique à Tanger.','المدرسة الوطنية للتجارة والتسيير بطنجة.','Dynamic public school of commerce and management in Tangier.','Bac Éco / Math / PC + Concours',NULL,NULL,'Diplôme Grade Master (Bac+5)',NULL,NULL,'ENCG Tanger.png','http://www.encgt.ac.ma','5 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(115,'EMSI','EMSI','EMSI','Rabat','الرباط','Rabat',2,NULL,'Private','private',10,12,'École d\'ingénieurs privée accréditée.','المدرسة المغربية لعلوم المهندس بالرباط مدرسة خاصة معتمدة.','Accredited private engineering school in Rabat.','Bac Sciences / Tech + Entretien',NULL,NULL,'Diplôme de l\'établissement',NULL,NULL,'EMSI Rabat.PNG','http://www.emsi.ma','5 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(116,'EMSI','EMSI','EMSI','Marrakech','مراكش','Marrakech',3,NULL,'Private','private',10,12,'Campus EMSI dans la ville ocre.','المدرسة المغربية لعلوم المهندس بمراكش.','EMSI engineering campus in Marrakech.','Bac Sciences / Tech + Entretien',NULL,NULL,'Diplôme de l\'établissement',NULL,NULL,'EMSI -Marrakech.png','http://www.emsi.ma','5 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(117,'ESITH','ESITH','ESITH','Casablanca','الدار البيضاء','Casablanca',1,NULL,'Engineering','semi-public',12,12,'Grande école d\'ingénieurs en gestion industrielle, logistique et textile en partenariat public-privé.','المدرسة العليا لصناعات النسيج والألبسة بالدار البيضاء شراكة عام-خاص.','Elite engineering school in logistics and industrial management.','Bac Math/PC + Sélection + Concours',NULL,NULL,'Diplôme de l\'établissement',NULL,NULL,'ESITH-Casablanca.png','http://www.esith.ac.ma','3-5 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(118,'ISMAC','ISMAC','ISMAC','Rabat','الرباط','Rabat',2,120,'Art','public',12,12,'Institut national pour la formation des professionnels du cinéma et de la télévision.','المعهد العالي لمهن السمعي البصري والسينما بالرباط.','National institute for cinema and television professions.','Bac + Sélection + Concours écrit & oral',NULL,NULL,'Diplôme de l\'établissement',NULL,NULL,'ISMAC - Rabat.png','http://www.ismac.ac.ma','3 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(119,'ISIC','ISIC','ISIC','Rabat','الرباط','Rabat',2,120,'Art','public',13,12,'La grande école de journalisme publique historique du Maroc.','المعهد العالي للإعلام والاتصال بالرباط مدرسة الصحافة العريقة.','Historical and leading public journalism school in Morocco.','Bac toutes séries + Sélection + Concours écrit/oral',NULL,NULL,'Diplôme de l\'établissement',NULL,NULL,'ISIC - Rabat.png','http://www.isic.ac.ma','3 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(120,'Université Mohammed V','جامعة محمد الخامس','Mohammed V University','Rabat','الرباط','Rabat',2,NULL,'University','public',10,10,'La première université moderne du Royaume du Maroc.','جامعة محمد الخامس بالرباط الجامعة العريقة بالمغرب.','The first prestigious modern university of Morocco.','Baccalauréat',NULL,NULL,'Licence / Master / Doctorat',NULL,NULL,'Université Mohammed V Rabat.PNG','http://www.um5.ac.ma','3-8 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(121,'Université Hassan I','جامعة الحسن الأول','Hassan 1st University','Settat','سطات','Settat',9,NULL,'University','public',10,10,'Université publique moderne de la région de Chaouia.','جامعة الحسن الأول بسطات قطب جامعي مهم.','Modern public university of the Chaouia region.','Baccalauréat',NULL,NULL,'Licence / Master / Doctorat',NULL,NULL,'Université Hassan I Setat.PNG','http://www.uh1.ac.ma','3-8 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(122,'Université Sultan Moulay Slimane','جامعة السلطان مولاي سليمان','Sultan Moulay Slimane University','Beni Mellal','بني ملال','Beni Mellal',15,NULL,'University','public',10,10,'Université publique régionale de Tadla-Azilal.','جامعة السلطان مولاي سليمان ببني ملال.','Regional public university of Tadla-Azilal.','Baccalauréat',NULL,NULL,'Licence / Master / Doctorat',NULL,NULL,'Université Sultan Moulay Slimane Bni melal.PNG','http://www.usms.ac.ma','3-8 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(123,'Université Sidi Mohamed Ben Abdellah','جامعة سيدي محمد بن عبد الله','Sidi Mohamed Ben Abdellah University','Fes','فاس','Fez',4,123,'University','public',10,10,'Grande université publique historique du Nord-Centre.','جامعة سيدي محمد بن عبد الله بفاس التاريخية.','Historical major public university of the North-Center.','Baccalauréat',NULL,NULL,'Licence / Master / Doctorat',NULL,NULL,'Université Sidi Mohamed Ben Abdellah Fes.png','http://www.usmba.ac.ma','3-8 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(124,'Université Chouaib Doukkali','جامعة شعيب الدكالي','Chouaib Doukkali University','El Jadida','الجديدة','El Jadida',17,NULL,'University','public',10,10,'Université publique côtière d\'El Jadida.','جامعة شعيب الدكالي بالجديدة قطب العلوم والآداب.','Coastal public university in El Jadida.','Baccalauréat',NULL,NULL,'Licence / Master / Doctorat',NULL,NULL,'Université Chouaib Doukkali El jadida.jpg','http://www.ucd.ac.ma','3-8 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(125,'ISITT','ISITT','ISITT','Tanger','طنجة','Tangier',5,45,'Management','public',11,12,'L\'institut national d\'excellence pour le management touristique et hôtelier.','المعهد العالي الدولي للسياحة بطنجة رائد تكوين السياحة.','National institute of excellence in hospitality and tourism management.','Bac + Dossier + Concours écrit & oral',NULL,NULL,'Diplôme de l\'établissement',NULL,NULL,'ISSIT -Tanger.png','http://www.isitt.ma','3-5 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(126,'CPGE','الأقسام التحضيرية للمدارس العليا','Preparatory Classes for Grandes Ecoles','Kenitra','القنيطرة','Kenitra',8,44,'Preparatory','public',14.5,16,'Filière d\'excellence préparant aux concours des grandes écoles d\'ingénieurs et de commerce.','الأقسام التحضيرية بالقنيطرة مسلك متميز.','Excellence track preparing students for elite schools exams.','Bac mention Bien ou Très Bien + Dossier',NULL,NULL,'Attestation de réussite CPGE',NULL,NULL,'CPGE Kenitra .jpg','http://www.cpge.ac.ma','2 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(127,'CPGE','الأقسام التحضيرية للمدارس العليا','Preparatory Classes for Grandes Ecoles','Oujda','وجدة','Oujda',7,NULL,'Preparatory','public',14.5,16,'Classes préparatoires d\'excellence du Maroc Oriental.','الأقسام التحضيرية بوجدة الشرق.','Excellence preparatory track in Oriental Morocco.','Bac mention Bien/Très Bien',NULL,NULL,'Attestation de réussite CPGE',NULL,NULL,'CPGE Oujda.PNG','http://www.cpge.ac.ma','2 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(128,'OFPPT','مكتب التكوين المهني وإنعاش الشغل','Office of Vocational Training and Employment Promotion','Casablanca','الدار البيضاء','Casablanca',1,NULL,'Technical','public',10,10,'Établissements de formation professionnelle technique et qualifiante.','مكتب التكوين المهني وإنعاش الشغل بالدار البيضاء.','Vocational and qualification technical training institutes.','Niveau Bac ou Baccalauréat',NULL,NULL,'Technicien / Technicien Spécialisé',NULL,NULL,'OFPPT CASABLANCA.png','http://www.ofppt.ma','2 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(130,'OFPPT','مكتب التكوين المهني وإنعاش الشغل','Office of Vocational Training and Employment Promotion','Agadir','أكادير','Agadir',6,NULL,'Technical','public',10,10,'Pôle régional de formation technique à Souss-Massa.','التكوين المهني بأكادير قطب مهم.','Regional technical training hub in Souss-Massa.','Niveau Bac / Bac',NULL,NULL,'Technicien / Technicien Spécialisé',NULL,NULL,'OFPPT Agadir.png','http://www.ofppt.ma','2 ans',NULL,NULL,0,'2026-05-18 09:43:59'),(131,'Solicode – Centre Solidaire Digital','سوليكود طنجة','SoliCode Tanger','Tanger','طنجة','Tangier',5,NULL,'Digital','alternative',NULL,0,'Centre de formation digitale solidaire à Tanger, proposant des formations intensives et gratuites en développement web et mobile, data, et design d\'interface pour faciliter l\'insertion professionnelle des jeunes.','مركز تضامني للتكوين الرقمي بطنجة، يقدم تكوينات مكثفة ومجانية في تطوير الويب والهاتف، البيانات وتصميم الواجهات لتسهيل الإدماج المهني للشباب.','Solidarity digital training center in Tangier, offering free intensive training in web and mobile development, data, and interface design to facilitate the professional integration of young people.','Avoir entre 18 et 35 ans, réussir le test de sélection en ligne (test de logique et de motivation) et l\'entretien individuel. Aucun diplôme préalable requis.','أن يتراوح عمرك بين 18 و 35 سنة، واجتياز اختبار الانتقاء عبر الإنترنت (اختبار المنطق والدافعية) والمقابلة الفردية. لا يشترط شهادة مسبقة.','Be between 18 and 35 years old, pass the online selection test (logic and motivation test) and the individual interview. No prior degree required.','Certificat de formation professionnelle (OFPPT / Fondation Mohamed V)','شهادة تكوين مهني (OFPPT / مؤسسة محمد الخامس)','Vocational training certificate (OFPPT / Mohamed V Foundation)','Solicode-Tanger.png','https://solicode.co/','2 ans','سنتين','2 years',1,'2026-05-19 11:17:10');
/*!40000 ALTER TABLE `institutions` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `title_ar` varchar(255) DEFAULT NULL,
  `title_en` varchar(255) DEFAULT NULL,
  `message` text NOT NULL,
  `message_ar` text DEFAULT NULL,
  `message_en` text DEFAULT NULL,
  `type` enum('system','school','filiere','announcement','maintenance','orientation','deadline') NOT NULL DEFAULT 'system',
  `related_link` varchar(255) DEFAULT NULL,
  `is_global` tinyint(1) DEFAULT 1,
  `target_user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `notifications` WRITE;
/*!40000 ALTER TABLE `notifications` DISABLE KEYS */;
INSERT INTO `notifications` VALUES (1,'Bienvenue sur Maslaki !','مرحباً بكم في مسلكي !','Welcome to Maslaki!','Découvrez notre nouveau système d\'orientation personnalisé.','اكتشفوا نظام التوجيه الشخصي الجديد الخاص بنا.','Discover our new personalized orientation system.','system',NULL,1,NULL,'2026-05-15 10:49:42'),(2,'Nouvelle École : ENSA Tanger','مدرسة جديدة: ENSA طنجة','New School: ENSA Tangier','L\'ENSA Tanger a été ajoutée à la plateforme.','تمت إضافة مدرسة ENSA طنجة إلى المنصة.','ENSA Tangier has been added to the platform.','school',NULL,1,NULL,'2026-05-15 10:49:42'),(3,'Rendez-vous Confirmé',NULL,NULL,'Votre rendez-vous pour \'ensa\' a été enregistré avec succès.',NULL,NULL,'system',NULL,0,3,'2026-05-19 10:51:23'),(4,'Rendez-vous Confirmé',NULL,NULL,'Votre rendez-vous pour \'ensa\' a été enregistré avec succès.',NULL,NULL,'system',NULL,0,3,'2026-05-19 16:44:39'),(5,'Concours disponible d\'ingenieurie maintenant  2026-2027','مباريات سلك المهندسين المفتوحة حالياً 2026-2027','Engineering competition available now for 2026-2027','Le corps des ingénieurs de l\'Institut national des postes et télécommunications de Rabat (INPT)\r\nDate limite : 19 juin 2026','سلك المهندسين المعهد الوطني للبريد والمواصلات  بالرباط INPT\r\n آخر أجل: 19 يونيو 2026','The engineers\' corps of the National Institute of Posts and Telecommunications in Rabat (INPT)\r\nDeadline: June 19, 2026','deadline','https://www.facebook.com/share/p/17ZnYatrvu',1,NULL,'2026-06-14 17:52:46');
/*!40000 ALTER TABLE `notifications` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `premium_plans`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `premium_plans` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `price` float NOT NULL,
  `duration_days` int(11) NOT NULL,
  `features` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `premium_plans` WRITE;
/*!40000 ALTER TABLE `premium_plans` DISABLE KEYS */;
INSERT INTO `premium_plans` VALUES (1,'Mensuel',50,30,'IA Illimitée, Coaching, Accès Anticipé'),(2,'Annuel',450,365,'IA Illimitée, Coaching, Accès Anticipé, Réduction 25%');
/*!40000 ALTER TABLE `premium_plans` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `reviews`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `institution_id` int(11) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `status` enum('pending','approved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `reviews` WRITE;
/*!40000 ALTER TABLE `reviews` DISABLE KEYS */;
INSERT INTO `reviews` VALUES (2,9,2,'dev web','approved','2026-06-09 12:40:29'),(3,3,131,'parfait','approved','2026-06-09 14:57:30'),(4,9,1,'parfait','pending','2026-06-09 16:09:03');
/*!40000 ALTER TABLE `reviews` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `saved_schools`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `saved_schools` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `institution_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `saved_schools` WRITE;
/*!40000 ALTER TABLE `saved_schools` DISABLE KEYS */;
INSERT INTO `saved_schools` VALUES (6,4,131,'2026-05-19 15:23:16');
/*!40000 ALTER TABLE `saved_schools` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `student_subscriptions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student_subscriptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `start_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `end_date` timestamp NULL DEFAULT NULL,
  `status` enum('active','expired','cancelled') DEFAULT 'active',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `student_subscriptions` WRITE;
/*!40000 ALTER TABLE `student_subscriptions` DISABLE KEYS */;
/*!40000 ALTER TABLE `student_subscriptions` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `students` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `bac_branch` varchar(50) DEFAULT NULL,
  `average` float DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `role` enum('student','admin','superadmin') NOT NULL DEFAULT 'student',
  `is_premium` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `avatar` varchar(512) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES (3,'Safa El Gharras','ehsafaa7@gmail.com','$2y$10$3tBjSk0rnstxe0fBuOtfQeZeGsD3OJ2VJWUwcQNii7W1VQeB7tGgG','SVT',15.64,'','superadmin',0,'2026-05-15 10:59:58',NULL),(4,'Test Student','teststudent@gmail.com','$2y$10$B6Tbpy40tcfnNoMDy/QjleSG8SJGuvmpbcidTHOsbHvnq0h3GtL02','Sciences Math',15.5,'','student',0,'2026-05-19 15:13:22',NULL),(5,'Safa El','safa@example.com','$2y$10$XmeMcLvh5kUKRQK.cCKrkOQi146g/1SPN1NFS1b4IWnUosd/b/cPm','Sciences Math',10,'Casablanca','admin',0,'2026-05-22 09:19:10',NULL),(6,'Ahmed Al','ahmed@test.ma','$2y$10$ssc68jwVxPDCCqsxhCpA4OYfUXhJh47y77AiejgtwJKTNqSbT5j.6','Sciences Math',10,'Casablanca','student',0,'2026-05-22 09:26:01',NULL),(7,'SOUNDOUSS l9araya','safacode71@gmail.com','$2y$10$vfDmwQl90oZEdnJK4EHpW.24KoQwVQuInmkI.sqdfXS0zSxUvlpue','Sciences Math',10,'Casablanca','student',0,'2026-05-22 09:27:18',NULL),(8,'Test User','test_darkmode_user@gmail.com','$2y$10$PpPAm2CsKrEHQxKLYvEp7OcZNl/VcEm5sDg62r.mWoPtTkDpj81JO','Sciences Math',15.5,'Tanger','student',0,'2026-06-01 09:24:37',NULL),(9,'walid ait tazount','walid123@gmail.com','$2y$10$/TdmXcbJ88VDiWS2Icq2h.kL6A7QRTGoe.bQNfyyklHNCQ/aIts8.','PC',16.2,'Tanger','student',0,'2026-06-09 12:39:12',NULL),(10,'Founti Code','founticode@gmail.com','$2y$10$n2b2WYErqLjYFyg3N6gwnOeOAocvt3Y7fdC.4hyw.xyvGtnYsttYq','PC',11,'Tetouan','admin',0,'2026-06-13 18:01:38',NULL);
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `translations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `translations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lang` varchar(5) NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `translations` WRITE;
/*!40000 ALTER TABLE `translations` DISABLE KEYS */;
INSERT INTO `translations` VALUES (1,'fr','welcome','Bienvenue sur Maslaki'),(2,'ar','welcome','مرحباً بكم في مسلكي'),(3,'en','welcome','Welcome to Maslaki');
/*!40000 ALTER TABLE `translations` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `user_notifications`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `notification_id` int(11) NOT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `is_deleted` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_user_notif` (`user_id`,`notification_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `user_notifications` WRITE;
/*!40000 ALTER TABLE `user_notifications` DISABLE KEYS */;
INSERT INTO `user_notifications` VALUES (1,3,1,1,0,NULL),(2,3,2,1,0,NULL),(3,3,3,1,0,NULL),(4,4,1,1,0,NULL),(5,4,2,1,0,NULL),(6,3,4,1,0,NULL),(7,7,1,1,0,NULL),(8,7,2,1,0,NULL),(9,9,1,1,0,NULL),(10,9,2,1,0,NULL),(11,10,2,1,0,'2026-06-14 18:04:41'),(12,10,1,1,0,'2026-06-14 18:04:38'),(13,10,5,1,0,'2026-06-14 18:04:26');
/*!40000 ALTER TABLE `user_notifications` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `user_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_requests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` int(11) DEFAULT NULL,
  `type` enum('suggestion','report','support','other') DEFAULT 'suggestion',
  `subject` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `status` enum('pending','seen','resolved') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `user_requests` WRITE;
/*!40000 ALTER TABLE `user_requests` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_requests` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `villes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `villes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `nom_ar` varchar(100) DEFAULT NULL,
  `nom_en` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_ville_nom` (`nom`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `villes` WRITE;
/*!40000 ALTER TABLE `villes` DISABLE KEYS */;
INSERT INTO `villes` VALUES (1,'Casablanca','الدار البيضاء','Casablanca'),(2,'Rabat','الرباط','Rabat'),(3,'Marrakech','مراكش','Marrakech'),(4,'Fes','فاس','Fez'),(5,'Tanger','طنجة','Tangier'),(6,'Agadir','أكادير','Agadir'),(7,'Oujda','وجدة','Oujda'),(8,'Kenitra','القنيطرة','Kenitra'),(9,'Settat','سطات','Settat'),(10,'Meknes','مكناس','Meknes'),(11,'Errachidia','الرشيدية','Errachidia'),(12,'Al Hoceima','الحسيمة','Al Hoceima'),(13,'Laayoune','العيون','Laayoune'),(14,'Mohammedia','المحمدية','Mohammedia'),(15,'Beni Mellal','بني ملال','Beni Mellal'),(16,'Safi','آسفي','Safi'),(17,'El Jadida','الجديدة','El Jadida'),(18,'Khouribga','خريبكة','Khouribga'),(19,'Essaouira','الصويرة','Essaouira'),(20,'Youssoufia','اليوسفية','Youssoufia'),(21,'Benguerir','بن جرير','Benguerir');
/*!40000 ALTER TABLE `villes` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;

-- ============================================================
-- FOREIGN KEY CONSTRAINTS
-- ============================================================

-- institutions → villes
ALTER TABLE `institutions`
  ADD CONSTRAINT `fk_institution_ville` FOREIGN KEY (`ville_id`) REFERENCES `villes`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- institutions → institutions (parent hierarchy)
ALTER TABLE `institutions`
  ADD CONSTRAINT `fk_institution_parent` FOREIGN KEY (`parent_id`) REFERENCES `institutions`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- domains → categories
ALTER TABLE `domains`
  ADD CONSTRAINT `fk_domain_category` FOREIGN KEY (`categorie_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- filieres → categories
ALTER TABLE `filieres`
  ADD CONSTRAINT `fk_filiere_category` FOREIGN KEY (`categorie_id`) REFERENCES `categories`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- filieres → domains
ALTER TABLE `filieres`
  ADD CONSTRAINT `fk_filiere_domain` FOREIGN KEY (`domain_id`) REFERENCES `domains`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- institution_domain → institutions + domains
ALTER TABLE `institution_domain`
  ADD CONSTRAINT `fk_inst_domain_inst` FOREIGN KEY (`institution_id`) REFERENCES `institutions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inst_domain_domain` FOREIGN KEY (`domain_id`) REFERENCES `domains`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- institution_filieres → institutions + filieres
ALTER TABLE `institution_filieres`
  ADD CONSTRAINT `fk_inst_fil_inst` FOREIGN KEY (`institution_id`) REFERENCES `institutions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inst_fil_fil` FOREIGN KEY (`filiere_id`) REFERENCES `filieres`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- institution_bac_types → institutions + bac_types
ALTER TABLE `institution_bac_types`
  ADD CONSTRAINT `fk_inst_bac_inst` FOREIGN KEY (`institution_id`) REFERENCES `institutions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_inst_bac_type` FOREIGN KEY (`bac_type_id`) REFERENCES `bac_types`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- institution_images → institutions
ALTER TABLE `institution_images`
  ADD CONSTRAINT `fk_inst_img_inst` FOREIGN KEY (`institution_id`) REFERENCES `institutions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- contests → institutions
ALTER TABLE `contests`
  ADD CONSTRAINT `fk_contest_inst` FOREIGN KEY (`institution_id`) REFERENCES `institutions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- deadlines → institutions
ALTER TABLE `deadlines`
  ADD CONSTRAINT `fk_deadline_inst` FOREIGN KEY (`institution_id`) REFERENCES `institutions`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- reviews → students + institutions
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_review_student` FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_review_inst` FOREIGN KEY (`institution_id`) REFERENCES `institutions`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- saved_schools → students + institutions
ALTER TABLE `saved_schools`
  ADD CONSTRAINT `fk_saved_student` FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_saved_inst` FOREIGN KEY (`institution_id`) REFERENCES `institutions`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- appointments → students
ALTER TABLE `appointments`
  ADD CONSTRAINT `fk_appt_student` FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- ai_recommendations → students
ALTER TABLE `ai_recommendations`
  ADD CONSTRAINT `fk_ai_rec_student` FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- user_requests → students
ALTER TABLE `user_requests`
  ADD CONSTRAINT `fk_request_student` FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- student_subscriptions → students + premium_plans
ALTER TABLE `student_subscriptions`
  ADD CONSTRAINT `fk_sub_student` FOREIGN KEY (`student_id`) REFERENCES `students`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_sub_plan` FOREIGN KEY (`plan_id`) REFERENCES `premium_plans`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- notifications → students (target_user_id)
ALTER TABLE `notifications`
  ADD CONSTRAINT `fk_notif_target_user` FOREIGN KEY (`target_user_id`) REFERENCES `students`(`id`) ON DELETE SET NULL ON UPDATE CASCADE;

-- user_notifications → students + notifications
ALTER TABLE `user_notifications`
  ADD CONSTRAINT `fk_usernotif_user` FOREIGN KEY (`user_id`) REFERENCES `students`(`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_usernotif_notif` FOREIGN KEY (`notification_id`) REFERENCES `notifications`(`id`) ON DELETE CASCADE ON UPDATE CASCADE;

-- ============================================================
-- ADDITIONAL UNIQUE CONSTRAINTS
-- ============================================================

ALTER TABLE `students` ADD UNIQUE INDEX `uniq_student_email` (`email`);
ALTER TABLE `admin_users` ADD UNIQUE INDEX `uniq_admin_email` (`email`);
ALTER TABLE `bac_types` ADD UNIQUE INDEX `uniq_bac_code` (`code`);
ALTER TABLE `translations` ADD UNIQUE INDEX `uniq_trans_lang_key` (`lang`, `key`(100));
ALTER TABLE `saved_schools` ADD UNIQUE INDEX `uniq_saved_school` (`student_id`, `institution_id`);

/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

