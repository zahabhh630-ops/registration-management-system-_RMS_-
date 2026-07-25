<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";

// Fetch all users
$stmt = $conn->prepare("SELECT * FROM users ORDER BY id DESC");
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>User Management</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body class="bg-light">

<div class="container mt-5">

<?php if (isset($_GET['success'])): ?>

    <?php if ($_GET['success'] === 'user_added'): ?>

        <div class="alert alert-success alert-dismissible fade show">
            User added successfully.
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>

    <?php elseif ($_GET['success'] === 'user_updated'): ?>

        <div class="alert alert-success alert-dismissible fade show">
            User updated successfully.
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
<?php if (isset($_GET['success']) && $_GET['success'] === 'status_updated'): ?>

<div class="alert alert-success alert-dismissible fade show">

    User status updated successfully.

    <button class="btn-close" data-bs-dismiss="alert"></button>

</div>

<?php endif; ?>
<?php elseif ($_GET['success'] === 'status_updated'): ?>

<div class="alert alert-success alert-dismissible fade show">
    User status updated successfully.
    <button class="btn-close" data-bs-dismiss="alert"></button>
</div>

<?php if (isset($_GET['error']) && $_GET['error'] === 'self_toggle'): ?>

<div class="alert alert-danger">

    You cannot change your own account status.

</div>

<?php endif; ?>
    <?php elseif ($_GET['success'] === 'user_deleted'): ?>

        <div class="alert alert-success alert-dismissible fade show">
            User deleted successfully.
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>

    <?php elseif ($_GET['success'] === 'password_reset'): ?>

        <div class="alert alert-success alert-dismissible fade show">
            Password reset successfully.
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>

    <?php endif; ?>

<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'self_delete'): ?>

<div class="alert alert-danger">
    You cannot delete your own account.
</div>

<?php endif; ?>

<?php if (isset($_GET['error']) && $_GET['error'] === 'user_not_found'): ?>

<div class="alert alert-warning">
    User not found.
</div>

<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">

<h2>

<i class="fa fa-users"></i>

User Management

</h2>

<a href="add.php" class="btn btn-success">

<i class="fa fa-user-plus"></i>

Add User

</a>

</div>

<div class="card shadow">

<div class="card-body">

<div class="table-responsive">

<table class="table table-bordered table-hover align-middle">

<thead class="table-dark">

<tr>

<th>ID</th>
<th>Full Name</th>
<th>Username</th>
<th>Email</th>
<th>Role</th>
<th>Status</th>
<th>Created</th>
<th width="220">Actions</th>

</tr>

</thead>

<tbody>

<?php if (count($users) > 0): ?>

<?php foreach ($users as $user): ?>

<tr>

<td><?= $user['id']; ?></td>

<td><?= htmlspecialchars($user['fullname']); ?></td>

<td><?= htmlspecialchars($user['username']); ?></td>

<td><?= htmlspecialchars($user['email'] ?? ''); ?></td>

<td>

<?php if ($user['role'] === "Admin"): ?>

<span class="badge bg-danger">Admin</span>

<?php else: ?>

<span class="badge bg-primary">Staff</span>

<?php endif; ?>

</td>

<td>

<?php if ($user['status'] === "Active"): ?>

    <a href="toggle.php?id=<?= $user['id']; ?>"
   class="btn btn-secondary btn-sm"
   title="Deactivate User"
   onclick="return confirm('Are you sure you want to deactivate this user?');">

    <i class="fa fa-user-slash"></i>

</a>

<?php else: ?>

<a href="toggle.php?id=<?= $user['id']; ?>"
   class="btn btn-success btn-sm"
   title="Activate User"
   onclick="return confirm('Are you sure you want to activate this user?');">

    <i class="fa fa-user-check"></i>

</a>

<?php endif; ?>

</td>

<td><?= $user['created_at']; ?></td>

<td>

<a href="edit.php?id=<?= $user['id']; ?>"
class="btn btn-warning btn-sm"
title="Edit">

<i class="fa fa-edit"></i>

</a>

<a href="reset.php?id=<?= $user['id']; ?>"
class="btn btn-info btn-sm"
title="Reset Password">

<i class="fa fa-key"></i>

</a>

<?php if ($user['status'] === "Active"): ?>

<a href="toggle.php?id=<?= $user['id']; ?>"
   class="btn btn-secondary btn-sm"
   title="Deactivate User"
   onclick="return confirm('Deactivate this user?')">

    <i class="fa fa-user-slash"></i>

</a>

<?php else: ?>

<a href="toggle.php?id=<?= $user['id']; ?>"
   class="btn btn-success btn-sm"
   title="Activate User"
   onclick="return confirm('Activate this user?')">

    <i class="fa fa-user-check"></i>

</a>

<?php endif; ?>

<?php if ($user['id'] != $_SESSION['user_id']): ?>

<a href="delete.php?id=<?= $user['id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this user?')"
title="Delete">

<i class="fa fa-trash"></i>

</a>

<?php else: ?>

<button class="btn btn-dark btn-sm" disabled>

Protected

</button>

<?php endif; ?>

</td>

</tr>

<?php endforeach; ?>

<?php else: ?>

<tr>

<td colspan="8" class="text-center">

No users found.

</td>

</tr>

<?php endif; ?>

</tbody>

</table>

</div>

</div>

</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>