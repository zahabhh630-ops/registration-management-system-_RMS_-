<?php

require_once "../includes/admin_only.php";
require_once "../config/database.php";
require_once "../includes/logger.php";

// Check ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];

// Prevent deleting yourself
if ($id === (int)$_SESSION['user_id']) {
    header("Location: index.php?error=self_delete");
    exit;
}

// Check if user exists
$stmt = $conn->prepare("SELECT fullname, username FROM users WHERE id = ?");
$stmt->execute([$id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: index.php?error=user_not_found");
    exit;
}

// Delete user
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->execute([$id]);

// Log the action
logActivity(
    $conn,
    "Deleted user: {$user['fullname']} ({$user['username']})"
);

// Redirect
header("Location: index.php?success=user_deleted");
exit;