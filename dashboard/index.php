<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";

$hour = date("H");

if ($hour < 12) {
    $greeting = "Good Morning";
} elseif ($hour < 17) {
    $greeting = "Good Afternoon";
} else {
    $greeting = "Good Evening";
}

// ==============================
// Registrations by Nationality
// ==============================

$stmt = $conn->query("
    SELECT nationality, COUNT(*) AS total
    FROM form_submissions
    GROUP BY nationality
    ORDER BY total DESC
");

$nationalityLabels = [];
$nationalityTotals = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $nationalityLabels[] = $row['nationality'];
    $nationalityTotals[] = $row['total'];
}

// ==============================
// Registrations by Occupation
// ==============================

$stmt = $conn->query("
    SELECT occupation, COUNT(*) AS total
    FROM form_submissions
    WHERE occupation <> ''
    GROUP BY occupation
    ORDER BY total DESC
");

$occupationLabels = [];
$occupationTotals = [];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $occupationLabels[] = $row['occupation'];
    $occupationTotals[] = $row['total'];
}
// ==========================
// Dashboard Statistics
// ==========================

// Users
$totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetchColumn();

$activeUsers = $conn->query("
    SELECT COUNT(*)
    FROM users
    WHERE status='Active'
")->fetchColumn();

$inactiveUsers = $conn->query("
    SELECT COUNT(*)
    FROM users
    WHERE status='Inactive'
")->fetchColumn();

// Records
$totalRecords = $conn->query("
    SELECT COUNT(*)
    FROM form_submissions
")->fetchColumn();

$todayRecords = $conn->query("
    SELECT COUNT(*)
    FROM form_submissions
    WHERE DATE(submitted_at)=CURDATE()
")->fetchColumn();

// Nationalities
$totalNationalities = $conn->query("
    SELECT COUNT(DISTINCT nationality)
    FROM form_submissions
    WHERE nationality <> ''
")->fetchColumn();

// Occupations
$totalOccupations = $conn->query("
    SELECT COUNT(DISTINCT occupation)
    FROM form_submissions
    WHERE occupation <> ''
")->fetchColumn();

// Latest Registrations
$stmt = $conn->query("
    SELECT *
    FROM form_submissions
    ORDER BY submitted_at DESC
    LIMIT 5
");

$latest = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Recent Activities
$stmt = $conn->query("
    SELECT *
    FROM audit_logs
    ORDER BY created_at DESC
    LIMIT 5
");

$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Dashboard</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{
    background:#f5f7fa;
}

.card{
    border:none;
    border-radius:15px;
    transition:.3s;
}

.card:hover{
    transform:translateY(-5px);
    box-shadow:0 12px 25px rgba(0,0,0,.15);
}

.dashboard-title{
    font-weight:bold;
}

</style>

</head>

<body>

<div class="container-fluid p-4">

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h2>

<?= htmlspecialchars($app['system_name']) ?>

</h2>

<p>

<?= htmlspecialchars($app['organization']) ?>

</p>

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h2>

<i class="fas fa-chart-line text-primary"></i>

Registration Management System

</h2>

<p class="text-muted">

Welcome back,

<strong><?= htmlspecialchars($_SESSION['fullname']) ?></strong>

</p>

</div>

<div>

<span class="badge bg-success fs-6">

System Online

</span>

</div>

</div>
</p>

</div>

<div>

<span class="badge bg-dark p-3">

<?= date("l, d F Y") ?>

</span>

</div>

</div>

<!-- Statistics -->

<div class="row g-4">

<div class="col-lg-2 col-md-4">

<div class="card shadow-sm">

<div class="card-body text-center">

<i class="fas fa-users fa-2x text-primary mb-2"></i>

<h3><?= $totalUsers ?></h3>

<p class="mb-0">Total Users</p>

</div>

</div>

</div>

<div class="col-lg-2 col-md-4">

<div class="card shadow-sm">

<div class="card-body text-center">

<i class="fas fa-user-check fa-2x text-success mb-2"></i>

<h3><?= $activeUsers ?></h3>

<p class="mb-0">Active Users</p>

</div>

</div>

</div>

<div class="col-lg-2 col-md-4">

<div class="card shadow-sm">

<div class="card-body text-center">

<i class="fas fa-user-slash fa-2x text-danger mb-2"></i>

<h3><?= $inactiveUsers ?></h3>

<p class="mb-0">Inactive Users</p>

</div>

</div>

</div>

<div class="col-lg-2 col-md-4">

<div class="card shadow-sm">

<div class="card-body text-center">

<i class="fas fa-folder-open fa-2x text-warning mb-2"></i>

<h3><?= $totalRecords ?></h3>

<p class="mb-0">Records</p>

</div>

</div>

</div>

<div class="col-lg-2 col-md-4">

<div class="card shadow-sm">

<div class="card-body text-center">

<i class="fas fa-calendar-day fa-2x text-info mb-2"></i>

<h3><?= $todayRecords ?></h3>

<p class="mb-0">Today's Records</p>

</div>

</div>

</div>

<div class="col-lg-2 col-md-4">

<div class="card shadow-sm">

<div class="card-body text-center">

<i class="fas fa-globe fa-2x text-secondary mb-2"></i>

<h3><?= $totalNationalities ?></h3>

<p class="mb-0">Nationalities</p>

</div>

</div>

</div>

</div>

<!-- Quick Actions -->

<div class="card shadow mt-4">

<div class="card-header bg-dark text-white">

<h5 class="mb-0">

<i class="fa fa-bolt"></i>

Quick Actions

</h5>

</div>

<div class="card-body text-center">

<a href="../records/add.php" class="btn btn-primary rounded-pill px-4 m-2">

<i class="fa fa-plus-circle"></i>

Add Record

</a>

<a href="../users/add.php" class="btn btn-success rounded-pill px-4 m-2">

<i class="fa fa-user-plus"></i>

Add User

</a>

<a href="../users/index.php" class="btn btn-warning rounded-pill px-4 m-2">

<i class="fa fa-users"></i>

Users

</a>

<a href="../audit/index.php" class="btn btn-dark rounded-pill px-4 m-2">

<i class="fa fa-history"></i>

Audit Logs

</a>

<a href="../records/export_excel.php" class="btn btn-success rounded-pill px-4 m-2">

<i class="fa fa-file-excel"></i>

Excel

</a>

<a href="../records/export_pdf.php" class="btn btn-danger rounded-pill px-4 m-2">

<i class="fa fa-file-pdf"></i>

PDF

</a>

</div>

</div>
<div class="row mt-4">

    <!-- Nationality Chart -->

    <div class="col-lg-6">

        <div class="card shadow">

            <div class="card-header bg-primary text-white">

                <h5 class="mb-0">

                    <i class="fa fa-globe-africa"></i>

                    Registrations by Nationality

                </h5>

            </div>

            <div class="card-body">

                <canvas id="nationalityChart"></canvas>

            </div>

        </div>

    </div>

    <!-- Occupation Chart -->

    <div class="col-lg-6">

        <div class="card shadow">

            <div class="card-header bg-success text-white">

                <h5 class="mb-0">

                    <i class="fa fa-briefcase"></i>

                    Occupation Distribution

                </h5>

            </div>

            <div class="card-body">

                <canvas id="occupationChart"></canvas>

            </div>

        </div>

    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const nationalityChart =
document.getElementById('nationalityChart');

new Chart(nationalityChart,{

    type:'pie',

    data:{

        labels: <?= json_encode($nationalityLabels) ?>,

        datasets:[{

            data: <?= json_encode($nationalityTotals) ?>,

            backgroundColor:[
                "#0d6efd",
                "#198754",
                "#ffc107",
                "#dc3545",
                "#6f42c1",
                "#20c997",
                "#fd7e14",
                "#6610f2"
            ]

        }]

    },

    options:{

        responsive:true,

        plugins:{

            legend:{

                position:'bottom'

            }

        }

    }

});

const occupationChart =
document.getElementById('occupationChart');

new Chart(occupationChart,{

    type:'bar',

    data:{

        labels: <?= json_encode($occupationLabels) ?>,

        datasets:[{

            label:'Registrations',

            data: <?= json_encode($occupationTotals) ?>,

            backgroundColor:"#0B5ED7"

        }]

    },

    options:{

        responsive:true,

        plugins:{

            legend:{

                display:false

            }

        },

        scales:{

            y:{

                beginAtZero:true,

                ticks:{

                    precision:0

                }

            }

        }

    }

});

</script>
<div class="row mt-4">

<div class="col-lg-6">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h5 class="mb-0">

<i class="fa fa-user-plus"></i>

Latest Registrations

</h5>

</div>

<div class="card-body">

<table class="table table-hover align-middle table-striped">

<thead>

<tr>

<th>Name</th>

<th>Nationality</th>

<th>Date</th>

</tr>

</thead>

<tbody>

<?php foreach($latest as $row): ?>

<tr>

<td>

<?= htmlspecialchars($row['surname']." ".$row['other_name']) ?>

</td>

<td>

<?= htmlspecialchars($row['nationality']) ?>

</td>

<td>

<?= date("d M Y",strtotime($row['submitted_at'])) ?>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>

</div><div class="col-lg-6">

<div class="card shadow">

<div class="card-header bg-dark text-white">

<h5 class="mb-0">

<i class="fa fa-history"></i>

Recent Activities

</h5>

</div>

<div class="card-body">

<table class="table table-hover align-middle table-striped">

<thead>

<tr>

<th>User</th>

<th>Action</th>

<th>Date</th>

</tr>

</thead>

<tbody>

<?php foreach($activities as $log): ?>

<tr>

<td>

<?= htmlspecialchars($log['fullname']) ?>

</td>

<td>

<?= htmlspecialchars($log['action']) ?>

</td>

<td>

<?= date("d M Y H:i",strtotime($log['created_at'])) ?>

</td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

</div>

</div>

</div>

</div>
<div class="card shadow mt-4">

<div class="card-header bg-secondary text-white">

<h5 class="mb-0">

<i class="fa fa-server"></i>

System Information

</h5>

</div>

<div class="card-body">

<div class="row">

<div class="col-md-3">

<strong>Logged User</strong>

<br>

<?= htmlspecialchars($_SESSION['fullname']) ?>

</div>

<div class="col-md-3">

<strong>Role</strong>

<br>

<?= htmlspecialchars($_SESSION['role']) ?>

</div>

<div class="col-md-3">

<strong>PHP Version</strong>

<br>

<?= phpversion() ?>

</div>

<div class="col-md-3">

<strong>Date</strong>

<br>

<?= date("d M Y H:i") ?>

</div>

</div>

</div>

</div>
<hr>

<div class="text-center text-muted">

Registration Management System (RMS)

<br>

Version 1.0

<br>

© <?= date("Y") ?>

</div>