<?php

require_once "../includes/admin_only.php";
require_once "../config/database.php";

$stmt = $conn->query("SELECT * FROM settings LIMIT 1");
$settings = $stmt->fetch(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>System Settings</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

</head>

<body class="bg-light">

<div class="container py-5">

<div class="row justify-content-center">

<div class="col-lg-8">

<div class="card shadow">

<div class="card-header bg-dark text-white">

<h4>

<i class="fas fa-cogs"></i>

System Settings

</h4>

</div>
<?php

if(isset($_GET['success'])){

?>

<div class="alert alert-success">

<i class="fas fa-check-circle"></i>

Settings Saved Successfully.

</div>

<?php

}

?>
<div class="card-body">

<form method="POST" action="save.php">

<div class="mb-3">

<label>System Name</label>

<input
type="text"
name="system_name"
class="form-control"
value="<?= htmlspecialchars($settings['system_name']) ?>">

</div>

<div class="mb-3">

<label>Organization</label>

<input
type="text"
name="organization"
class="form-control"
value="<?= htmlspecialchars($settings['organization']) ?>">

</div>

<div class="mb-3">

<label>Footer Text</label>

<input
type="text"
name="footer_text"
class="form-control"
value="<?= htmlspecialchars($settings['footer_text']) ?>">

</div>

<div class="mb-3">

<label>Timezone</label>

<select
name="timezone"
class="form-control">

<option
value="Africa/Lagos"
<?= $settings['timezone']=="Africa/Lagos"?"selected":"" ?>>

Africa/Lagos

</option>

<option
value="UTC"
<?= $settings['timezone']=="UTC"?"selected":"" ?>>

UTC

</option>

</select>

</div>

<button class="btn btn-success">

<i class="fas fa-save"></i>

Save Settings

</button>

<a href="../dashboard/index.php"
class="btn btn-secondary">

Dashboard

</a>

</form>

</div>

</div>

</div>

</div>

</div>

</body>

</html>