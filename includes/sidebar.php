<?php

$current = basename($_SERVER['PHP_SELF']);

?>
<div class="sidebar">

    <div class="text-center py-4">

<i class="fas fa-database fa-3x text-primary"></i>

<h3 class="mt-2 mb-1">

RMS

</h3>

<small class="text-muted">

Registration Management System

</small>
<hr>
</div>

    <ul>

        <li>

<a href="../dashboard/index.php"

class="<?= $current=='index.php'
?'active':'' ?>">

Dashboard

</a>

</li>
        <li>
            <a href="../records/view.php">
                <i class="fas fa-users"></i>
                Registrations
            </a>
        </li>

        <li>
            <a href="../reports/report.php">
                <i class="fas fa-file-alt"></i>
                Reports
            </a>
        </li>

        <li>
            <a href="../records/export_pdf.php">
                <i class="fas fa-file-pdf"></i>
                Export PDF
            </a>
        </li>

        <li>
            <a href="#">
                <i class="fas fa-file-excel"></i>
                Export Excel
            </a>
        </li>
<?php if ($_SESSION['role'] === "Admin"): ?>

<li>
    <a href="../users/index.php">
        User Management
    </a>
</li>

<?php endif; ?>
        <li>

<a href="../settings/index.php">

<i class="fas fa-cogs"></i>

Settings

</a>

</li>
<li>
    <a href="../backup/index.php">
        <i class="fas fa-database"></i>
        Backup Database
    </a>
</li>
<li>
    <a href="../profile/index.php">
        <i class="fas fa-user-circle"></i>
        My Profile
    </a>
</li>
        <li>
            <a href="../auth/logout.php">
                <i   class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </li>

    </ul>

</div>