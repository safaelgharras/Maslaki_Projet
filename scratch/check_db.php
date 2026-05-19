<?php
require __DIR__ . '/../config/DataBase.php';

// Check students table
$stmt = $pdo->query("SELECT id, name, email FROM students");
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo "Students in DB:\n";
print_r($students);

// Check appointments table structure
$stmt = $pdo->query("SHOW CREATE TABLE appointments");
$table = $stmt->fetch(PDO::FETCH_ASSOC);
echo "\nAppointments Table:\n";
print_r($table['Create Table']);

// Check notifications table structure
$stmt = $pdo->query("SHOW CREATE TABLE notifications");
$table = $stmt->fetch(PDO::FETCH_ASSOC);
echo "\nNotifications Table:\n";
print_r($table['Create Table']);
?>
