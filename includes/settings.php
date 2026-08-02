<?php

$stmt = $conn->query("
SELECT *
FROM settings
LIMIT 1
");

$app = $stmt->fetch(PDO::FETCH_ASSOC);

date_default_timezone_set(
$app['timezone']
);