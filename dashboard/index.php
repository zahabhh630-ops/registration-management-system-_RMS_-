<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../config/database.php";
// =============================
// Dashboard Statistics
// =============================

// Total Records
$totalRecords = $conn->query("
SELECT COUNT(*)
FROM form_submissions
")->fetchColumn();

// Today's Records
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

   <link rel="stylesheet" href="../assets/css/style.css">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>

<body>

<div class="dashboard-header">

    <div>
        <h1>Registration Management System</h1>
        <p>Welcome back, Administrator</p>
    </div>

   <div class="header-right">

    <div class="header-info">
        <i class="fa-solid fa-calendar-days"></i>
        <?= date("d M Y") ?>
    </div>

    <a href="../auth/logout.php" class="logout-btn">
        <i class="fa-solid fa-right-from-bracket"></i>
        Logout
    </a>

</div>

</div>


<div class="dashboard-cards">

    <div class="card">
        <i class="fas fa-users"></i>
        <h2><?= $totalRecords ?></h2>
        <p>Total Records</p>
    </div>

    <div class="card">
        <i class="fas fa-calendar-day"></i>
        <h2><?= $todayRecords ?></h2>
        <p>Today's Records</p>
    </div>

    <div class="card">
        <i class="fas fa-globe-africa"></i>
        <h2><?= $totalNationalities ?></h2>
        <p>Nationalities</p>
    </div>

    <div class="card">
        <i class="fas fa-briefcase"></i>
        <h2><?= $totalOccupations ?></h2>
        <p>Occupations</p>
    </div>

</div>
<div>
<div class="table-section">

    <h2>Latest Registrations</h2>
<table>

<thead>

<tr>
<th>ID</th>
<th>Other Name</th>
<th>Surname</th>
<th>Nationality</th>
<th>Date</th>
</tr>
</thead>

<tbody>
<?php foreach($latest as $row): ?>
</tbody>
<tr>

<td><?= $row['id_key'] ?></td>

<td><?= htmlspecialchars($row['other_name']) ?></td>

<td><?= htmlspecialchars($row['surname']) ?></td>

<td><?= htmlspecialchars($row['nationality']) ?></td>

<td><?= $row['submitted_at'] ?></td>

</tr>

<?php endforeach; ?>

</table>
</div>
</div>
</body>
</html>