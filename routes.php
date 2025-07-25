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

// CSRF token setup
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Handle Add
if (isset($_POST['add'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid CSRF token.';
    } else if (empty($_POST['origin']) || empty($_POST['destination']) || empty($_POST['distance']) || empty($_POST['duration'])) {
        $error = 'All fields are required.';
    } else {
        $origin = $_POST['origin'];
        $destination = $_POST['destination'];
        $distance = $_POST['distance'];
        $duration = $_POST['duration'];
        $pdo->prepare("INSERT INTO routes (origin, destination, distance, duration) VALUES (?, ?, ?, ?)")
            ->execute([$origin, $destination, $distance, $duration]);
    }
}

// Handle Edit
if (isset($_POST['edit'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid CSRF token.';
    } else if (empty($_POST['origin']) || empty($_POST['destination']) || empty($_POST['distance']) || empty($_POST['duration'])) {
        $error = 'All fields are required.';
    } else {
        $route_id = $_POST['route_id'];
        $origin = $_POST['origin'];
        $destination = $_POST['destination'];
        $distance = $_POST['distance'];
        $duration = $_POST['duration'];
        $pdo->prepare("UPDATE routes SET origin=?, destination=?, distance=?, duration=? WHERE route_id=?")
            ->execute([$origin, $destination, $distance, $duration, $route_id]);
        $edit_success = true;
    }
}
// Handle Delete (POST only)
if (isset($_POST['delete'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid CSRF token.';
    } else {
        $id = $_POST['delete'];
        $pdo->prepare("DELETE FROM routes WHERE route_id = ?")->execute([$id]);
        $delete_success = true;
    }
}

$routes = $pdo->query("SELECT * FROM routes")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Routes</title>
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
                <a href="routes.php" class="nav-link text-white fw-bold bg-info bg-opacity-25">Routes</a>
                <a href="buses.php" class="nav-link text-white">Buses</a>
                <a href="users.php" class="nav-link text-white">Users</a>
                <a href="sales.php" class="nav-link text-white">Sales</a>
                <hr>
                <a href="logout.php" class="nav-link text-danger">Logout</a>
            </div>
            <div class="col-md-10 p-4">
                <h2 class="mb-4">Routes Management</h2>
                <?php if (isset($_POST['add'])): ?>
                <div class="alert alert-success">Route added successfully!</div>
                <?php endif; ?>
                <?php if (!empty($edit_success)): ?>
                <div class="alert alert-success">Route updated successfully!</div>
                <?php endif; ?>
                <?php if (!empty($delete_success)): ?>
                <div class="alert alert-success">Route deleted successfully!</div>
                <?php endif; ?>
                <?php if (!empty($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <form method="POST" class="row g-3 mb-4">
                    <div class="col-md-3">
                        <input type="text" name="origin" class="form-control" placeholder="Origin" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" name="destination" class="form-control" placeholder="Destination" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="distance" class="form-control" placeholder="Distance (km)" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="duration" class="form-control" placeholder="Duration" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" name="add" class="btn btn-primary w-100">Add Route</button>
                    </div>
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                </form>
                <table class="table table-bordered table-hover bg-white">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Origin</th>
                            <th>Destination</th>
                            <th>Distance</th>
                            <th>Duration</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($routes as $r): ?>
                        <tr>
                            <td><?= $r['route_id'] ?></td>
                            <td><?= $r['origin'] ?></td>
                            <td><?= $r['destination'] ?></td>
                            <td><?= $r['distance'] ?></td>
                            <td><?= $r['duration'] ?></td>
                            <td>
                                <!-- Edit Button triggers modal -->
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#editRouteModal<?= $r['route_id'] ?>">Edit</button>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Delete this route?')">
                                    <input type="hidden" name="delete" value="<?= $r['route_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                                <!-- Edit Modal -->
                                <div class="modal fade" id="editRouteModal<?= $r['route_id'] ?>" tabindex="-1"
                                    aria-labelledby="editRouteLabel<?= $r['route_id'] ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editRouteLabel<?= $r['route_id'] ?>">
                                                        Edit Route</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body row g-3">
                                                    <input type="hidden" name="route_id" value="<?= $r['route_id'] ?>">
                                                    <div class="col-12 mb-2">
                                                        <input type="text" name="origin" class="form-control"
                                                            value="<?= $r['origin'] ?>" required>
                                                    </div>
                                                    <div class="col-12 mb-2">
                                                        <input type="text" name="destination" class="form-control"
                                                            value="<?= $r['destination'] ?>" required>
                                                    </div>
                                                    <div class="col-12 mb-2">
                                                        <input type="number" name="distance" class="form-control"
                                                            value="<?= $r['distance'] ?>" required>
                                                    </div>
                                                    <div class="col-12 mb-2">
                                                        <input type="text" name="duration" class="form-control"
                                                            value="<?= $r['duration'] ?>" required>
                                                    </div>
                                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary"
                                                        data-bs-dismiss="modal">Cancel</button>
                                                    <button type="submit" name="edit" class="btn btn-primary">Save
                                                        Changes</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
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