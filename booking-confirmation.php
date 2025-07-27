<?php
session_start();
require 'config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$bookingId = (int)($_GET['booking_id'] ?? 0);
if (!$bookingId) {
    header('Location: My_Booking.php');
    exit();
}

// Fetch booking details with route and bus information
$stmt = $pdo->prepare("
    SELECT b.*, r.origin, r.destination, r.depart_time, r.arrival_time, r.price,
           bu.bus_name, bu.type, bu.seat_layout
    FROM bookings b
    JOIN routes r ON b.route_id = r.route_id
    JOIN buses bu ON b.bus_id = bu.bus_id
    WHERE b.booking_id = ? AND b.user_id = ?
");
$stmt->execute([$bookingId, $_SESSION['user_id']]);
$booking = $stmt->fetch();

if (!$booking) {
    header('Location: My_Booking.php');
    exit();
}

// Handle payment confirmation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    $paymentMethod = $_POST['payment_method'] ?? 'cash';
    
    $pdo->beginTransaction();
    try {
        // Update booking status
        $updateStmt = $pdo->prepare("UPDATE bookings SET status = 'confirmed', payment_method = ? WHERE booking_id = ?");
        $updateStmt->execute([$paymentMethod, $bookingId]);
        
        // Create payment record
        $paymentStmt = $pdo->prepare("
            INSERT INTO payments (booking_id, amount, payment_method, payment_date, status)
            VALUES (?, ?, ?, NOW(), 'completed')
        ");
        $paymentStmt->execute([$bookingId, $booking['total_price'], $paymentMethod]);
        
        $pdo->commit();
        
        // Redirect to success page
        header('Location: booking-confirmation.php?booking_id=' . $bookingId . '&success=1');
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = 'Payment confirmation failed. Please try again.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation | E-ticket Myanmar</title>
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

    .confirmation-header {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        padding: 40px 20px;
        color: white;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        text-align: center;
    }

    .ticket-card {
        background: white;
        border-radius: 15px;
        padding: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        margin-bottom: 30px;
    }

    .ticket-header {
        border-bottom: 2px solid #e9ecef;
        padding-bottom: 20px;
        margin-bottom: 25px;
    }

    .ticket-code {
        background: linear-gradient(45deg, #1e3a8a, #3b82f6);
        color: white;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: bold;
        font-size: 18px;
        display: inline-block;
    }

    .journey-details {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .detail-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #e9ecef;
    }

    .detail-row:last-child {
        border-bottom: none;
    }

    .detail-label {
        color: #6b7280;
        font-weight: 500;
    }

    .detail-value {
        font-weight: bold;
        color: #374151;
    }

    .passenger-info {
        background: #e8f5e8;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .payment-section {
        background: #fff3cd;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 25px;
    }

    .price-display {
        background: linear-gradient(45deg, #f59e0b, #d97706);
        color: white;
        padding: 15px 25px;
        border-radius: 25px;
        font-weight: bold;
        font-size: 20px;
        text-align: center;
        margin-bottom: 20px;
    }

    .btn-primary {
        background: linear-gradient(45deg, #1e3a8a, #3b82f6);
        border: none;
        padding: 12px 30px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(30, 58, 138, 0.4);
    }

    .btn-success {
        background: linear-gradient(45deg, #28a745, #20c997);
        border: none;
        padding: 12px 30px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(40, 167, 69, 0.4);
    }

    .status-badge {
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: bold;
        font-size: 14px;
    }

    .status-pending {
        background: #fff3cd;
        color: #856404;
    }

    .status-confirmed {
        background: #d4edda;
        color: #155724;
    }

    .qr-code {
        background: white;
        padding: 20px;
        border-radius: 10px;
        text-align: center;
        border: 2px dashed #dee2e6;
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 15px;
        border-radius: 8px;
        border: 1px solid #c3e6cb;
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

    .print-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-top: 30px;
    }

    @media print {

        .navbar,
        .btn,
        .print-section {
            display: none !important;
        }

        .ticket-card {
            box-shadow: none;
            border: 2px solid #000;
        }
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
        <?php if (isset($_GET['success'])): ?>
        <div class="success-message">
            <i class="bi bi-check-circle"></i> Payment confirmed successfully! Your booking is now active.
        </div>
        <?php endif; ?>

        <?php if (isset($error)): ?>
        <div class="error-message">
            <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <!-- Confirmation Header -->
        <div class="confirmation-header">
            <i class="bi bi-check-circle" style="font-size: 48px; margin-bottom: 20px;"></i>
            <h2>Booking Confirmed!</h2>
            <p>Your ticket has been successfully booked. Please review the details below.</p>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <!-- Ticket Details -->
                <div class="ticket-card">
                    <div class="ticket-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <h4><i class="bi bi-ticket-perforated"></i> E-Ticket</h4>
                            <span
                                class="status-badge <?= $booking['status'] === 'confirmed' ? 'status-confirmed' : 'status-pending' ?>">
                                <?= ucfirst($booking['status']) ?>
                            </span>
                        </div>
                        <div class="mt-3">
                            <span class="ticket-code"><?= htmlspecialchars($booking['ticket_code']) ?></span>
                        </div>
                    </div>

                    <!-- Journey Details -->
                    <div class="journey-details">
                        <h6 class="mb-3"><i class="bi bi-route"></i> Journey Details</h6>
                        <div class="detail-row">
                            <span class="detail-label">Route</span>
                            <span class="detail-value">
                                <?= htmlspecialchars($booking['origin']) ?> <i class="bi bi-arrow-right"></i>
                                <?= htmlspecialchars($booking['destination']) ?>
                            </span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Date</span>
                            <span class="detail-value"><?= date('F j, Y', strtotime($booking['depart_time'])) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Departure</span>
                            <span class="detail-value"><?= date('H:i', strtotime($booking['depart_time'])) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Arrival</span>
                            <span class="detail-value"><?= date('H:i', strtotime($booking['arrival_time'])) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Duration</span>
                            <span class="detail-value">
                                <?= date_diff(date_create($booking['depart_time']), date_create($booking['arrival_time']))->format('%h hrs %i min') ?>
                            </span>
                        </div>
                    </div>

                    <!-- Bus Details -->
                    <div class="journey-details">
                        <h6 class="mb-3"><i class="bi bi-bus-front"></i> Bus Details</h6>
                        <div class="detail-row">
                            <span class="detail-label">Bus Name</span>
                            <span class="detail-value"><?= htmlspecialchars($booking['bus_name']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Bus Type</span>
                            <span class="detail-value"><?= htmlspecialchars($booking['type']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Seat Layout</span>
                            <span class="detail-value"><?= htmlspecialchars($booking['seat_layout']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Your Seat</span>
                            <span
                                class="detail-value fw-bold text-primary"><?= htmlspecialchars($booking['seat_no']) ?></span>
                        </div>
                    </div>

                    <!-- Passenger Details -->
                    <div class="passenger-info">
                        <h6 class="mb-3"><i class="bi bi-person"></i> Passenger Details</h6>
                        <div class="detail-row">
                            <span class="detail-label">Name</span>
                            <span class="detail-value"><?= htmlspecialchars($booking['passenger_name']) ?></span>
                        </div>
                        <div class="detail-row">
                            <span class="detail-label">Phone</span>
                            <span class="detail-value"><?= htmlspecialchars($booking['passenger_phone']) ?></span>
                        </div>
                        <?php if (!empty($booking['passenger_nrc'])): ?>
                        <div class="detail-row">
                            <span class="detail-label">NRC</span>
                            <span class="detail-value"><?= htmlspecialchars($booking['passenger_nrc']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>

                    <!-- QR Code Placeholder -->
                    <div class="qr-code">
                        <i class="bi bi-qr-code" style="font-size: 100px; color: #6b7280;"></i>
                        <p class="mt-2 text-muted">Scan this QR code at the bus station</p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <!-- Payment Section -->
                <div class="ticket-card">
                    <h6 class="mb-3"><i class="bi bi-credit-card"></i> Payment Details</h6>

                    <div class="price-display">
                        <div>Total Amount</div>
                        <div><?= number_format($booking['total_price']) ?> MMK</div>
                    </div>

                    <?php if ($booking['status'] === 'pending'): ?>
                    <div class="payment-section">
                        <h6 class="mb-3">Complete Payment</h6>
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <select class="form-select" name="payment_method" required>
                                    <option value="cash">Cash Payment</option>
                                    <option value="mpu">MPU Card</option>
                                    <option value="wave">Wave Pay</option>
                                    <option value="kpay">KBZ Pay</option>
                                </select>
                            </div>
                            <button type="submit" name="confirm_payment" class="btn btn-success w-100">
                                <i class="bi bi-check-circle"></i> Confirm Payment
                            </button>
                        </form>
                    </div>
                    <?php else: ?>
                    <div class="payment-section">
                        <h6 class="mb-3">Payment Completed</h6>
                        <p class="mb-2"><strong>Method:</strong> <?= ucfirst($booking['payment_method']) ?></p>
                        <p class="mb-0"><strong>Status:</strong> <span class="text-success">Paid</span></p>
                    </div>
                    <?php endif; ?>

                    <hr>

                    <div class="d-grid gap-2">
                        <a href="My_Booking.php" class="btn btn-primary">
                            <i class="bi bi-list"></i> View All Bookings
                        </a>
                        <button onclick="window.print()" class="btn btn-outline-primary">
                            <i class="bi bi-printer"></i> Print Ticket
                        </button>
                        <a href="index.php" class="btn btn-outline-success">
                            <i class="bi bi-house"></i> Back to Home
                        </a>
                    </div>
                </div>

                <!-- Important Information -->
                <div class="ticket-card">
                    <h6 class="mb-3"><i class="bi bi-info-circle"></i> Important Information</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2"><i class="bi bi-clock text-primary"></i> Arrive 30 minutes before departure
                        </li>
                        <li class="mb-2"><i class="bi bi-person-check text-primary"></i> Bring valid ID for verification
                        </li>
                        <li class="mb-2"><i class="bi bi-phone text-primary"></i> Keep this ticket handy</li>
                        <li class="mb-2"><i class="bi bi-exclamation-triangle text-warning"></i> No refunds for no-shows
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Print Section -->
        <div class="print-section">
            <div class="row">
                <div class="col-md-6">
                    <h6>Contact Information</h6>
                    <p><i class="bi bi-telephone"></i> +959965509210</p>
                    <p><i class="bi bi-envelope"></i> e-ticketmyanmar@nonipoly.net</p>
                </div>
                <div class="col-md-6 text-end">
                    <h6>Booking Reference</h6>
                    <p><strong><?= htmlspecialchars($booking['ticket_code']) ?></strong></p>
                    <p><small>Booked on: <?= date('F j, Y H:i', strtotime($booking['booking_date'])) ?></small></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>