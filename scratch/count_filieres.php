<?php
require 'config/DataBase.php';
echo $pdo->query('SELECT COUNT(*) FROM institution_filieres')->fetchColumn();
