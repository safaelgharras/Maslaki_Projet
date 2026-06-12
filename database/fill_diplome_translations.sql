-- ============================================================
-- Fill ALL missing diplome_ar and diplome_en translations
-- Run this after translate_institution_details.sql
-- ============================================================

-- Engineering diplomas
UPDATE institutions SET
    diplome_ar = 'دبلوم مهندس الدولة',
    diplome_en = 'State Engineering Degree'
WHERE (diplome LIKE '%Ingénieur d%État%' OR diplome LIKE '%Ingénieur%Etat%')
  AND (diplome_ar IS NULL OR diplome_ar = '');

UPDATE institutions SET
    diplome_ar = 'دبلوم مهندس',
    diplome_en = 'Engineering Degree'
WHERE (diplome LIKE '%Diplôme d%Ingénieur%' OR diplome LIKE '%Ingénieur d%État%')
  AND (diplome_ar IS NULL OR diplome_ar = '');

-- ENCG / Management diplomas
UPDATE institutions SET
    diplome_ar = 'دبلوم المدارس الوطنية للتجارة والتسيير',
    diplome_en = 'ENCG Diploma'
WHERE (diplome LIKE '%ENCG%' OR diplome LIKE '%Master en Management%')
  AND (diplome_ar IS NULL OR diplome_ar = '');

-- Technology diplomas
UPDATE institutions SET
    diplome_ar = 'الدبلوم الجامعي للتكنولوجيا',
    diplome_en = 'University Technology Diploma'
WHERE diplome LIKE '%DUT%'
  AND (diplome_ar IS NULL OR diplome_ar = '');

-- Licence / Bachelor / Master
UPDATE institutions SET
    diplome_ar = 'إجازة / ماستر',
    diplome_en = 'Bachelor / Master'
WHERE (diplome LIKE '%Licence%' OR diplome LIKE '%Bachelor%')
  AND (diplome_ar IS NULL OR diplome_ar = '');

-- Certificat de formation
UPDATE institutions SET
    diplome_ar = 'شهادة التكوين',
    diplome_en = 'Training Certificate'
WHERE diplome LIKE '%Certificat de formation%'
  AND (diplome_ar IS NULL OR diplome_ar = '');

-- Technicien / Technicien
UPDATE institutions SET
    diplome_ar = 'تقني / تقني متخصص',
    diplome_en = 'Technician / Specialized Technician'
WHERE diplome LIKE '%Technicien%'
  AND (diplome_ar IS NULL OR diplome_ar = '');

-- CPGE / Prépa
UPDATE institutions SET
    diplome_ar = 'شهادة الأقسام التحضيرية',
    diplome_en = 'Preparatory Classes Certificate'
WHERE (diplome LIKE '%CPGE%' OR diplome LIKE '%Attestation%Prépa%' OR diplome LIKE '%Classes Préparatoires%')
  AND (diplome_ar IS NULL OR diplome_ar = '');

-- Doctorat
UPDATE institutions SET
    diplome_ar = 'دكتوراه',
    diplome_en = 'Doctorate'
WHERE diplome LIKE '%Doctorat%'
  AND (diplome_ar IS NULL OR diplome_ar = '');

-- Master spécialisé
UPDATE institutions SET
    diplome_ar = 'ماستر متخصص',
    diplome_en = 'Specialized Master'
WHERE diplome LIKE '%Master%Spécialisé%' OR diplome LIKE '%Master spécialisé%'
  AND (diplome_ar IS NULL OR diplome_ar = '');

-- DEUG / DEUST
UPDATE institutions SET
    diplome_ar = 'دبلوم الدراسات الجامعية العامة',
    diplome_en = 'General University Studies Diploma'
WHERE (diplome LIKE '%DEUG%' OR diplome LIKE '%DEUST%')
  AND (diplome_ar IS NULL OR diplome_ar = '');

-- OFPPT / Formation Professionnelle
UPDATE institutions SET
    diplome_ar = 'دبلوم التكوين المهني',
    diplome_en = 'Vocational Training Diploma'
WHERE (diplome LIKE '%OFPPT%' OR diplome LIKE '%Formation Professionnelle%')
  AND (diplome_ar IS NULL OR diplome_ar = '');

-- Attestation
UPDATE institutions SET
    diplome_ar = 'شهادة',
    diplome_en = 'Certificate'
WHERE diplome LIKE '%Attestation%'
  AND (diplome_ar IS NULL OR diplome_ar = '');

-- Diplôme (generic catch-all for remaining "Diplôme" entries)
UPDATE institutions SET
    diplome_ar = 'دبلوم',
    diplome_en = 'Diploma'
WHERE diplome LIKE '%Diplôme%'
  AND (diplome_ar IS NULL OR diplome_ar = '');

-- Also translate duree_etudes if still missing
UPDATE institutions SET
    duree_etudes_ar = '5 سنوات',
    duree_etudes_en = '5 years'
WHERE duree_etudes LIKE '5 ans%'
  AND (duree_etudes_ar IS NULL OR duree_etudes_ar = '');

UPDATE institutions SET
    duree_etudes_ar = 'سنتان',
    duree_etudes_en = '2 years'
WHERE duree_etudes LIKE '2 ans%'
  AND (duree_etudes_ar IS NULL OR duree_etudes_ar = '');

UPDATE institutions SET
    duree_etudes_ar = '3 سنوات',
    duree_etudes_en = '3 years'
WHERE duree_etudes LIKE '3 ans%'
  AND (duree_etudes_ar IS NULL OR duree_etudes_ar = '');

UPDATE institutions SET
    duree_etudes_ar = '4 سنوات',
    duree_etudes_en = '4 years'
WHERE duree_etudes LIKE '4 ans%'
  AND (duree_etudes_ar IS NULL OR duree_etudes_ar = '');

UPDATE institutions SET
    duree_etudes_ar = 'سنة واحدة',
    duree_etudes_en = '1 year'
WHERE duree_etudes LIKE '1 an%'
  AND (duree_etudes_ar IS NULL OR duree_etudes_ar = '');

UPDATE institutions SET
    duree_etudes_ar = '3-5 سنوات',
    duree_etudes_en = '3-5 years'
WHERE duree_etudes LIKE '3-5 ans%'
  AND (duree_etudes_ar IS NULL OR duree_etudes_ar = '');
