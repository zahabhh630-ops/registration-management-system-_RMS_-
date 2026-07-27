<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";

    // 2. HANDLE DELETE ACTION
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $delete_id = $_GET['id'];
        $del_sql = "DELETE FROM form_submissions WHERE id_key = :id";
        $del_stmt = $conn->prepare($del_sql);
        $del_stmt->bindParam(':id', $delete_id, PDO::PARAM_INT);
        $del_stmt->execute();
        
        // Refresh page to show updated table
        header("Location: view.php");
        exit;
    }
// Pagination starts here...
$limit = 2;
 

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

if ($page < 1) {
    $page = 1;
}

$offset = ($page - 1) * $limit;

$search = trim($_GET['search'] ?? '');

if (!empty($search)) {

    $countStmt = $conn->prepare("
        SELECT COUNT(*)
        FROM form_submissions
        WHERE other_name LIKE :search
           OR surname LIKE :search
           OR nationality LIKE :search
           OR occupation LIKE :search
           OR national_id LIKE :search
    ");

    $keyword = "%{$search}%";

    $countStmt->bindParam(':search', $keyword);
    $countStmt->execute();

    $totalRecords = $countStmt->fetchColumn();

    $stmt = $conn->prepare("
        SELECT *
        FROM form_submissions
        WHERE other_name LIKE :search
           OR surname LIKE :search
           OR nationality LIKE :search
           OR occupation LIKE :search
           OR national_id LIKE :search
        ORDER BY id_key DESC
        LIMIT :limit OFFSET :offset
    ");

    $stmt->bindParam(':search', $keyword);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

}
else {

    $totalRecords = $conn->query("
        SELECT COUNT(*)
        FROM form_submissions
    ")->fetchColumn();

    $stmt = $conn->prepare("
        SELECT *
        FROM form_submissions
        ORDER BY id_key DESC
        LIMIT :limit OFFSET :offset
    ");

    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

}

$stmt->execute();

$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalPages = ceil($totalRecords / $limit);
    

?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Submissions Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>
<?php include "../includes/sidebar.php"; ?>

<div class="main-content">
   <div class="header-bar">
    <h2><i class="fas fa-users"></i> Registered Submissions Dashboard</h2>
    <a href="view.php?action=logout" class="btn btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Search Form -->
<div class="search-box">

    <form method="GET">

        <input typetype="text"
    name="search"
    class="form-control"
    placeholder="Search by name, nationality, occupation..."
    value="<?= htmlspecialchars($search) ?>">

        <button type="submit">
            <i class="fas fa-search"></i>
            Search
        </button>

        <a href="view.php">Clear</a>

        <a href="export_pdf.php" class="btn">
            <i class="fas fa-file-pdf"></i>
            Export PDF
        </a>
        <a href="add.php" class="btn btn-success">
    <i class="fas fa-plus"></i> Add Registration
</a>
<a href="export_excel.php" class="btn btn-success">
    <i class="fas fa-file-excel"></i> Export Excel
</a>

    </form>

</div>
<?php if (isset($_GET['success']) && $_GET['success'] == 'record_deleted'): ?>

<div class="alert alert-success alert-dismissible fade show">
    Record deleted successfully.
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>

<?php endif; ?>
<?php if (isset($_GET['error']) && $_GET['error'] == 'record_not_found'): ?>

<div class="alert alert-danger alert-dismissible fade show">
    Record not found.
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>

<?php endif; ?>
<?php if (isset($_GET['success']) && $_GET['success'] === 'record_deleted'): ?>

<div class="alert alert-success alert-dismissible fade show">
    Record deleted successfully.
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>

<?php endif; ?>
<?php if (isset($_GET['error']) && $_GET['error'] === 'record_not_found'): ?>

<div class="alert alert-danger alert-dismissible fade show">
    Record not found.
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>

<?php endif; ?>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Other Name</th>
                <th>Surname</th>
                <th>Address</th>
                <th>National ID</th>
                <th>Occupation</th>
                <th>Nationality</th>
                <th>Submission Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($submissions) > 0): ?>
                <?php foreach ($submissions as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id_key']); ?></td>
                        <td><?php echo htmlspecialchars($row['other_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['surname']); ?></td>
                        <td><?php echo htmlspecialchars($row['address']); ?></td>
                        <td><?php echo htmlspecialchars($row['national_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['occupation']); ?></td>
                        <td><?php echo htmlspecialchars($row['nationality']); ?></td>
                        <td><?php echo htmlspecialchars($row['submitted_at']); ?></td>
                        
                          <td>

<div class="action-buttons">

<a href="view_record.php?id=<?= $row['id_key']; ?>" class="btn-view">
    <i class="fas fa-eye"></i> View
</a>

<a href="edit.php?id=<?= $row['id_key']; ?>" class="btn-edit">
<i class="fas fa-pen"></i> Edit
</a>

<a href="delete.php?id=<?= $row['id_key']; ?>"
class="btn-delete"
onclick="return confirm('Delete this record?');">
<i class="fas fa-trash"></i> Delete
</a>

</div>

</td>
                        
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="9" style="text-align: center;">No records found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
    <div class="d-flex justify-content-between align-items-center mt-3">

    <div>
        Showing page <strong><?= $page ?></strong>
        of <strong><?= $totalPages ?></strong>
        (<?= $totalRecords ?> records)
    </div>

    <nav>

        <ul class="pagination mb-0">

            <?php if ($page > 1): ?>

                <li class="page-item">

                    <a class="page-link"
                       href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>">

                        Previous

                    </a>

                </li>

            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>

                <li class="page-item <?= $i == $page ? 'active' : '' ?>">

                    <a class="page-link"
                       href="?page=<?= $i ?>&search=<?= urlencode($search) ?>">

                        <?= $i ?>

                    </a>

                </li>

            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>

                <li class="page-item">

                    <a class="page-link"
                       href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>">

                        Next

                    </a>

                </li>

            <?php endif; ?>

        </ul>

    </nav>

</div>
</div>
</body>
</html>