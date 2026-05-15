-- 1. Add missing translation columns for study duration
ALTER TABLE institutions ADD COLUMN IF NOT EXISTS duree_etudes_ar VARCHAR(50) AFTER duree_etudes;
ALTER TABLE institutions ADD COLUMN IF NOT EXISTS duree_etudes_en VARCHAR(50) AFTER duree_etudes_ar;

-- 2. Populate common translations for study duration
UPDATE institutions SET 
    duree_etudes_ar = '5 سنوات', 
    duree_etudes_en = '5 years' 
WHERE duree_etudes LIKE '5 ans%';

UPDATE institutions SET 
    duree_etudes_ar = 'سنتان', 
    duree_etudes_en = '2 years' 
WHERE duree_etudes LIKE '2 ans%';

UPDATE institutions SET 
    duree_etudes_ar = '3 سنوات', 
    duree_etudes_en = '3 years' 
WHERE duree_etudes LIKE '3 ans%';

-- 3. Populate translations for Diplomas
UPDATE institutions SET 
    diplome_ar = 'دبلوم مهندس', 
    diplome_en = 'Engineering Degree' 
WHERE diplome = 'Diplôme d\'Ingénieur' OR diplome = 'Ingénieur d\'État';

UPDATE institutions SET 
    diplome_ar = 'دبلوم المدارس الوطنية للتجارة والتسيير', 
    diplome_en = 'ENCG Diploma' 
WHERE diplome = 'Diplôme ENCG' OR diplome = 'Master en Management';

UPDATE institutions SET 
    diplome_ar = 'الدبلوم الجامعي للتكنولوجيا', 
    diplome_en = 'University Technology Diploma' 
WHERE diplome = 'DUT / Licence Pro';

UPDATE institutions SET 
    diplome_ar = 'إجازة / ماستر', 
    diplome_en = 'Bachelor / Master' 
WHERE diplome = 'Licence / Master' OR diplome = 'Bachelor / Master';

-- 4. Translate Requirements (Example for ENSA)
UPDATE institutions SET 
    requirements_ar = 'باكالوريا علوم رياضية / فيزيائية + مباراة', 
    requirements_en = 'Bac Sciences Math / Physics + contest' 
WHERE (name LIKE '%ENSA%' OR name LIKE '%EMI%') AND (requirements LIKE '%Bac Sciences Math%');

UPDATE institutions SET 
    requirements_ar = 'باكالوريا اقتصاد / رياضيات + انتقاء', 
    requirements_en = 'Bac Eco / Math + selection' 
WHERE (name LIKE '%ENCG%') AND (requirements LIKE '%Bac Eco / Math%');
