<?php
require __DIR__ . '/../config/DataBase.php';

try {
    // Check if Solicode already exists
    $stmt = $pdo->prepare("SELECT id FROM institutions WHERE name = ? OR name_en = ?");
    $stmt->execute(['Solicode – Centre Solidaire Digital', 'SoliCode Tanger']);
    $existing = $stmt->fetch();
    
    if ($existing) {
        $id = $existing['id'];
        echo "Solicode already exists with ID: $id. Updating missing info...\n";
        
        $sql = "UPDATE institutions SET 
            name = ?, name_ar = ?, name_en = ?,
            city = ?, city_ar = ?, city_en = ?, ville_id = ?,
            type = ?, sector_type = ?,
            min_average = NULL, seuil = 0,
            description = ?, description_ar = ?, description_en = ?,
            requirements = ?, requirements_ar = ?, requirements_en = ?,
            diplome = ?, diplome_ar = ?, diplome_en = ?,
            image = ?, site_web = ?,
            duree_etudes = ?, duree_etudes_ar = ?, duree_etudes_en = ?,
            is_popular = 1
            WHERE id = ?";
        
        $updateStmt = $pdo->prepare($sql);
        $updateStmt->execute([
            'Solicode – Centre Solidaire Digital', 'سوليكود طنجة', 'SoliCode Tanger',
            'Tanger', 'طنجة', 'Tangier', 5,
            'Technical', 'alternative',
            'Centre de formation digitale solidaire à Tanger, proposant des formations intensives et gratuites en développement web et mobile, data, et design d\'interface pour faciliter l\'insertion professionnelle des jeunes.',
            'مركز تضامني للتكوين الرقمي بطنجة، يقدم تكوينات مكثفة ومجانية في تطوير الويب والهاتف، البيانات وتصميم الواجهات لتسهيل الإدماج المهني للشباب.',
            'Solidarity digital training center in Tangier, offering free intensive training in web and mobile development, data, and interface design to facilitate the professional integration of young people.',
            'Avoir entre 18 et 35 ans, réussir le test de sélection en ligne (test de logique et de motivation) et l\'entretien individuel. Aucun diplôme préalable requis.',
            'أن يتراوح عمرك بين 18 و 35 سنة، واجتياز اختبار الانتقاء عبر الإنترنت (اختبار المنطق والدافعية) والمقابلة الفردية. لا يشترط شهادة مسبقة.',
            'Be between 18 and 35 years old, pass the online selection test (logic and motivation test) and the individual interview. No prior degree required.',
            'Certificat de formation professionnelle (OFPPT / Fondation Mohamed V)', 'شهادة تكوين مهني (OFPPT / مؤسسة محمد الخامس)', 'Vocational training certificate (OFPPT / Mohamed V Foundation)',
            'Solicode-Tanger.png', 'https://solicode.co/',
            '2 ans', 'سنتين', '2 years',
            $id
        ]);
        echo "Updated successfully!\n";
    } else {
        echo "Inserting new Solicode record...\n";
        
        $sql = "INSERT INTO institutions (
            name, name_ar, name_en,
            city, city_ar, city_en, ville_id,
            type, sector_type,
            min_average, seuil,
            description, description_ar, description_en,
            requirements, requirements_ar, requirements_en,
            diplome, diplome_ar, diplome_en,
            image, site_web,
            duree_etudes, duree_etudes_ar, duree_etudes_en,
            is_popular
        ) VALUES (
            ?, ?, ?,
            ?, ?, ?, ?,
            ?, ?,
            NULL, 0,
            ?, ?, ?,
            ?, ?, ?,
            ?, ?, ?,
            ?, ?,
            ?, ?, ?,
            1
        )";
        
        $insertStmt = $pdo->prepare($sql);
        $insertStmt->execute([
            'Solicode – Centre Solidaire Digital', 'سوليكود طنجة', 'SoliCode Tanger',
            'Tanger', 'طنجة', 'Tangier', 5,
            'Digital', 'alternative',
            'Centre de formation digitale solidaire à Tanger, proposant des formations intensives et gratuites en développement web et mobile, data, et design d\'interface pour faciliter l\'insertion professionnelle des jeunes.',
            'مركز تضامني للتكوين الرقمي بطنجة، يقدم تكوينات مكثفة ومجانية في تطوير الويب والهاتف، البيانات وتصميم الواجهات لتسهيل الإدماج المهني للشباب.',
            'Solidarity digital training center in Tangier, offering free intensive training in web and mobile development, data, and interface design to facilitate the professional integration of young people.',
            'Avoir entre 18 et 35 ans, réussir le test de sélection en ligne (test de logique et de motivation) et l\'entretien individuel. Aucun diplôme préalable requis.',
            'أن يتراوح عمرك بين 18 و 35 سنة، واجتياز اختبار الانتقاء عبر الإنترنت (اختبار المنطق والدافعية) والمقابلة الفردية. لا يشترط شهادة مسبقة.',
            'Be between 18 and 35 years old, pass the online selection test (logic and motivation test) and the individual interview. No prior degree required.',
            'Certificat de formation professionnelle (OFPPT / Fondation Mohamed V)', 'شهادة تكوين مهني (OFPPT / مؤسسة محمد الخامس)', 'Vocational training certificate (OFPPT / Mohamed V Foundation)',
            'Solicode-Tanger.png', 'https://solicode.co/',
            '2 ans', 'سنتين', '2 years'
        ]);
        $id = $pdo->lastInsertId();
        echo "Inserted Solicode with ID: $id\n";
    }
    
    // Now handle institution_domain mapping
    // Domains: 1 (Sciences Exactes & Technologies), 10 (Formation Professionnelle & Métiers)
    $domains = [1, 10];
    foreach ($domains as $dId) {
        $check = $pdo->prepare("SELECT 1 FROM institution_domain WHERE institution_id = ? AND domain_id = ?");
        $check->execute([$id, $dId]);
        if (!$check->fetch()) {
            $insDom = $pdo->prepare("INSERT INTO institution_domain (institution_id, domain_id) VALUES (?, ?)");
            $insDom->execute([$id, $dId]);
            echo "Linked Solicode to domain ID $dId\n";
        }
    }
    
    // Check if 'Développement Web' filiere exists, if not insert it
    $webFiliereStmt = $pdo->prepare("SELECT id FROM filieres WHERE nom = ? OR nom_en = ?");
    $webFiliereStmt->execute(['Développement Web', 'Web Development']);
    $webFiliere = $webFiliereStmt->fetch();
    if ($webFiliere) {
        $webFiliereId = $webFiliere['id'];
        echo "Found existing Web Development filiere with ID: $webFiliereId\n";
    } else {
        echo "Inserting new 'Développement Web' filiere...\n";
        $insWebFil = $pdo->prepare("INSERT INTO filieres (nom, nom_ar, nom_en, domain_id, categorie_id, description, description_ar, description_en) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $insWebFil->execute([
            'Développement Web', 'تطوير الويب', 'Web Development',
            1, 1,
            'Formation axée sur la création et l\'intégration de sites internet et d\'applications web (front-end et back-end).',
            'تكوين يركز على إنشاء ودمj مواقع الإنترنت وتطبيقات الويب (الواجهة الأمامية والخلفية).',
            'Training focused on creating and integrating websites and web applications (front-end and back-end).'
        ]);
        $webFiliereId = $pdo->lastInsertId();
        echo "Inserted new Web Development filiere with ID: $webFiliereId\n";
    }

    // Now handle institution_filieres mapping
    // Clean up existing mapping for Solicode first to ensure only web and mobile dev are linked
    $delStmt = $pdo->prepare("DELETE FROM institution_filieres WHERE institution_id = ?");
    $delStmt->execute([$id]);
    echo "Cleaned up old filieres mapping for Solicode\n";

    // Filieres to link: $webFiliereId, 56 (Développement Mobile)
    $filieres = [$webFiliereId, 56];
    foreach ($filieres as $fId) {
        $insFil = $pdo->prepare("INSERT INTO institution_filieres (institution_id, filiere_id) VALUES (?, ?)");
        $insFil->execute([$id, $fId]);
        echo "Linked Solicode to filiere ID $fId\n";
    }
    
    echo "SoliCode Tanger database setup completed successfully!\n";
    
} catch (PDOException $e) {
    echo "Database Error: " . $e->getMessage() . "\n";
}
?>
