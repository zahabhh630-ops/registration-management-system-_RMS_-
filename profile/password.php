<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../includes/logger.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Get current user's password
    $stmt = $conn->prepare("
        SELECT password
        FROM users
        WHERE id = ?
    ");

    $stmt->execute([$_SESSION['user_id']]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($oldPassword, $user['password'])) {

        $errors[] = "Current password is incorrect.";

    }

    if (strlen($newPassword) < 8) {

        $errors[] = "New password must be at least 8 characters.";

    }

    if ($newPassword !== $confirmPassword) {

        $errors[] = "Passwords do not match.";

    }

    if (empty($errors)) {

        $hash = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            UPDATE users
            SET password = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $hash,
            $_SESSION['user_id']
        ]);

        logActivity(
            $conn,
            "Changed own password"
        );

        header("Location: index.php?success=password_changed");
        exit;
    }
}

$_SESSION['profile_errors'] = $errors;

header("Location: index.php");

exit;