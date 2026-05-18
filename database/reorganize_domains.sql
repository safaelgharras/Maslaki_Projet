-- ====================================================================
-- MASLAKI PLATFORM REORGANIZATION MIGRATION
-- Hierarchical Domain Structure & Modern Institution Classification
-- ====================================================================

SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------------------
-- 1. EXTEND VILLES TABLE WITH MISSING CITIES
-- --------------------------------------------------------------------
INSERT IGNORE INTO `villes` (`id`, `nom`, `nom_ar`, `nom_en`) VALUES
(16, 'Safi', 'آسفي', 'Safi'),
(17, 'El Jadida', 'الجديدة', 'El Jadida'),
(18, 'Khouribga', 'خريبكة', 'Khouribga'),
(19, 'Essaouira', 'الصويرة', 'Essaouira'),
(20, 'Youssoufia', 'اليوسفية', 'Youssoufia'),
(21, 'Benguerir', 'بن جرير', 'Benguerir');

-- --------------------------------------------------------------------
-- 2. CREATE INSTITUTION_DOMAIN PIVOT TABLE
-- --------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `institution_domain` (
  `institution_id` int(11) NOT NULL,
  `domain_id` int(11) NOT NULL,
  PRIMARY KEY (`institution_id`, `domain_id`),
  CONSTRAINT `fk_instdom_inst` FOREIGN KEY (`institution_id`) REFERENCES `institutions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_instdom_dom` FOREIGN KEY (`domain_id`) REFERENCES `domains` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------------------
-- 3. EXTEND INSTITUTIONS TABLE
-- --------------------------------------------------------------------
ALTER TABLE `institutions` 
  ADD COLUMN IF NOT EXISTS `parent_id` int(11) DEFAULT NULL AFTER `ville_id`,
  ADD COLUMN IF NOT EXISTS `sector_type` enum('public', 'private', 'semi-public', 'alternative') DEFAULT 'public' AFTER `type`,
  ADD CONSTRAINT `fk_inst_parent` FOREIGN KEY (`parent_id`) REFERENCES `institutions` (`id`) ON DELETE SET NULL;

-- --------------------------------------------------------------------
-- 4. RECREATE / UPDATE CATEGORIES AND DOMAINS (1-to-1 Setup)
-- --------------------------------------------------------------------
DELETE FROM `domains`;
DELETE FROM `categories`;

-- Seed Categories (1 to 10)
INSERT INTO `categories` (`id`, `nom`, `nom_ar`, `nom_en`) VALUES
(1, 'Sciences Exactes & Technologies', 'العلوم الدقيقة والتكنولوجيات', 'Exact Sciences & Technologies'),
(2, 'Ingénierie & Industrie', 'الهندسة والصناعة', 'Engineering & Industry'),
(3, 'Santé & Sciences de la Vie', 'الصحة وعلوم الحياة', 'Health & Life Sciences'),
(4, 'Agriculture & Environnement', 'الفلاحة والبيئة', 'Agriculture & Environment'),
(5, 'Business, Gestion & Finance', 'الأعمال، التسيير والمالية', 'Business, Management & Finance'),
(6, 'Droit, Politique & Société', 'القانون، السياسة والمجتمع', 'Law, Politics & Society'),
(7, 'Arts, Design & Médias', 'الفنون، التصميم والإعلام', 'Arts, Design & Media'),
(8, 'Services, Tourisme & Transport', 'الخدمات، السياحة والنقل', 'Services, Tourism & Transportation'),
(9, 'Éducation & Sciences Humaines', 'التربية والعلوم الإنسانية', 'Education & Human Sciences'),
(10, 'Formation Professionnelle & Métiers', 'التكوين المهني والحرف', 'Vocational Training & Trades');

-- Seed Domains (1 to 10, mapping 1-to-1 to Categories)
INSERT INTO `domains` (`id`, `categorie_id`, `nom`, `nom_ar`, `nom_en`, `description`) VALUES
(1, 1, 'Sciences Exactes & Technologies', 'العلوم الدقيقة والتكنولوجيات', 'Exact Sciences & Technologies', 'Mathématiques, Physique, Informatique, Développement, Big Data et IA'),
(2, 2, 'Ingénierie & Industrie', 'الهندسة والصناعة', 'Engineering & Industry', 'Génie Civil, Électrique, Mécanique, Industriel, Procédés et Infrastructures'),
(3, 3, 'Santé & Sciences de la Vie', 'الصحة وعلوم الحياة', 'Health & Life Sciences', 'Médecine, Pharmacie, Odontologie, Professions Infirmières et Biologie Médicale'),
(4, 4, 'Agriculture & Environnement', 'الفلاحة والبيئة', 'Agriculture & Environment', 'Sciences Agronomiques, Agroalimentaire, Foresterie et Environnement'),
(5, 5, 'Business, Gestion & Finance', 'الأعمال، التسيير والمالية', 'Business, Management & Finance', 'Management, Audit, Marketing, Commerce International, Supply Chain'),
(6, 6, 'Droit, Politique & Société', 'القانون، السياسة والمجتمع', 'Law, Politics & Society', 'Droit Public, Privé, Sciences Politiques et Relations Internationales'),
(7, 7, 'Arts, Design & Médias', 'الفنون، التصميم والإعلام', 'Arts, Design & Media', 'Architecture, Beaux-Arts, Cinéma, Journalisme et Communication'),
(8, 8, 'Services, Tourisme & Transport', 'الخدمات، السياحة والنقل', 'Services, Tourism & Transportation', 'Management Hôtelier, Restauration, Transport et Logistique du Service'),
(9, 9, 'Éducation & Sciences Humaines', 'التربية والعلوم الإنسانية', 'Education & Human Sciences', 'Lettres, Langues, Psychologie, Enseignement et Histoire-Géographie'),
(10, 10, 'Formation Professionnelle & Métiers', 'التكوين المهني والحرف', 'Vocational Training & Trades', 'Filières professionnalisantes courtes de l\'OFPPT et métiers techniques');

-- --------------------------------------------------------------------
-- 5. RE-MAP ALL FILIERES TO THE 10 NEW DOMAINS & CATEGORIES
-- --------------------------------------------------------------------
UPDATE `filieres` SET `domain_id` = 1, `categorie_id` = 1 WHERE `nom` LIKE '%Informatique%' OR `nom` LIKE '%Data%' OR `nom` LIKE '%Web%' OR `nom` LIKE '%Mobile%' OR `nom` LIKE '%Logiciel%' OR `nom` LIKE '%Système%' OR `nom` LIKE '%Intelligence%' OR `nom` LIKE '%Cybersécurité%' OR `nom` LIKE '%Math%' OR `nom` LIKE '%Physique%' OR `nom` LIKE '%Réseaux%';
UPDATE `filieres` SET `domain_id` = 2, `categorie_id` = 2 WHERE `nom` LIKE '%Génie Civil%' OR `nom` LIKE '%Industriel%' OR `nom` LIKE '%Électrique%' OR `nom` LIKE '%Mécanique%' OR `nom` LIKE '%Industrie%' OR `nom` LIKE '%Procédés%';
UPDATE `filieres` SET `domain_id` = 3, `categorie_id` = 3 WHERE `nom` LIKE '%Médecine%' OR `nom` LIKE '%Pharmacie%' OR `nom` LIKE '%Infirmier%' OR `nom` LIKE '%Kinésithérapie%' OR `nom` LIKE '%Santé%' OR `nom` LIKE '%Biologie%';
UPDATE `filieres` SET `domain_id` = 4, `categorie_id` = 4 WHERE `nom` LIKE '%Agro%' OR `nom` LIKE '%Ferme%' OR `nom` LIKE '%Environnement%';
UPDATE `filieres` SET `domain_id` = 5, `categorie_id` = 5 WHERE `nom` LIKE '%Finance%' OR `nom` LIKE '%Marketing%' OR `nom` LIKE '%Gestion%' OR `nom` LIKE '%Commerce%' OR `nom` LIKE '%Audit%' OR `nom` LIKE '%Comptabilité%' OR `nom` LIKE '%Management%' OR `nom` LIKE '%Logistique%' OR `nom` LIKE '%Économie%';
UPDATE `filieres` SET `domain_id` = 6, `categorie_id` = 6 WHERE `nom` LIKE '%Droit%' OR `nom` LIKE '%Politique%' OR `nom` LIKE '%Relations%';
UPDATE `filieres` SET `domain_id` = 7, `categorie_id` = 7 WHERE `nom` LIKE '%Design%' OR `nom` LIKE '%Architecture%' OR `nom` LIKE '%Cinéma%' OR `nom` LIKE '%Audiovisuel%' OR `nom` LIKE '%Mode%';
UPDATE `filieres` SET `domain_id` = 8, `categorie_id` = 8 WHERE `nom` LIKE '%Hôtel%' OR `nom` LIKE '%Tourisme%' OR `nom` LIKE '%Restaur%' OR `nom` LIKE '%Service%';
UPDATE `filieres` SET `domain_id` = 9, `categorie_id` = 9 WHERE `nom` LIKE '%Sociologie%' OR `nom` LIKE '%Psychologie%' OR `nom` LIKE '%Philosophie%' OR `nom` LIKE '%Histoire%' OR `nom` LIKE '%Géographie%' OR `nom` LIKE '%Drapeau%' OR `nom` LIKE '%Français%' OR `nom` LIKE '%Anglais%' OR `nom` LIKE '%Arabe%' OR `nom` LIKE '%Traduction%' OR `nom` LIKE '%Linguistique%';

-- Add a vocational filiere for domain 10
INSERT IGNORE INTO `filieres` (`id`, `nom`, `nom_ar`, `nom_en`, `description`, `categorie_id`, `domain_id`) VALUES
(100, 'Technicien Spécialisé en Diagnostic Automobile', 'تقني متخصص في تشخيص السيارات', 'Specialized Technician in Automotive Diagnostics', 'Diagnostic et réparation des pannes complexes de l\'automobile.', 10, 10),
(101, 'Technicien en Électricité de Bâtiment', 'تقني في كهرباء البناء', 'Technician in Building Electricity', 'Installation et maintenance électrique dans le secteur de la construction.', 10, 10);

-- --------------------------------------------------------------------
-- 6. SEED NEW MOROCCAN INSTITUTIONS REQUESTED BY USER
-- --------------------------------------------------------------------
INSERT IGNORE INTO `institutions` (`id`, `name`, `name_ar`, `name_en`, `city`, `city_ar`, `city_en`, `ville_id`, `type`, `sector_type`, `min_average`, `seuil`, `description`, `description_ar`, `description_en`, `requirements`, `diplome`, `site_web`, `duree_etudes`, `image`) VALUES
-- Sciences Exactes & Technologies (Sciences / Tech)
(100, 'ENSA Safi', 'المدرسة الوطنية للعلوم التطبيقية بآسفي', 'National School of Applied Sciences Safi', 'Safi', 'آسفي', 'Safi', 16, 'Engineering', 'public', 12, 12, 'École d\'ingénieurs publique.', 'المدرسة الوطنية للعلوم التطبيقية بآسفي مدرسة مهندسين عمومية.', 'National School of Applied Sciences Safi is a public engineering school.', 'Bac Sciences + Concours', 'Ingénieur d\'État', 'http://www.ensas.uca.ma', '5 ans', 'ensa_safi.jpg'),
(101, 'ENSA El Jadida', 'المدرسة الوطنية للعلوم التطبيقية بالجديدة', 'National School of Applied Sciences El Jadida', 'El Jadida', 'الجديدة', 'El Jadida', 17, 'Engineering', 'public', 12, 12, 'École nationale publique d\'ingénieurs.', 'مدرسة عمومية لتكوين المهندسين.', 'Public national engineering school.', 'Bac Sciences + Concours', 'Ingénieur d\'État', 'http://www.ensaj.ac.ma', '5 ans', 'ensa_eljadida.jpg'),
(102, 'ENSA Khouribga', 'المدرسة الوطنية للعلوم التطبيقية بخريبكة', 'National School of Applied Sciences Khouribga', 'Khouribga', 'خريبكة', 'Khouribga', 18, 'Engineering', 'public', 12, 12, 'École publique prestigieuse d\'ingénieurs.', 'مدرسة مهندسين حكومية بخريبكة.', 'Prestigious public engineering school.', 'Bac Sciences + Concours', 'Ingénieur d\'État', 'http://ensa.usms.ac.ma', '5 ans', 'ensa_khouribga.jpg'),
(103, 'FST Oujda', 'كلية العلوم والتقنيات بوجدة', 'Faculty of Sciences and Technologies Oujda', 'Oujda', 'وجدة', 'Oujda', 7, 'Science', 'public', 10, 10, 'Faculté des sciences et techniques publique.', 'كلية العلوم والتقنيات بوجدة مؤسسة جامعية عمومية.', 'Faculty of Sciences and Technologies Oujda is a public university institution.', 'Bac Sciences', 'Licence / Master', 'http://fsto.ump.ma', '3-5 ans', 'fst_oujda.jpg'),
(104, 'FST Al Jadida', 'كلية العلوم والتقنيات بالجديدة', 'Faculty of Sciences and Technologies El Jadida', 'El Jadida', 'الجديدة', 'El Jadida', 17, 'Science', 'public', 10, 10, 'Faculté universitaire de sciences.', 'كلية العلوم والتقنيات بالجديدة.', 'University faculty of sciences.', 'Bac Sciences', 'Licence / Master / Ingénieur', 'http://www.fsth.ac.ma', '3-5 ans', 'fst_eljadida.jpg'),
(105, 'EST Safi', 'المدرسة العليا للتكنولوجيا بآسفي', 'Higher School of Technology Safi', 'Safi', 'آسفي', 'Safi', 16, 'Technical', 'public', 10, 10, 'École technique publique pour diplômes courts.', 'المدرسة العليا للتكنولوجيا بآسفي تكوين تقني قصير.', 'Higher School of Technology Safi public technical school.', 'Bac Sciences / Tech', 'DUT / Licence Professionnelle', 'http://www.ests.uca.ma', '2 ans', 'est_safi.jpg'),
(106, 'EST Essaouira', 'المدرسة العليا للتكنولوجيا بالصويرة', 'Higher School of Technology Essaouira', 'Essaouira', 'الصويرة', 'Essaouira', 19, 'Technical', 'public', 10, 10, 'École de technologie à Essaouira.', 'المدرسة العليا للتكنولوجيا بالصويرة.', 'Higher School of Technology in Essaouira.', 'Bac Sciences / Tech', 'DUT / Licence Pro', 'http://www.este.uca.ma', '2 ans', 'est_essaouira.jpg'),
(107, 'FS Agadir', 'كلية العلوم بأكادير', 'Faculty of Sciences Agadir', 'Agadir', 'أكادير', 'Agadir', 6, 'Science', 'public', 10, 10, 'Faculté des sciences d\'Agadir.', 'كلية العلوم بأكادير.', 'Faculty of Sciences in Agadir.', 'Bac Sciences', 'Licence / Master', 'http://www.fsa.ac.ma', '3-5 ans', 'fs_agadir.jpg'),
(108, 'FS Marrakech', 'كلية العلوم السملالية بمراكش', 'Faculty of Sciences Semlalia Marrakech', 'Marrakech', 'مراكش', 'Marrakech', 3, 'Science', 'public', 10, 10, 'Faculté scientifique prestigieuse.', 'كلية العلوم السملالية بمراكش.', 'Prestigious scientific faculty.', 'Bac Sciences', 'Licence / Master', 'http://www.fssm.uca.ma', '3-5 ans', 'fs_marrakech.jpg'),
(109, 'FS Tanger', 'كلية العلوم بطنجة', 'Faculty of Sciences Tangier', 'Tangier', 'طنجة', 'Tangier', 5, 'Science', 'public', 10, 10, 'Faculté des sciences de Tanger.', 'كلية العلوم بطنجة.', 'Faculty of Sciences in Tangier.', 'Bac Sciences', 'Licence / Master / Doctorat', 'http://www.fst.ac.ma', '3-5 ans', 'fs_tanger.jpg'),
(110, 'ENSIAS Rabat', 'المدرسة الوطنية العليا لتحليل النظم والمعلوماتية بالرباط', 'National Higher School for Computer Science and Systems Analysis', 'Rabat', 'الرباط', 'Rabat', 2, 'Engineering', 'public', 15.5, 15.5, 'La plus prestigieuse grande école en informatique au Maroc.', 'المدرسة الوطنية العليا لتحليل النظم بالرباط مدرسة متميزة في الهندسة المعلوماتية.', 'Prestigous elite IT engineering school in Rabat.', 'Concours National Commun (CNC) après CPGE', 'Ingénieur d\'État', 'http://ensias.um5.ac.ma', '3 ans', 'ensias.jpg'),

-- Santé & Sciences de la Vie
(111, 'ISPITS Casablanca', 'المعهد العالي للمهن التمريضية وتقنيات الصحة بالدار البيضاء', 'Higher Institute of Nursing Professions and Health Techniques', 'Casablanca', 'الدار البيضاء', 'Casablanca', 1, 'Medical', 'public', 12.5, 12.5, 'Institut public leader dans les professions de santé au Maroc.', 'المعهد العالي للمهن التمريضية وتقنيات الصحة بالدار البيضاء.', 'Leading public institute in nursing and health sciences.', 'Bac SVT / PC + Sélection + Concours', 'Licence / Master Professionnel', 'http://ispits.sante.gov.ma', '3 ans', 'ispits.jpg'),

-- Agriculture & Environnement
(112, 'IAV Hassan II Rabat', 'معهد الحسن الثاني للزراعة والبيطرة بالرباط', 'Hassan II Institute of Agronomy and Veterinary Medicine', 'Rabat', 'الرباط', 'Rabat', 2, 'Science', 'public', 14, 14, 'Le pôle national d\'excellence pour l\'agriculture et la médecine vétérinaire.', 'معهد الحسن الثاني للزراعة والبيطرة بالرباط مرجع الهندسة الزراعية.', 'National agricultural and veterinary center of excellence.', 'Bac SVT/PC/Math + Sélection + Concours', 'Ingénieur / Docteur Vétérinaire', 'http://www.iav.ac.ma', '5-6 ans', 'iav.jpg'),

-- Business, Gestion & Finance
(113, 'ENCG El Jadida', 'المدرسة الوطنية للتجارة والتسيير بالجديدة', 'National School of Business and Management El Jadida', 'El Jadida', 'الجديدة', 'El Jadida', 17, 'Business', 'public', 11, 11, 'École publique de commerce et management de l\'Université Chouaib Doukkali.', 'المدرسة الوطنية للتجارة والتسيير بالجديدة.', 'Public school of business and management at Chouaib Doukkali University.', 'Bac Éco / Math + Sélection dossier', 'Diplôme ENCG (Grade Master)', 'http://www.encg.ac.ma', '5 ans', 'encg_eljadida.jpg'),
(114, 'ENCG Tanger', 'المدرسة الوطنية للتجارة والتسيير بطنجة', 'National School of Business and Management Tangier', 'Tangier', 'طنجة', 'Tangier', 5, 'Business', 'public', 11, 11, 'École supérieure de commerce et de gestion dynamique à Tanger.', 'المدرسة الوطنية للتجارة والتسيير بطنجة.', 'Dynamic public school of commerce and management in Tangier.', 'Bac Éco / Math / PC + Concours', 'Diplôme ENCG', 'http://www.encgt.ac.ma', '5 ans', 'encg_tanger.jpg'),
(115, 'EMSI Rabat', 'المدرسة المغربية لعلوم المهندس بالرباط', 'Moroccan School of Engineering Sciences Rabat', 'Rabat', 'الرباط', 'Rabat', 2, 'Private', 'private', 10, 10, 'École d\'ingénieurs privée accréditée.', 'المدرسة المغربية لعلوم المهندس بالرباط مدرسة خاصة معتمدة.', 'Accredited private engineering school in Rabat.', 'Bac Sciences / Tech + Entretien', 'Diplôme EMSI (Reconnaissance d\'État)', 'http://www.emsi.ma', '5 ans', 'emsi_rabat.jpg'),
(116, 'EMSI Marrakech', 'المدرسة المغربية لعلوم المهندس بمراكش', 'Moroccan School of Engineering Sciences Marrakech', 'Marrakech', 'مراكش', 'Marrakech', 3, 'Private', 'private', 10, 10, 'Campus EMSI dans la ville ocre.', 'المدرسة المغربية لعلوم المهندس بمراكش.', 'EMSI engineering campus in Marrakech.', 'Bac Sciences / Tech + Entretien', 'Diplôme EMSI', 'http://www.emsi.ma', '5 ans', 'emsi_marrakech.jpg'),
(117, 'ESITH Casablanca', 'المدرسة العليا لصناعات النسيج والألبسة بالدار البيضاء', 'Higher School of Textile and Clothing Industries', 'Casablanca', 'الدار البيضاء', 'Casablanca', 1, 'Engineering', 'semi-public', 12, 12, 'Grande école d\'ingénieurs en gestion industrielle, logistique et textile en partenariat public-privé.', 'المدرسة العليا لصناعات النسيج والألبسة بالدار البيضاء شراكة عام-خاص.', 'Elite engineering school in logistics and industrial management.', 'Bac Math/PC + Sélection + Concours', 'Ingénieur d\'État / Licence Pro', 'http://www.esith.ac.ma', '3-5 ans', 'esith.jpg'),

-- Arts, Design & Médias
(118, 'ISMAC Rabat', 'المعهد العالي لمهن السمعي البصri والسينما بالرباط', 'Higher Institute of Audiovisual and Cinema Professions', 'Rabat', 'الرباط', 'Rabat', 2, 'Art', 'public', 12, 12, 'Institut national pour la formation des professionnels du cinéma et de la télévision.', 'المعهد العالي لمهن السمعي البصري والسينما بالرباط.', 'National institute for cinema and television professions.', 'Bac + Sélection + Concours écrit & oral', 'Licence Professionnelle / Master', 'http://www.ismac.ac.ma', '3 ans', 'ismac.jpg'),
(119, 'ISIC Rabat', 'المعهد العالي للإعلام والاتصال بالرباط', 'Higher Institute of Information and Communication', 'Rabat', 'الرباط', 'Rabat', 2, 'Art', 'public', 13, 13, 'La grande école de journalisme publique historique du Maroc.', 'المعهد العالي للإعلام والاتصال بالرباط مدرسة الصحافة العريقة.', 'Historical and leading public journalism school in Morocco.', 'Bac toutes séries + Sélection + Concours écrit/oral', 'Licence / Master / Doctorat', 'http://www.isic.ac.ma', '3 ans', 'isic.jpg'),

-- Droit, Politique & Société (Universities)
(120, 'Université Mohammed V', 'جامعة محمد الخامس بالرباط', 'Mohammed V University Rabat', 'Rabat', 'الرباط', 'Rabat', 2, 'University', 'public', 10, 10, 'La première université moderne du Royaume du Maroc.', 'جامعة محمد الخامس بالرباط الجامعة العريقة بالمغرب.', 'The first prestigious modern university of Morocco.', 'Baccalauréat', 'Conteneur Institutionnel', 'http://www.um5.ac.ma', '3-8 ans', 'uni_mohammed5.jpg'),
(121, 'Université Hassan I', 'جامعة الحسن الأول بسطات', 'Hassan I University Settat', 'Settat', 'سطات', 'Settat', 9, 'University', 'public', 10, 10, 'Université publique moderne de la région de Chaouia.', 'جامعة الحسن الأول بسطات قطب جامعي مهم.', 'Modern public university of the Chaouia region.', 'Baccalauréat', 'Conteneur Institutionnel', 'http://www.uh1.ac.ma', '3-8 ans', 'uni_hassan1.jpg'),
(122, 'Université Sultan Moulay Slimane', 'جامعة السلطان مولاي سليمان ببني ملال', 'Sultan Moulay Slimane University Beni Mellal', 'Beni Mellal', 'بني ملال', 'Beni Mellal', 15, 'University', 'public', 10, 10, 'Université publique régionale de Tadla-Azilal.', 'جامعة السلطان مولاي سليمان ببني ملال.', 'Regional public university of Tadla-Azilal.', 'Baccalauréat', 'Conteneur Institutionnel', 'http://www.usms.ac.ma', '3-8 ans', 'uni_sultanms.jpg'),
(123, 'Université Sidi Mohamed Ben Abdellah', 'جامعة سيدي محمد بن عبد الله بفاس', 'Sidi Mohamed Ben Abdellah University Fez', 'Fes', 'فاس', 'Fez', 4, 'University', 'public', 10, 10, 'Grande université publique historique du Nord-Centre.', 'جامعة سيدي محمد بن عبد الله بفاس التاريخية.', 'Historical major public university of the North-Center.', 'Baccalauréat', 'Conteneur Institutionnel', 'http://www.usmba.ac.ma', '3-8 ans', 'uni_smba.jpg'),
(124, 'Université Chouaib Doukkali', 'جامعة شعيب الدكالي بالجديدة', 'Chouaib Doukkali University El Jadida', 'El Jadida', 'الجديدة', 'El Jadida', 17, 'University', 'public', 10, 10, 'Université publique côtière d\'El Jadida.', 'جامعة شعيب الدكالي بالجديدة قطب العلوم والآداب.', 'Coastal public university in El Jadida.', 'Baccalauréat', 'Conteneur Institutionnel', 'http://www.ucd.ac.ma', '3-8 ans', 'uni_chouaibd.jpg'),

-- Services, Tourisme & Transport
(125, 'ISITT Tanger', 'المعهد العالي الدولي للسياحة بطنجة', 'International Higher Institute of Tourism Tangier', 'Tangier', 'طنجة', 'Tangier', 5, 'Management', 'public', 11, 11, 'L\'institut national d\'excellence pour le management touristique et hôtelier.', 'المعهد العالي الدولي للسياحة بطنجة رائد تكوين السياحة.', 'National institute of excellence in hospitality and tourism management.', 'Bac + Dossier + Concours écrit & oral', 'Licence Professionnelle / Master', 'http://www.isitt.ma', '3-5 ans', 'isitt.jpg'),

-- Éducation & Sciences Humaines (CPGE)
(126, 'CPGE Kenitra', 'الأقسام التحضيرية بالقنيطرة', 'Preparatory Classes Kenitra', 'Kenitra', 'القنيطرة', 'Kenitra', 8, 'Preparatory', 'public', 14.5, 14.5, 'Filière d\'excellence préparant aux concours des grandes écoles d\'ingénieurs et de commerce.', 'الأقسام التحضيرية بالقنيطرة مسلك متميز.', 'Excellence track preparing students for elite schools exams.', 'Bac mention Bien ou Très Bien + Dossier', 'Attestation d\'études CPGE', 'http://www.cpge.ac.ma', '2 ans', 'cpge_kenitra.jpg'),
(127, 'CPGE Oujda', 'الأقسام التحضيرية بوجدة', 'Preparatory Classes Oujda', 'Oujda', 'وجدة', 'Oujda', 7, 'Preparatory', 'public', 14.5, 14.5, 'Classes préparatoires d\'excellence du Maroc Oriental.', 'الأقسام التحضيرية بوجدة الشرق.', 'Excellence preparatory track in Oriental Morocco.', 'Bac mention Bien/Très Bien', 'Attestation d\'études', 'http://www.cpge.ac.ma', '2 ans', 'cpge_oujda.png'),

-- Formation Professionnelle (OFPPT)
(128, 'OFPPT Casablanca', 'مكتب التكوين المهني بالدار البيضاء', 'OFPPT Casablanca', 'Casablanca', 'الدار البيضاء', 'Casablanca', 1, 'Technical', 'public', 10, 10, 'Établissements de formation professionnelle technique et qualifiante.', 'مكتب التكوين المهني وإنعاش الشغل بالدار البيضاء.', 'Vocational and qualification technical training institutes.', 'Niveau Bac ou Baccalauréat', 'DUT / Technicien Spécialisé / Qualifié', 'http://www.ofppt.ma', '2 ans', 'ofppt_casa.jpg'),
(129, 'OFPPT Marrakech', 'مكتب التكوين المهني بمراكش', 'OFPPT Marrakech', 'Marrakech', 'مراكش', 'Marrakech', 3, 'Technical', 'public', 10, 10, 'Formation professionnelle courte et professionnalisante dans la région de Marrakech.', 'التكوين المهني بمراكش شعب مختلفة.', 'Short technical training in Marrakech region.', 'Niveau Bac / Bac', 'Technicien / Technicien Spécialisé', 'http://www.ofppt.ma', '2 ans', 'ofppt_marrakech.jpg'),
(130, 'OFPPT Agadir', 'مكتب التكوين المهني بأكادير', 'OFPPT Agadir', 'Agadir', 'أكادير', 'Agadir', 6, 'Technical', 'public', 10, 10, 'Pôle régional de formation technique à Souss-Massa.', 'التكوين المهني بأكادير قطب مهم.', 'Regional technical training hub in Souss-Massa.', 'Niveau Bac / Bac', 'Technicien / Technicien Spécialisé', 'http://www.ofppt.ma', '2 ans', 'ofppt_agadir.png');

-- --------------------------------------------------------------------
-- 7. ESTABLISH UNIVERSITY CONTAINER HIERARCHY
-- --------------------------------------------------------------------
-- Set Parent ID for faculties under Université Hassan II (ID = 43 or let's find/update them)
-- Let's update using subqueries to support safe IDs
UPDATE `institutions` SET `parent_id` = (SELECT id FROM (SELECT id FROM `institutions` WHERE `name` = 'Université Hassan II') AS temp)
WHERE `name` IN ('ENSA Casablanca', 'ENCG Casablanca', 'EST Casablanca', 'FS Casablanca', 'OFPPT Casablanca', 'ISPITS Casablanca', 'ESITH Casablanca', 'EAC Casablanca');

UPDATE `institutions` SET `parent_id` = (SELECT id FROM (SELECT id FROM `institutions` WHERE `name` = 'Université Mohammed V') AS temp)
WHERE `name` IN ('EMI Rabat', 'ENSIAS Rabat', 'ENS Rabat', 'FS Rabat', 'CPGE Rabat', 'ENA Rabat', 'FMP', 'IAV Hassan II Rabat', 'ISMAC Rabat', 'ISIC Rabat');

UPDATE `institutions` SET `parent_id` = (SELECT id FROM (SELECT id FROM `institutions` WHERE `name` = 'Université Cadi Ayyad') AS temp)
WHERE `name` IN ('ENSA Marrakech', 'ENCG Marrakech', 'CPGE Marrakech', 'FS Marrakech', 'OFPPT Marrakech', 'EST Safi', 'ENSA Safi', 'EST Essaouira');

UPDATE `institutions` SET `parent_id` = (SELECT id FROM (SELECT id FROM `institutions` WHERE `name` = 'Université Ibn Tofail') AS temp)
WHERE `name` IN ('ENSA Kenitra', 'ENCG Kenitra', 'EST Kenitra', 'CPGE Kenitra');

UPDATE `institutions` SET `parent_id` = (SELECT id FROM (SELECT id FROM `institutions` WHERE `name` = 'Université Abdelmalek Essaadi') AS temp)
WHERE `name` IN ('ENSA Tanger', 'ENCG Tanger', 'FS Tanger', 'FST Tanger', 'ISITT Tanger');

UPDATE `institutions` SET `parent_id` = (SELECT id FROM (SELECT id FROM `institutions` WHERE `name` = 'Université Hassan I') AS temp)
WHERE `name` IN ('FST Settat', 'ENCG Settat');

UPDATE `institutions` SET `parent_id` = (SELECT id FROM (SELECT id FROM `institutions` WHERE `name` = 'Université Sultan Moulay Slimane') AS temp)
WHERE `name` IN ('FST Beni Mellal', 'ENSA Khouribga');

UPDATE `institutions` SET `parent_id` = (SELECT id FROM (SELECT id FROM `institutions` WHERE `name` = 'Université Sidi Mohamed Ben Abdellah') AS temp)
WHERE `name` IN ('ENSA Fes', 'EST Fes', 'CPGE Fes', 'Université Sidi Mohamed Ben Abdellah');

-- --------------------------------------------------------------------
-- 8. SET SECTOR TYPE FOR ALL EXISTING INSTITUTIONS
-- --------------------------------------------------------------------
-- Private
UPDATE `institutions` SET `sector_type` = 'private' WHERE `type` = 'Private' OR `name` IN ('UIR Rabat', 'EMSI Casablanca', 'SUPMTI Rabat', 'IGA Casablanca', 'HEM Casablanca', 'ISGA Marrakech', 'SUPINFO Casablanca', 'EIGSI Casablanca', 'HECI Casablanca', 'ESCA Ecole de Management', 'Art\'Com Sup', 'EAC Casablanca', 'UM6SS');
-- Alternative
UPDATE `institutions` SET `sector_type` = 'alternative' WHERE `name` IN ('1337', 'YouCode', 'UM6P');
-- Public
UPDATE `institutions` SET `sector_type` = 'public' WHERE `sector_type` IS NULL OR `sector_type` = 'public';

-- --------------------------------------------------------------------
-- 9. POPULATE INSTITUTION_DOMAIN PIVOT TABLE
-- --------------------------------------------------------------------
-- Clear pivot table to avoid constraint failures during re-population
DELETE FROM `institution_domain`;

-- Map multi-domain institutions first:
-- EMSI (Engineering & Business)
INSERT IGNORE INTO `institution_domain` (`institution_id`, `domain_id`)
SELECT `id`, 1 FROM `institutions` WHERE `name` LIKE '%EMSI%'; -- Tech/Science exactes
INSERT IGNORE INTO `institution_domain` (`institution_id`, `domain_id`)
SELECT `id`, 2 FROM `institutions` WHERE `name` LIKE '%EMSI%'; -- Ingénierie
INSERT IGNORE INTO `institution_domain` (`institution_id`, `domain_id`)
SELECT `id`, 5 FROM `institutions` WHERE `name` LIKE '%EMSI%'; -- Business

-- SUPMTI (Exact Sciences & Business)
INSERT IGNORE INTO `institution_domain` (`institution_id`, `domain_id`)
SELECT `id`, 1 FROM `institutions` WHERE `name` LIKE '%SUPMTI%'; -- Tech
INSERT IGNORE INTO `institution_domain` (`institution_id`, `domain_id`)
SELECT `id`, 5 FROM `institutions` WHERE `name` LIKE '%SUPMTI%'; -- Business

-- ESITH (Ingénierie & Business)
INSERT IGNORE INTO `institution_domain` (`institution_id`, `domain_id`)
SELECT `id`, 2 FROM `institutions` WHERE `name` = 'ESITH Casablanca'; -- Ingénierie
INSERT IGNORE INTO `institution_domain` (`institution_id`, `domain_id`)
SELECT `id`, 5 FROM `institutions` WHERE `name` = 'ESITH Casablanca'; -- Business

-- Seed standard schools based on their core type and matching domain:
-- Engineering (Domain 2)
INSERT IGNORE INTO `institution_domain` (`institution_id`, `domain_id`)
SELECT `id`, 2 FROM `institutions` WHERE `type` = 'Engineering' AND `name` NOT LIKE '%EMSI%' AND `name` != 'ESITH Casablanca';

-- Business (Domain 5)
INSERT IGNORE INTO `institution_domain` (`institution_id`, `domain_id`)
SELECT `id`, 5 FROM `institutions` WHERE `type` = 'Business' AND `name` NOT LIKE '%SUPMTI%';

-- Technical (EST - Domain 1 and 2 and 10 depending on tech skills)
INSERT IGNORE INTO `institution_domain` (`institution_id`, `domain_id`)
SELECT `id`, 1 FROM `institutions` WHERE `type` = 'Technical' AND `name` LIKE '%EST%';
INSERT IGNORE INTO `institution_domain` (`institution_id`, `domain_id`)
SELECT `id`, 10 FROM `institutions` WHERE `type` = 'Technical' AND `name` LIKE '%OFPPT%';

-- Science (Domain 1 and 3 and 4)
INSERT IGNORE INTO `institution_domain` (`institution_id`, `domain_id`)
SELECT `id`, 1 FROM `institutions` WHERE `type` = 'Science' AND `name` NOT IN ('IAV Hassan II Rabat');
INSERT IGNORE INTO `institution_domain` (`institution_id`, `domain_id`)
SELECT `id`, 4 FROM `institutions` WHERE `name` = 'IAV Hassan II Rabat';

-- Medical (Domain 3)
INSERT IGNORE INTO `institution_domain` (`institution_id`, `domain_id`)
SELECT `id`, 3 FROM `institutions` WHERE `type` = 'Medical' OR `name` IN ('FMP', 'UM6SS', 'ISPITS Casablanca');

-- Education & Human Sciences (Domain 9)
INSERT IGNORE INTO `institution_domain` (`institution_id`, `domain_id`)
SELECT `id`, 9 FROM `institutions` WHERE `type` = 'Education' OR `type` = 'Preparatory' OR `name` IN ('FLSH');

-- Law & Politics (Domain 6)
INSERT IGNORE INTO `institution_domain` (`institution_id`, `domain_id`)
SELECT `id`, 6 FROM `institutions` WHERE `name` IN ('FSJES', 'Université Hassan II', 'Université Mohammed V', 'Université Cadi Ayyad', 'Université Ibn Tofail', 'Université Abdelmalek Essaadi', 'Université Hassan I', 'Université Sultan Moulay Slimane', 'Université Sidi Mohamed Ben Abdellah', 'Université Chouaib Doukkali');

-- Arts & Media (Domain 7)
INSERT IGNORE INTO `institution_domain` (`institution_id`, `domain_id`)
SELECT `id`, 7 FROM `institutions` WHERE `type` = 'Art' OR `name` IN ('ENA Rabat', 'EAC Casablanca', 'Art\'Com Sup', 'ISMAC Rabat', 'ISIC Rabat');

-- Services & Tourism (Domain 8)
INSERT IGNORE INTO `institution_domain` (`institution_id`, `domain_id`)
SELECT `id`, 8 FROM `institutions` WHERE `name` IN ('ISITT Tanger');

-- --------------------------------------------------------------------
-- 10. CLEAN UP AND SYNC INST-FILIERES
-- --------------------------------------------------------------------
-- Map new schools to their respective filieres so they show up beautifully on explore maps
INSERT IGNORE INTO `institution_filieres` (`institution_id`, `filiere_id`)
SELECT i.id, f.id FROM `institutions` i, `filieres` f 
WHERE i.name IN ('ENSA Safi', 'ENSA El Jadida', 'ENSA Khouribga', 'ENSIAS Rabat') AND f.nom = 'Génie Informatique';

INSERT IGNORE INTO `institution_filieres` (`institution_id`, `filiere_id`)
SELECT i.id, f.id FROM `institutions` i, `filieres` f 
WHERE i.name IN ('FST Oujda', 'FST Al Jadida') AND f.nom = 'Sciences Physiques';

INSERT IGNORE INTO `institution_filieres` (`institution_id`, `filiere_id`)
SELECT i.id, f.id FROM `institutions` i, `filieres` f 
WHERE i.name IN ('EST Safi', 'EST Essaouira') AND f.nom = 'Développement Web';

INSERT IGNORE INTO `institution_filieres` (`institution_id`, `filiere_id`)
SELECT i.id, f.id FROM `institutions` i, `filieres` f 
WHERE i.name IN ('ISPITS Casablanca') AND f.nom = 'Infirmier';

INSERT IGNORE INTO `institution_filieres` (`institution_id`, `filiere_id`)
SELECT i.id, f.id FROM `institutions` i, `filieres` f 
WHERE i.name IN ('IAV Hassan II Rabat') AND f.nom = 'Sciences Agronomiques';

INSERT IGNORE INTO `institution_filieres` (`institution_id`, `filiere_id`)
SELECT i.id, f.id FROM `institutions` i, `filieres` f 
WHERE i.name IN ('ENCG El Jadida', 'ENCG Tanger') AND f.nom = 'Finance';

INSERT IGNORE INTO `institution_filieres` (`institution_id`, `filiere_id`)
SELECT i.id, f.id FROM `institutions` i, `filieres` f 
WHERE i.name IN ('ISMAC Rabat') AND f.nom = 'Cinéma';

INSERT IGNORE INTO `institution_filieres` (`institution_id`, `filiere_id`)
SELECT i.id, f.id FROM `institutions` i, `filieres` f 
WHERE i.name IN ('ISIC Rabat') AND f.nom = 'Audiovisuel';

INSERT IGNORE INTO `institution_filieres` (`institution_id`, `filiere_id`)
SELECT i.id, f.id FROM `institutions` i, `filieres` f 
WHERE i.name IN ('ISITT Tanger') AND f.nom = 'Gestion Hôtelière';

INSERT IGNORE INTO `institution_filieres` (`institution_id`, `filiere_id`)
SELECT i.id, f.id FROM `institutions` i, `filieres` f 
WHERE i.name IN ('OFPPT Casablanca', 'OFPPT Marrakech', 'OFPPT Agadir') AND f.id IN (100, 101);

SET FOREIGN_KEY_CHECKS = 1;
