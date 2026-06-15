<?php
/**
 * logout.php — Destroy the current session and redirect to the login page.
 */
session_start();
session_destroy();

header("Location: login.php");
exit();
?>