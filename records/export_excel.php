<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../includes/logger.php";

logActivity($conn, "Exported registrations to Excel");

// File name
$filename = "RMS_Registrations_" . date("Y-m-d_H-i-s") . ".csv";

// Headers
header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=\"$filename\"");

// Output stream
$output = fopen("php://output", "w");

// UTF-8 BOM for Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

// Column headings
fputcsv($output, [
    "ID",
    "Other Name",
    "Surname",
    "Address",
    "National ID",
    "Occupation",
    "Nationality",
    "Submission Date"
]);

// Fetch records
$stmt = $conn->query("
    SELECT
        id_key,
        other_name,
        surname,
        address,
        national_id,
        occupation,
        nationality,
        submitted_at
    FROM form_submissions
    ORDER BY id_key DESC
");

// Export rows
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {

    fputcsv($output, [
        $row['id_key'],
        $row['other_name'],
        $row['surname'],
        $row['address'],
        $row['national_id'],
        $row['occupation'],
        $row['nationality'],
        $row['submitted_at']
    ]);
}

fclose($output);
exit;