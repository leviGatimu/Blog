<?php
// includes/auth.php
session_start();

// Check if the admin session exists
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // If not logged in, redirect to login page
    header("Location: login.php");
    exit;
}
?>