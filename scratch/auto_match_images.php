<?php
require 'config/DataBase.php';

$dir = 'assets/images/institutions/';
$files = array_diff(scandir($dir), array('.', '..', 'desktop.ini'));

// Fetch institutions that need images
$institutions = $pdo->query("SELECT id, name, city FROM institutions WHERE image IS NULL OR image = '' OR image = 'default_school.jpg'")->fetchAll(PDO::FETCH_ASSOC);

echo "Found " . count($institutions) . " institutions to process.\n";
echo "Found " . count($files) . " image files.\n";

$matches = 0;

function clean($str) {
    $str = strtolower($str);
    $str = str_replace(['é', 'è', 'ê', 'ë'], 'e', $str);
    $str = str_replace(['à', 'â', 'ä'], 'a', $str);
    $str = str_replace(['î', 'ï'], 'i', $str);
    $str = str_replace(['ô', 'ö'], 'o', $str);
    $str = str_replace(['û', 'ü'], 'u', $str);
    $str = preg_replace('/[^a-z0-9]/', '', $str);
    return $str;
}

foreach ($institutions as $inst) {
    $instName = $inst['name'];
    $instCity = $inst['city'];
    $cleanName = clean($instName);
    
    $bestMatch = null;
    
    foreach ($files as $file) {
        $cleanFile = clean(pathinfo($file, PATHINFO_FILENAME));
        
        // Exact match of cleaned names
        if ($cleanFile === $cleanName) {
            $bestMatch = $file;
            break;
        }
        
        // Match with name + city
        if (strpos($cleanFile, $cleanName) !== false || strpos($cleanName, $cleanFile) !== false) {
            $bestMatch = $file;
        }
    }
    
    if ($bestMatch) {
        $stmt = $pdo->prepare("UPDATE institutions SET image = ? WHERE id = ?");
        $stmt->execute([$bestMatch, $inst['id']]);
        echo "Matched: '$instName' -> '$bestMatch'\n";
        $matches++;
    } else {
        echo "No match for: '$instName'\n";
    }
}

echo "\nTotal matches: $matches\n";
