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
$user_id = $_GET['id'];
$user = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$user->execute([$user_id]);
$user = $user->fetch();

$bookings = $pdo->prepare("SELECT * FROM bookings WHERE user_id = ?");
$bookings->execute([$user_id]);
$bookings = $bookings->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">User Profile</h4>
                    </div>
                    <div class="card-body">
                        <p><strong>Name:</strong> <?= $user['name'] ?></p>
                        <p><strong>Email:</strong> <?= $user['email'] ?></p>
                        <p><strong>Phone:</strong> <?= $user['phone'] ?></p>
                        <p><strong>Role:</strong> <?= $user['role'] ?></p>
                        <h5 class="mt-4">Bookings</h5>
                        <ul class="list-group mb-3">
                            <?php foreach ($bookings as $b): ?>
                            <li class="list-group-item">
                                <?= $b['booking_date'] ?> - <?= $b['ticket_code'] ?> - <?= $b['status'] ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                        <a href="users.php" class="btn btn-secondary">Back to Users</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>