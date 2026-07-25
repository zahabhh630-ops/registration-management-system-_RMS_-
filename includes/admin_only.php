<?php

require_once "auth_check.php";

if (!isset($_SESSION['role']) || $_SESSION['role'] !== "Admin") {

    $_SESSION['error'] = "Access denied.";

    header("Location: ../dashboard/index.php");

    exit;
}