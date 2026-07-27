<?php
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../auth/login.php");
    exit;
}

$host     = 'localhost';
$db_name  = 'form_db';
$username = 'root';
$password = '';

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name;charset=utf8mb4", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get the record ID from the browser address bar url
    if (!isset($_GET['id'])) {
        header("Location: ../auth/login.php");
        exit;
    }
    $id = $_GET['id'];

    // 1. HANDLE UPDATE FORM SUBMISSION
    if (isset($_POST['update'])) {
        $other_name   = $_POST['other_name'] ?? '';
        $surname      = $_POST['surname'] ?? '';
        $address      = $_POST['address'] ?? '';
        $national_id  = $_POST['national_id'] ?? '';
        $occupation   = $_POST['occupation'] ?? '';
        $nationality  = $_POST['nationality'] ?? '';

        $sql = "UPDATE form_submissions 
                SET other_name = :other_name, surname = :surname, address = :address, national_id = :national_id, occupation = :occupation, nationality = :nationality 
                WHERE id_key = :id";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':other_name', $other_name);
        $stmt->bindParam(':surname', $surname);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':national_id', $national_id);
        $stmt->bindParam(':occupation', $occupation);
        $stmt->bindParam(':nationality', $nationality);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            header("Location: ../auth/login.php"); // Bounce back to view dashboard upon success
            exit;
        }
    }

    // 2. FETCH ORIGINAL CURRENT VALUES TO POPULATE THE INPUTS
    $fetch_sql = "SELECT * FROM form_submissions WHERE id_key = :id";
    $fetch_stmt = $conn->prepare($fetch_sql);
    $fetch_stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $fetch_stmt->execute();
    $row = $fetch_stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        die("Record not found.");
    }

} catch (PDOException $e) {
    die("Database edit error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Record</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 40px; background: #f4f4f4; }
        .form-container { background: white; padding: 25px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); max-width: 400px; }
        input[type="text"] { width: 100%; padding: 8px; margin: 8px 0 15px 0; box-sizing: border-box; }
        .btn-save { background: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; }
        .btn-cancel { background: #aaa; color: white; padding: 10px 15px; text-decoration: none; border-radius: 4px; margin-left: 10px; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Edit Registration Entry</h2>
    <form method="post" action="">
        <!-- Optional field -->
        <label>Other Name:</label>
        <input type="text" name="other_name" value="<?php echo htmlspecialchars($row['other_name']); ?>">

        <!-- Required fields (added 'required' attribute) -->
        <label>Surname *</label>
        <input type="text" name="surname" value="<?php echo htmlspecialchars($row['surname']); ?>" required>

        <label>Address *</label>
        <input type="text" name="address" value="<?php echo htmlspecialchars($row['address']); ?>" required>

        <label>ID *</label>
        <input type="text" name="national_id" value="<?php echo htmlspecialchars($row['national_id']); ?>" required>

        <!-- Optional fields -->
        <label>Occupation:</label>
        <input type="text" name="occupation" value="<?php echo htmlspecialchars($row['occupation']); ?>">

        <label>Nationality:</label>
        <input type="text" name="nationality" value="<?php echo htmlspecialchars($row['nationality']); ?>">

        <button type="submit" name="update" class="btn-save">Save Changes</button>
        <a href="view.php" class="btn-cancel">Cancel</a>
    </form>
</div>
</body>
</html>