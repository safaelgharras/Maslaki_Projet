<?php
/**
 * get_domains.php — AJAX endpoint returning domains and filières for a category.
 *
 * Accepts GET parameter ?cat_id=N (category ID).
 * Returns a deduplicated JSON array of domains and filières belonging to
 * that category, with localized names. Used by institutions.php and
 * orientation_explore.php to populate the domain filter dropdown.
 */
require "config/DataBase.php";

$cat_id = isset($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;

if ($cat_id > 0) {
    require_once "includes/lang_helper.php";
    
    // 1. Get domains
    $stmt = $pdo->prepare("SELECT id, nom, nom_ar, nom_en, 'domain' as type FROM domains WHERE categorie_id = ? ORDER BY nom ASC");
    $stmt->execute([$cat_id]);
    $domains = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // 2. Get filieres for this category (optionally those without a domain, or just all)
    $stmt2 = $pdo->prepare("SELECT id, nom, nom_ar, nom_en, 'filiere' as type FROM filieres WHERE categorie_id = ? ORDER BY nom ASC");
    $stmt2->execute([$cat_id]);
    $filieres = $stmt2->fetchAll(PDO::FETCH_ASSOC);
    
    $all_items = array_merge($domains, $filieres);
    
    $unique_results = [];
    $seen_names = [];
    
    foreach ($all_items as $item) {
        $localizedNom = getLocalizedDbField($item, 'nom');
        if (!isset($seen_names[$localizedNom])) {
            $item['nom'] = $localizedNom;
            $unique_results[] = $item;
            $seen_names[$localizedNom] = true;
        }
    }
    
    echo json_encode($unique_results);
} else {
    echo json_encode([]);
}
?>
