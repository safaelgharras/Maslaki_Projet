<?php
require __DIR__ . '/../config/DataBase.php';

$stmt = $pdo->query("SELECT id, name, city, image FROM institutions WHERE image = 'default_school.jpg'");
$missing = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Institutions with default_school.jpg:\n";
foreach ($missing as $m) {
    echo "ID: {$m['id']} | Name: {$m['name']} | City: {$m['city']}\n";
}

echo "\nAvailable images in assets/images/institutions:\n";
$imagesDir = __DIR__ . '/../assets/images/institutions';
$imageFiles = array_diff(scandir($imagesDir), array('..', '.'));
foreach ($imageFiles as $img) {
    echo "- $img\n";
}
?>
