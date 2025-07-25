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

// Admin guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: login.php');
    exit();
}

// Handle delete
if (isset($_GET['delete'])) {
    $deleteId = (int) $_GET['delete'];
    $pdo->prepare("DELETE FROM bookings WHERE booking_id = ?")->execute([$deleteId]);
    header("Location: bookings.php");
    exit();
}

// Fetch bookings with JOINs
$sql = "SELECT b.booking_id, u.name AS user_name, r.origin, r.destination, bu.bus_name, b.seat_no, b.booking_date, b.status, b.ticket_code
        FROM bookings b
        JOIN users u ON b.user_id = u.user_id
        JOIN buses bu ON b.bus_id = bu.bus_id
        JOIN routes r ON b.route_id = r.route_id
        ORDER BY b.booking_date DESC";
$stmt = $pdo->query($sql);
$bookings = $stmt->fetchAll();
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin - Manage Bookings</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-4">
        <h2 class="mb-4">Manage Bookings</h2>
        <table class="table table-bordered table-hover">
            <thead class="table-primary">
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Route</th>
                    <th>Bus</th>
                    <th>Seat</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Ticket</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($bookings as $row): ?>
                <tr>
                    <td><?= $row['booking_id'] ?></td>
                    <td><?= htmlspecialchars($row['user_name']) ?></td>
                    <td><?= htmlspecialchars($row['origin'] . ' ➔ ' . $row['destination']) ?></td>
                    <td><?= htmlspecialchars($row['bus_name']) ?></td>
                    <td><?= htmlspecialchars($row['seat_no']) ?></td>
                    <td><?= htmlspecialchars($row['booking_date']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['ticket_code']) ?></td>
                    <td>
                        <a href="edit_booking.php?id=<?= $row['booking_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="bookings.php?delete=<?= $row['booking_id'] ?>" class="btn btn-sm btn-danger"
                            onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>