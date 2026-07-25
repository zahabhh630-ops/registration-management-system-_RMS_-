<?php

require_once "../includes/admin_only.php";
require_once "../config/database.php";
require_once "../includes/logger.php";

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    $fullname = trim($_POST['fullname']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    // Required fields
    if (empty($fullname) || empty($username) || empty($password)) {
        $errors[] = "Please fill in all required fields.";
    }

    // Password confirmation
    if ($password !== $confirmPassword) {
        $errors[] = "Passwords do not match.";
    }

    // Check duplicate username
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->execute([$username]);

    if ($stmt->fetch()) {
        $errors[] = "Username already exists.";
    }

    // Check duplicate email
    if (!empty($email)) {

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->fetch()) {
            $errors[] = "Email already exists.";
        }

    }

    if (empty($errors)) {

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare("
            INSERT INTO users
            (fullname, username, email, password, role, status)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        try {

    $stmt->execute([
        $fullname,
        $username,
        $email,
        $hashedPassword,
        $role,
        $status
    ]);
    
logActivity(
    $conn,
    "Created user: {$fullname} ({$username})"
);


    header("Location: index.php?success=user_added");
exit;

    // Comment this out temporarily while testing
    // header("Location: index.php?success=user_added");
    // exit;

} catch (PDOException $e) {

    die("Database Error: " . $e->getMessage());

}
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Add User</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body class="bg-light">

<div class="container mt-5">

    <div class="row justify-content-center">

        <div class="col-md-8">

            <div class="card shadow">

                <div class="card-header bg-success text-white">

                    <h4 class="mb-0">

                        <i class="fa fa-user-plus"></i>

                        Add New User

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
<?php if (isset($_GET['success']) && $_GET['success'] === 'user_added'): ?>

<div class="alert alert-success alert-dismissible fade show">

    <strong>Success!</strong>
    <?php endif; ?>
    User added successfully.

    <button
        type="button"
        class="btn-close"
        data-bs-dismiss="alert">
    </button>

</div>



<?php endif; ?>

                    <form method="POST">

                        <div class="mb-3">

                            <label class="form-label">

                                Full Name

                            </label>

                            <input
                                type="text"
                                name="fullname"
                                class="form-control"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Username

                            </label>

                            <input
                                type="text"
                                name="username"
                                class="form-control"
                                required>

                        </div>

                        <div class="mb-3">

                            <label class="form-label">

                                Email

                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control">

                        </div>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="mb-3">

                                    <label class="form-label">

                                        Password

                                    </label>

                                    <input
                                        type="password"
                                        name="password"
                                        class="form-control"
                                        required>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="mb-3">

                                    <label class="form-label">

                                        Confirm Password

                                    </label>

                                    <input
                                        type="password"
                                        name="confirm_password"
                                        class="form-control"
                                        required>

                                </div>

                            </div>

                        </div>

                        <div class="row">

                            <div class="col-md-6">

                                <div class="mb-3">

                                    <label class="form-label">

                                        Role

                                    </label>

                                    <select
                                        name="role"
                                        class="form-select">

                                        <option value="Staff">

                                            Staff

                                        </option>

                                        <option value="Admin">

                                            Admin

                                        </option>

                                    </select>

                                </div>

                            </div>

                            <div class="col-md-6">

                                <div class="mb-3">

                                    <label class="form-label">

                                        Status

                                    </label>

                                    <select
                                        name="status"
                                        class="form-select">

                                        <option value="Active">

                                            Active

                                        </option>

                                        <option value="Inactive">

                                            Inactive

                                        </option>

                                    </select>

                                </div>

                            </div>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-success">

                            <i class="fa fa-save"></i>

                            Save User

                        </button>

                        <a
                            href="index.php"
                            class="btn btn-secondary">

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