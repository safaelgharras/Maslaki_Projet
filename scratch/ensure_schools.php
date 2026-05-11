<?php
require "config/DataBase.php";

$schools = [
    ["name" => "ENSA Tanger", "city" => "Tanger", "type" => "Engineering", "description" => "École Nationale des Sciences Appliquées"],
    ["name" => "ENCG Casablanca", "city" => "Casablanca", "type" => "Business", "description" => "École Nationale de Commerce et de Gestion"],
    ["name" => "FST Settat", "city" => "Settat", "type" => "Science", "description" => "Faculté des Sciences et Techniques"],
    ["name" => "EST Fes", "city" => "Fes", "type" => "Technical", "description" => "École Supérieure de Technologie"],
    ["name" => "EMI Rabat", "city" => "Rabat", "type" => "Engineering", "description" => "École Mohammadia d'Ingénieurs"],
    ["name" => "ENSIAS Rabat", "city" => "Rabat", "type" => "Engineering", "description" => "École Nationale Supérieure d'Informatique et d'Analyse des Systèmes"],
    ["name" => "INPT Rabat", "city" => "Rabat", "type" => "Engineering", "description" => "Institut National des Postes et Télécommunications"],
    ["name" => "ENSEM Casablanca", "city" => "Casablanca", "type" => "Engineering", "description" => "École Nationale Supérieure d'Électricité et de Mécanique"],
    ["name" => "EHTP Casablanca", "city" => "Casablanca", "type" => "Engineering", "description" => "École Hassania des Travaux Publics"],
    ["name" => "IAV Hassan II Rabat", "city" => "Rabat", "type" => "Engineering", "description" => "Institut Agronomique et Vétérinaire Hassan II"],
    ["name" => "ISPITS Casablanca", "city" => "Casablanca", "type" => "Health", "description" => "Institut Supérieur des Professions Infirmières et Techniques de Santé"],
    ["name" => "ISCAE Casablanca", "city" => "Casablanca", "type" => "Business", "description" => "Institut Supérieur de Commerce et d'Administration des Entreprises"],
    ["name" => "ISMAC Rabat", "city" => "Rabat", "type" => "Media", "description" => "Institut Supérieur des Métiers de l'Audiovisuel et du Cinéma"],
    ["name" => "ENA Meknès", "city" => "Meknès", "type" => "Engineering", "description" => "École Nationale d'Agriculture de Meknès"],
];

foreach ($schools as $s) {
    // Check if exists
    $stmt = $pdo->prepare("SELECT id FROM institutions WHERE name = ?");
    $stmt->execute([$s['name']]);
    if (!$stmt->fetch()) {
        $ins = $pdo->prepare("INSERT INTO institutions (name, city, type, description) VALUES (?, ?, ?, ?)");
        $ins->execute([$s['name'], $s['city'], $s['type'], $s['description']]);
        echo "Inserted school: " . $s['name'] . "\n";
    }
}

echo "Core schools ensured.\n";
?>
