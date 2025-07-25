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
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit();
}
$users = $pdo->query("SELECT * FROM users")->fetchAll();

// CSRF token setup
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];

// Handle Edit
if (isset($_POST['edit'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid CSRF token.';
    } else if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['phone']) || empty($_POST['role'])) {
        $error = 'All fields are required.';
    } else {
        $user_id = $_POST['user_id'];
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $role = $_POST['role'];
        $pdo->prepare("UPDATE users SET name=?, email=?, phone=?, role=? WHERE user_id=?")
            ->execute([$name, $email, $phone, $role, $user_id]);
        $edit_success = true;
    }
}
// Handle Delete
if (isset($_POST['delete'])) {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = 'Invalid CSRF token.';
    } else {
        $user_id = $_POST['delete'];
        // Prevent deleting self or admin
        if ($user_id != $_SESSION['user_id']) {
            $pdo->prepare("DELETE FROM users WHERE user_id = ?")->execute([$user_id]);
            $delete_success = true;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Users Management</title>
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
                <a href="users.php" class="nav-link text-white fw-bold bg-info bg-opacity-25">Users</a>
                <a href="sales.php" class="nav-link text-white">Sales</a>
                <hr>
                <a href="logout.php" class="nav-link text-danger">Logout</a>
            </div>
            <div class="col-md-10 p-4">
                <h2 class="mb-4">User List</h2>
                <?php if (!empty($edit_success)): ?>
                    <div class="alert alert-success">User updated successfully!</div>
                <?php endif; ?>
                <?php if (!empty($delete_success)): ?>
                    <div class="alert alert-success">User deleted successfully!</div>
                <?php endif; ?>
                <?php if (!empty($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <table class="table table-bordered table-hover bg-white">
                    <thead class="table-primary">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                        <tr>
                            <td><?= $u['user_id'] ?></td>
                            <td><?= $u['name'] ?></td>
                            <td><?= $u['email'] ?></td>
                            <td><?= $u['phone'] ?></td>
                            <td><?= $u['role'] ?></td>
                            <td>
                                <!-- Edit Button triggers modal -->
                                <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editUserModal<?= $u['user_id'] ?>">Edit</button>
                                <?php if ($u['user_id'] != $_SESSION['user_id']): ?>
                                <form method="POST" class="d-inline" onsubmit="return confirm('Delete this user?')">
                                    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                    <input type="hidden" name="delete" value="<?= $u['user_id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                </form>
                                <?php endif; ?>
                                <!-- Edit Modal -->
                                <div class="modal fade" id="editUserModal<?= $u['user_id'] ?>" tabindex="-1" aria-labelledby="editUserLabel<?= $u['user_id'] ?>" aria-hidden="true">
                                  <div class="modal-dialog">
                                    <div class="modal-content">
                                      <form method="POST">
                                        <div class="modal-header">
                                          <h5 class="modal-title" id="editUserLabel<?= $u['user_id'] ?>">Edit User</h5>
                                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                        </div>
                                        <div class="modal-body row g-3">
                                          <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                          <input type="hidden" name="user_id" value="<?= $u['user_id'] ?>">
                                          <div class="col-12 mb-2">
                                            <input type="text" name="name" class="form-control" value="<?= $u['name'] ?>" required>
                                          </div>
                                          <div class="col-12 mb-2">
                                            <input type="email" name="email" class="form-control" value="<?= $u['email'] ?>" required>
                                          </div>
                                          <div class="col-12 mb-2">
                                            <input type="text" name="phone" class="form-control" value="<?= $u['phone'] ?>" required>
                                          </div>
                                          <div class="col-12 mb-2">
                                            <select name="role" class="form-select" required>
                                              <option value="user" <?= $u['role'] == 'user' ? 'selected' : '' ?>>User</option>
                                              <option value="admin" <?= $u['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                                            </select>
                                          </div>
                                        </div>
                                        <div class="modal-footer">
                                          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                          <button type="submit" class="btn btn-primary">Save Changes</button>
                                        </div>
                                      </form>
                                    </div>
                                  </div>
                                </div>
                            </td>
                            <td><a href="user_profile.php?id=<?= $u['user_id'] ?>" class="btn btn-sm btn-info">View</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <script src=" https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>