<?php
require "config/DataBase.php";

try {
    // Get some domain IDs
    $domains = $pdo->query("SELECT id, nom FROM domains")->fetchAll(PDO::FETCH_KEY_PAIR);
    $filieres = $pdo->query("SELECT id, nom FROM filieres")->fetchAll(PDO::FETCH_KEY_PAIR);
    
    // ENSA Tanger -> Informatique & Digital
    $ensaId = $pdo->query("SELECT id FROM institutions WHERE name = 'ENSA Tanger'")->fetchColumn();
    if ($ensaId && isset($domains['Informatique & Digital'])) {
        $pdo->exec("UPDATE institutions SET type = 'Engineering' WHERE id = $ensaId");
        // Link to domain (we'll need a table for institution_domains if we want multiple, 
        // but for now let's use categorie_id in institutions or just the filieres link)
    }

    // Let's create some filieres for ENSA
    $ensaFiliere = "Génie Informatique";
    $domId = $domains['Informatique & Digital'];
    $stmt = $pdo->prepare("INSERT INTO filieres (nom, domain_id) VALUES (?, ?)");
    $stmt->execute([$ensaFiliere, $domId]);
    $fId = $pdo->lastInsertId();

    $stmt = $pdo->prepare("INSERT INTO institution_filieres (institution_id, filiere_id) VALUES (?, ?)");
    $stmt->execute([$ensaId, $fId]);

    echo "Linked ENSA Tanger to Génie Informatique.\n";

} catch (Exception $e) {
    echo "Linking failed: " . $e->getMessage() . "\n";
}
?>
