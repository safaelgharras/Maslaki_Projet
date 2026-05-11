<?php
require "config/DataBase.php";

$dir = "assets/images/";
$files = scandir($dir);
$images = [];
foreach ($files as $f) {
    if ($f !== '.' && $f !== '..') {
        $images[] = $f;
    }
}

$stmt = $pdo->query("SELECT id, name, image FROM institutions");
$institutions = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($institutions as $inst) {
    $name = $inst['name'];
    $currentImage = $inst['image'];
    
    // 1. Try to find exact match with common extensions
    $match = null;
    $possibleNames = [
        $name . ".webp",
        $name . ".png",
        $name . ".jpg",
        strtolower($name) . ".webp",
        strtolower($name) . ".png",
        strtolower($name) . ".jpg"
    ];

    foreach ($possibleNames as $pn) {
        if (in_array($pn, $images)) {
            $match = $pn;
            break;
        }
    }

    // 2. If no exact match, try prefix match (e.g., ENSA)
    if (!$match) {
        if (strpos($name, 'ENSA') === 0) $match = 'ENSA Tanger.png'; // Fallback to a real ENSA photo if specific is missing
        elseif (strpos($name, 'ENCG') === 0) $match = 'ENCG Settat.webp';
        elseif (strpos($name, 'FST') === 0) $match = 'FST Tanger.png';
        elseif (strpos($name, 'EST') === 0) $match = 'EST Casablanca.png';
        elseif (strpos($name, 'FS ') === 0) $match = 'FS Casablanca.png';
    }

    // 3. Fallback to default
    if (!$match) {
        $match = "default_school.jpg";
    }

    if ($match !== $currentImage) {
        $upd = $pdo->prepare("UPDATE institutions SET image = ? WHERE id = ?");
        $upd->execute([$match, $inst['id']]);
        echo "Updated [$name]: $currentImage -> $match\n";
    }
}

echo "Image references synchronized with filesystem.\n";
?>
