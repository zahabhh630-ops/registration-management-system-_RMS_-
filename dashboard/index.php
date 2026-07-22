<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../auth/login.php");
    exit;
}

echo "<h1>Welcome to RMS Dashboard</h1>";
echo "<p>Dashboard coming soon...</p>";