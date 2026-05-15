-- Translate Filiere Descriptions and Names

-- 1. Génie Informatique
UPDATE filieres SET 
    nom_ar = 'الهندسة المعلوماتية', 
    nom_en = 'Computer Engineering',
    description_ar = 'تصميم وتطوير أنظمة البرمجيات', 
    description_en = 'Design and development of software systems' 
WHERE nom = 'Génie Informatique';

-- 2. Finance
UPDATE filieres SET 
    nom_ar = 'المالية', 
    nom_en = 'Finance',
    description_ar = 'التدبير المالي وأسواق الرساميل', 
    description_en = 'Financial management and capital markets' 
WHERE nom = 'Finance';

-- 3. Marketing
UPDATE filieres SET 
    nom_ar = 'التسويق', 
    nom_en = 'Marketing',
    description_ar = 'الاستراتيجيات التجارية والتواصل', 
    description_en = 'Commercial strategies and communication' 
WHERE nom = 'Marketing';

-- 4. Droit Français
UPDATE filieres SET 
    nom_ar = 'القانون الفرنسي', 
    nom_en = 'French Law',
    description_ar = 'دراسة النظام القانوني الفرانكفوني', 
    description_en = 'Study of the French-speaking legal system' 
WHERE nom = 'Droit Français';

-- 5. Médecine
UPDATE filieres SET 
    nom_ar = 'الطب', 
    nom_en = 'Medicine',
    description_ar = 'الدراسات الطبية العامة', 
    description_en = 'General medical studies' 
WHERE nom = 'Médecine';

-- 6. Architecture
UPDATE filieres SET 
    nom_ar = 'الهندسة المعمارية', 
    nom_en = 'Architecture',
    description_ar = 'التصميم المعماري والتعمير', 
    description_en = 'Architectural design and urban planning' 
WHERE nom = 'Architecture';

-- 7. Data Science
UPDATE filieres SET 
    nom_ar = 'علم البيانات', 
    nom_en = 'Data Science',
    description_ar = 'تحليل البيانات والذكاء الاصطناعي', 
    description_en = 'Data analysis and artificial intelligence' 
WHERE nom = 'Data Science';

-- 8. Gestion des Entreprises
UPDATE filieres SET 
    nom_ar = 'تسيير المقاولات', 
    nom_en = 'Business Management',
    description_ar = 'التدبير والإدارة', 
    description_en = 'Management and administration' 
WHERE nom = 'Gestion des Entreprises';

-- 9. General catch-all for descriptions if still empty (optional but good practice)
UPDATE filieres SET 
    description_ar = description, 
    description_en = description 
WHERE (description_ar IS NULL OR description_ar = '') AND (description_en IS NULL OR description_en = '');
