<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $from = $_POST['from'] ?? '';
    $to = $_POST['to'] ?? '';
    $date = $_POST['date'] ?? '';
    $passenger = $_POST['passenger'] ?? '';
    $type = $_POST['type'] ?? '';
    $group = $_POST['group'] ?? '';

    header("Location: route.php?from=$from&to=$to&date=$date&passenger=$passenger&type=$type&group=$group");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>E-ticket</title>
  <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">

  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
    }

    .navbar {
      background-color:#1E3A8A;
      color: white;
      display: flex;
      align-items: center;
      padding: 10px 20px;
      justify-content: space-between;
    }

    .logo {
      font-weight: bold;
      font-size: 24px;
    }

    .nav-links {
      list-style-type: none;
      display: flex;
      gap: 20px;
    }

    .nav-links li {
      position: relative;
    }

    .nav-links a {
      color: white;
      text-decoration: none;
      padding: 8px;
    }

    .dropdown-content {
      display: none;
      position: absolute;
      background-color: white;
      color: black;
      min-width: 160px;
      box-shadow: 0px 8px 16px rgba(0,0,0,0.2);
      z-index: 1;
    }

    .dropdown-content a {
      color: black;
      padding: 12px 16px;
      display: block;
      text-decoration: none;
    }

    .dropdown-content a:hover {
      background-color: #f1f1f1;
    }

    .dropdown:hover .dropdown-content {
      display: block;
    }

    .bookings-btn {
      background-color:rgb(76, 175, 89);
      padding: 10px 16px;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }
    .top-bar {
      background-color:rgb(0, 179, 107); 
      color: white;
      padding: 6px 20px;
      display: flex;
      justify-content: space-between;
      font-size: 14px;
      align-items: center;
    }

    .top-bar a {
      color: white;
      text-decoration: none;
      margin-left: 10px;
    }

    .top-bar a:hover {
      text-decoration: underline;
    }

    article{
      transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    article:hover{
      transform: translate(-8px) scale(1.02);
      box-shadow: 0 6px 15px rgba(0,0,0,0.15);
    }

    .navbar {
      background-color: #1E3A8A; 
    }

    .top-bar {
      background-color: #E5E1DC; 
      color: #1E3A8A; 
    }

    .top-bar a {
      color: #1E3A8A; 
    }

    .bookings-btn {
      background-color: #0EA5E9; 
    }
       .banner h1{
      padding: 40px 20px;
      color: black;
      text-align: center;
      margin-bottom: 0;
    }
    .banner p{
      color: black;
    }
    .search-wrapper {
      background-color: #0c3d91;
      border-radius: 20px;
      padding: 20px 30px;
      color: white;
      margin-top: -20px;
      margin-bottom: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
      position: relative;
      z-index: 10;
    }
    .search-wrapper .form-label {
      font-weight: bold;
      font-size: 14px;
    }
    .search-wrapper input,
    .search-wrapper select {
      border-radius: 10px;
      font-size: 14px;
    }
    .search-wrapper .form-control::placeholder {
      color: #999;
    }
    .search-wrapper .btn-search {
      background-color: #28a745;
      border: none;
      padding: 12px 25px;
      border-radius: 10px;
      color: white;
      font-weight: bold;
      box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
    .search-wrapper .btn-search:hover {
      background-color: #218838;
    }
    .search-wrapper .promo-code {
      background-color: white;
      color: black;
      border-radius: 8px;
      padding: 10px;
      border: none;
      width: 200px;
    }

    footer {
      background-color: #374151; 
    }
     .banner {
      background: url('images/banner.jpg') center/cover no-repeat;
      padding: 80px 20px;
      color: white;
      text-align: center;
    }
   
    
  </style>
</head>

<body>

  <!-- Loader -->
  <div id="loader" style="
    position: fixed;
    top: 0; left: 0;
    width: 100%; height: 100%;
    background: white;
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    flex-direction: column;
  ">
    <div style="
      border: 8px solid #f3f3f3;
      border-top: 8px solid #3498db;
      border-radius: 50%;
      width: 60px;
      height: 60px;
      animation: spin 10s linear infinite;
    "></div>
    <p style="margin-top: 10px; color: #3498db;">Loading...</p>
  </div>

  <style>
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
  </style>

<!-- Top Bar -->
<div class="top-bar d-flex justify-content-between align-items-center px-3">
  <div>📞 +959965509210 / ✉️ e-ticketmyanmar@nonipoly.net</div>
  <div>
    <?php if (isset($_SESSION['user_id'])): ?>           <!-- ▲ CHANGED -->
      Welcome, <?= htmlspecialchars($_SESSION['user']) ?> |
      <a href="logout.php">Logout</a>
    <?php else: ?>
      <a href="login.php">Login</a> |
      <a href="register.php">Register</a>
    <?php endif; ?>
  </div>
</div>

  <!-- Navbar -->
  <div class="navbar">
    <div class="logo">E-ticket Myanmar</div>
    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
      <li><a href="about.php">About Us</a></li>
      <li class="dropdown">
        <a href="#">Routes ▾</a>
        <div class="dropdown-content">
          <a href="routes.php?type=popular">Popular Routes</a>
          <a href="routes.php?type=recent">Recent Routes</a>
        </div>
      </li>
      <li class="dropdown">
        <a href="#">Help ▾</a>
        <div class="dropdown-content">
          <a href="help.php?type=faq">FAQ</a>
          <a href="help.php?type=contact">Contact Us</a>
        </div>
      </li>
    </ul>
    <div style="display: flex; gap: 10px; align-items: center;">
      <a href="My_Booking.php" class="bookings-btn">My Bookings</a>
      <button onclick="toggleChatbot()" style="background-color: #10B981; border: none; color: white; padding: 10px 16px; border-radius: 5px; cursor: pointer;">
        💬 Chatbot
      </button>
    </div>
  </div>



  <script>
    window.addEventListener("load", function(){
      document.getElementById("loader").style.display = "none";
    });
  </script>
  <script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
  <script>
    AOS.init();
  </script>

   <div class="banner">
  <h1>Search Express Bus Tickets in Myanmar</h1>
  <p>Safe, reliable, and affordable travel with E-ticket Myanmar</p>
</div>

<div class="container search-wrapper mt-4">
  <form class="row g-3" action="route.php" method="GET">
    <div class="col-md-2">
      <select class="form-select"name="ticket" required>
        <option selected>Ticket Type</option>
        <option value="1">Normal</option>
        <option value="2">VIP</option>
      </select>
    </div>
    <div class="col-md-3">
      <label class="form-label">From</label>
      <select class="form-select"name="from" required>
        <option>Yangon (ရန်ကုန်)</option>
        <option>Mandalay (မန္တလေး)</option>
        <option>Taunggyi (တောင်ကြီး)</option>
        <option>Bagan (ပုဂံ)</option>
        <option>Naypyitaw (နေပြည်တော်)</option>
        <option>Meiktila (မိတ္ထီလာ)</option>
        <option>Monywa (မုံရွာ)</option>
        <option>Bago (ပဲခူး)</option>
        <option>KyaukSe (ကျောက်ဆည်)</option>
        <option>Pyin Oo Lwin (ပြင်ဦးလင်း)</option>
        <option>Naung Cho (နောင်ချို)</option>
      </select>
      <small class="text-warning">* Choose Taunggyi (From/To) for Inle lake(Nyaung Shwe), Kalaw</small>
    </div>
    <div class="col-md-3">
      <label class="form-label">To</label>
      <select class="form-select" name="to" required>
        <option>Yangon (ရန်ကုန်)</option>
        <option>Mandalay (မန္တလေး)</option>
        <option>Taunggyi (တောင်ကြီး)</option>
        <option>Bagan (ပုဂံ)</option>
        <option>Naypyitaw (နေပြည်တော်)</option>
        <option>Meiktila (မိတ္ထီလာ)</option>
        <option>Monywa (မုံရွာ)</option>
        <option>Bago (ပဲခူး)</option>
        <option>KyaukSe (ကျောက်ဆည်)</option>
        <option>Pyin Oo Lwin (ပြင်ဦးလင်း)</option>
        <option>Naung Cho (နောင်ချို)</option>
      </select>
    </div>
    <div class="col-md-2">
      <label class="form-label">Depart on</label>
      <input type="date" class="form-control"name="depart" required>
    </div>
    <div class="col-md-2">
      <label class="form-label">Passenger</label>
      <select class="form-select"name="passenger" required>
        <option>1 Person</option>
        <option>2 Persons</option>
        <option>3 Persons</option>
        <option>4 Persons</option>
        <option>Group (Male)</option>
        <option>Group (Female)</option>
        <option>Group (Monk)</option>
      </select>
    </div>
    <div class="col-md-12 d-flex justify-content-between align-items-center">
      <a href="#" class="text-white text-decoration-underline">FAQ ?</a>
      <input type="text" class="promo-code" name="promo" placeholder="Promo Code">
      <button class="btn-search" type="submit">SEARCH <span>&#128269;</span></button>
    </div>
  </form>
</div>
  <!-- Travel News Section -->
  <section style="padding: 40px 20px; font-family: Arial, sans-serif; background-color: #f9f9f9;">
    <div style="max-width: 1200px; margin: 0 auto;">
      <h2 style="margin-bottom: 30px; font-size: 24px; color: #374151;">E-ticket News</h2>

      <div style="display: flex; flex-wrap: wrap; gap: 20px; justify-content: space-between;">

        <!-- News Card 1 -->
        <article style="flex: 1 1 250px; max-width: 270px; background-color: #fff; border: 1px solid #ddd; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
          <img src="images/images.jpg" alt="E-ticket News 1" style="width: 100%; height: 160px; object-fit: cover;">
          <div style="padding: 15px;">
            <h3 style="font-size: 16px; margin-bottom: 10px; color: #333;">E-ticket ရဲ့ သတင်းဌာန...</h3>
            <p style="font-size: 14px; color: #666;">E-ticket နှင့် ခရီးသွားမယ်ဆိုရင် စိတ်ချလို့ရပါတယ်။ ခရီးစဉ်သစ်များနှင့် မိမိစိတ်ကြိုက်...</p>
            <a href="#" style="color: #374151; font-weight: bold; text-decoration: none;">Read More →</a>
          </div>
        </article>

        <!-- News Card 2 -->
        <article style="flex: 1 1 250px; max-width: 270px; background-color: #fff; border: 1px solid #ddd; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
          <img src="images/snack.avif" alt="E-ticket News 2" style="width: 100%; height: 160px; object-fit: cover;">
          <div style="padding: 15px;">
            <h3 style="font-size: 16px; margin-bottom: 10px; color: #333;">အခမဲ့ မုန့်လက်ဆောင်...</h3>
            <p style="font-size: 14px; color: #666;">ခရီးစဉ်များအတွက် 5,000Ks Meal Coupon သီးသန့်ပေးအပ်ပါသည်။ စတင် 7th June 2025...</p>
            <a href="#" style="color: #374151; font-weight: bold; text-decoration: none;">Read More →</a>
          </div>
        </article>

        <!-- News Card 3 -->
        <article style="flex: 1 1 250px; max-width: 270px; background-color: #fff; border: 1px solid #ddd; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
          <img src="images/promotion.jpg" alt="E-ticket News 3" style="width: 100%; height: 160px; object-fit: cover;">
          <div style="padding: 15px;">
            <h3 style="font-size: 16px; margin-bottom: 10px; color: #333;">နွေရာသီ အထူးပရိုမိုရှင်း...</h3>
            <p style="font-size: 14px; color: #666;">E-ticket နှင့်အတူလှည့်လည်ရန် အထူးသင့်တော်သော နွေရာသီခရီးစဉ်များ...</p>
            <a href="#" style="color: #374151; font-weight: bold; text-decoration: none;">Read More →</a>
          </div>
        </article>

        <!-- News Card 4 -->
        <article style="flex: 1 1 250px; max-width: 270px; background-color: #fff; border: 1px solid #ddd; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 6px rgba(0,0,0,0.08);">
          <img src="images/mobile.png" alt="E-ticket News 4" style="width: 100%; height: 160px; object-fit: cover;">
          <div style="padding: 15px;">
            <h3 style="font-size: 16px; margin-bottom: 10px; color: #333;">Mobile Application အသစ်...</h3>
            <p style="font-size: 14px; color: #666;">E-ticket Myanmar Mobile App ကို Google Play Store မှာ ရယူနိုင်ပါပြီ။...</p>
            <a href="#" style="color: #374151; font-weight: bold; text-decoration: none;">Read More →</a>
          </div>
        </article>

      </div>
    </div>
  </section>

  <!-- Footer Section -->
  <footer style="background-color: #374151; color: white; padding: 40px 0 0 0; font-family: Arial, sans-serif;">
    <div style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">

      <!-- Top Row -->
      <div style="display: flex; flex-direction: column; align-items: center; padding-bottom: 30px; border-bottom: 1px solid #ccc; text-align: center;">

        <!-- E-ticket Story -->
        <div style="flex: 1 1 60%; min-width: 300px;">
          <h3 style="color: #ffffff; margin-bottom: 15px;">E-ticket Story</h3>
          <p style="color: #d1d5db; font-size: 14px; line-height: 1.6;">
            E-ticket is a modern travel solution redefining bus travel comfort and convenience. We're committed to providing seamless ticketing experiences through our web and mobile apps, enabling easy booking and secure digital payments. Whether you're planning a city commute or a countryside journey, E-ticket ensures safety, efficiency, and satisfaction.
          </p>
          <p style="color: #d1d5db; font-size: 14px; line-height: 1.6;">
            We proudly serve routes across major cities: Yangon, Mandalay, Bagan, Taunggyi, Kalaw, and Naypyidaw. Most of our services include both day and night options depending on demand. Stay updated with promotions and routes through our official platform.
          </p>
        </div>

        <!-- Payment Methods -->
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

      <!-- Bottom Row -->
      <div style="display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; padding: 20px 0; font-size: 14px;">
        
        <!-- Links -->
        <div style="flex: 1 1 auto; display: flex; flex-wrap: wrap; gap: 20px;">
          <a href="about.php" style="color: #d1d5db; text-decoration: none;">About Us</a>
          <a href="contact.php" style="color: #d1d5db; text-decoration: none;">Contact Us</a>
          <a href="affiliate.php" style="color: #d1d5db; text-decoration: none;">Affiliates</a>
          <a href="career.php" style="color: #d1d5db; text-decoration: none;">Careers</a>
          <a href="TandC.php" style="color: #d1d5db; text-decoration: none;">Terms & Conditions</a>
          <a href="privacy.php" style="color: #d1d5db; text-decoration: none;">Privacy / Cookies Policy</a>
        </div>

        <!-- Social -->
        <div style="flex: 1 1 auto; text-align: right;">
          <a href="#" style="display: inline-block; background-color: #2563eb; color: white; padding: 10px 15px; border-radius: 5px; text-decoration: none; font-weight: bold;">
            <img src="images/Facebook.webp" alt="Facebook" style="height: 30px; vertical-align: middle; margin-right: 8px;">
            Like Us, Love Us
          </a>
        </div>
      </div>

      <!-- Copyright -->
      <div style="background-color: #111827; text-align: center; color: #9ca3af; padding: 15px; font-size: 13px;">
        © 2025 <span style="color:rgba(224, 227, 232, 0.9);">E-ticket</span>. All Rights Reserved.
      </div>

    </div>
  </footer>

  <!-- Chatbot Box -->
  <div id="chatbot-box" style="position: fixed; bottom: 90px; right: 20px; width: 300px; background: white; border: 1px solid #ccc; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.2); display: none; z-index: 9999;">
    <div style="background-color: #1E3A8A; color: white
