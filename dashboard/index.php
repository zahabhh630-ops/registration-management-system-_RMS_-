<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";

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

</style>

</head>

<body>

<div class="container-fluid p-4">

<h2>

<i class="fa fa-chart-line"></i>

Dashboard

</h2>

<p class="text-muted">

Welcome back,

<strong><?= htmlspecialchars($_SESSION['fullname']) ?></strong>

</p>
<div class="row g-4">

<div class="col-md-3">

<div class="card bg-primary text-white shadow">

<div class="card-body">

<h6>Total Users</h6>

<h2><?= $totalUsers ?></h2>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card bg-success text-white shadow">

<div class="card-body">

<h6>Active Users</h6>

<h2><?= $activeUsers ?></h2>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card bg-secondary text-white shadow">

<div class="card-body">

<h6>Inactive Users</h6>

<h2><?= $inactiveUsers ?></h2>

</div>

</div>

</div>

<div class="col-md-3">

<div class="card bg-warning shadow">

<div class="card-body">

<h6>Total Records</h6>

<h2><?= $totalRecords ?></h2>

</div>

</div>

</div>

</div>
<div class="row g-4 mt-1">

<div class="col-md-4">

<div class="card">

<div class="card-body">

<h6>Today's Registrations</h6>

<h2><?= $todayRecords ?></h2>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card">

<div class="card-body">

<h6>Nationalities</h6>

<h2><?= $totalNationalities ?></h2>

</div>

</div>

</div>

<div class="col-md-4">

<div class="card">

<div class="card-body">

<h6>Occupations</h6>

<h2><?= $totalOccupations ?></h2>


</div>

</div>

</div>

</div>
</div>
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

                <table class="table table-hover">

                    <thead>

                        <tr>

                            <th>Name</th>

                            <th>Occupation</th>

                            <th>Date</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($latest as $row): ?>

                        <tr>

                            <td><?= htmlspecialchars($row['surname'] . " " . $row['other_name']) ?></td>

                            <td><?= htmlspecialchars($row['occupation']) ?></td>

                            <td><?= date("d M Y", strtotime($row['submitted_at'])) ?></td>

                        </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>
        <div class="col-lg-6">

        <div class="card shadow">

            <div class="card-header bg-dark text-white">

                <h5 class="mb-0">

                    <i class="fa fa-history"></i>

                    Recent Activities

                </h5>

            </div>

            <div class="card-body">

                <table class="table table-hover">

                    <thead>

                        <tr>

                            <th>User</th>

                            <th>Activity</th>

                            <th>Date</th>

                        </tr>

                    </thead>

                    <tbody>

                        <?php foreach ($activities as $log): ?>

                        <tr>

                            <td><?= htmlspecialchars($log['fullname']) ?></td>

                            <td><?= htmlspecialchars($log['action']) ?></td>

                            <td><?= date("d M Y H:i", strtotime($log['created_at'])) ?></td>

                        </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

<div class="card shadow mt-4">

    <div class="card-header bg-success text-white">

        <h5 class="mb-0">

            <i class="fa fa-bolt"></i>

            Quick Actions

        </h5>

    </div>

    <div class="card-body text-center">

        <a href="../users/add.php" class="btn btn-success m-2">

            <i class="fa fa-user-plus"></i>

            Add User

        </a>

        <a href="../records/add.php" class="btn btn-primary m-2">

            <i class="fa fa-file-circle-plus"></i>

            Add Record

        </a>

        <a href="../users/index.php" class="btn btn-warning m-2">

            <i class="fa fa-users"></i>

            User Management

        </a>

        <a href="../audit/index.php" class="btn btn-dark m-2">

            <i class="fa fa-history"></i>

            Audit Logs

        </a>

    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</body>

</html>