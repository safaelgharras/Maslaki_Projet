<?php
require "config/DataBase.php";
$stmt = $pdo->query("DESCRIBE notifications");
while($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo $row['Field'] . " (" . $row['Type'] . ")\n";
}
?>
