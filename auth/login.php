<?php
session_start();

require_once "../config/database.php";
require_once "../config/app.php";
require_once "../includes/logger.php";

$error = "";

if (isset($_POST['login'])) {

    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Find user by username
    $stmt = $conn->prepare("
        SELECT *
        FROM users
        WHERE username = ?
        LIMIT 1
    ");

    $stmt->execute([$username]);

    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {

        // Verify password first
        if (!password_verify($password, $user['password'])) {

            $error = "Invalid username or password.";

        }
        // Check if account is active
        elseif ($user['status'] !== 'Active') {

            $error = "Your account has been deactivated. Please contact the administrator.";

        }
        // Successful login
        else {

            $_SESSION['logged_in'] = true;
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['fullname'] = $user['fullname'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];

            // Audit Log
            logActivity(
                $conn,
                "Logged into the system"
            );

            header("Location: ../dashboard/index.php");
            exit;
        }

    } else {

        $error = "Invalid username or password.";

    }
}
?>

<!DOCTYPE html>
<html>

<head>

    <title>Administrator Login</title>

    <style>

        body{
            font-family:Arial;
            background:#f4f4f4;
            display:flex;
            justify-content:center;
            align-items:center;
            height:100vh;
        }

        .login-box{

            width:350px;
            background:white;
            padding:30px;
            border-radius:10px;
            box-shadow:0 5px 15px rgba(0,0,0,.15);

        }

        input{

            width:100%;
            padding:12px;
            margin:10px 0;
            border:1px solid #ddd;
            border-radius:6px;
            box-sizing:border-box;

        }

        button{

            width:100%;
            padding:12px;
            background:#0B1F3A;
            color:white;
            border:none;
            border-radius:6px;
            cursor:pointer;

        }

        button:hover{

            background:#16345d;

        }

        .error{

            color:red;
            margin-bottom:15px;

        }

    </style>

</head>

<body>

<div class="login-box">

    <h2>Administrator Login</h2>

    <?php if ($error): ?>

        <p class="error"><?= htmlspecialchars($error); ?></p>

    <?php endif; ?>

    <form method="post">

        <input
            type="text"
            name="username"
            placeholder="Username"
            required>

        <input
            type="password"
            name="password"
            placeholder="Password"
            required>

        <button
            type="submit"
            name="login">

            Login

        </button>

    </form>

</div>

</body>

</html>