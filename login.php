<?php
session_start();
require 'config.php';

/* ── bounce an already-signed-in visitor ─────────────────── */
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

/* ── handle POST ─────────────────────────────────────────── */
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password =       $_POST['password']?? '';

    if ($email === '' || $password === '') {
        $error = 'Email and Password are required.';
    } else {
        /*  ▼▼ ONLY THESE LINES CHANGE ▼▼  */
          $stmt = $pdo->prepare(
    'SELECT user_id, name, role, password           -- role included
       FROM users
      WHERE email = ? LIMIT 1'
);

          $stmt->execute([$email]);
          $user = $stmt->fetch(PDO::FETCH_ASSOC);

          if ($user && password_verify($password, $user['password'])) {  // ★ CHANGED
              session_regenerate_id(true);
              $_SESSION['user_id'] = $user['user_id'];                   // ★ CHANGED
              $_SESSION['user']    = $user['name'];
              $_SESSION['role']    = $user['role'];                       // ★ CHANGED
              $target = ($user['role'] === 'admin')
          ? ADMIN_HOME         // resolves to dashboard.php
          : 'index.php';
header("Location: $target");
exit();

              exit();
          }
        /*  ▲▲ ONLY THESE LINES CHANGE ▲▲  */

        $error = 'Invalid email or password.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - E-ticket Myanmar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* ——— RESTORED ORIGINAL STYLING ——— */
    body {
      background-color: #f0f4f8;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      font-family: Arial, sans-serif;
    }
    .login-container {
      background: #ffffff;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
      width: 100%;
      max-width: 400px;
    }
    .login-container h2 {
      text-align: center;
      color: #0ea5e9;
      margin-bottom: 25px;
    }
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 20px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    button {
      width: 100%;
      padding: 12px;
      background-color: #0ea5e9;
      border: none;
      border-radius: 6px;
      color: white;
      font-size: 16px;
      cursor: pointer;
    }
    button:hover {
      background-color: #0284c7;
    }
    .error {
      color: red;
      text-align: center;
      margin-bottom: 15px;
    }
  </style>
</head>
<body>
  <div class="login-container">
    <h2>Login to Your Account</h2>

    <?php if (!empty($error)) echo "<div class='error'>$error</div>"; ?>

    <form method="POST" autocomplete="off">
      <input type="email"
             name="email"
             placeholder="Email"
             value="<?= htmlspecialchars($email ?? '') ?>"
             required>
      <input type="password"
             name="password"
             placeholder="Password"
             required>
      <button type="submit">Login</button>
    </form>

    <p class="text-center mt-3">
      Don't have an account?
      <a href="register.php">Register</a>
    </p>
  </div>
</body>
</html>
