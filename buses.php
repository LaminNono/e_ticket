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
    } else if (empty($_POST['route_id']) || empty($_POST['bus_name']) || empty($_POST['type']) || empty($_POST['seat_count']) || empty($_POST['departure']) || empty($_POST['arrival']) || empty($_POST['price'])) {
        $error = 'All fields are required.';
    } else {
        $route_id = $_POST['route_id'];
        $bus_name = $_POST['bus_name'];
        $type = $_POST['type'];
        $seats = $_POST['seat_count'];
        $departure = $_POST['departure'];
        $arrival = $_POST['arrival'];
        $price = $_POST['price'];
        $pdo->prepare("INSERT INTO buses (route_id, bus_name, type, seat_count, departure_time, arrival_time, price) VALUES (?, ?, ?, ?, ?, ?, ?)")
            ->execute([$route_id, $bus_name, $type, $seats, $departure, $arrival, $price]);
    }
}

// Handle Edit
if (isset($_POST['edit'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid CSRF token.';
    } else if (empty($_POST['route_id']) || empty($_POST['bus_name']) || empty($_POST['type']) || empty($_POST['seat_count']) || empty($_POST['departure']) || empty($_POST['arrival']) || empty($_POST['price'])) {
        $error = 'All fields are required.';
    } else {
        $bus_id = $_POST['bus_id'];
        $route_id = $_POST['route_id'];
        $bus_name = $_POST['bus_name'];
        $type = $_POST['type'];
        $seats = $_POST['seat_count'];
        $departure = $_POST['departure'];
        $arrival = $_POST['arrival'];
        $price = $_POST['price'];
        $pdo->prepare("UPDATE buses SET route_id=?, bus_name=?, type=?, seat_count=?, departure_time=?, arrival_time=?, price=? WHERE bus_id=?")
            ->execute([$route_id, $bus_name, $type, $seats, $departure, $arrival, $price, $bus_id]);
        $edit_success = true;
    }
}
// Handle Delete
if (isset($_POST['delete'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid CSRF token.';
    } else {
        $bus_id = $_POST['delete'];
        $pdo->prepare("DELETE FROM buses WHERE bus_id = ?")->execute([$bus_id]);
        $delete_success = true;
    }
}

$buses = $pdo->query("SELECT * FROM buses")->fetchAll();
$routes = $pdo->query("SELECT * FROM routes")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Manage Buses</title>
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
                <a href="buses.php" class="nav-link text-white fw-bold bg-info bg-opacity-25">Buses</a>
                <a href="users.php" class="nav-link text-white">Users</a>
                <a href="sales.php" class="nav-link text-white">Sales</a>
                <hr>
                <a href="logout.php" class="nav-link text-danger">Logout</a>
            </div>
            <div class="col-md-10 p-4">
                <h2 class="mb-4">Buses Management</h2>
                <?php if (isset($_POST['add'])): ?>
                <div class="alert alert-success">Bus added successfully!</div>
                <?php endif; ?>
                <?php if (!empty($edit_success)): ?>
                <div class="alert alert-success">Bus updated successfully!</div>
                <?php endif; ?>
                <?php if (!empty($delete_success)): ?>
                <div class="alert alert-success">Bus deleted successfully!</div>
                <?php endif; ?>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <form method="POST" class="row g-3 mb-4">
                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                    <div class="col-md-3">
                        <select name="route_id" class="form-select" required>
                            <option value="">Select Route</option>
                            <?php foreach ($routes as $r): ?>
                            <option value="<?= $r['route_id'] ?>"><?= $r['origin'] ?> → <?= $r['destination'] ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="bus_name" class="form-control" placeholder="Bus Name" required>
                    </div>
                    <div class="col-md-2">
                        <input type="text" name="type" class="form-control" placeholder="VIP/Normal" required>
                    </div>
                    <div class="col-md-1">
                        <input type="number" name="seat_count" class="form-control" placeholder="Seats" required>
                    </div>
                    <div class="col-md-2">
                        <input type="time" name="departure" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <input type="time" name="arrival" class="form-control" required>
                    </div>
                    <div class="col-md-2">
                        <input type="number" name="price" class="form-control" placeholder="Price" required>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" name="add" class="btn btn-primary w-100">Add Bus</button>
                    </div>
                </form>
                <table class="table table-bordered table-hover bg-white">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Route</th>
                            <th>Name</th>
                            <th>Type</th>
                            <th>Seats</th>
                            <th>Departure</th>
                            <th>Arrival</th>
                            <th>Price</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($buses as $b): ?>
                        <tr>
                            <td><?= $b['bus_id'] ?></td>
                            <td><?php 
                            foreach ($routes as $r) {
                                if ($r['route_id'] == $b['route_id']) {
                                    echo $r['origin'] . ' → ' . $r['destination'];
                                    break;
                                }
                            }
                        ?></td>
                            <td><?= $b['bus_name'] ?></td>
                            <td><?= $b['type'] ?></td>
                            <td><?= $b['seat_count'] ?></td>
                            <td><?= $b['departure_time'] ?></td>
                            <td><?= $b['arrival_time'] ?></td>
                            <td><?= $b['price'] ?></td>
                            <td>
                                <!-- Edit Button triggers modal -->
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal"
                                    data-bs-target="#editBusModal<?= $b['bus_id'] ?>">Edit</button>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Delete this bus?')">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                    <input type="hidden" name="delete" value="<?= $b['bus_id'] ?>">
                                    <button type="submit" name="delete" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                                <!-- Edit Modal -->
                                <div class="modal fade" id="editBusModal<?= $b['bus_id'] ?>" tabindex="-1"
                                    aria-labelledby="editBusLabel<?= $b['bus_id'] ?>" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <form method="POST">
                                                <div class="modal-header">
                                                    <h5 class="modal-title" id="editBusLabel<?= $b['bus_id'] ?>">Edit
                                                        Bus</h5>
                                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                        aria-label="Close"></button>
                                                </div>
                                                <div class="modal-body row g-3">
                                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                    <input type="hidden" name="bus_id" value="<?= $b['bus_id'] ?>">
                                                    <div class="col-12 mb-2">
                                                        <select name="route_id" class="form-select" required>
                                                            <?php foreach ($routes as $r): ?>
                                                            <option value="<?= $r['route_id'] ?>"
                                                                <?= $r['route_id'] == $b['route_id'] ? 'selected' : '' ?>>
                                                                <?= $r['origin'] ?> → <?= $r['destination'] ?></option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>
                                                    <div class="col-12 mb-2">
                                                        <input type="text" name="bus_name" class="form-control"
                                                            value="<?= $b['bus_name'] ?>" required>
                                                    </div>
                                                    <div class="col-12 mb-2">
                                                        <input type="text" name="type" class="form-control"
                                                            value="<?= $b['type'] ?>" required>
                                                    </div>
                                                    <div class="col-12 mb-2">
                                                        <input type="number" name="seat_count" class="form-control"
                                                            value="<?= $b['seat_count'] ?>" required>
                                                    </div>
                                                    <div class="col-12 mb-2">
                                                        <input type="time" name="departure" class="form-control"
                                                            value="<?= $b['departure_time'] ?>" required>
                                                    </div>
                                                    <div class="col-12 mb-2">
                                                        <input type="time" name="arrival" class="form-control"
                                                            value="<?= $b['arrival_time'] ?>" required>
                                                    </div>
                                                    <div class="col-12 mb-2">
                                                        <input type="number" name="price" class="form-control"
                                                            value="<?= $b['price'] ?>" required>
                                                    </div>
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