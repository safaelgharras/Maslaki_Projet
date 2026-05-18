<?php
require __DIR__ . '/../config/DataBase.php';

$imagesDir = __DIR__ . '/../assets/images/institutions';
$imageFiles = array_diff(scandir($imagesDir), array('..', '.'));

$stmt = $pdo->query("SELECT id, name, city, image FROM institutions");
$institutions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$usedImages = array_column($institutions, 'image');

echo "UNUSED IMAGES:\n";
foreach ($imageFiles as $img) {
    if (!in_array($img, $usedImages)) {
        echo "- $img\n";
    }
}

echo "\nINSTITUTIONS WITH DEFAULT IMAGE:\n";
foreach ($institutions as $inst) {
    if ($inst['image'] === 'default_school.jpg') {
        echo "- ID: {$inst['id']} | {$inst['name']} {$inst['city']}\n";
    }
}
?>
