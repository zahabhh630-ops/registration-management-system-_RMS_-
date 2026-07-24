<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../config/database.php";

// Total registrations
$total = $conn->query("SELECT COUNT(*) FROM form_submissions")->fetchColumn();

// Today's registrations
$today = $conn->query("
    SELECT COUNT(*)
    FROM form_submissions
    WHERE DATE(submitted_at) = CURDATE()
")->fetchColumn();

// Latest 5 registrations
$stmt = $conn->query("
    SELECT *
    FROM form_submissions
    ORDER BY submitted_at DESC
    LIMIT 5
");

$latest = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>

    <style>
        body{
            font-family:Arial;
            margin:40px;
            background:#f5f5f5;
        }

        .card{
            display:inline-block;
            width:220px;
            padding:20px;
            margin:10px;
            background:white;
            border-left:6px solid #0d6efd;
            box-shadow:0 0 10px rgba(0,0,0,.1);
        }

        table{
            width:100%;
            border-collapse:collapse;
            margin-top:30px;
            background:white;
        }

        th,td{
            border:1px solid #ddd;
            padding:12px;
        }

        th{
            background:#0d6efd;
            color:white;
        }
    </style>
</head>

<body>

<h1>Registration Management System</h1>

<div class="card">
    <h3>Total Records</h3>
    <h2><?= $total ?></h2>
</div>

<div class="card">
    <h3>Today's Records</h3>
    <h2><?= $today ?></h2>
</div>

<h2>Latest Registrations</h2>

<table>

<tr>
<th>ID</th>
<th>Other Name</th>
<th>Surname</th>
<th>Nationality</th>
<th>Date</th>
</tr>

<?php foreach($latest as $row): ?>

<tr>

<td><?= $row['id_key'] ?></td>

<td><?= htmlspecialchars($row['other_name']) ?></td>

<td><?= htmlspecialchars($row['surname']) ?></td>

<td><?= htmlspecialchars($row['nationality']) ?></td>

<td><?= $row['submitted_at'] ?></td>

</tr>

<?php endforeach; ?>

</table>

</body>
</html>