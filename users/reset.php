<?php
require_once "../includes/admin_only.php";
require_once "../config/database.php";
require_once "../includes/logger.php";

// Validate ID
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = (int) $_GET['id'];

// Get user
$stmt = $conn->prepare("
    SELECT id, fullname, username
    FROM users
    WHERE id = ?
");

$stmt->execute([$id]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: index.php?error=user_not_found");
    exit;
}

$errors = [];

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $password = $_POST['password'];
    $confirm = $_POST['confirm_password'];

    if (empty($password)) {
        $errors[] = "Password is required.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters.";
    }

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if (empty($errors)) {

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            UPDATE users
            SET password = ?
            WHERE id = ?
        ");

        $stmt->execute([$hash, $id]);

        logActivity(
            $conn,
            "Reset password for {$user['fullname']} ({$user['username']})"
        );

        header("Location: index.php?success=password_reset");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Reset Password</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body class="bg-light">
    <div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-6">

            <div class="card shadow">

                <div class="card-header bg-warning text-dark">

                    <h4 class="mb-0">
                        <i class="fa fa-key"></i>
                        Reset Password
                    </h4>

                </div>

                <div class="card-body">

                    <h5><?= htmlspecialchars($user['fullname']); ?></h5>

                    <p>
                        Username:
                        <strong><?= htmlspecialchars($user['username']); ?></strong>
                    </p>

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

                            <label class="form-label">New Password</label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">Confirm Password</label>

                            <input
                                type="password"
                                name="confirm_password"
                                class="form-control"
                                required>

                        </div>

                        <button class="btn btn-warning">

                            <i class="fa fa-key"></i>

                            Reset Password

                        </button>

                        <a href="index.php" class="btn btn-secondary">

                            Cancel

                        </a>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>