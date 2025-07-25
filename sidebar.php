<?php
$current = basename($_SERVER['PHP_SELF']);
?>
<div class="col-md-2 sidebar bg-primary text-white p-3" style="min-height:100vh;">
    <h4 class="mb-4">Admin Panel</h4>
    <a href="dashboard.php" class="nav-link text-white d-flex align-items-center gap-2 <?= ($current == 'dashboard.php') ? 'fw-bold bg-info bg-opacity-25' : '' ?>">
        <i class="bi bi-grid"></i> Dashboard
    </a>
    <a href="bookings.php" class="nav-link text-white d-flex align-items-center gap-2 <?= ($current == 'bookings.php') ? 'fw-bold bg-info bg-opacity-25' : '' ?>">
        <i class="bi bi-ticket-detailed"></i> Bookings
    </a>
    <a href="routes.php" class="nav-link text-white d-flex align-items-center gap-2 <?= ($current == 'routes.php') ? 'fw-bold bg-info bg-opacity-25' : '' ?>">
        <i class="bi bi-geo-alt-fill"></i> Routes
    </a>
    <a href="buses.php" class="nav-link text-white d-flex align-items-center gap-2 <?= ($current == 'buses.php') ? 'fw-bold bg-info bg-opacity-25' : '' ?>">
        <i class="bi bi-bus-front"></i> Buses
    </a>
    <a href="users.php" class="nav-link text-white d-flex align-items-center gap-2 <?= ($current == 'users.php') ? 'fw-bold bg-info bg-opacity-25' : '' ?>">
        <i class="bi bi-people-fill"></i> Users
    </a>
    <a href="sales.php" class="nav-link text-white d-flex align-items-center gap-2 <?= ($current == 'sales.php') ? 'fw-bold bg-info bg-opacity-25' : '' ?>">
        <i class="bi bi-bar-chart-line-fill"></i> Sales
    </a>
    <hr>
    <a href="logout.php" class="nav-link text-danger d-flex align-items-center gap-2">
        <i class="bi bi-box-arrow-right"></i> Logout
    </a>
</div>
