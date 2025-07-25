<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['user_id'];
$username = $_SESSION['user'] ?? 'Guest';

// Fetch user bookings
$stmt = $pdo->prepare("SELECT b.*, r.origin, r.destination, s.departure_time 
                       FROM bookings b 
                       JOIN routes r ON b.route_id = r.route_id 
                       JOIN buses s ON b.bus_id = s.bus_id 
                       WHERE b.user_id = ? ORDER BY b.booking_date DESC");
$stmt->execute([$userId]);
$bookings = $stmt->fetchAll();

// Fetch user info
$userStmt = $pdo->prepare("SELECT name, email, phone, address, nrc, password FROM users WHERE user_id = ?");
$userStmt->execute([$userId]);
$userInfo = $userStmt->fetch();

$profileMsg = '';
$passwordMsg = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $nrc = $_POST['nrc'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];

    $updateStmt = $pdo->prepare("UPDATE users SET name = ?, nrc = ?, phone = ?, address = ? WHERE user_id = ?");
    $updateStmt->execute([$name, $nrc, $phone, $address, $userId]);

    $userStmt->execute([$userId]);
    $userInfo = $userStmt->fetch();
    $profileMsg = 'Profile updated successfully.';
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $oldPassword = $_POST['old_password'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (password_verify($oldPassword, $userInfo['password'])) {
        if ($newPassword === $confirmPassword) {
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?")->execute([$hashedPassword, $userId]);
            $passwordMsg = '<div class="alert alert-success">Password updated successfully.</div>';
        } else {
            $passwordMsg = '<div class="alert alert-danger">New passwords do not match.</div>';
        }
    } else {
        $passwordMsg = '<div class="alert alert-danger">Old password is incorrect.</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>My Bookings - E-ticket Myanmar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body { background-color: #f8f9fa; }
    .navbar { background-color: #0d47a1; color: white; }
    .navbar span { color: white; margin-right: 20px; }
    .sidebar {
      background-color: #1e40af;
      height: 100%;
      min-height: 100vh;
      padding-top: 20px;
      color: white;
    }
    .sidebar a {
      color: white;
      display: block;
      padding: 12px 20px;
      text-decoration: none;
    }
    .sidebar a:hover { background-color: #2563eb; }
    .content { padding: 20px; }
    .table thead th { background-color: #1e40af; color: white; }
    .info-box {
      background: white;
      padding: 20px;
      margin-bottom: 20px;
      border-radius: 10px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
    .top-section {
      background-color: #fff;
      padding: 15px;
      border-radius: 10px;
      margin-bottom: 20px;
    }
    .bonus-table td { font-weight: bold; }
  </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark px-4">
  <div class="container-fluid justify-content-between">
    <div class="d-flex gap-3">
      <span>📞 +959123456789</span>
      <span>📧 support@eticket-myanmar.com</span>
    </div>
    <div><?= htmlspecialchars($username) ?> (0 MMK)</div>
  </div>
</nav>

<div class="container-fluid">
  <div class="row">

    <!-- Sidebar -->
    <div class="col-md-2 sidebar">
      <a href="#">Top up</a>
      <a href="#">Changes</a>
      <a href="#">Bonus</a>
      <a href="My_Booking.php">Bookings</a>
      <a href="#profile">Profile</a>
    </div>

    <!-- Content -->
    <div class="col-md-10 content">
      <div class="top-section text-center">
        <h4>My Bookings - E-ticket Myanmar</h4>
        <p>If you need help with your booking or payment, please contact <strong>support@eticket-myanmar.com</strong>.</p>
      </div>

      <!-- Booking Table -->
      <div class="info-box">
        <h5>Booking History</h5>
        <table class="table table-bordered">
          <thead>
            <tr>
              <th>Booking Date</th>
              <th>Travel Date</th>
              <th>Ticket Code</th>
              <th>From ➔ To</th>
              <th>Departure Time</th>
              <th>Seat No</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <?php if ($bookings): foreach ($bookings as $b): ?>
              <tr>
                <td><?= htmlspecialchars($b['booking_date']) ?></td>
                <td><?= htmlspecialchars($b['booking_date']) ?></td>
                <td><?= htmlspecialchars($b['ticket_code']) ?></td>
                <td><?= htmlspecialchars($b['origin']) ?> ➔ <?= htmlspecialchars($b['destination']) ?></td>
                <td><?= htmlspecialchars($b['departure_time']) ?></td>
                <td><?= htmlspecialchars($b['seat_no']) ?></td>
                <td><?= htmlspecialchars($b['status']) ?></td>
              </tr>
            <?php endforeach; else: ?>
              <tr><td colspan="7" class="text-center">You have no bookings yet.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Profile Update -->
      <div id="profile" class="info-box">
        <h5>Update Profile</h5>
        <?php if ($profileMsg): ?><div class="alert alert-success"><?= $profileMsg ?></div><?php endif; ?>
        <form method="post" class="row g-3">
          <input type="hidden" name="update_profile" value="1">
          <div class="col-md-6">
            <label class="form-label">Name</label>
            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($userInfo['name']) ?>" required>
          </div>
          <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" value="<?= htmlspecialchars($userInfo['email']) ?>" disabled>
          </div>
          <div class="col-md-6">
            <label class="form-label">NRC / Passport No.</label>
            <input type="text" name="nrc" class="form-control" value="<?= htmlspecialchars($userInfo['nrc']) ?>">
          </div>
          <div class="col-md-6">
            <label class="form-label">Mobile No.</label>
            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($userInfo['phone']) ?>">
          </div>
          <div class="col-md-12">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($userInfo['address']) ?>">
          </div>
          <div class="col-12">
            <button type="submit" class="btn btn-primary">Update Profile</button>
          </div>
        </form>
      </div>

      <!-- Change Password -->
      <div class="info-box">
        <h5>Change Password</h5>
        <?= $passwordMsg ?>
        <form method="post">
          <input type="hidden" name="change_password" value="1">
          <div class="mb-3">
            <label>Old Password</label>
            <input type="password" name="old_password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>New Password</label>
            <input type="password" name="new_password" class="form-control" required>
          </div>
          <div class="mb-3">
            <label>Confirm New Password</label>
            <input type="password" name="confirm_password" class="form-control" required>
          </div>
          <button type="submit" class="btn btn-warning">Change Password</button>
        </form>
      </div>

    </div>
  </div>
</div>
</body>
</html>
