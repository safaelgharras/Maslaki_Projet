-- 1. Add missing prestigious institutions
INSERT IGNORE INTO institutions (name, name_ar, name_en, type, city, city_ar, city_en, description, description_ar, description_en, image) VALUES
('UM6P', 'جامعة محمد السادس متعددة التخصصات التقنية', 'Mohammed VI Polytechnic University', 'Private', 'Benguerir', 'بن جرير', 'Benguerir', 'Université de recherche de classe mondiale.', 'جامعة بحثية ذات مستوى عالمي.', 'World-class research university.', 'um6p.jpg'),
('INPT', 'المعهد الوطني للبريد والمواصلات', 'National Institute of Posts and Telecommunications', 'Public', 'Rabat', 'الرباط', 'Rabat', 'Grande école d\'ingénieurs en TIC.', 'مدرسة كبرى للمهندسين في تكنولوجيا المعلومات والاتصالات.', 'Elite engineering school in ICT.', 'inpt.jpg'),
('ENSEM', 'المدرسة الوطنية العليا للكهرباء والميكانيك', 'National Higher School of Electricity and Mechanics', 'Public', 'Casablanca', 'الدار البيضاء', 'Casablanca', 'École d\'ingénieurs pluridisciplinaire.', 'مدرسة للمهندسين متعددة التخصصات.', 'Multidisciplinary engineering school.', 'ensem.jpg'),
('EHTP', 'المدرسة الحسنية للأشغال العمومية', 'Hassania School of Public Works', 'Public', 'Casablanca', 'الدار البيضاء', 'Casablanca', 'Référence en génie civil et infrastructures.', 'مرجع في الهندسة المدنية والبنية التحتية.', 'Reference in civil engineering and infrastructure.', 'ehtp.jpg'),
('ENSAM', 'المدرسة الوطنية العليا للفنون والمهن', 'National Higher School of Arts and Crafts', 'Public', 'Meknes', 'مكناس', 'Meknes', 'Formation d\'ingénieurs Arts & Métiers.', 'تكوين مهندسي الفنون والمهن.', 'Arts & Crafts engineering training.', 'ensam.jpg'),
('YouCode', 'يوكود', 'YouCode', 'Public', 'Youssoufia', 'اليوسفية', 'Youssoufia', 'École de programmation inclusive.', 'مدرسة البرمجة الشاملة.', 'Inclusive programming school.', 'youcode.jpg'),
('1337', '1337', '1337', 'Public', 'Khouribga', 'خريبكة', 'Khouribga', 'Le futur de l\'éducation informatique.', 'مستقبل التعليم المعلوماتي.', 'The future of IT education.', '1337.jpg'),
('OFPPT', 'مكتب التكوين المهني وإنعاش الشغل', 'OFPPT', 'Public', 'National', 'وطني', 'National', 'Formation professionnelle et technique.', 'التكوين المهني وإنعاش الشغل.', 'Vocational and technical training.', 'ofppt.jpg'),
('FSJES', 'كلية العلوم القانونية والاقتصادية والاجتماعية', 'Faculty of Legal, Economic and Social Sciences', 'Public', 'Casablanca', 'الدار البيضاء', 'Casablanca', 'Études juridiques et économiques.', 'الدراسات القانونية والاقتصادية.', 'Legal and economic studies.', 'fsjes.jpg'),
('FLSH', 'كلية الآداب والعلوم الإنسانية', 'Faculty of Letters and Human Sciences', 'Public', 'Rabat', 'الرباط', 'Rabat', 'Études littéraires et humaines.', 'الدراسات الأدبية والإنسانية.', 'Literary and human studies.', 'flsh.jpg'),
('ENA Rabat', 'المدرسة الوطنية للهندسة المعمارية', 'National School of Architecture', 'Public', 'Rabat', 'الرباط', 'Rabat', 'Formation d\'architectes d\'excellence.', 'تكوين المهندسين المعماريين المتميزين.', 'Excellence in architectural training.', 'ena.jpg'),
('EAC Casablanca', 'المدرسة العليا للهندسة المعمارية والدار البيضاء', 'School of Architecture Casablanca', 'Private', 'Casablanca', 'الدار البيضاء', 'Casablanca', 'Architecture et urbanisme.', 'الهندسة المعمارية والتعمير.', 'Architecture and urbanism.', 'eac.jpg'),
('Art\'Com Sup', 'أرت كوم سوب', 'Art\'Com Sup', 'Private', 'Casablanca', 'الدار البيضاء', 'Casablanca', 'Design graphique et digital.', 'التصميم الغرافيكي والرقمي.', 'Graphic and digital design.', 'artcom.jpg'),
('UM6SS', 'جامعة محمد السادس لعلوم الصحة', 'Mohammed VI University of Health Sciences', 'Private', 'Casablanca', 'الدار البيضاء', 'Casablanca', 'Pôle d\'excellence en santé.', 'قطب التميز في الصحة.', 'Health excellence hub.', 'um6ss.jpg'),
('FMP', 'كلية الطب والصيدلة', 'Faculty of Medicine and Pharmacy', 'Public', 'Rabat', 'الرباط', 'Rabat', 'Formation médicale de référence.', 'تكوين طبي مرجعي.', 'Reference medical training.', 'fmp.jpg');

-- 2. Establish relationships (institution_filieres)
-- Helper to get IDs (I'll use specific IDs if I know them, otherwise subqueries)

-- Genius Informatique (ID 1 and 14)
INSERT IGNORE INTO institution_filieres (institution_id, filiere_id)
SELECT i.id, f.id FROM institutions i, filieres f 
WHERE i.name IN ('ENSA Casablanca', 'EMI Rabat', 'EMSI Casablanca', 'INPT') 
AND f.nom = 'Génie Informatique';

-- Intelligence Artificielle (ID 20)
INSERT IGNORE INTO institution_filieres (institution_id, filiere_id)
SELECT i.id, f.id FROM institutions i, filieres f 
WHERE i.name IN ('UM6P', 'ENSA Casablanca', 'EMSI Casablanca', 'INPT') 
AND f.nom = 'Intelligence Artificielle';

-- Réseaux & Télécommunications (ID 19)
INSERT IGNORE INTO institution_filieres (institution_id, filiere_id)
SELECT i.id, f.id FROM institutions i, filieres f 
WHERE i.name IN ('INPT', 'ENSA Casablanca', 'ENSEM', 'EMSI Casablanca') 
AND f.nom = 'Réseaux & Télécommunications';

-- Génie Civil (ID 15)
INSERT IGNORE INTO institution_filieres (institution_id, filiere_id)
SELECT i.id, f.id FROM institutions i, filieres f 
WHERE i.name IN ('EHTP', 'ENSA Casablanca', 'EMI Rabat', 'EMSI Casablanca') 
AND f.nom = 'Génie Civil';

-- Génie Industriel (ID 16)
INSERT IGNORE INTO institution_filieres (institution_id, filiere_id)
SELECT i.id, f.id FROM institutions i, filieres f 
WHERE i.name IN ('ENSAM', 'ENSA Casablanca', 'EMSI Casablanca', 'ENSEM') 
AND f.nom = 'Génie Industriel';

-- Développement Web (ID 55) - wait, check ID for Dev Web
INSERT IGNORE INTO institution_filieres (institution_id, filiere_id)
SELECT i.id, f.id FROM institutions i, filieres f 
WHERE i.name IN ('YouCode', '1337', 'OFPPT', 'EMSI Casablanca') 
AND f.nom = 'Développement Web';

-- Data Science (ID 7)
INSERT IGNORE INTO institution_filieres (institution_id, filiere_id)
SELECT i.id, f.id FROM institutions i, filieres f 
WHERE i.name IN ('UM6P', 'INPT', 'ENSA Casablanca', 'FST Settat') 
AND f.nom = 'Data Science';

-- Finance (ID 2)
INSERT IGNORE INTO institution_filieres (institution_id, filiere_id)
SELECT i.id, f.id FROM institutions i, filieres f 
WHERE i.name IN ('ENCG Casablanca', 'ISCAE Casablanca', 'HEM Casablanca', 'ESCA Ecole de Management') 
AND f.nom = 'Finance';

-- Marketing (ID 3)
INSERT IGNORE INTO institution_filieres (institution_id, filiere_id)
SELECT i.id, f.id FROM institutions i, filieres f 
WHERE i.name IN ('ENCG Casablanca', 'ESCA Ecole de Management', 'HEM Casablanca', 'ISCAE Casablanca') 
AND f.nom = 'Marketing';

-- Droit Français (ID 4)
INSERT IGNORE INTO institution_filieres (institution_id, filiere_id)
SELECT i.id, f.id FROM institutions i, filieres f 
WHERE i.name IN ('FSJES', 'UIR Rabat', 'Université Hassan II') 
AND f.nom = 'Droit Français';

-- Psychologie (ID 37)
INSERT IGNORE INTO institution_filieres (institution_id, filiere_id)
SELECT i.id, f.id FROM institutions i, filieres f 
WHERE i.name IN ('FLSH', 'Université Mohammed V', 'Université Hassan II') 
AND f.nom = 'Psychologie';

-- Médecine (ID 5)
INSERT IGNORE INTO institution_filieres (institution_id, filiere_id)
SELECT i.id, f.id FROM institutions i, filieres f 
WHERE i.name IN ('FMP', 'UM6SS', 'UIR Rabat') 
AND f.nom = 'Médecine';

-- Architecture (ID 6)
INSERT IGNORE INTO institution_filieres (institution_id, filiere_id)
SELECT i.id, f.id FROM institutions i, filieres f 
WHERE i.name IN ('ENA Rabat', 'EAC Casablanca', 'Art\'Com Sup') 
AND f.nom = 'Architecture';
