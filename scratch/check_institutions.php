<?php
require_once __DIR__ . '/../config/DataBase.php';
$stmt = $pdo->query("SELECT id, name, type FROM institutions ORDER BY name ASC");
$schools = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Total institutions in DB: " . count($schools) . "\n";
foreach ($schools as $s) {
    echo "ID: " . $s['id'] . " | " . $s['name'] . " | " . $s['type'] . "\n";
}
?>
