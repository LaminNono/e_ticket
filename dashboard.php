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
  header('Location: login.php');
  exit();
}

$totalRevenue = 0;
$checkCol = $pdo->query("SHOW COLUMNS FROM bookings LIKE 'total_price'");
if ($checkCol && $checkCol->rowCount() > 0) {
    $stmt = $pdo->query("SELECT SUM(total_price) FROM bookings");
    $totalRevenue = $stmt->fetchColumn() ?? 0;
}

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
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body { background-color: #f4f6fb; color: #1e293b; font-family: 'Segoe UI', sans-serif; }
    .sidebar { height: 100vh; background-color: #1E3A8A; padding: 20px; }
    .sidebar .nav-link { color: #ffffff; margin-bottom: 10px; }
    .sidebar .nav-link.active, .sidebar .nav-link:hover { background-color: #0ea5e9; border-radius: 5px; }
    .dashboard-content { padding: 30px; }
    .card-box { background-color: #2563eb; padding: 20px; border-radius: 12px; text-align: center; color: #ffffff; }
    .card-box i { font-size: 28px; margin-bottom: 12px; }
    .section-box { background-color: #ffffff; padding: 20px; border-radius: 10px; height: 300px; box-shadow: 0 2px 6px rgba(0, 0, 0, 0.04); }
    .section-box h5, .section-box h6 { color: #1E3A8A; }
    .footer { text-align: center; color: #6b7280; padding: 20px; }
  </style>
</head>
<body>
<div class="container-fluid">
  <div class="row">
    <?php include 'sidebar.php'; ?>
    <div class="col-md-10 dashboard-content">
      <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-primary">Dashboard</h2>
        <button class="btn btn-outline-primary">Generate Report</button>
      </div>

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

      <div class="row">
        <div class="col-md-6">
          <div class="section-box">
            <h5>Sales Trend</h5>
            <canvas id="salesTrendChart" style="width:100%; height:200px;"></canvas>
          </div>
        </div>
        <div class="col-md-6">
          <div class="section-box">
            <h5>Payment Methods</h5>
            <canvas id="paymentMethodChart" style="width:100%; height:200px;"></canvas>
          </div>
        </div>
      </div>

      <div class="row mt-4">
        <div class="col-md-6">
          <div class="section-box">
            <h6>Top Routes</h6>
            <div id="topRoutesList"></div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="section-box">
            <h6>Top Users</h6>
            <div id="topUsersList"></div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<div class="footer">&copy; 2025 E-ticket Myanmar Admin Panel</div>

<script>
fetch('dashboard-data.php')
  .then(res => res.json())
  .then(data => {
    new Chart(document.getElementById('salesTrendChart'), {
      type: 'line',
      data: {
        labels: data.sales_trend.map(item => item.month),
        datasets: [{
          label: 'Revenue (Ks)',
          data: data.sales_trend.map(item => item.revenue),
          fill: true,
          borderColor: 'blue',
          backgroundColor: 'rgba(0,123,255,0.1)'
        }]
      }
    });

    new Chart(document.getElementById('paymentMethodChart'), {
      type: 'pie',
      data: {
        labels: data.payment_methods.map(item => item.payment_method),
        datasets: [{
          data: data.payment_methods.map(item => item.count),
          backgroundColor: ['#007bff', '#ffc107', '#28a745', '#dc3545']
        }]
      }
    });

    document.getElementById('topRoutesList').innerHTML =
      '<ul>' + data.top_routes.map(r => `<li>${r.route} - ${r.count} bookings</li>`).join('') + '</ul>';

    document.getElementById('topUsersList').innerHTML =
      '<ul>' + data.top_users.map(u => `<li>${u.name} - ${u.count} bookings</li>`).join('') + '</ul>';
  });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>