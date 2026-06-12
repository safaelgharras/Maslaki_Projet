<?php
require_once "includes/lang_helper.php";
require "config/DataBase.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = trim($_POST["name"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $bac_branch = trim($_POST["bac_branch"]);
    $average = $_POST["average"];
    $city = trim($_POST["city"]);

    // Check if email already exists
    $check = $pdo->prepare("SELECT id FROM students WHERE email = ?");
    $check->execute([$email]);

    if ($check->fetch()) {
        header("Location: views/register.php?error=" . urlencode(__('error_email_exists')));
        exit();
    }

    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO students (name, email, password, bac_branch, average, city)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $email, $hashedPassword, $bac_branch, $average, $city]);

    header("Location: views/login.php?success=" . urlencode(__('success_registration')));
    exit();
}
?>