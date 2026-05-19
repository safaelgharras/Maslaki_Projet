<?php
require __DIR__ . '/../config/DataBase.php';

$stmt = $pdo->prepare("SELECT * FROM institutions WHERE name LIKE '%YouCode%' OR name_en LIKE '%YouCode%'");
$stmt->execute();
$youcode = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "YouCode Rows:\n";
print_r($youcode);

foreach ($youcode as $yc) {
    // Domains
    $ycId = $yc['id'];
    $stmt = $pdo->prepare("SELECT * FROM institution_domain WHERE institution_id = ?");
    $stmt->execute([$ycId]);
    $domains = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "\nDomains for YouCode ID $ycId:\n";
    print_r($domains);
    
    // Filieres
    $stmt = $pdo->prepare("SELECT f.* FROM filieres f JOIN institution_filieres inf ON f.id = inf.filiere_id WHERE inf.institution_id = ?");
    $stmt->execute([$ycId]);
    $filieres = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "\nFilieres for YouCode ID $ycId:\n";
    print_r($filieres);
    
    // Bac Types
    $stmt = $pdo->prepare("SELECT * FROM institution_bac_types WHERE institution_id = ?");
    $stmt->execute([$ycId]);
    $bac = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "\nBac Types for YouCode ID $ycId:\n";
    print_r($bac);
}

// Let's also check all domains
$stmt = $pdo->query("SELECT * FROM domains");
$allDomains = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "\nAll Domains:\n";
print_r($allDomains);
?>
