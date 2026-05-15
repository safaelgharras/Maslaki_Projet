-- Synchronize Categories and Populate Filieres

-- 1. Update/Add Categories
UPDATE categories SET nom = 'Sciences', nom_ar = 'العلوم', nom_en = 'Sciences' WHERE id = 1;
UPDATE categories SET nom = 'Économie & Gestion', nom_ar = 'الاقتصاد والتسيير', nom_en = 'Economy & Management' WHERE id = 2;
UPDATE categories SET nom = 'Lettres & Langues', nom_ar = 'الآداب واللغات', nom_en = 'Letters & Languages' WHERE id = 3;
UPDATE categories SET nom = 'Sciences Humaines & Sociales', nom_ar = 'العلوم الإنسانية والاجتماعية', nom_en = 'Human & Social Sciences' WHERE id = 4;
UPDATE categories SET nom = 'Informatique', nom_ar = 'المعلوميات', nom_en = 'Computer Science' WHERE id = 5;
UPDATE categories SET nom = 'Santé', nom_ar = 'الصحة', nom_en = 'Health' WHERE id = 6;
UPDATE categories SET nom = 'Droit & Sciences Politiques', nom_ar = 'القانون والعلوم السياسية', nom_en = 'Law & Political Science' WHERE id = 7;
UPDATE categories SET nom = 'Arts & Design', nom_ar = 'الفنون والتصميم', nom_en = 'Arts & Design' WHERE id = 8;
UPDATE categories SET nom = 'Technologie & Ingénierie', nom_ar = 'التكنولوجيا والهندسة', nom_en = 'Technology & Engineering' WHERE id = 9;

INSERT IGNORE INTO categories (id, nom, nom_ar, nom_en) VALUES (10, 'Tourisme & Hôtellerie', 'السياحة والفندقة', 'Tourism & Hospitality');

-- Clear existing filieres to repopulate according to the new structure
-- (Optional: only if you want a clean start. I'll use INSERT IGNORE and update)

-- 1. Sciences
INSERT IGNORE INTO filieres (categorie_id, nom, nom_ar, nom_en) VALUES
(1, 'Sciences Mathématiques A', 'العلوم الرياضية أ', 'Mathematical Sciences A'),
(1, 'Sciences Mathématiques B', 'العلوم الرياضية ب', 'Mathematical Sciences B'),
(1, 'Sciences Physiques', 'العلوم الفيزيائية', 'Physical Sciences'),
(1, 'Sciences de la Vie et de la Terre (SVT)', 'علوم الحياة والأرض', 'Life and Earth Sciences'),
(1, 'Sciences Agronomiques', 'العلوم الزراعية', 'Agronomic Sciences');

-- 2. Technologie & Ingénierie
INSERT IGNORE INTO filieres (categorie_id, nom, nom_ar, nom_en) VALUES
(9, 'Génie Informatique', 'الهندسة المعلوماتية', 'Computer Engineering'),
(9, 'Génie Civil', 'الهندسة المدنية', 'Civil Engineering'),
(9, 'Génie Industriel', 'الهندسة الصناعية', 'Industrial Engineering'),
(9, 'Génie Électrique', 'الهندسة الكهربائية', 'Electrical Engineering'),
(9, 'Génie Mécanique', 'الهندسة الميكانيكية', 'Mechanical Engineering'),
(9, 'Réseaux & Télécommunications', 'الشبكات والاتصالات', 'Networks & Telecommunications'),
(9, 'Intelligence Artificielle', 'الذكاء الاصطناعي', 'Artificial Intelligence'),
(9, 'Cybersécurité', 'الأمن السيبراني', 'Cybersecurity'),
(9, 'Big Data', 'البيانات الضخمة', 'Big Data');

-- 3. Économie & Gestion
INSERT IGNORE INTO filieres (categorie_id, nom, nom_ar, nom_en) VALUES
(2, 'Économie', 'الاقتصاد', 'Economics'),
(2, 'Gestion', 'التسيير', 'Management'),
(2, 'Comptabilité', 'المحاسبة', 'Accounting'),
(2, 'Finance', 'المالية', 'Finance'),
(2, 'Marketing', 'التسويق', 'Marketing'),
(2, 'Commerce International', 'التجارة الدولية', 'International Trade'),
(2, 'Management', 'الإدارة', 'Management'),
(2, 'Audit & Contrôle de Gestion', 'التدقيق ومراقبة التسيير', 'Audit & Management Control'),
(2, 'Logistique', 'اللوجستيك', 'Logistics');

-- 4. Droit & Sciences Politiques
INSERT IGNORE INTO filieres (categorie_id, nom, nom_ar, nom_en) VALUES
(7, 'Droit Français', 'القانون الفرنسي', 'French Law'),
(7, 'Droit Arabe', 'القانون العربي', 'Arabic Law'),
(7, 'Sciences Politiques', 'العلوم السياسية', 'Political Science'),
(7, 'Relations Internationales', 'العلاقات الدولية', 'International Relations');

-- 5. Sciences Humaines & Sociales
INSERT IGNORE INTO filieres (categorie_id, nom, nom_ar, nom_en) VALUES
(4, 'Sociologie', 'علم الاجتماع', 'Sociology'),
(4, 'Psychologie', 'علم النفس', 'Psychology'),
(4, 'Philosophie', 'الفلسفة', 'Philosophy'),
(4, 'Géographie', 'الجغرافيا', 'Geography'),
(4, 'Histoire', 'التاريخ', 'History'),
(4, 'Études Islamiques', 'الدراسات الإسلامية', 'Islamic Studies'),
(4, 'Travail Social', 'العمل الاجتماعي', 'Social Work');

-- 6. Lettres & Langues
INSERT IGNORE INTO filieres (categorie_id, nom, nom_ar, nom_en) VALUES
(3, 'Études Françaises', 'الدراسات الفرنسية', 'French Studies'),
(3, 'Études Anglaises', 'الدراسات الإنجليزية', 'English Studies'),
(3, 'Études Hispaniques', 'الدراسات الإسبانية', 'Hispanic Studies'),
(3, 'Littérature Arabe', 'الأدب العربي', 'Arabic Literature'),
(3, 'Traduction', 'الترجمة', 'Translation'),
(3, 'Linguistique', 'اللسانيات', 'Linguistics');

-- 7. Santé
INSERT IGNORE INTO filieres (categorie_id, nom, nom_ar, nom_en) VALUES
(6, 'Médecine', 'الطب', 'Medicine'),
(6, 'Pharmacie', 'الصيدلة', 'Pharmacy'),
(6, 'Médecine Dentaire', 'طب الأسنان', 'Dentistry'),
(6, 'Infirmier', 'التمريض', 'Nursing'),
(6, 'Kinésithérapie', 'الترويض الطبي', 'Physiotherapy'),
(6, 'Biologie Médicale', 'البيولوجيا الطبية', 'Medical Biology');

-- 8. Informatique
INSERT IGNORE INTO filieres (categorie_id, nom, nom_ar, nom_en) VALUES
(5, 'Développement Web', 'تطوير الويب', 'Web Development'),
(5, 'Développement Mobile', 'تطوير المحمول', 'Mobile Development'),
(5, 'Data Science', 'علم البيانات', 'Data Science'),
(5, 'Systèmes Informatiques', 'الأنظمة المعلوماتية', 'Information Systems'),
(5, 'Génie Logiciel', 'هندسة البرمجيات', 'Software Engineering');

-- 9. Arts & Design
INSERT IGNORE INTO filieres (categorie_id, nom, nom_ar, nom_en) VALUES
(8, 'Design Graphique', 'التصميم الغرافيكي', 'Graphic Design'),
(8, 'Architecture', 'الهندسة المعمارية', 'Architecture'),
(8, 'Cinéma', 'السينما', 'Cinema'),
(8, 'Audiovisuel', 'السمعي البصري', 'Audiovisual'),
(8, 'Mode', 'الموضة', 'Fashion');

-- 10. Tourisme & Hôtellerie
INSERT IGNORE INTO filieres (categorie_id, nom, nom_ar, nom_en) VALUES
(10, 'Gestion Hôtelière', 'التسيير الفندقي', 'Hotel Management'),
(10, 'Tourisme', 'السياحة', 'Tourism'),
(10, 'Restauration', 'المطاعم', 'Catering');
