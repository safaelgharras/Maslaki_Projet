<?php
require "config/DataBase.php";

$updates = [
    // ENSA
    ['pattern' => 'ENSA%', 'data' => [
        'diplome' => "Ingénieur d'État",
        'duree_etudes' => '5 ans',
        'type' => 'Engineering',
        'requirements' => 'Bac S (Math, PC, SVT), Concours National ENSA'
    ]],
    // ENCG
    ['pattern' => 'ENCG%', 'data' => [
        'diplome' => "Diplôme de l'ENCG (Master)",
        'duree_etudes' => '5 ans',
        'type' => 'Business',
        'requirements' => 'Bac S, Eco, Gestion. Concours TAFEM'
    ]],
    // FST
    ['pattern' => 'FST%', 'data' => [
        'diplome' => "LST, MST, Ingénieur",
        'duree_etudes' => '3 - 5 ans',
        'type' => 'Science',
        'requirements' => 'Bac Scientifique ou Technique, Présélection dossier'
    ]],
    // EST
    ['pattern' => 'EST%', 'data' => [
        'diplome' => "DUT, Licence Professionnelle",
        'duree_etudes' => '2 - 3 ans',
        'type' => 'Technical',
        'requirements' => 'Bac Scientifique ou Technique, Sélection sur dossier'
    ]],
    // FS (Faculté des Sciences)
    ['pattern' => 'FS %', 'data' => [
        'diplome' => "Licence, Master, Doctorat",
        'duree_etudes' => '3 - 8 ans',
        'type' => 'University',
        'requirements' => 'Bac Scientifique'
    ]],
    ['pattern' => 'FS-%', 'data' => [
        'diplome' => "Licence, Master, Doctorat",
        'duree_etudes' => '3 - 8 ans',
        'type' => 'University',
        'requirements' => 'Bac Scientifique'
    ]],
    // CPGE
    ['pattern' => 'CPGE%', 'data' => [
        'diplome' => "Classes Préparatoires (Accès Grandes Écoles)",
        'duree_etudes' => '2 ans',
        'type' => 'Preparatory',
        'requirements' => 'Bac S (Math ou PC), Dossier d\'excellence'
    ]],
    // Specifics
    ['pattern' => 'ISCAE%', 'data' => [
        'diplome' => "Diplôme Grande École (Grade Master)",
        'duree_etudes' => '3 ans (après Bac+2)',
        'type' => 'Business',
        'requirements' => 'Bac+2 (CPGE, DUT, BTS), Concours écrit & oral'
    ]],
    ['pattern' => 'EMI %', 'data' => [
        'diplome' => "Ingénieur d'État",
        'duree_etudes' => '3 ans (après Bac+2)',
        'type' => 'Engineering',
        'requirements' => 'Bac+2, Concours National Commun (CNC)'
    ]],
    ['pattern' => 'INPT %', 'data' => [
        'diplome' => "Ingénieur d'État",
        'duree_etudes' => '3 ans (après Bac+2)',
        'type' => 'Engineering',
        'requirements' => 'Bac+2, Concours National Commun (CNC)'
    ]],
    ['pattern' => 'ISIC %', 'data' => [
        'diplome' => "Licence en Information et Communication",
        'duree_etudes' => '3 ans',
        'type' => 'Media',
        'requirements' => 'Bac toutes séries, Concours ISIC'
    ]]
];

foreach ($updates as $u) {
    $sql = "UPDATE institutions SET ";
    $parts = [];
    $params = [];
    foreach ($u['data'] as $col => $val) {
        $parts[] = "$col = ?";
        $params[] = $val;
    }
    $sql .= implode(", ", $parts);
    $sql .= " WHERE name LIKE ?";
    $params[] = $u['pattern'];
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    echo "Updated " . $stmt->rowCount() . " rows for pattern " . $u['pattern'] . "\n";
}

// Set images for some common ones if they are default
$pdo->exec("UPDATE institutions SET image = 'ensa.png' WHERE name LIKE 'ENSA%' AND (image = 'default_school.jpg' OR image IS NULL)");
$pdo->exec("UPDATE institutions SET image = 'encg.png' WHERE name LIKE 'ENCG%' AND (image = 'default_school.jpg' OR image IS NULL)");
$pdo->exec("UPDATE institutions SET image = 'fst.png' WHERE name LIKE 'FST%' AND (image = 'default_school.jpg' OR image IS NULL)");
$pdo->exec("UPDATE institutions SET image = 'est.png' WHERE name LIKE 'EST%' AND (image = 'default_school.jpg' OR image IS NULL)");

echo "Database information filled successfully.\n";
?>
