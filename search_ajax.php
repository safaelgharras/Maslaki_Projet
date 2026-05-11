<?php
require "config/DataBase.php";

$search = isset($_GET["search"]) ? trim($_GET["search"]) : "";
$cityId = isset($_GET["city_id"]) ? trim($_GET["city_id"]) : "";
$catId = isset($_GET["cat_id"]) ? trim($_GET["cat_id"]) : "";
$domainId = isset($_GET["domain_id"]) ? trim($_GET["domain_id"]) : "";
$filiereId = isset($_GET["filiere_id"]) ? trim($_GET["filiere_id"]) : "";
$bacId = isset($_GET["bac_id"]) ? trim($_GET["bac_id"]) : "";
$type = isset($_GET["type"]) ? trim($_GET["type"]) : "";

$sql = "SELECT i.*, v.nom as city_name, v.nom_ar as city_name_ar, 
               GROUP_CONCAT(DISTINCT f.nom SEPARATOR ', ') as filieres_list,
               GROUP_CONCAT(DISTINCT f.nom_ar SEPARATOR ', ') as filieres_list_ar
        FROM institutions i 
        LEFT JOIN villes v ON i.ville_id = v.id
        LEFT JOIN institution_filieres ifil ON i.id = ifil.institution_id
        LEFT JOIN filieres f ON ifil.filiere_id = f.id
        LEFT JOIN domains d ON f.domain_id = d.id
        LEFT JOIN institution_bac_types ibt ON i.id = ibt.institution_id
        WHERE 1=1";
$params = [];

if (!empty($search)) {
    $sql .= " AND (i.name LIKE ? OR i.description LIKE ? OR f.nom LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($cityId)) {
    $sql .= " AND i.ville_id = ?";
    $params[] = $cityId;
}

if (!empty($catId)) {
    $sql .= " AND d.categorie_id = ?";
    $params[] = $catId;
}

if (!empty($domainId)) {
    $sql .= " AND f.domain_id = ?";
    $params[] = $domainId;
}

if (!empty($filiereId)) {
    $sql .= " AND ifil.filiere_id = ?";
    $params[] = $filiereId;
}

if (!empty($bacId)) {
    $sql .= " AND ibt.bac_type_id = ?";
    $params[] = $bacId;
}

if (!empty($type)) {
    if ($type === 'Public') {
        $sql .= " AND i.type != 'Private'";
    } else {
        $sql .= " AND i.type = ?";
        $params[] = $type;
    }
}

$sql .= " GROUP BY i.id";
$sql .= " ORDER BY i.is_popular DESC, i.name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$institutions = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once "includes/lang_helper.php";
foreach ($institutions as &$inst) {
    $inst['name'] = getLocalizedDbField($inst, 'name');
    $inst['description'] = getLocalizedDbField($inst, 'description');
    
    // City localization
    $cityData = [
        'city' => $inst['city'] ?? $inst['city_name'] ?? '',
        'city_ar' => $inst['city_ar'] ?? $inst['city_name_ar'] ?? ''
    ];
    $inst['city'] = getLocalizedDbField($cityData, 'city');
    
    // Filieres list localization
    $inst['filieres_list'] = getLocalizedDbField([
        'filieres_list' => $inst['filieres_list'],
        'filieres_list_ar' => $inst['filieres_list_ar']
    ], 'filieres_list');
}
unset($inst);


header("Content-Type: application/json");
echo json_encode($institutions);
?>
