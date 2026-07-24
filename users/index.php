<?php

require_once "../includes/auth_check.php";
require_once "../config/database.php";

$stmt = $conn->prepare("
    SELECT *
    FROM users
    ORDER BY id DESC
");

$stmt->execute();

$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>User Management</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css">

</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h2>
            <i class="fas fa-users"></i>
            User Management
        </h2>

        <a href="add.php" class="btn btn-success">

            <i class="fas fa-user-plus"></i>

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

                    <?php foreach ($users as $user) : ?>

                        <tr>

                            <td><?= $user['id']; ?></td>

                            <td><?= htmlspecialchars($user['fullname']); ?></td>

                            <td><?= htmlspecialchars($user['username']); ?></td>

                            <td><?= htmlspecialchars($user['email']); ?></td>

                            <td>

                                <?php if ($user['role'] == 'Admin') : ?>

                                    <span class="badge bg-danger">
                                        Admin
                                    </span>

                                <?php else : ?>

                                    <span class="badge bg-primary">
                                        Staff
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <?php if ($user['status'] == 'Active') : ?>

                                    <span class="badge bg-success">
                                        Active
                                    </span>

                                <?php else : ?>

                                    <span class="badge bg-secondary">
                                        Inactive
                                    </span>

                                <?php endif; ?>

                            </td>

                            <td>

                                <?= $user['created_at']; ?>

                            </td>

                            <td>

                                <a href="edit.php?id=<?= $user['id']; ?>"
                                   class="btn btn-warning btn-sm">

                                    <i class="fas fa-edit"></i>

                                </a>

                                <a href="reset.php?id=<?= $user['id']; ?>"
                                   class="btn btn-info btn-sm">

                                    <i class="fas fa-key"></i>

                                </a>

                                <a href="toggle.php?id=<?= $user['id']; ?>"
                                   class="btn btn-secondary btn-sm">

                                    <i class="fas fa-sync"></i>

                                </a>

                                <?php if ($user['id'] != $_SESSION['user_id']) : ?>

                                    <a href="delete.php?id=<?= $user['id']; ?>"
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Delete this user?');">

                                        <i class="fas fa-trash"></i>

                                    </a>

                                <?php else : ?>

                                    <button class="btn btn-dark btn-sm"
                                            disabled>

                                        Protected

                                    </button>

                                <?php endif; ?>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

</body>

</html>