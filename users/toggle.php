<?php

require_once "../includes/admin_only.php";
require_once "../config/database.php";
require_once "../includes/logger.php";

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int)$_GET['id'];

// Prevent changing your own status
if ($id === (int)$_SESSION['user_id']) {
    header("Location: index.php?error=self_toggle");
    exit;
}

// Fetch user
$stmt = $conn->prepare("
    SELECT fullname, username, status
    FROM users
    WHERE id = ?
");

$stmt->execute([$id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: index.php?error=user_not_found");
    exit;
}

// Toggle status
$newStatus = ($user['status'] === 'Active') ? 'Inactive' : 'Active';

// Update
$stmt = $conn->prepare("
    UPDATE users
    SET status = ?
    WHERE id = ?
");

$stmt->execute([$newStatus, $id]);

// Audit log
logActivity(
    $conn,
    "Changed status of {$user['fullname']} ({$user['username']}) to {$newStatus}"
);

header("Location: index.php?success=status_updated");
exit;