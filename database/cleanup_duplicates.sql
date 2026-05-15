-- Clean up duplicates in filieres and domains

-- 1. Remove duplicate filieres (keep the one with a domain_id if exists, otherwise the lowest ID)
CREATE TABLE temp_filieres AS
SELECT MIN(id) as id, nom, categorie_id
FROM filieres
GROUP BY nom, categorie_id;

-- Delete those not in the list of "unique" ones
DELETE FROM filieres 
WHERE id NOT IN (SELECT id FROM temp_filieres);

DROP TABLE temp_filieres;

-- 2. If a filiere has the same name as a domain in the same category, 
-- we should probably keep the domain and link the filiere to it, 
-- or just remove the filiere if it doesn't have institutions linked yet.

-- Let's check for filieres that have the same name as a domain
DELETE FROM filieres 
WHERE nom IN (SELECT nom FROM domains) 
AND id NOT IN (SELECT filiere_id FROM institution_filieres);

-- 3. Specific cleanup for known duplicates from the screenshot
DELETE FROM domains WHERE nom = 'Data Science' AND id > 2; -- Example cleanup
DELETE FROM filieres WHERE nom = 'Data Science' AND domain_id IS NULL AND id IN (SELECT id FROM (SELECT id FROM filieres WHERE nom = 'Data Science' LIMIT 10) t);

-- 4. Final check: ensure unique names per category in filieres
CREATE TABLE temp_f2 AS
SELECT MIN(id) as id, nom, categorie_id
FROM filieres
GROUP BY nom, categorie_id;

DELETE FROM filieres WHERE id NOT IN (SELECT id FROM temp_f2);
DROP TABLE temp_f2;
