<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Careers - E-ticket Myanmar</title>
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
    .nav-links a, .top-bar a {
      color: white;
      text-decoration: none;
    }
    .top-bar {
      background-color: #E5E1DC;
      color: #1E3A8A;
      padding: 6px 20px;
      font-size: 14px;
    }
    .bookings-btn {
      background-color: #0EA5E9;
      padding: 10px 16px;
      border-radius: 5px;
      color: white;
      text-decoration: none;
    }
    .career-section {
      padding: 60px 20px;
      background-color: #f9f9f9;
    }
    .career-section h2 {
      color: #1E3A8A;
      margin-bottom: 20px;
    }
    .career-section p {
      font-size: 16px;
      color: #374151;
      line-height: 1.8;
    }
    .highlight-box {
      background-color: #0EA5E9;
      color: white;
      padding: 30px;
      border-radius: 12px;
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
          <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">Help</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="help.php?type=faq">FAQ</a></li>
            <li><a class="dropdown-item" href="contact.php">Contact Us</a></li>
          </ul>
        </li>
        <li class="nav-item"><a class="nav-link text-white" href="affiliate.php">Affiliates</a></li>
        <li class="nav-item"><a class="nav-link text-white active" href="career.php">Careers</a></li>
      </ul>
      <a class="bookings-btn" href="#">My Bookings</a>
    </div>
  </nav>

  <!-- Career Content -->
  <section class="career-section">
    <div class="container">
      <div class="row align-items-center" data-aos="fade-up">
        <div class="col-md-12">
          <h2>Join Our Team</h2>
          <p>
            At E-ticket Myanmar, we’re transforming how people travel across the country. We believe technology and teamwork can improve lives by making journeys more convenient and efficient. We’re looking for passionate, creative, and driven individuals to be part of our growing team.
          </p>
          <p>
            Whether you’re a developer, marketer, customer support specialist, or transportation expert, there’s a place for you here. We offer flexible roles, inclusive culture, and opportunities for career advancement.
          </p>
        </div>
      </div>

      <div class="row mt-5">
        <div class="col-md-12 highlight-box text-center" data-aos="fade-up">
          <h4>“Be part of Myanmar’s digital transport revolution.”</h4>
          <p class="mt-3">Check back soon for job openings or email your resume to <strong>hr@eticketmyanmar.com</strong>.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer style="background-color: #374151; color: white; padding: 40px 0 0 0; font-family: Arial, sans-serif;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">
      <div style="display: flex; flex-direction: column; align-items: center; padding-bottom: 30px; border-bottom: 1px solid #ccc; text-align: center;">
        <div style="flex: 1 1 60%; min-width: 300px;">
          <h3 style="color: #ffffff; margin-bottom: 15px;">E-ticket Story</h3>
          <p style="color: #d1d5db; font-size: 14px; line-height: 1.6;">
            E-ticket is a modern travel solution redefining bus travel comfort and convenience. We're committed to providing seamless ticketing experiences through our web and mobile apps, enabling easy booking and secure digital payments.
          </p>
          <p style="color: #d1d5db; font-size: 14px; line-height: 1.6;">
            We proudly serve routes across major cities: Yangon, Mandalay, Bagan, Taunggyi, Kalaw, and Naypyidaw. Stay updated with promotions and routes through our official platform.
          </p>
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
          <a href="about.php" style="color: #d1d5db; text-decoration: none;">About Us</a>
          <a href="contact.php" style="color: #d1d5db; text-decoration: none;">Contact Us</a>
          <a href="affiliate.php" style="color: #d1d5db; text-decoration: none;">Affiliates</a>
          <a href="career.php" style="color: #d1d5db; text-decoration: none;">Careers</a>
          <a href="#" style="color: #d1d5db; text-decoration: none;">Terms & Conditions</a>
          <a href="#" style="color: #d1d5db; text-decoration: none;">Privacy / Cookies Policy</a>
        </div>
        <div style="flex: 1 1 auto; text-align: right;">
          <a href="#" style="display: inline-block; background-color: #2563eb; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; font-weight: bold;">
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
