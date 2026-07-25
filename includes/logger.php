<?php


function logActivity(\PDO $conn, string $action): void
{
    $stmt = $conn->prepare("
        INSERT INTO audit_logs
        (user_id, fullname, action, ip_address)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $_SESSION['user_id'],
        $_SESSION['fullname'],
        $action,
        $_SERVER['REMOTE_ADDR']
    ]);
}