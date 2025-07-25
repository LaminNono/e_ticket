<?php
session_start();
if (isset($_GET['debug_session'])) {
    echo '<pre style="background:#222;color:#0f0;padding:10px;">SESSION DEBUG\n';
    echo 'session_id: ' . session_id() . "\n";
    echo '\n$_COOKIE:\n';
    print_r($_COOKIE);
    echo '\n$_SESSION:\n';
    print_r($_SESSION);
    echo '</pre>';
}
require 'config.php';
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: login.php");
    exit();
}
$sales = $pdo->query("SELECT * FROM payments JOIN bookings USING(booking_id)")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sales</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container-fluid">
    <div class="row">
        <div class="col-md-2 sidebar bg-primary text-white p-3" style="min-height:100vh;">
            <h4 class="mb-4">Admin Panel</h4>
            <a href="dashboard.php" class="nav-link text-white">Dashboard</a>
            <a href="bookings.php" class="nav-link text-white">Bookings</a>
            <a href="routes.php" class="nav-link text-white">Routes</a>
            <a href="buses.php" class="nav-link text-white">Buses</a>
            <a href="users.php" class="nav-link text-white">Users</a>
            <a href="sales.php" class="nav-link text-white fw-bold bg-info bg-opacity-25">Sales</a>
            <hr>
            <a href="logout.php" class="nav-link text-danger">Logout</a>
        </div>
        <div class="col-md-10 p-4">
            <h2 class="mb-4">Sales / Payments</h2>
            <table class="table table-bordered table-hover bg-white">
                <thead class="table-primary">
                    <tr>
                        <th>Payment ID</th>
                        <th>Booking ID</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($sales as $s): ?>
                    <tr>
                        <td><?= $s['payment_id'] ?></td>
                        <td><?= $s['booking_id'] ?></td>
                        <td><?= $s['amount'] ?> MMK</td>
                        <td><?= $s['method'] ?></td>
                        <td><?= $s['status'] ?></td>
                        <td><?= $s['payment_date'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>