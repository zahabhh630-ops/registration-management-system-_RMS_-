<?php

require_once "../includes/admin_only.php";
require_once "../includes/logger.php";
require_once "../config/database.php";

$database = "form_db";

$date = date("Y-m-d_H-i-s");

$filename = "RMS_Backup_" . $date . ".sql";

// Adjust this path if your MySQL installation is elsewhere.
$mysqldump = "C:\\xampp\\mysql\\bin\\mysqldump.exe";

$command = "\"$mysqldump\" -u root $database > \"$filename\"";

exec($command);

if (file_exists($filename)) {

    logActivity(
        $conn,
        "Downloaded database backup"
    );

    header("Content-Type: application/sql");
    header("Content-Disposition: attachment; filename=\"$filename\"");
    readfile($filename);

    unlink($filename);

    exit;
}

die("Backup failed.");