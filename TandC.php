<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Terms & Conditions - E-ticket Myanmar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
    }
    .navbar, footer {
      background-color: #1E3A8A;
      color: white;
    }
    .top-bar {
      background-color: #E5E1DC;
      color: #1E3A8A;
      padding: 6px 20px;
      font-size: 14px;
    }
    .top-bar a, .nav-link, .navbar-brand {
      color: white;
      text-decoration: none;
    }
    .bookings-btn {
      background-color: #0EA5E9;
      padding: 10px 16px;
      border-radius: 5px;
      color: white;
      text-decoration: none;
    }
    .terms-section {
      padding: 60px 20px;
      background-color: #f9f9f9;
    }
    .terms-section h2 {
      color: #1E3A8A;
      margin-bottom: 20px;
    }
    .terms-section p {
      font-size: 15px;
      color: #374151;
      line-height: 1.8;
    }
  </style>
</head>

<body>

<!-- Loader -->
<div id="loader" style="position: fixed; top:0; left:0; width:100%; height:100%; background:white; display:flex; align-items:center; justify-content:center; z-index:9999;">
  <div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>
</div>

<!-- Top Bar -->
<div class="top-bar d-flex justify-content-between align-items-center px-3">
  <div>
    📞 +959965509210 / ✉️ e-ticketmyanmar@nonipoly.net
  </div>
  <div>
    <a href="#">Login</a> |
    <a href="#">Register</a>
  </div>
</div>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg px-3">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">E-ticket Myanmar</a>
    <ul class="navbar-nav me-auto mb-2 mb-lg-0">
      <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
      <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Routes</a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="routes.php?type=popular">Popular Routes</a></li>
          <li><a class="dropdown-item" href="routes.php?type=recent">Recent Routes</a></li>
        </ul>
      </li>
      <li class="nav-item dropdown">
        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Help</a>
        <ul class="dropdown-menu">
          <li><a class="dropdown-item" href="help.php?type=faq">FAQ</a></li>
          <li><a class="dropdown-item" href="contact.php">Contact Us</a></li>
        </ul>
      </li>
      
    </ul>
    <a class="bookings-btn" href="#">My Bookings</a>
  </div>
</nav>

<!-- Terms Section -->
<section class="terms-section">
  <div class="container">
    <h2>Terms and Conditions</h2>
    <p>Welcome to E-ticket Myanmar. By using our services, you agree to be bound by the following terms and conditions. Please read them carefully before making a booking.</p>

    <h5>1. Ticket Booking & Payments</h5>
    <p>All bookings must be confirmed via our online platform. Payments can be made through available digital payment methods. Once payment is made, a digital ticket will be issued.</p>

    <h5>2. Cancellations & Refunds</h5>
    <p>Cancellations must be made at least 24 hours before departure. Refunds (if eligible) will be processed through the same payment method after deducting cancellation charges.</p>

    <h5>3. Travel Responsibilities</h5>
    <p>Passengers must arrive at the bus station at least 30 minutes before departure. Failure to arrive on time may result in a missed trip without refund.</p>

    <h5>4. Privacy Policy</h5>
    <p>Your personal information is collected solely for booking and service improvement purposes. We do not share or sell customer data with third parties without consent.</p>

    <h5>5. Changes to Service</h5>
    <p>E-ticket Myanmar reserves the right to modify routes, schedules, and pricing without prior notice in case of operational or legal requirements.</p>

    <h5>6. Customer Support</h5>
    <p>For any inquiries or issues, you may contact our support team via email or phone. We aim to respond within 24 hours.</p>

    <p class="mt-4">By continuing to use our services, you acknowledge that you have read and agree to abide by these terms and conditions.</p>
  </div>
</section>

<!-- Footer -->
<footer style="background-color: #374151; color: white; padding: 40px 0 0 0; font-family: Arial, sans-serif;">
  <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
    <div style="display: flex; flex-direction: column; align-items: center; padding-bottom: 30px; border-bottom: 1px solid #ccc; text-align: center;">
      <div style="flex: 1 1 60%; min-width: 300px;">
        <h3 style="color: #ffffff; margin-bottom: 15px;">E-ticket Story</h3>
        <p style="color: #d1d5db; font-size: 14px;">E-ticket is a modern travel solution redefining bus travel comfort and convenience. We're committed to providing seamless ticketing experiences through web and mobile apps.</p>
        <p style="color: #d1d5db; font-size: 14px;">We proudly serve major cities: Yangon, Mandalay, Bagan, Taunggyi, Kalaw, and Naypyidaw.</p>
      </div>
      <div style="flex: 1 1 30%; min-width: 250px; text-align: center;">
        <h4 style="color: #ffffff; margin-bottom: 15px;">We Accept</h4>
        <div style="display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
          <img src="images/visa1.jpg" alt="Visa" style="height: 35px;">
          <img src="images/master.png" alt="MasterCard" style="height: 35px;">
          <img src="images/MPU1.jpg" alt="MPU" style="height: 35px;">
          <img src="images/wave1.png" alt="WavePay" style="height: 35px;">
          <img src="images/kpay1.jpg" alt="KBZ Pay" style="height: 35px;">
        </div>
      </div>
    </div>

    <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; padding: 20px 0; font-size: 14px;">
      <div style="flex: 1 1 auto; display: flex; flex-wrap: wrap; gap: 20px;">
        <a href="about.php" style="color: #d1d5db;">About Us</a>
        <a href="contact.php" style="color: #d1d5db;">Contact Us</a>
        <a href="affiliate.php" style="color: #d1d5db;">Affiliates</a>
        <a href="career.php" style="color: #d1d5db;">Careers</a>
        <a href="TandC.php" style="color: #d1d5db;">Terms & Conditions</a>
        <a href="#" style="color: #d1d5db;">Privacy Policy</a>
      </div>
      <div style="flex: 1 1 auto; text-align: right;">
        <a href="#" style="display: inline-block; background-color: #2563eb; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none;">
          <img src="images/Facebook.webp" alt="Facebook" style="height: 30px; vertical-align: middle; margin-right: 8px;">
          Like Us, Love Us
        </a>
      </div>
    </div>

    <div style="background-color: #111827; text-align: center; color: #9ca3af; padding: 15px; font-size: 13px;">
      © 2025 <span style="color:rgba(224, 227, 232, 0.9);">E-ticket</span>. All Rights Reserved.
    </div>
  </div>
</footer>

<!-- Scripts -->
<script>
  window.addEventListener("load", function () {
    document.getElementById("loader").style.display = "none";
  });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init();
</script>
</body>
</html>
