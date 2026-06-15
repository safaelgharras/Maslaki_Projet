<?php
/**
 * register_process.php — Handle student registration form submission (POST).
 *
 * Collects name, email, password, bac branch, average, and city.
 * Checks for duplicate email, hashes the password, inserts into `students`,
 * then redirects to login with a success message.
 */

require_once "includes/lang_helper.php";
require "config/DataBase.php";

// Only accept POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name       = trim($_POST["name"]);
    $email      = trim($_POST["email"]);
    $password   = $_POST["password"];
    $bac_branch = trim($_POST["bac_branch"]);
    $average    = $_POST["average"];
    $city       = trim($_POST["city"]);

    // Check if email already exists to prevent duplicate accounts
    $check = $pdo->prepare("SELECT id FROM students WHERE email = ?");
    $check->execute([$email]);

    if ($check->fetch()) {
        header("Location: views/register.php?error=" . urlencode(__('error_email_exists')));
        exit();
    }

    // Hash the password before storing (never store plain-text passwords)
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insert new student record
    $sql = "INSERT INTO students (name, email, password, bac_branch, average, city)
            VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$name, $email, $hashedPassword, $bac_branch, $average, $city]);

    // Redirect to login with localized success message
    header("Location: views/login.php?success=" . urlencode(__('success_registration')));
    exit();
}
?>