<?php

require_once "../includes/admin_only.php";

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Database Backup</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body class="bg-light">

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-lg-6">

<div class="card shadow">

<div class="card-header bg-success text-white">

<h4>

<i class="fas fa-database"></i>

Database Backup

</h4>

</div>

<div class="card-body text-center">

<p>

Click the button below to download a backup of your RMS database.

</p>

<a href="download.php"

class="btn btn-success btn-lg">

<i class="fas fa-download"></i>

Download Backup

</a>

</div>

</div>

</div>

</div>

</div>

</body>

</html>