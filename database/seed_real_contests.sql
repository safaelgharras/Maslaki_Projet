-- Populate Contests with Realistic Moroccan Data for 2026

-- Clear existing sample contests to avoid duplicates
TRUNCATE TABLE contests;

INSERT INTO contests (institution_id, title, title_ar, title_en, description, description_ar, description_en, exam_date, registration_deadline, status, is_featured) VALUES
-- ENSA (National School of Applied Sciences)
(1, 'Concours Commun ENSA Maroc 2026', 'المباراة المشتركة لولوج المدارس الوطنية للعلوم التطبيقية 2026', 'ENSA Morocco Common Contest 2026', 
 'Concours d\'accès en 1ère année du cycle préparatoire des ENSA Maroc.', 
 'مباراة الولوج للسنة الأولى من السلك التحضيري للمدارس الوطنية للعلوم التطبيقية بالمغرب.', 
 'Entrance contest for the 1st year of the preparatory cycle of ENSA Morocco.',
 '2026-07-20', '2026-07-10', 'soon', 1),

-- ENCG (TAFEM)
(2, 'Concours TAFEM - ENCG 2026', 'اختبار القبول للتكوين في التدبير - المدارس الوطنية للتجارة والتسيير 2026', 'TAFEM Contest - ENCG 2026', 
 'Test d\'Aptitude à la Formation en Management pour l\'accès aux ENCG.', 
 'اختبار القدرات للتكوين في التسيير لولوج المدارس الوطنية للتجارة والتسيير.', 
 'Aptitude test for management training for access to ENCG.',
 '2026-07-22', '2026-07-12', 'soon', 1),

-- EMI (Mohammadia School of Engineers)
(10, 'Concours National Commun (CNC) 2026', 'المباراة الوطنية المشتركة 2026', 'National Common Contest (CNC) 2026', 
 'Concours national pour l\'accès aux grandes écoles d\'ingénieurs marocaines (CPGE).', 
 'المباراة الوطنية لولوج مؤسسات تكوين المهندسين الكبرى والمؤسسات التي في حكمها.', 
 'National contest for access to major Moroccan engineering schools.',
 '2026-05-15', '2026-04-30', 'open', 1),

-- ISCAE
(5, 'Concours d\'accès à l\'ISCAE 2026', 'مباراة ولوج المعهد العالي للتجارة وإدارة المقاولات 2026', 'ISCAE Entrance Contest 2026', 
 'Concours d\'accès en 1ère année de Licence à l\'ISCAE Casablanca.', 
 'مباراة الولوج للسنة الأولى إجازة بالمعهد العالي للتجارة وإدارة المقاولات بالدار البيضاء.', 
 'Entrance contest for the 1st year of Bachelor at ISCAE Casablanca.',
 '2026-06-10', '2026-05-25', 'soon', 1),

-- FST (Faculty of Science and Technology)
(3, 'Sélection FST Settat 2026', 'انتقاء كلية العلوم والتقنيات بسطات 2026', 'FST Settat Selection 2026', 
 'Sélection basée sur la moyenne du baccalauréat pour l\'accès aux FST.', 
 'انتقاء بناءً على معدل البكالوريا لولوج كليات العلوم والتقنيات.', 
 'Selection based on baccalaureate average for access to FST.',
 '2026-08-05', '2026-07-31', 'soon', 0),

-- EST (School of Technology)
(4, 'Inscription EST Casablanca 2026', 'تسجيل المدرسة العليا للتكنولوجيا بالدار البيضاء 2026', 'EST Casablanca Registration 2026', 
 'Candidature pour le Diplôme Universitaire de Technologie (DUT).', 
 'الترشيح للدبلوم الجامعي للتكنولوجيا.', 
 'Application for the University Technology Diploma (DUT).',
 '2026-08-10', '2026-07-31', 'soon', 0);
