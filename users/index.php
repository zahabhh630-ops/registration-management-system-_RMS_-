<?php
require_once "../includes/auth_check.php";
require_once "../config/database.php";

$stmt=$conn->prepare("SELECT * FROM users ORDER BY id DESC");
$stmt->execute();
$users=$stmt->fetchAll(PDO::FETCH_ASSOC);

$successMessages=[
'user_added'=>'User added successfully.',
'user_updated'=>'User updated successfully.',
'user_deleted'=>'User deleted successfully.',
'password_reset'=>'Password reset successfully.',
'status_updated'=>'User status updated successfully.'
];
$errorMessages=[
'self_delete'=>'You cannot delete your own account.',
'self_toggle'=>'You cannot change your own account status.',
'user_not_found'=>'User not found.'
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>User Management</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-light">
<div class="container mt-5">

<?php if(isset($_GET['success']) && isset($successMessages[$_GET['success']])): ?>
<div class="alert alert-success alert-dismissible fade show">
<?= htmlspecialchars($successMessages[$_GET['success']]) ?>
<button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<?php if(isset($_GET['error']) && isset($errorMessages[$_GET['error']])): ?>
<div class="alert alert-danger alert-dismissible fade show">
<?= htmlspecialchars($errorMessages[$_GET['error']]) ?>
<button class="btn-close" data-bs-dismiss="alert"></button>
</div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
<h2><i class="fa fa-users"></i> User Management</h2>
<a href="add.php" class="btn btn-success"><i class="fa fa-user-plus"></i> Add User</a>
</div>

<div class="card shadow">
<div class="card-body">
<div class="table-responsive">
<table class="table table-bordered table-hover align-middle">
<thead class="table-dark">
<tr>
<th>ID</th><th>Full Name</th><th>Username</th><th>Email</th><th>Role</th><th>Status</th><th>Created</th><th>Actions</th>
</tr>
</thead>
<tbody>
<?php foreach($users as $user): ?>
<tr>
<td><?= $user['id'] ?></td>
<td><?= htmlspecialchars($user['fullname']) ?></td>
<td><?= htmlspecialchars($user['username']) ?></td>
<td><?= htmlspecialchars($user['email']) ?></td>
<td><?= $user['role']=="Admin" ? '<span class="badge bg-danger">Admin</span>' : '<span class="badge bg-primary">Staff</span>' ?></td>
<td><?= $user['status']=="Active" ? '<span class="badge bg-success">Active</span>' : '<span class="badge bg-secondary">Inactive</span>' ?></td>
<td><?= $user['created_at'] ?></td>
<td>
<a href="edit.php?id=<?= $user['id'] ?>" class="btn btn-warning btn-sm"><i class="fa fa-edit"></i></a>
<a href="reset.php?id=<?= $user['id'] ?>" class="btn btn-info btn-sm"><i class="fa fa-key"></i></a>
<?php if($user['id']!=$_SESSION['user_id']): ?>
<a href="toggle.php?id=<?= $user['id'] ?>" class="btn btn-sm <?= $user['status']=='Active'?'btn-secondary':'btn-success' ?>" onclick="return confirm('<?= $user['status']=='Active'?'Deactivate':'Activate' ?> this user?')">
<i class="fa <?= $user['status']=='Active'?'fa-user-slash':'fa-user-check' ?>"></i>
</a>
<a href="delete.php?id=<?= $user['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this user?')"><i class="fa fa-trash"></i></a>
<?php else: ?>
<button class="btn btn-dark btn-sm" disabled>Protected</button>
<?php endif; ?>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div></div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>