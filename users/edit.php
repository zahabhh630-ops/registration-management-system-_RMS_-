<?php

require_once "../includes/admin_only.php";
require_once "../config/database.php";
require_once "../includes/logger.php";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];

$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("User not found.");
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $role     = $_POST['role'];
    $status   = $_POST['status'];

    if (empty($fullname) || empty($username)) {
        $errors[] = "Full Name and Username are required.";
    }

    // Check duplicate username (excluding this user)
    $stmt = $conn->prepare(
        "SELECT id FROM users WHERE username = ? AND id != ?"
    );
    $stmt->execute([$username, $id]);

    if ($stmt->fetch()) {
        $errors[] = "Username already exists.";
    }

    // Check duplicate email (excluding this user)
    if (!empty($email)) {

        $stmt = $conn->prepare(
            "SELECT id FROM users WHERE email = ? AND id != ?"
        );
        $stmt->execute([$email, $id]);

        if ($stmt->fetch()) {
            $errors[] = "Email already exists.";
        }
    }

    if (empty($errors)) {

        $stmt = $conn->prepare("
            UPDATE users
            SET
                fullname = ?,
                username = ?,
                email = ?,
                role = ?,
                status = ?
            WHERE id = ?
        ");

        $stmt->execute([
            $fullname,
            $username,
            $email,
            $role,
            $status,
            $id
        ]);
        logActivity(

    $conn,

    "Updated user: " . $username

);
        header("Location: index.php?success=user_updated");
        exit;
    }

    // Keep edited values on validation errors
    $user['fullname'] = $fullname;
    $user['username'] = $username;
    $user['email']    = $email;
    $user['role']     = $role;
    $user['status']   = $status;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Edit User</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="row justify-content-center">

<div class="col-md-8">

<div class="card shadow">

<div class="card-header bg-warning">

<h4 class="mb-0">

<i class="fa fa-user-pen"></i>

Edit User

</h4>

</div>

<div class="card-body">

<?php if (!empty($errors)): ?>

<div class="alert alert-danger">

<ul class="mb-0">

<?php foreach ($errors as $error): ?>

<li><?= htmlspecialchars($error); ?></li>

<?php endforeach; ?>

</ul>

</div>

<?php endif; ?>

<form method="POST">

<div class="mb-3">

<label class="form-label">Full Name</label>

<input
type="text"
name="fullname"
class="form-control"
value="<?= htmlspecialchars($user['fullname']); ?>"
required>

</div>

<div class="mb-3">

<label class="form-label">Username</label>

<input
type="text"
name="username"
class="form-control"
value="<?= htmlspecialchars($user['username']); ?>"
required>

</div>

<div class="mb-3">

<label class="form-label">Email</label>

<input
type="email"
name="email"
class="form-control"
value="<?= htmlspecialchars($user['email']); ?>">

</div>

<div class="row">

<div class="col-md-6">

<label class="form-label">Role</label>

<select
name="role"
class="form-select">

<option value="Admin"
<?= $user['role']=="Admin" ? "selected" : ""; ?>>

Admin

</option>

<option value="Staff"
<?= $user['role']=="Staff" ? "selected" : ""; ?>>

Staff

</option>

</select>

</div>

<div class="col-md-6">

<label class="form-label">Status</label>

<select
name="status"
class="form-select">

<option value="Active"
<?= $user['status']=="Active" ? "selected" : ""; ?>>

Active

</option>

<option value="Inactive"
<?= $user['status']=="Inactive" ? "selected" : ""; ?>>

Inactive

</option>

</select>

</div>

</div>

<div class="mt-4">

<button
type="submit"
class="btn btn-warning">

<i class="fa fa-save"></i>

Update User

</button>

<a
href="index.php"
class="btn btn-secondary">

Cancel

</a>

</div>

</form>

</div>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>