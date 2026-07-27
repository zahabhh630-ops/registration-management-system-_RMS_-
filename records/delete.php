<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../includes/logger.php";

// Check if ID exists
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: view.php?error=record_not_found");
    exit;
}

$id = (int) $_GET['id'];

// Fetch record before deleting
$stmt = $conn->prepare("
    SELECT other_name, surname
    FROM form_submissions
    WHERE id_key = ?
");

$stmt->execute([$id]);

$record = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$record) {
    header("Location: view.php?error=record_not_found");
    exit;
}

// Delete record
$stmt = $conn->prepare("
    DELETE FROM form_submissions
    WHERE id_key = ?
");

$stmt->execute([$id]);

// Audit Log
logActivity(
    $conn,
    "Deleted record: {$record['other_name']} {$record['surname']}"
);

// Redirect
header("Location: view.php?success=record_deleted");
exit;