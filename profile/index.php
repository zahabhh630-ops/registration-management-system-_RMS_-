<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";

$stmt = $conn->prepare("
SELECT *
FROM users
WHERE id=?
");

$stmt->execute([$_SESSION['user_id']]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);


?>
<?php if(isset($_GET['success']) && $_GET['success']=="password_changed"): ?>

<div class="alert alert-success">

Password changed successfully.

</div>

<?php endif; ?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>My Profile</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body class="bg-light">
<?php

if(isset($_SESSION['profile_errors'])){

echo '<div class="container mt-3">';

echo '<div class="alert alert-danger">';

foreach($_SESSION['profile_errors'] as $error){

echo htmlspecialchars($error)."<br>";

}

echo '</div>';

echo '</div>';

unset($_SESSION['profile_errors']);

}

?>

<?php if(isset($_GET['success']) && $_GET['success']=="profile_updated"): ?>

<div class="container mt-3">

<div class="alert alert-success">

<i class="fas fa-check-circle"></i>

Profile updated successfully.

</div>

</div>

<?php endif; ?>

<?php if(isset($_GET['success']) && $_GET['success']=="password_changed"): ?>

<div class="container mt-3">

<div class="alert alert-success">

<i class="fas fa-key"></i>

Password changed successfully.

</div>

</div>

<?php endif; ?>
<div class="container py-5">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card shadow">

<div class="card-header bg-primary text-white">

<h4>

<i class="fas fa-user-circle"></i>

My Profile

</h4>

</div>

<div class="card-body">

<form method="post" action="update.php">

<div class="mb-3">

<label>Full Name</label>

<input
type="text"
name="fullname"
class="form-control"
value="<?= htmlspecialchars($user['fullname']) ?>"
required>

</div>

<div class="mb-3">

<label>Username</label>

<input
type="text"
class="form-control"
value="<?= htmlspecialchars($user['username']) ?>"
readonly>

</div>

<div class="mb-3">

<label>Email</label>

<input
type="email"
name="email"
class="form-control"
value="<?= htmlspecialchars($user['email']) ?>">

</div>

<div class="row">

<div class="col-md-6">

<label>Role</label>

<input
type="text"
class="form-control"
value="<?= htmlspecialchars($user['role']) ?>"
readonly>

</div>

<div class="col-md-6">

<label>Status</label>

<input
type="text"
class="form-control"
value="<?= htmlspecialchars($user['status']) ?>"
readonly>

</div>

</div>

<br>

<button class="btn btn-primary">

<i class="fas fa-save"></i>

Update Profile

</button>

<a href="../dashboard/index.php"
class="btn btn-secondary">

Dashboard

</a>

</form>

<hr>

<h5>

Change Password

</h5>

<form method="post" action="password.php">

<div class="mb-3">

<input
type="password"
name="old_password"
class="form-control"
placeholder="Current Password"
required>

</div>

<div class="mb-3">

<input
type="password"
name="new_password"
class="form-control"
placeholder="New Password"
required>

</div>

<div class="mb-3">

<input
type="password"
name="confirm_password"
class="form-control"
placeholder="Confirm Password"
required>

</div>

<button class="btn btn-success">

<i class="fas fa-key"></i>

Change Password

</button>

</form>

</div>

</div>

</div>

</div>

</div>

</body>

</html>