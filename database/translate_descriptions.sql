-- Translate Common Institution Descriptions

-- Engineering Schools
UPDATE institutions SET 
    description_ar = 'مدرسة عمومية للمهندسين', 
    description_en = 'Public engineering school' 
WHERE description LIKE '%école d\'ingénieur publique%' OR description = 'école ingénieur';

-- Business Schools
UPDATE institutions SET 
    description_ar = 'مدرسة عمومية للتجارة والتسيير', 
    description_en = 'Public business and management school' 
WHERE description LIKE '%école de commerce publique%' OR description = 'école commerce' OR description = 'école nationale de commerce';

-- FST / Technical Faculties
UPDATE institutions SET 
    description_ar = 'كلية العلوم والتقنيات', 
    description_en = 'Faculty of Science and Technology' 
WHERE description LIKE '%faculté des sciences et techniques%' OR description = 'faculté technique';

-- EST / Technology Schools
UPDATE institutions SET 
    description_ar = 'المدرسة العليا للتكنولوجيا', 
    description_en = 'Higher School of Technology' 
WHERE description LIKE '%école supérieure de technologie%' OR description = 'école technologie';

-- ISCAE / Management
UPDATE institutions SET 
    description_ar = 'المعهد العالي للتجارة وإدارة المقاولات', 
    description_en = 'Higher Institute of Commerce and Business Administration' 
WHERE description = 'institut supérieur de commerce';

-- CPGE
UPDATE institutions SET 
    description_ar = 'الأقسام التحضيرية للمدارس العليا', 
    description_en = 'Preparatory Classes for Higher Schools' 
WHERE description LIKE '%classes préparatoires%' OR description LIKE '%prépa%';

-- University
UPDATE institutions SET 
    description_ar = 'جامعة عمومية', 
    description_en = 'Public university' 
WHERE description = 'université publique';

-- Private Schools
UPDATE institutions SET 
    description_ar = 'جامعة خاصة', 
    description_en = 'Private university' 
WHERE description = 'université privée';

UPDATE institutions SET 
    description_ar = 'مدرسة خاصة للمهندسين', 
    description_en = 'Private engineering school' 
WHERE description = 'école ingénieur privée';

UPDATE institutions SET 
    description_ar = 'مدرسة خاصة للإدارة', 
    description_en = 'Private management school' 
WHERE description = 'école management' OR description = 'école commerce' AND type = 'Private';

-- Other Faculties
UPDATE institutions SET 
    description_ar = 'كلية العلوم', 
    description_en = 'Faculty of Science' 
WHERE description = 'faculté sciences' OR description = 'faculté scientifique';

-- Specific for EMI
UPDATE institutions SET 
    description_ar = 'المدرسة المحمدية للمهندسين', 
    description_en = 'Mohammadia School of Engineers' 
WHERE name LIKE '%EMI%';

-- Catch all: if description_ar is still null, use description
UPDATE institutions SET 
    description_ar = description, 
    description_en = description 
WHERE (description_ar IS NULL OR description_ar = '') AND (description_en IS NULL OR description_en = '');
