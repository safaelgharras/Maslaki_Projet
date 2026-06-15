<?php
/**
 * search_ajax.php — AJAX endpoint for filtering institutions.
 *
 * Accepts GET parameters: search, city_id, cat_id, domain_id, filiere_id, bac_id, type.
 * Returns a JSON array of matching institutions with localized fields.
 * Used by the live-search on views/institutions.php.
 *
 * Joins through: villes, institution_filieres, filieres, domains,
 * institution_bac_types, institution_domain for multi-criteria filtering.
 */

require "config/DataBase.php";

// Collect filter parameters from the AJAX request
$search    = isset($_GET["search"]) ? trim($_GET["search"]) : "";
$cityId    = isset($_GET["city_id"]) ? trim($_GET["city_id"]) : "";
$catId     = isset($_GET["cat_id"]) ? trim($_GET["cat_id"]) : "";
$domainId  = isset($_GET["domain_id"]) ? trim($_GET["domain_id"]) : "";
$filiereId = isset($_GET["filiere_id"]) ? trim($_GET["filiere_id"]) : "";
$bacId     = isset($_GET["bac_id"]) ? trim($_GET["bac_id"]) : "";
$type      = isset($_GET["type"]) ? trim($_GET["type"]) : "";

// Base query: join institutions with related tables for filtering and display data
$sql = "SELECT i.*, v.nom as city_name, v.nom_ar as city_name_ar, 
               GROUP_CONCAT(DISTINCT f.nom SEPARATOR ', ') as filieres_list,
               GROUP_CONCAT(DISTINCT f.nom_ar SEPARATOR ', ') as filieres_list_ar
        FROM institutions i 
        LEFT JOIN villes v ON i.ville_id = v.id
        LEFT JOIN institution_filieres ifil ON i.id = ifil.institution_id
        LEFT JOIN filieres f ON ifil.filiere_id = f.id
        LEFT JOIN domains d ON f.domain_id = d.id
        LEFT JOIN institution_bac_types ibt ON i.id = ibt.institution_id
        LEFT JOIN institution_domain idom ON i.id = idom.institution_id
        LEFT JOIN domains d2 ON idom.domain_id = d2.id
        WHERE 1=1";
$params = [];

// Free-text search: match name, name_ar, description, or filiere names
if (!empty($search)) {
    $sql .= " AND (i.name LIKE ? OR i.name_ar LIKE ? OR i.description LIKE ? OR f.nom LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

// Filter by city (villes table FK)
if (!empty($cityId)) {
    $sql .= " AND i.ville_id = ?";
    $params[] = $cityId;
}

// Filter by category (checks both filiere domain and institution domain)
if (!empty($catId)) {
    $sql .= " AND (d.categorie_id = ? OR d2.categorie_id = ?)";
    $params[] = $catId;
    $params[] = $catId;
}

// Filter by domain (checks filiere's domain, institution domain mapping, or direct filiere match)
if (!empty($domainId)) {
    $sql .= " AND (f.domain_id = ? OR idom.domain_id = ? OR ifil.filiere_id = ?)";
    $params[] = $domainId;
    $params[] = $domainId;
    $params[] = $domainId;
}

// Filter by specific filiere
if (!empty($filiereId)) {
    $sql .= " AND ifil.filiere_id = ?";
    $params[] = $filiereId;
}

// Filter by accepted bac type
if (!empty($bacId)) {
    $sql .= " AND ibt.bac_type_id = ?";
    $params[] = $bacId;
}

// Filter by institution type/sector
if (!empty($type)) {
    $sql .= " AND (i.type = ? OR i.sector_type = ?)";
    $params[] = $type;
    $params[] = strtolower($type);
}

// Group by institution to avoid duplicates from JOINs; sort by popular first
$sql .= " GROUP BY i.id";
$sql .= " ORDER BY (i.id = 131) DESC, i.is_popular DESC, i.name ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$institutions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Localize all display fields based on the user's current language
require_once "includes/lang_helper.php";
foreach ($institutions as &$inst) {
    $inst['name'] = getLocalizedDbField($inst, 'name');
    $inst['description'] = getLocalizedDbField($inst, 'description');
    
    // City localization (fallback between institution city field and villes table)
    $cityData = [
        'city' => $inst['city'] ?? $inst['city_name'] ?? '',
        'city_ar' => $inst['city_ar'] ?? $inst['city_name_ar'] ?? ''
    ];
    $inst['city'] = getLocalizedDbField($cityData, 'city');
    
    $inst['diplome'] = getLocalizedDbField($inst, 'diplome');
    $inst['duree_etudes'] = getLocalizedDbField($inst, 'duree_etudes');
    
    // Localize the concatenated filieres list
    $inst['filieres_list'] = getLocalizedDbField([
        'filieres_list' => $inst['filieres_list'],
        'filieres_list_ar' => $inst['filieres_list_ar']
    ], 'filieres_list');
}
unset($inst);


header("Content-Type: application/json");
echo json_encode($institutions);
?>
