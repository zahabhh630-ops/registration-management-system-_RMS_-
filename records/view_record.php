<?php
session_start();

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../config/database.php";

// Check if ID exists
if (!isset($_GET['id'])) {
    die("No record selected.");
}

$id = $_GET['id'];

// Fetch record
$sql = "SELECT * FROM form_submissions WHERE id_key = :id";

$stmt = $conn->prepare($sql);

$stmt->bindParam(':id', $id, PDO::PARAM_INT);

$stmt->execute();

$record = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$record) {
    die("Record not found.");
}
?>

<?php include "../includes/header.php"; ?>

<div style="max-width:900px;margin:40px auto;">

<h1>Registration Details</h1>

<table>

<tr>
<th>ID</th>
<td><?= htmlspecialchars($record['id_key']) ?></td>
</tr>

<tr>
<th>Other Name</th>
<td><?= htmlspecialchars($record['other_name']) ?></td>
</tr>

<tr>
<th>Surname</th>
<td><?= htmlspecialchars($record['surname']) ?></td>
</tr>

<tr>
<th>Address</th>
<td><?= htmlspecialchars($record['address']) ?></td>
</tr>

<tr>
<th>Occupation</th>
<td><?= htmlspecialchars($record['occupation']) ?></td>
</tr>

<tr>
<th>Nationality</th>
<td><?= htmlspecialchars($record['nationality']) ?></td>
</tr>

<tr>
<th>National ID</th>
<td><?= htmlspecialchars($record['national_id']) ?></td>
</tr>

<tr>
<th>Date Submitted</th>
<td><?= htmlspecialchars($record['submitted_at']) ?></td>
</tr>

</table>

<br>

<a href="view.php" class="btn btn-edit">
← Back
</a>

<a href="edit.php?id=<?= $record['id_key'] ?>" class="btn btn-edit">
✏ Edit
</a>

<a href="view.php?action=delete&id=<?= $record['id_key'] ?>"
class="btn btn-delete"
onclick="return confirm('Delete this record?')">
🗑 Delete
</a>

</div>

<?php include "../includes/footer.php"; ?>