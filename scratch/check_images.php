<?php
require "config/DataBase.php";

$stmt = $pdo->query("SELECT DISTINCT image, name FROM institutions");
$institutions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$missing = [];
$found = [];

foreach ($institutions as $inst) {
    $image = $inst['image'];
    $name = $inst['name'];
    
    if (empty($image)) {
        $missing[] = "[$name] Image field is empty";
        continue;
    }

    $paths = [
        "assets/images/" . $image,
        "assets/images/institutions/" . $image,
        "assets/images/" . $name . ".webp",
        "assets/images/" . $name . ".png",
        "assets/images/" . $name . ".jpg"
    ];

    $exists = false;
    foreach ($paths as $p) {
        if (file_exists($p)) {
            $exists = true;
            $found[] = "[$name] Found at: $p";
            break;
        }
    }

    if (!$exists) {
        $missing[] = "[$name] Missing image: $image (Also tried name-based paths)";
    }
}

echo "--- FOUND IMAGES ---\n";
echo implode("\n", $found) . "\n\n";

echo "--- MISSING IMAGES ---\n";
echo implode("\n", $missing) . "\n";
?>
