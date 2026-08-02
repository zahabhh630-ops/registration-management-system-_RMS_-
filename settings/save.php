<?php

require_once "../includes/admin_only.php";
require_once "../config/database.php";
require_once "../includes/logger.php";

$stmt = $conn->prepare("

UPDATE settings

SET

system_name=?,

organization=?,

footer_text=?,

timezone=?

WHERE id=1

");

$stmt->execute([

$_POST['system_name'],

$_POST['organization'],

$_POST['footer_text'],

$_POST['timezone']

]);

logActivity(
$conn,
"Updated System Settings"
);

header("Location:index.php?success=1");

exit;