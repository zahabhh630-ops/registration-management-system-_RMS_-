<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../auth/login.php");
    exit;
}

// Handle Logout
if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    header("Location: ../auth/login.php");
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
        header("Location: ../auth/login.php");
        exit;
    }

    // 3. FETCH ALL SUBMISSIONS
    $sql = "SELECT * FROM form_submissions ORDER BY id_key DESC";
    $stmt = $conn->prepare($sql);
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
    <style>
        body { font-family: Arial, sans-serif; margin: 30px; background-color: #f9f9f9; }
        .header-bar { display: flex; justify-content: space-between; align-items: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        th, td { padding: 12px; border: 1px solid #ddd; text-align: left; }
        th { background-color: #333; color: white; }
        tr:nth-child(even) { background-color: #f2f2f2; }
        .btn { padding: 6px 12px; text-decoration: none; border-radius: 4px; color: white; font-size: 14px; }
        .btn-edit { background: #FFA500; margin-right: 5px; }
        .btn-delete { background: #f44336; }
        .btn-logout { background: #555; padding: 10px 15px; }
    </style>
</head>
<body>

    <div class="header-bar">
        <h2>Registered Submissions Dashboard</h2>
        <a href="view.php?action=logout" class="btn btn-logout">Logout</a>
    </div>

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
                            <a href="edit.php?id=<?php echo $row['id_key']; ?>" class="btn btn-edit">Edit</a>
                            <!-- Link to delete action passing ID with javascript confirmation check -->
                            <a href="view.php?action=delete&id=<?php echo $row['id_key']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure you want to delete this record?');">Delete</a>
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