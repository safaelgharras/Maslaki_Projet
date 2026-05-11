<?php
require 'config/DataBase.php';
echo "--- Search Results ---\n";
$stmt = $pdo->query("SELECT id, nom, 'category' as type FROM categories WHERE nom LIKE '%Sciences Humaines%' 
                   UNION 
                   SELECT id, nom, 'domain' as type FROM domains WHERE nom LIKE '%Sciences Humaines%'");
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
