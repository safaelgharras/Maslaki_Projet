<?php
require __DIR__ . '/../config/DataBase.php';

// Path to images
$imagesDir = __DIR__ . '/../assets/images/institutions';
$imageFiles = array_diff(scandir($imagesDir), array('..', '.'));

// Fetch all institutions
$stmt = $pdo->query("SELECT id, name, city FROM institutions");
$institutions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$updatedCount = 0;

foreach ($institutions as $inst) {
    $id = $inst['id'];
    $oldName = trim($inst['name']);
    $oldCity = trim($inst['city']);
    
    $newName = $oldName;
    $newCity = $oldCity;

    // 1. Separate Name and City
    // A lot of names have the format "NAME - City" or "NAME City"
    // Let's use known base names to extract them properly
    $knownBases = [
        'ENSA', 'EST', 'FST', 'FSJES', 'FLSH', 'FS', 'ENCG', 'ENSAM', 'ENSEM', 'ENSET', 'ENSIAS', 'ENS', 'CPGE', 
        'Université Hassan I', 'Université Hassan II', 'Université Mohammed V', 
        'Université Sidi Mohamed Ben Abdellah', 'Université Sultan Moulay Slimane',
        'Université Cadi Ayyad', 'Université Ibn Tofail', 'Université Abdelmalek Essaadi',
        'Université Chouaib Doukkali', 'UM6P', 'UM6SS', 'INPT', 'EHTP', 'EMI', 'EIGSI', 
        'HECI', 'ESCA', 'HEM', 'IGA', 'ISGA', 'SUPINFO', 'SUPMTI', 'EMSI', 'ESITH', 
        'ISMAC', 'ISIC', 'ISITT', 'OFPPT', 'ISPITS', 'IAV Hassan II', 'ENA', 'EAC', 'Art\'Com Sup',
        '1337', 'YouCode', 'FMP'
    ];

    usort($knownBases, function($a, $b) {
        return strlen($b) - strlen($a);
    });

    foreach ($knownBases as $base) {
        if (stripos($oldName, $base) === 0) {
            $newName = $base;
            
            // Extract the rest as city if it exists
            $rest = trim(substr($oldName, strlen($base)));
            // Remove leading dash or spaces
            $rest = ltrim($rest, ' -');
            
            if (!empty($rest)) {
                $newCity = $rest;
            }
            break;
        }
    }

    // Fix some special cases where name might be just "Université ..."
    // Let's translate base names to Arabic and English for the DB update
    $nameAr = $newName;
    $nameEn = $newName;

    $arMap = [
        'ENSA' => 'المدرسة الوطنية للعلوم التطبيقية',
        'EST' => 'المدرسة العليا للتكنولوجيا',
        'FST' => 'كلية العلوم والتقنيات',
        'FSJES' => 'كلية العلوم القانونية والاقتصادية والاجتماعية',
        'FLSH' => 'كلية الآداب والعلوم الإنسانية',
        'FS' => 'كلية العلوم',
        'ENCG' => 'المدرسة الوطنية للتجارة والتسيير',
        'ENSAM' => 'المدرسة الوطنية العليا للفنون والمهن',
        'ENSEM' => 'المدرسة الوطنية العليا للكهرباء والميكانيك',
        'ENS' => 'المدرسة العليا للأساتذة',
        'CPGE' => 'الأقسام التحضيرية للمدارس العليا',
        'Université Hassan I' => 'جامعة الحسن الأول',
        'Université Hassan II' => 'جامعة الحسن الثاني',
        'Université Mohammed V' => 'جامعة محمد الخامس',
        'Université Sidi Mohamed Ben Abdellah' => 'جامعة سيدي محمد بن عبد الله',
        'Université Sultan Moulay Slimane' => 'جامعة السلطان مولاي سليمان',
        'Université Cadi Ayyad' => 'جامعة القاضي عياض',
        'Université Ibn Tofail' => 'جامعة ابن طفيل',
        'Université Abdelmalek Essaadi' => 'جامعة عبد المالك السعدي',
        'Université Chouaib Doukkali' => 'جامعة شعيب الدكالي',
        'FMP' => 'كلية الطب والصيدلة',
        'OFPPT' => 'مكتب التكوين المهني وإنعاش الشغل'
    ];
    
    $enMap = [
        'ENSA' => 'National School of Applied Sciences',
        'EST' => 'Higher School of Technology',
        'FST' => 'Faculty of Sciences and Technologies',
        'FSJES' => 'Faculty of Legal, Economic and Social Sciences',
        'FLSH' => 'Faculty of Letters and Human Sciences',
        'FS' => 'Faculty of Sciences',
        'ENCG' => 'National School of Commerce and Management',
        'ENSAM' => 'National Higher School of Arts and Crafts',
        'ENSEM' => 'National Higher School of Electricity and Mechanics',
        'ENS' => 'Higher Normal School',
        'CPGE' => 'Preparatory Classes for Grandes Ecoles',
        'Université Hassan I' => 'Hassan 1st University',
        'Université Hassan II' => 'Hassan II University',
        'Université Mohammed V' => 'Mohammed V University',
        'Université Sidi Mohamed Ben Abdellah' => 'Sidi Mohamed Ben Abdellah University',
        'Université Sultan Moulay Slimane' => 'Sultan Moulay Slimane University',
        'Université Cadi Ayyad' => 'Cadi Ayyad University',
        'Université Ibn Tofail' => 'Ibn Tofail University',
        'Université Abdelmalek Essaadi' => 'Abdelmalek Essaadi University',
        'Université Chouaib Doukkali' => 'Chouaib Doukkali University',
        'FMP' => 'Faculty of Medicine and Pharmacy',
        'OFPPT' => 'Office of Vocational Training and Employment Promotion'
    ];

    if (isset($arMap[$newName])) $nameAr = $arMap[$newName];
    if (isset($enMap[$newName])) $nameEn = $enMap[$newName];

    // 2. Image Mapping
    $matchedImage = 'default_school.jpg';
    $searchString = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $newName . $newCity));
    
    foreach ($imageFiles as $img) {
        $imgNameWithoutExt = pathinfo($img, PATHINFO_FILENAME);
        $normalizedImgName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $imgNameWithoutExt));
        if ($normalizedImgName === $searchString || strpos($normalizedImgName, $searchString) !== false) {
            $matchedImage = $img;
            break;
        }
    }
    
    // Fallback if not found: try to match just the name if it's unique enough
    if ($matchedImage === 'default_school.jpg') {
        $searchStringNameOnly = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $newName));
        foreach ($imageFiles as $img) {
            $imgNameWithoutExt = pathinfo($img, PATHINFO_FILENAME);
            $normalizedImgName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '', $imgNameWithoutExt));
            if ($normalizedImgName === $searchStringNameOnly) {
                $matchedImage = $img;
                break;
            }
        }
    }

    // 3. Seuil and Diplome assignment
    $seuil = '';
    $diplome = '';

    if (in_array($newName, ['ENSA', 'ENSAM', 'EHTP', 'EMI', 'ENSIAS', 'ENSEM', 'INPT', 'EIGSI'])) {
        $seuil = '14.00';
        $diplome = 'Diplôme d\'Ingénieur d\'État';
    } elseif (in_array($newName, ['ENCG', 'ISCAE', 'ESCA', 'HEM'])) {
        $seuil = '14.50';
        $diplome = 'Diplôme Grade Master (Bac+5)';
    } elseif (in_array($newName, ['EST'])) {
        $seuil = '13.00';
        $diplome = 'Diplôme Universitaire de Technologie (DUT)';
    } elseif (in_array($newName, ['FST'])) {
        $seuil = '13.50';
        $diplome = 'Licence en Sciences et Techniques (LST)';
    } elseif (in_array($newName, ['FS', 'FSJES', 'FLSH', 'ENS'])) {
        $seuil = '10.00';
        $diplome = 'Licence Fondamentale';
    } elseif ($newName === 'CPGE') {
        $seuil = '16.00';
        $diplome = 'Attestation de réussite CPGE';
    } elseif (in_array($newName, ['1337', 'YouCode'])) {
        $seuil = 'Sélection par tests';
        $diplome = 'Certificat de formation';
    } elseif (in_array($newName, ['FMP', 'ISPITS'])) {
        $seuil = '14.00';
        $diplome = 'Doctorat en Médecine / Diplôme d\'État';
    } elseif (strpos($newName, 'Université') !== false || strpos($newName, 'UM6P') !== false || strpos($newName, 'UM6SS') !== false) {
        $seuil = '10.00';
        $diplome = 'Licence / Master / Doctorat';
    } elseif (in_array($newName, ['OFPPT'])) {
        $seuil = '10.00';
        $diplome = 'Technicien / Technicien Spécialisé';
    } else {
        $seuil = '12.00';
        $diplome = 'Diplôme de l\'établissement';
    }

    // 4. Update the DB
    $updateStmt = $pdo->prepare("UPDATE institutions 
        SET name = ?, name_ar = ?, name_en = ?, city = ?, image = ?, seuil = ?, diplome = ? 
        WHERE id = ?");
    $updateStmt->execute([$newName, $nameAr, $nameEn, $newCity, $matchedImage, $seuil, $diplome, $id]);
    
    echo "Updated [{$id}]: {$oldName} -> {$newName} | City: {$newCity} | Image: {$matchedImage} | Seuil: {$seuil}\n";
    $updatedCount++;
}

echo "\nSuccessfully updated {$updatedCount} institutions.\n";
?>
