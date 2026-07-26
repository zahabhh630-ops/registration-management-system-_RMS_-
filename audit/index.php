<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";
$limit = 10;

$page = isset($_GET['page'])
    ? max(1, (int)$_GET['page'])
    : 1;

$offset = ($page - 1) * $limit;

$search = trim($_GET['search'] ?? '');
$date = trim($_GET['date'] ?? '');

$where = " WHERE 1 ";
$params = [];

if ($search != "") {

    $where .= " AND (fullname LIKE ? OR action LIKE ?)";

    $keyword = "%{$search}%";

    $params[] = $keyword;
    $params[] = $keyword;
}

if ($date != "") {

    $where .= " AND DATE(created_at)=?";

    $params[] = $date;
}

$countStmt = $conn->prepare("
SELECT COUNT(*)
FROM audit_logs
$where
");

$countStmt->execute($params);

$totalRecords = $countStmt->fetchColumn();

$totalPages = ceil($totalRecords / $limit);

$sql = "
SELECT *
FROM audit_logs
$where
ORDER BY created_at DESC
LIMIT $limit
OFFSET $offset
";

$stmt = $conn->prepare($sql);

$stmt->execute($params);

$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Audit Logs</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body class="bg-light">

<div class="container mt-5">

<h2 class="mb-4">

<i class="fa fa-history"></i>

Audit Logs

</h2>

<div class="card shadow">

<div class="card-body">

<div class="table-responsive">

<table class="table table-striped table-hover">

<thead class="table-dark">

<tr>

<th>ID</th>

<th>User</th>

<th>Action</th>

<th>IP Address</th>

<th>Date & Time</th>

</tr>

</thead>
<form method="GET" class="row g-2 mb-3">

    <div class="col-md-5">

        <input
            type="text"
            name="search"
            class="form-control"
            placeholder="Search user or action..."
            value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">

    </div>

    <div class="col-md-3">

        <input
            type="date"
            name="date"
            class="form-control"
            value="<?= htmlspecialchars($_GET['date'] ?? '') ?>">

    </div>

    <div class="col-md-4">

        <button class="btn btn-primary">

            <i class="fa fa-search"></i>

            Search

        </button>

        <a
            href="index.php"
            class="btn btn-secondary">

            Reset

        </a>

    </div>

</form>
<tbody>

<?php foreach($logs as $log): ?>

<tr>

<td><?= $log['id']; ?></td>

<td><?= htmlspecialchars($log['fullname']); ?></td>

<td><?= htmlspecialchars($log['action']); ?></td>

<td><?= htmlspecialchars($log['ip_address']); ?></td>

<td><?= $log['created_at']; ?></td>

</tr>

<?php endforeach; ?>

</tbody>

</table>

<nav>

<ul class="pagination justify-content-center">

<?php if($page > 1): ?>

<li class="page-item">

<a class="page-link"
href="?page=<?= $page-1 ?>&search=<?= urlencode($search) ?>&date=<?= urlencode($date) ?>">

Previous

</a>

</li>

<?php endif; ?>

<?php for($i=1;$i<=$totalPages;$i++): ?>

<li class="page-item <?= $i==$page ? 'active' : '' ?>">

<a class="page-link"
href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&date=<?= urlencode($date) ?>">

<?= $i ?>

</a>

</li>

<?php endfor; ?>

<?php if($page < $totalPages): ?>

<li class="page-item">

<a class="page-link"
href="?page=<?= $page+1 ?>&search=<?= urlencode($search) ?>&date=<?= urlencode($date) ?>">

Next

</a>

</li>

<?php endif; ?>

</ul>

</nav>
</div>

</div>

</div>

</div>

</body>

</html>