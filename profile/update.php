<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../includes/logger.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: index.php");
    exit;
}

$fullname = trim($_POST['fullname']);
$email    = trim($_POST['email']);

$errors = [];

// Validation
if (empty($fullname)) {
    $errors[] = "Full name is required.";
}

if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email address.";
}

if (!empty($errors)) {

    $_SESSION['profile_errors'] = $errors;

    header("Location: index.php");

    exit;
}

// Update profile
$stmt = $conn->prepare("
UPDATE users
SET fullname = ?, email = ?
WHERE id = ?
");

$stmt->execute([
    $fullname,
    $email,
    $_SESSION['user_id']
]);

// Update session
$_SESSION['fullname'] = $fullname;

// Audit Log
logActivity(
    $conn,
    "Updated own profile"
);

header("Location: index.php?success=profile_updated");
exit;