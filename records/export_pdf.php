<?php
require_once "../includes/auth_check.php";
require_once "../config/database.php";
require_once "../vendor/autoload.php";

use Dompdf\Dompdf;
use Dompdf\Options;

// Fetch all records
$stmt = $conn->query("
    SELECT *
    FROM form_submissions
    ORDER BY submitted_at DESC
");

$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Configure Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);

$dompdf = new Dompdf($options);

// Build HTML
$html = '
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">

<style>

body{
    font-family: DejaVu Sans, sans-serif;
    font-size:12px;
}

h2{
    text-align:center;
    margin-bottom:5px;
}

p{
    text-align:center;
    margin-top:0;
}

table{
    width:100%;
    border-collapse:collapse;
}

table,th,td{
    border:1px solid #000;
}

th{
    background:#0B1F3A;
    color:white;
    padding:8px;
}

td{
    padding:6px;
}

</style>

</head>

<body>

<h2>Registration Management System</h2>

<p>
Generated on '.date("d M Y H:i").'
</p>

<table>

<tr>

<th>ID</th>
<th>Other Name</th>
<th>Surname</th>
<th>Occupation</th>
<th>Nationality</th>
<th>Date</th>

</tr>';
foreach($records as $row){

$html .= '

<tr>

<td>'.$row['id_key'].'</td>

<td>'.htmlspecialchars($row['other_name']).'</td>

<td>'.htmlspecialchars($row['surname']).'</td>

<td>'.htmlspecialchars($row['occupation']).'</td>

<td>'.htmlspecialchars($row['nationality']).'</td>

<td>'.$row['submitted_at'].'</td>

</tr>

';

}

$html .= '

</table>

</body>

</html>

';

$dompdf->loadHtml($html);

$dompdf->setPaper('A4','landscape');

$dompdf->render();

$dompdf->stream(
    "Registered_Submissions.pdf",
    ["Attachment" => true]
);