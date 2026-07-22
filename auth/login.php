<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../config/database.php";

// Total registrations
$total = $conn->query("SELECT COUNT(*) FROM form_submissions")->fetchColumn();

// Today's registrations
$today = $conn->query("
SELECT COUNT(*)
FROM form_submissions
WHERE DATE(submitted_at)=CURDATE()
")->fetchColumn();

// Latest five registrations
$stmt = $conn->query("
SELECT *
FROM form_submissions
ORDER BY submitted_at DESC
LIMIT 5
");

$latest = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>