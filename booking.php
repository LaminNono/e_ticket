<?php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit();
}

$routeId = (int)($_GET['route_id'] ?? 0);
if (!$routeId) { 
    http_response_code(400); 
    exit('Route missing'); 
}

/* 1  Load route + bus */
$stmt = $pdo->prepare(
  'SELECT r.*, b.bus_id, b.bus_name, b.seat_layout, b.capacity, b.type, rb.price
     FROM routes r
     JOIN route_buses rb ON rb.route_id = r.route_id
     JOIN buses  b ON b.bus_id = rb.bus_id
    WHERE r.route_id = ? AND rb.status = "active"'
);
$stmt->execute([$routeId]);
$route = $stmt->fetch();
if (!$route) { 
    http_response_code(404); 
    exit('Route not found'); 
}

/* 2  Occupied seats */
$booked = $pdo->prepare(
  'SELECT seat_no FROM bookings WHERE route_id = ? AND bus_id = ? AND status != "cancelled"'
);
$booked->execute([$routeId, $route['bus_id']]);
$occupiedSeats = array_column($booked->fetchAll(), 'seat_no');

/* 3  Handle POST */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seat = $_POST['seat_number'] ?? '';
    $pay  = $_POST['payment_method'] ?? 'cash';
    $passengerName = $_POST['passenger_name'] ?? '';
    $passengerPhone = $_POST['passenger_phone'] ?? '';
    $passengerNRC = $_POST['passenger_nrc'] ?? '';

    if ($seat === '' || in_array($seat, $occupiedSeats, true)) {
        $error = 'Seat invalid or already taken';
    } elseif (empty($passengerName) || empty($passengerPhone)) {
        $error = 'Please fill in all passenger details';
    } else {
        // Start transaction
        $pdo->beginTransaction();
        try {
            $ins = $pdo->prepare(
              'INSERT INTO bookings
                 (user_id, bus_id, route_id, seat_no, passenger_name, passenger_phone, passenger_nrc,
                  booking_date, status, ticket_code, total_price, payment_method)
               VALUES (?,?,?,?,?,?,?,NOW(),"pending",?,?,?)'
            );
            $ticketCode = 'TCK'.date('Ymd').rand(1000,9999);
            $ins->execute([
                $_SESSION['user_id'],
                $route['bus_id'],
                $routeId,
                $seat,
                $passengerName,
                $passengerPhone,
                $passengerNRC,
                $ticketCode,
                $route['price'],
                $pay
            ]);
            
            $bookingId = $pdo->lastInsertId();
            $pdo->commit();
            
            header('Location: booking-confirmation.php?booking_id=' . $bookingId);
            exit();
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Booking failed. Please try again.';
        }
    }
}

// Parse seat layout (e.g., "2+2" means 2 seats on left, 2 on right)
$layout = $route['seat_layout'] ?? '2+2';
$parts = explode('+', $layout);
$left = (int)($parts[0] ?? 2);
$right = (int)($parts[1] ?? 2);
$rows = ceil($route['capacity'] / ($left + $right));

// Generate seat alphabet (A, B, C, etc.)
$alphabet = range('A', 'Z');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Seat – <?= htmlspecialchars($route['origin']).' → '.htmlspecialchars($route['destination']) ?> |
        E-ticket Myanmar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f8f9fa;
    }

    .navbar {
        background-color: #1e3a8a;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .navbar-brand,
    .nav-link {
        color: white !important;
    }

    .booking-header {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        padding: 30px 20px;
        color: white;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .route-info {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 30px;
        border: 1px solid #e9ecef;
    }

    .seat-container {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
    }

    .seat-grid {
        display: inline-block;
        background: #f8f9fa;
        padding: 30px;
        border-radius: 15px;
        border: 2px solid #e9ecef;
    }

    .seat-row {
        display: flex;
        align-items: center;
        margin-bottom: 10px;
    }

    .seat {
        width: 50px;
        height: 50px;
        border: 2px solid #dee2e6;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 2px;
        cursor: pointer;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s ease;
        position: relative;
    }

    .seat.free {
        background: #ffffff;
        color: #495057;
        border-color: #28a745;
    }

    .seat.free:hover {
        background: #e8f5e8;
        transform: scale(1.05);
        box-shadow: 0 4px 8px rgba(40, 167, 69, 0.3);
    }

    .seat.selected {
        background: linear-gradient(45deg, #28a745, #20c997);
        color: white;
        border-color: #28a745;
        transform: scale(1.1);
        box-shadow: 0 6px 12px rgba(40, 167, 69, 0.4);
    }

    .seat.taken {
        background: #6c757d;
        color: white;
        border-color: #6c757d;
        cursor: not-allowed;
        opacity: 0.7;
    }

    .aisle {
        width: 40px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #6c757d;
        font-weight: bold;
    }

    .driver-area {
        background: #343a40;
        color: white;
        padding: 10px;
        border-radius: 8px;
        text-align: center;
        margin-bottom: 20px;
        font-weight: bold;
    }

    .seat-legend {
        display: flex;
        gap: 20px;
        margin-top: 20px;
        justify-content: center;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .legend-seat {
        width: 30px;
        height: 30px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }

    .passenger-form {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
    }

    .form-control:focus {
        border-color: #1e3a8a;
        box-shadow: 0 0 0 0.2rem rgba(30, 58, 138, 0.25);
    }

    .btn-confirm {
        background: linear-gradient(45deg, #28a745, #20c997);
        border: none;
        padding: 15px 40px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s ease;
    }

    .btn-confirm:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(40, 167, 69, 0.4);
    }

    .btn-confirm:disabled {
        background: #6c757d;
        cursor: not-allowed;
    }

    .price-display {
        background: linear-gradient(45deg, #f59e0b, #d97706);
        color: white;
        padding: 15px 25px;
        border-radius: 25px;
        font-weight: bold;
        font-size: 18px;
        text-align: center;
        margin-bottom: 20px;
    }

    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #f5c6cb;
        margin-bottom: 20px;
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #c3e6cb;
        margin-bottom: 20px;
    }
    </style>
</head>

<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">
                <i class="bi bi-bus-front"></i> E-ticket Myanmar
            </a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="My_Booking.php">My Bookings</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Booking Header -->
        <div class="booking-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="mb-2">
                        <i class="bi bi-ticket-perforated"></i> Select Your Seat
                    </h3>
                    <p class="mb-0">
                        <?= htmlspecialchars($route['origin']) ?> <i class="bi bi-arrow-right"></i>
                        <?= htmlspecialchars($route['destination']) ?>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <h4 class="mb-0"><?= htmlspecialchars($route['bus_name']) ?></h4>
                    <small><?= htmlspecialchars($route['type']) ?> •
                        <?= htmlspecialchars($route['seat_layout']) ?></small>
                </div>
            </div>
        </div>

        <?php if (isset($error)): ?>
        <div class="error-message">
            <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Seat Selection -->
            <div class="col-lg-8">
                <div class="seat-container">
                    <h5 class="mb-4">
                        <i class="bi bi-grid-3x3-gap"></i> Seat Layout
                    </h5>

                    <div class="seat-grid">
                        <!-- Driver Area -->
                        <div class="driver-area">
                            <i class="bi bi-steering-wheel"></i> DRIVER
                        </div>

                        <!-- Seat Grid -->
                        <?php
            $seatIndex = 0;
            for ($r = 0; $r < $rows; $r++) {
                echo '<div class="seat-row">';
                // Left block
                for ($c = 0; $c < $left; $c++, $seatIndex++) {
                    $seat = $alphabet[$r].($c+1);
                    $state = in_array($seat, $occupiedSeats) ? 'taken' : 'free';
                    echo "<div class='seat $state' data-seat='$seat'>$seat</div>";
                }
                echo '<div class="aisle"><i class="bi bi-arrow-left-right"></i></div>'; // aisle gap
                // Right block
                for ($c = 0; $c < $right; $c++, $seatIndex++) {
                    $seat = $alphabet[$r].($left+$c+1);
                    $state = in_array($seat, $occupiedSeats) ? 'taken' : 'free';
                    echo "<div class='seat $state' data-seat='$seat'>$seat</div>";
                }
                echo '</div>';
            }
            ?>
                    </div>

                    <!-- Seat Legend -->
                    <div class="seat-legend">
                        <div class="legend-item">
                            <div class="legend-seat" style="background: #28a745; color: white;">A1</div>
                            <span>Available</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-seat" style="background: #6c757d; color: white;">A2</div>
                            <span>Occupied</span>
                        </div>
                        <div class="legend-item">
                            <div class="legend-seat"
                                style="background: linear-gradient(45deg, #28a745, #20c997); color: white;">A3</div>
                            <span>Selected</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Passenger Details & Booking -->
            <div class="col-lg-4">
                <div class="route-info">
                    <h6 class="mb-3"><i class="bi bi-info-circle"></i> Journey Details</h6>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">From</small>
                            <div class="fw-bold"><?= htmlspecialchars($route['origin']) ?></div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">To</small>
                            <div class="fw-bold"><?= htmlspecialchars($route['destination']) ?></div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Departure</small>
                            <div class="fw-bold"><?= date('H:i', strtotime($route['depart_time'])) ?></div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Arrival</small>
                            <div class="fw-bold"><?= date('H:i', strtotime($route['arrival_time'])) ?></div>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-6">
                            <small class="text-muted">Duration</small>
                            <div class="fw-bold">
                                <?= date_diff(date_create($route['depart_time']), date_create($route['arrival_time']))->format('%h hrs %i min') ?>
                            </div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted">Available Seats</small>
                            <div class="fw-bold text-success">
                                <?= $route['capacity'] - count($occupiedSeats) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="passenger-form">
                    <h6 class="mb-3"><i class="bi bi-person"></i> Passenger Details</h6>

                    <form id="bookingForm" method="post">
                        <input type="hidden" name="seat_number" id="seatInput">

                        <div class="mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" name="passenger_name" required
                                value="<?= htmlspecialchars($_POST['passenger_name'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" class="form-control" name="passenger_phone" required
                                value="<?= htmlspecialchars($_POST['passenger_phone'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">NRC Number</label>
                            <input type="text" class="form-control" name="passenger_nrc"
                                value="<?= htmlspecialchars($_POST['passenger_nrc'] ?? '') ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Payment Method</label>
                            <select class="form-select" name="payment_method">
                                <option value="cash">Cash Payment</option>
                                <option value="mpu">MPU Card</option>
                                <option value="wave">Wave Pay</option>
                                <option value="kpay">KBZ Pay</option>
                            </select>
                        </div>

                        <div class="price-display">
                            <div>Total Price</div>
                            <div><?= number_format($route['price']) ?> MMK</div>
                        </div>

                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span>Selected Seat:</span>
                                <span id="chosenSeat" class="fw-bold text-primary">None</span>
                            </div>
                        </div>

                        <button type="submit" class="btn btn-confirm w-100" disabled id="confirmBtn">
                            <i class="bi bi-check-circle"></i> Confirm Booking
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    const seats = document.querySelectorAll('.seat.free');
    const chosenSpan = document.getElementById('chosenSeat');
    const seatInput = document.getElementById('seatInput');
    const confirmBtn = document.getElementById('confirmBtn');
    const bookingForm = document.getElementById('bookingForm');

    // Seat selection
    seats.forEach(s => s.addEventListener('click', () => {
        seats.forEach(x => x.classList.remove('selected'));
        s.classList.add('selected');
        const seat = s.dataset.seat;
        chosenSpan.textContent = seat;
        seatInput.value = seat;
        confirmBtn.disabled = false;
    }));

    // Form validation
    bookingForm.addEventListener('submit', function(e) {
        const seat = seatInput.value;
        const name = document.querySelector('input[name="passenger_name"]').value;
        const phone = document.querySelector('input[name="passenger_phone"]').value;

        if (!seat) {
            e.preventDefault();
            alert('Please select a seat');
            return;
        }

        if (!name || !phone) {
            e.preventDefault();
            alert('Please fill in all required fields');
            return;
        }

        // Show loading state
        confirmBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing...';
        confirmBtn.disabled = true;
    });

    // Auto-fill user details if available
    <?php if (isset($_SESSION['user'])): ?>
    document.addEventListener('DOMContentLoaded', function() {
        const nameInput = document.querySelector('input[name="passenger_name"]');
        if (!nameInput.value) {
            nameInput.value = '<?= htmlspecialchars($_SESSION['user']) ?>';
        }
    });
    <?php endif; ?>
    </script>
</body>

</html>