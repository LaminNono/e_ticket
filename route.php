<?php
require 'config.php';                           // DB connection

/* collect filters */
$ticket    = $_GET['ticket']    ?? '';
$from      = $_GET['from']      ?? '';
$to        = $_GET['to']        ?? '';
$depart    = $_GET['depart']    ?? '';
$passenger = $_GET['passenger'] ?? '';
$group     = $_GET['group']     ?? '';
$promo     = $_GET['promo']     ?? '';

/* pull matching routes */
$sql = "
 SELECT r.route_id, r.depart_time, r.arrival_time, r.price,
        b.bus_name, b.seat_layout, b.capacity, b.image_path
 FROM   routes r
 JOIN   buses  b ON b.bus_id = r.bus_id
 WHERE  (:from = '' OR r.origin      LIKE CONCAT('%',:from,'%'))
   AND  (:to   = '' OR r.destination LIKE CONCAT('%',:to,'%'))
   AND  (:date = '' OR DATE(r.depart_time) = :date)
";
$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':from' => $from,
    ':to'   => $to,
    ':date' => $depart
]);
$routes = $stmt->fetchAll();      // ← used in the loop below
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Routes Result - E-ticket Myanmar</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Segoe UI', sans-serif;
      background-color: #f5f5f5;
    }

    .navbar {
      background-color: #1e3a8a;
    }

    .navbar-brand,
    .nav-link {
      color: white !important;
    }

    .results-header {
      background-color: #1e3a8a;
      padding: 20px;
      color: white;
      border-radius: 10px;
    }

    .bus-card {
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
      margin-bottom: 20px;
    }

    .bus-card img {
      height: 160px;
      object-fit: cover;
    }

    .filter-box {
      background-color: #ffffff;
      padding: 20px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.05);
    }

    footer {
      background-color: #1e3a8a;
      color: white;
      padding: 30px 0;
      text-align: center;
    }

    footer a {
      color: #ddd;
      text-decoration: none;
    }
  </style>
</head>

<body>

  <nav class="navbar navbar-expand-lg">
    <div class="container">
      <a class="navbar-brand" href="index.php">E-ticket Myanmar</a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
          <li class="nav-item"><a class="nav-link" href="routes.php">Routes</a></li>
          <li class="nav-item"><a class="nav-link" href="contact.php">Help</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container mt-4">
    <div class="results-header">
      <h4><?= htmlspecialchars($from) ?> &rarr; <?= htmlspecialchars($to) ?> Bus Routes</h4>
      <p><?= htmlspecialchars($group) ?> | <?= htmlspecialchars($ticket) ?> | <?= htmlspecialchars($depart) ?> | <?= htmlspecialchars($passenger) ?></p>
    </div>

    <div class="row mt-4">
      <!-- Filters -->
      <div class="col-md-3">
        <div class="filter-box">
          <h6><strong>Search Filter</strong></h6>
          <hr>
          <p><strong>Bus Class</strong></p>
          <div><input type="checkbox"> (1+1)VVIP</div>
          <div><input type="checkbox"> (2+1)VIP</div>
          <div><input type="checkbox"> (2+2)General+ </div>
          <div><input type="checkbox"> (2+2) General</div>
          <hr>
          <p><strong>Departure Time</strong></p>
          <div><input type="checkbox"> 08:30 AM</div>
          <div><input type="checkbox"> 09:00 AM</div>
          <div><input type="checkbox"> 10:00 PM</div>
          <div><input type="checkbox"> 10:05 PM</div>
          <button class="btn btn-primary mt-3">Search</button>
        </div>
      </div>

      <!-- Bus Results -->
      <div class="col-md-9">
        <div class="bus-card d-flex">
          <img src="images/bus1.jpg" width="200" alt="Bus">
          <div class="p-3">
            <h5><strong>Super Seat</strong> (2+2)</h5>
            <p>ဧည့်သည် 43 | ထွက်ခွာချိန် 08:30 AM | ခန့်မှန်း ကြာချိန် 10 နာရီ</p>
            <p><strong>Local - 29800 MMK</strong></p>
            <!-- inside the loop that lists routes -->
            <a href="/booking.php?route_id=<?= $row['route_id'] ?>"
              class="btn btn-primary btn-sm">
              Book Now
            </a>

          </div>
        </div>

        <div class="bus-card d-flex">
          <img src="images/bus2.jpg" width="200" alt="Bus">
          <div class="p-3">
            <h5><strong>(2+1)</strong> Direct to Ho Pong</h5>
            <p>ဧည့်သည် 40 | ထွက်ခွာချိန် 10:05 PM | ခန့်မှန်း ကြာချိန် 9 နာရီ</p>
            <p><strong>Local - 25800 MMK</strong></p>
            <!-- inside the loop that lists routes -->
            <a href="/booking.php?route_id=<?= $row['route_id'] ?>"
              class="btn btn-primary btn-sm">
              Book Now
            </a>

          </div>
        </div>

        <div class="bus-card d-flex">
          <img src="images/bus3.jpg" width="200" alt="Bus">
          <div class="p-3">
            <h5><strong>Standard Seat</strong> (2+2)</h5>
            <p>ဧည့်သည် 50 | ထွက်ခွာချိန် 09:00 AM | ခန့်မှန်း ကြာချိန် 11 နာရီ</p>
            <p><strong>Local - 24800 MMK</strong></p>
            <!-- inside the loop that lists routes -->
            <a href="/booking.php?route_id=<?= $row['route_id'] ?>"
              class="btn btn-primary btn-sm">
              Book Now
            </a>

          </div>
        </div>
      </div>
    </div>
  </div>

  <footer class="mt-5">
    <div class="container">
      <p>&copy; 2025 E-ticket Myanmar. All Rights Reserved.</p>
      <div>
        <a href="about.php">About Us</a> |
        <a href="contact.php">Contact</a> |
        <a href="TandC.php">Terms</a> |
        <a href="privacy.php">Privacy</a>
      </div>
    </div>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>