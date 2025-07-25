<?php
session_start();
require 'config.php';

$routeId = (int)($_GET['route_id'] ?? 0);
if (!$routeId) { http_response_code(400); exit('Route missing'); }

/* 1  Load route + bus */
$stmt = $pdo->prepare(
  'SELECT r.*, b.bus_id, b.bus_name, b.seat_layout, b.capacity, r.price
     FROM routes r
     JOIN buses  b ON b.bus_id = r.bus_id
    WHERE r.route_id = ?'
);
$stmt->execute([$routeId]);
$route = $stmt->fetch();
if (!$route) { http_response_code(404); exit('Route not found'); }

/* 2  Occupied seats */
$booked = $pdo->prepare(
  'SELECT seat_no FROM bookings WHERE route_id = ? AND bus_id = ?'
);
$booked->execute([$routeId, $route['bus_id']]);
$occupiedSeats = array_column($booked->fetchAll(), 'seat_no');

/* 3  Handle POST – now $route and $occupiedSeats exist */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seat = $_POST['seat_number']    ?? '';
    $pay  = $_POST['payment_method'] ?? 'cash';

    if ($seat === '' || in_array($seat, $occupiedSeats, true)) {
        $error = 'Seat invalid or already taken';
    } else {
        $ins = $pdo->prepare(
          'INSERT INTO bookings
             (user_id, bus_id, route_id, seat_no,
              booking_date, status, ticket_code,
              total_price, payment_method)
           VALUES (?,?,?,?,NOW(),"pending",?,?,?)'
        );
        $ticketCode = 'TCK'.rand(100000,999999);
        $ins->execute([
            $_SESSION['user_id'],
            $route['bus_id'],
            $routeId,
            $seat,
            $ticketCode,
            $route['price'],
            $pay
        ]);
        header('Location: my_booking.php?success=1');
        exit();
    }
}

/* 4  Build seat grid … (existing HTML/JS stays unchanged) */
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Select Seat – <?= $route['origin'].' → '.$route['destination'] ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .seat      { width:40px; height:40px; border:1px solid #ccc;
                 display:flex; align-items:center; justify-content:center;
                 margin:2px; cursor:pointer; border-radius:4px; }
    .free      { background:#f7f7f7; }
    .selected  { background:#0d6efd; color:#fff; }
    .taken     { background:#adb5bd; cursor:not-allowed; }
    .aisle     { width:25px; }
  </style>
</head>
<body class="bg-light">
<div class="container py-4">

<h4 class="mb-3">Choose your seat – <?= htmlspecialchars($route['bus_name']) ?> (<?= $layout ?>)</h4>

<form id="seatForm" method="post">
  <input type="hidden" name="seat_number" id="seatInput">
  <input type="hidden" name="payment_method" value="cash">   <!-- default -->
  <div class="d-inline-block p-3 bg-white shadow rounded">

  <?php
  $seatIndex = 0;
  for ($r = 0; $r < $rows; $r++) {
      echo '<div class="d-flex">';
      /* left block */
      for ($c=0; $c<$left; $c++, $seatIndex++) {
          $seat = $alphabet[$r].($c+1);
          $state = in_array($seat,$occupiedSeats) ? 'taken' : 'free';
          echo "<div class='seat $state' data-seat='$seat'>$seat</div>";
      }
      echo '<div class="aisle"></div>'; /* aisle gap */
      /* right block */
      for ($c=0; $c<$right; $c++, $seatIndex++) {
          $seat = $alphabet[$r].($left+$c+1);
          $state = in_array($seat,$occupiedSeats) ? 'taken' : 'free';
          echo "<div class='seat $state' data-seat='$seat'>$seat</div>";
      }
      echo '</div>';
  }
  ?>
  </div>

  <p class="mt-3">Selected Seat: <span id="chosenSeat" class="fw-bold text-primary">None</span></p>

  <button type="submit" class="btn btn-success" disabled id="confirmBtn">
      Confirm Booking
  </button>
</form>
</div>

<script>
const seats      = document.querySelectorAll('.seat.free');
const chosenSpan = document.getElementById('chosenSeat');
const seatInput  = document.getElementById('seatInput');
const confirmBtn = document.getElementById('confirmBtn');

seats.forEach(s => s.addEventListener('click', () => {
    seats.forEach(x => x.classList.remove('selected'));
    s.classList.add('selected');
    const seat = s.dataset.seat;
    chosenSpan.textContent = seat;
    seatInput.value = seat;
    confirmBtn.disabled = false;
}));
</script>
</body>
</html>
