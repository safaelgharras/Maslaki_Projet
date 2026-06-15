<?php
/**
 * login_process.php — Handle student login form submission (POST).
 *
 * Validates email + password against the `students` table.
 * On success: stores user_id and user_name in the session, redirects to dashboard.
 * On failure: redirects back to login page with a localized error message.
 */

session_start();
require_once "includes/lang_helper.php";
require "config/DataBase.php";

// Only accept POST requests
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email    = trim($_POST["email"]);
    $password = $_POST["password"];

    // Look up the student by email
    $sql = "SELECT * FROM students WHERE email = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email]);

    $user = $stmt->fetch();

    // Verify password hash and log in
    if ($user && password_verify($password, $user["password"])) {

        $_SESSION["user_id"]   = $user["id"];
        $_SESSION["user_name"] = $user["name"];

        header("Location: views/dashboard.php");
        exit();
        
    } else {
        // Invalid credentials — redirect with localized error
        header("Location: views/login.php?error=" . urlencode(__('error_invalid_credentials')));
        exit();
    }
}
?>