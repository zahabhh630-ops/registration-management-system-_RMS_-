<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
   header("Location: view.php");
exit;
}

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: view.php");
    exit;
    
}


// Database configuration
$host     = 'localhost';
$db_name  = 'form_db';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

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

    // 3. FETCH ALL SUBMISSIONS
   $search = trim($_GET['search'] ?? '');

if (!empty($search)) {

    $sql = "SELECT *
            FROM form_submissions
            WHERE other_name LIKE :search
               OR surname LIKE :search
               OR nationality LIKE :search
               OR occupation LIKE :search
               OR national_id LIKE :search
            ORDER BY id_key DESC";

    $stmt = $conn->prepare($sql);

    $keyword = "%{$search}%";

    $stmt->bindParam(':search', $keyword);

} else {

    $sql = "SELECT *
            FROM form_submissions
            ORDER BY id_key DESC";

    $stmt = $conn->prepare($sql);
}

$stmt->execute();

$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
    

} catch (PDOException $e) {
    die("Database view error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Form Submissions Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">
</head>
<body>

   <div class="header-bar">
    <h2><i class="fas fa-users"></i> Registered Submissions Dashboard</h2>
    <a href="view.php?action=logout" class="btn btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a>
</div>

<!-- Search Form -->
<form method="GET" class="search-box">

    <input
        type="text"
        name="search"
        value="<?php echo htmlspecialchars($search); ?>"
        placeholder="Search by Name, Occupation, Nationality..."
        style="padding:10px; width:350px; border:1px solid #ccc; border-radius:5px;">

    <button
        type="submit"
        style="padding:10px 15px; background:#0d6efd; color:white; border:none; border-radius:5px;">
        <i class="fas fa-search"></i> Search
    </button>

    <a href="view.php">❌ Clear</a>

</form>

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
                            <!-- Link to edit file passing ID -->
                            <a href="edit.php?id=<?php echo $row['id_key']; ?>" class="btn btn-edit"><i class="fas fa-pen"></i> Edit</a>
                            <!-- Link to delete action passing ID with javascript confirmation check -->
                            <a href="view.php?action=delete&id=<?php echo $row['id_key']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this record?');"><i class="fas fa-trash"></i> Delete</a>
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

</body>
</html>