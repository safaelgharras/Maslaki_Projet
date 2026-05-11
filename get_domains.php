<?php
require "config/DataBase.php";

$cat_id = isset($_GET['cat_id']) ? intval($_GET['cat_id']) : 0;

if ($cat_id > 0) {
    $stmt = $pdo->prepare("SELECT * FROM domains WHERE categorie_id = ? ORDER BY nom ASC");
    $stmt->execute([$cat_id]);
    $domains = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    require_once "includes/lang_helper.php";
    foreach ($domains as &$d) {
        $d['nom'] = getLocalizedDbField($d, 'nom');
    }
    unset($d);
    
    echo json_encode($domains);
} else {
    echo json_encode([]);
}
?>
