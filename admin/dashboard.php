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
require '../config.php';


/* single guard: allow only admins */
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
  header('Location: ../login.php');   // ★ go to root login
  exit();
}

$stmt = $pdo->query("SELECT SUM(total_price) FROM bookings");
$totalRevenue = $stmt->fetchColumn() ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) FROM bookings");
$totalOrders = $stmt->fetchColumn() ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) FROM routes");
$totalRoutes = $stmt->fetchColumn() ?? 0;

$stmt = $pdo->query("SELECT COUNT(*) FROM users");
$totalUsers = $stmt->fetchColumn() ?? 0;

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    body {
        background-color: #f4f6fb;
        color: #1e293b;
        font-family: 'Segoe UI', sans-serif;
    }

    .sidebar {
        height: 100vh;
        background-color: #1E3A8A;
        padding: 20px;
    }

    .sidebar .nav-link {
        color: #ffffff;
        margin-bottom: 10px;
    }

    .sidebar .nav-link.active,
    .sidebar .nav-link:hover {
        background-color: #0ea5e9;
        border-radius: 5px;
    }

    .dashboard-content {
        padding: 30px;
    }

    .card-box {
        background-color: #2563eb;
        padding: 20px;
        border-radius: 12px;
        text-align: center;
        color: #ffffff;
    }

    .card-box i {
        font-size: 28px;
        margin-bottom: 12px;
    }

    .section-box {
        background-color: #ffffff;
        padding: 20px;
        border-radius: 10px;
        height: 250px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04);
    }

    .section-box h5,
    .section-box h6 {
        color: #1E3A8A;
    }

    .footer {
        text-align: center;
        color: #6b7280;
        padding: 20px;
    }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar">
                <h4 class="text-white mb-4">Admin Panel</h4>

                <?php $current = basename($_SERVER['PHP_SELF']); ?>

                <a href="dashboard.php" class="nav-link <?= ($current == 'dashboard.php') ? 'active' : '' ?>">
                    <i class="bi bi-grid"></i> Dashboard
                </a>

                <a href="/admin/bookings.php"
                    class="nav-link <?= ($current == '/admin/bookings.php') ? 'active' : '' ?>">
                    <i class="bi bi-ticket-detailed"></i> Bookings
                </a>

                <a href="routes.php" class="nav-link <?= ($current == 'routes.php') ? 'active' : '' ?>">
                    <i class="bi bi-geo-alt-fill"></i> Routes
                </a>

                <a href="users.php" class="nav-link <?= ($current == 'users.php') ? 'active' : '' ?>">
                    <i class="bi bi-people-fill"></i> Users
                </a>

                <a href="sales.php" class="nav-link <?= ($current == 'sales.php') ? 'active' : '' ?>">
                    <i class="bi bi-bar-chart-line-fill"></i> Sales
                </a>

                <hr>
                <a href="logout.php" class="nav-link text-danger">
                    <i class="bi bi-box-arrow-right"></i> Logout
                </a>
            </div>



            <!-- Main content -->
            <div class="col-md-10 dashboard-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="text-primary">Dashboard</h2>
                    <button class="btn btn-outline-primary">Generate Report</button>
                </div>

                <!-- Metrics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card-box">
                            <i class="bi bi-currency-dollar"></i>
                            <p class="mb-1">Total Revenue</p>
                            <h4>$<?= $totalRevenue ?></h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-box">
                            <i class="bi bi-ticket-detailed"></i>
                            <p class="mb-1">Total Bookings</p>
                            <h4><?= $totalOrders ?></h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-box">
                            <i class="bi bi-geo-alt-fill"></i>
                            <p class="mb-1">Total Routes</p>
                            <h4><?= $totalRoutes ?></h4>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card-box">
                            <i class="bi bi-people-fill"></i>
                            <p class="mb-1">Total Users</p>
                            <h4><?= $totalUsers ?></h4>
                        </div>
                    </div>
                </div>

                <!-- Graphs -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="section-box">
                            <h5>Sales Trend</h5>
                            <!-- Graph Placeholder -->
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="section-box">
                            <h5>Payment Methods</h5>
                            <!-- Graph Placeholder -->
                        </div>
                    </div>
                </div>

                <!-- Lists -->
                <div class="row mt-4">
                    <div class="col-md-6">
                        <div class="section-box">
                            <h6>Top Routes</h6>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="section-box">
                            <h6>Top Users</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer">
        &copy; 2025 E-ticket Myanmar Admin Panel
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>