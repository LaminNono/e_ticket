<?php
session_start();
require 'config.php';

$success = '';
$error = '';

// Only process if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    if ($name && $email && $message) {
        $stmt = $pdo->prepare("INSERT INTO feedback (name, email, subject, message) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $subject, $message]);
        $success = "✅ Thank you! Your message has been sent.";
    } else {
        $error = "⚠️ Please fill in all required fields.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Us - E-ticket Myanmar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <style>
    body { font-family: Arial, sans-serif; margin: 0; }
    .navbar, footer { background-color: #1E3A8A; color: white; }
    .top-bar { background-color: #E5E1DC; color: #1E3A8A; padding: 6px 20px; font-size: 14px; }
    .bookings-btn { background-color: #0EA5E9; padding: 10px 16px; border-radius: 5px; color: white; text-decoration: none; }
    .contact-section { padding: 60px 20px; background-color: #f9f9f9; }
    .contact-section h2 { color: #1E3A8A; margin-bottom: 20px; }
    .btn-submit { background-color: #0EA5E9; color: white; }
    .btn-submit:hover { background-color: #0284c7; }
  </style>
</head>
<body>

<!-- Loader -->
<div id="loader" style="position: fixed; top:0; left:0; width:100%; height:100%; background:white; display:flex; align-items:center; justify-content:center; z-index:9999;">
  <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
</div>

<!-- Top Bar -->
<div class="top-bar d-flex justify-content-between align-items-center px-3">
  <div>📞 +959965509210 / ✉️ e-ticketmyanmar@nonipoly.net</div>
  <div>
    <?php if (isset($_SESSION['user_id'])): ?>
      Welcome, <?= htmlspecialchars($_SESSION['user']) ?> |
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a> |
      <a href="register.php">Register</a>
    <?php endif; ?>
  </div>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg px-3">
  <div class="container-fluid">
    <a class="navbar-brand text-white fw-bold" href="index.php">E-ticket Myanmar</a>
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
      <li class="nav-item"><a class="nav-link text-white" href="index.php">Home</a></li>
      <li class="nav-item"><a class="nav-link text-white" href="about.php">About Us</a></li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">Routes</a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="routes.php?type=popular">Popular Routes</a></li>
          <li><a class="dropdown-item" href="routes.php?type=recent">Recent Routes</a></li>
        </ul>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle text-white active" href="#" role="button" data-bs-toggle="dropdown">Help</a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item active" href="contact.php">Contact Us</a></li>
          <li><a class="dropdown-item" href="help.php?type=faq">FAQ</a></li>
        </ul>
      </li>
    </ul>
    <a class="bookings-btn" href="My_Booking.php">My Bookings</a>
  </div>
</nav>

<!-- Contact Section -->
<section class="contact-section">
  <div class="container">
    <div class="row justify-content-center" data-aos="fade-up">
      <div class="col-md-8">
        <h2>Contact Us</h2>
        <p class="mb-4">Have a question or feedback? We're here to help. Fill out the form below and our team will respond shortly.</p>

        <!-- ✅ Feedback confirmation -->
        <?php if (isset($_GET['success'])): ?>
          <div class="alert alert-success">Thank you! Your message has been sent.</div>
        <?php elseif (isset($_GET['error'])): ?>
          <div class="alert alert-danger">Please fill in all required fields.</div>
        <?php endif; ?>

        <!-- ✅ Contact Form -->
        <form method="POST" action="contact.php">
          <div class="mb-3">
            <label for="name" class="form-label">Your Name</label>
            <input type="text" name="name" id="name" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" required>
          </div>
          <div class="mb-3">
            <label for="subject" class="form-label">Subject</label>
            <input type="text" name="subject" id="subject" class="form-control">
          </div>
          <div class="mb-3">
            <label for="message" class="form-label">Message</label>
            <textarea name="message" id="message" rows="5" class="form-control" required></textarea>
          </div>
          <button type="submit" class="btn btn-submit px-4">Send Message</button>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- Footer -->
<footer class="text-white text-center py-4 mt-5" style="background-color: #1E3A8A;">
  <p>&copy; 2025 E-ticket Myanmar. All Rights Reserved.</p>
</footer>

<script>
  window.addEventListener("load", function () {
    document.getElementById("loader").style.display = "none";
  });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>AOS.init();</script>
</body>
</html>
