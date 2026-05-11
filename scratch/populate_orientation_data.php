<?php
require "config/DataBase.php";

// 1. Truncate existing orientation tables to start fresh
$pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
$pdo->exec("TRUNCATE TABLE categories");
$pdo->exec("TRUNCATE TABLE domains");
$pdo->exec("TRUNCATE TABLE filieres");
$pdo->exec("TRUNCATE TABLE institution_filieres");
$pdo->exec("TRUNCATE TABLE bac_types");
$pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

// 2. Insert Categories
$categories = [
    "Sciences Exactes & Technologies",
    "Ingénierie & Industrie",
    "Santé & Sciences de la Vie",
    "Agriculture & Environnement",
    "Business, Gestion & Finance",
    "Droit, Politique & Société",
    "Arts, Design & Médias",
    "Services, Tourisme & Transport",
    "Éducation & Sciences Humaines",
    "Formation Professionnelle & Métiers"
];

$catIds = [];
foreach ($categories as $cat) {
    $stmt = $pdo->prepare("INSERT INTO categories (nom) VALUES (?)");
    $stmt->execute([$cat]);
    $catIds[$cat] = $pdo->lastInsertId();
    echo "Added Category: $cat\n";
}

// 3. Insert Domains
$domains = [
    "Sciences Exactes & Technologies" => [
        "Informatique & Digital", "Intelligence Artificielle & Data", "Génie & Technologie",
        "Robotique & Automatisation", "Télécommunications & Réseaux", "Génie Logiciel & Systèmes Embarqués",
        "Mathématiques & Statistiques", "Physique & Chimie", "Recherche & Innovation"
    ],
    "Ingénierie & Industrie" => [
        "Génie Civil & BTP", "Industrie & Maintenance", "Automobile & Mécanique",
        "Énergie & Énergies Renouvelables", "Mines & Géosciences", "Eau, Hydraulique & Assainissement",
        "Textile & Industrie du vêtement"
    ],
    "Santé & Sciences de la Vie" => [
        "Santé & Médecine", "Biologie & Biotechnologie", "Laboratoire & Analyses", "Psychologie & Sciences Sociales"
    ],
    "Agriculture & Environnement" => [
        "Agriculture & Agroalimentaire", "Environnement & Développement Durable", "Maritime & Pêche"
    ],
    "Business, Gestion & Finance" => [
        "Commerce, Gestion & Économie", "Finance & Banque", "Comptabilité, Audit & Fiscalité",
        "Marketing & Commerce International", "Logistique & Supply Chain", "Ressources Humaines & Management",
        "Actuariat & Assurance"
    ],
    "Droit, Politique & Société" => [
        "Droit & Sciences Politiques", "Sécurité & Défense", "Social & Développement Humain", "Études Islamiques & Charia"
    ],
    "Arts, Design & Médias" => [
        "Architecture, Design & Arts", "Multimédia & Audiovisuel", "Communication, Journalisme & Médias",
        "Cinéma & Production Audiovisuelle", "Musique & Arts de la scène", "Mode, Stylisme & Beauté"
    ],
    "Services, Tourisme & Transport" => [
        "Aviation, Transport & Maritime", "Tourisme, Hôtellerie & Restauration", "Cuisine & Arts culinaires"
    ],
    "Éducation & Sciences Humaines" => [
        "Éducation & Langues", "Géographie, Géologie & Urbanisme", "Patrimoine & Archéologie"
    ],
    "Formation Professionnelle & Métiers" => [
        "Formation Professionnelle & Métiers Techniques", "Artisanat & Métiers manuels"
    ]
];

$domainIds = [];
foreach ($domains as $catName => $domList) {
    foreach ($domList as $dom) {
        $stmt = $pdo->prepare("INSERT INTO domains (categorie_id, nom) VALUES (?, ?)");
        $stmt->execute([$catIds[$catName], $dom]);
        $domainIds[$dom] = $pdo->lastInsertId();
    }
}
echo "Domains added.\n";

// 4. Insert Bac Types
$bacTypes = [
    ["nom" => "Sciences Mathématiques A", "code" => "SMA"],
    ["nom" => "Sciences Mathématiques B", "code" => "SMB"],
    ["nom" => "Sciences Physiques-Chimie", "code" => "PC"],
    ["nom" => "Sciences de la Vie et de la Terre", "code" => "SVT"],
    ["nom" => "Sciences Économiques", "code" => "ECO"],
    ["nom" => "Gestion Comptable", "code" => "GEST"],
    ["nom" => "Sciences et Technologies", "code" => "TECH"],
    ["nom" => "Lettres", "code" => "LET"],
    ["nom" => "Sciences Humaines", "code" => "SH"],
    ["nom" => "Bac Professionnel", "code" => "PROF"]
];

foreach ($bacTypes as $bt) {
    $stmt = $pdo->prepare("INSERT INTO bac_types (nom, code) VALUES (?, ?)");
    $stmt->execute([$bt['nom'], $bt['code']]);
}
echo "Bac types added.\n";

echo "Orientation data populated successfully.\n";
?>
