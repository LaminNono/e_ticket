<?php
session_start();
require 'config.php';

// Get all routes for demo
$sql = "
 SELECT r.route_id, r.origin, r.destination, r.depart_time, r.arrival_time, r.price,
        b.bus_id, b.bus_name, b.seat_layout, b.capacity, b.type, b.image_path,
        rb.departure_time as bus_departure, rb.arrival_time as bus_arrival, rb.price as bus_price,
        (SELECT COUNT(*) FROM bookings bk WHERE bk.route_id = r.route_id AND bk.bus_id = b.bus_id AND bk.status != 'cancelled') as booked_seats
 FROM   routes r
 JOIN   route_buses rb ON rb.route_id = r.route_id
 JOIN   buses  b ON b.bus_id = rb.bus_id
 WHERE  rb.status = 'active'
 ORDER BY r.depart_time ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute();
$all_routes = $stmt->fetchAll();

$pageTitle = 'E-ticket Myanmar - Demo';
include 'includes/header.php';
?>

<!-- Demo Header -->
<section class="hero-section"
    style="background: linear-gradient(135deg, rgba(102, 126, 234, 0.9), rgba(118, 75, 162, 0.9));">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">🎓 E-ticket Myanmar - School Assignment Demo</h1>
            <p class="hero-subtitle">Complete Bus Booking System with Modern UI/UX</p>
        </div>
    </div>
</section>

<div class="container mt-4">
    <!-- Demo Features -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card" style="border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4">🚀 System Features</h3>
                    <div class="row text-center">
                        <div class="col-md-3 mb-3">
                            <div class="p-3">
                                <i class="bi bi-search" style="font-size: 2rem; color: #667eea;"></i>
                                <h5>Route Search</h5>
                                <small>Find routes between cities</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="p-3">
                                <i class="bi bi-ticket-perforated" style="font-size: 2rem; color: #10b981;"></i>
                                <h5>Seat Booking</h5>
                                <small>Interactive seat selection</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="p-3">
                                <i class="bi bi-credit-card" style="font-size: 2rem; color: #f59e0b;"></i>
                                <h5>Payment System</h5>
                                <small>Multiple payment methods</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="p-3">
                                <i class="bi bi-phone" style="font-size: 2rem; color: #8b5cf6;"></i>
                                <h5>Responsive Design</h5>
                                <small>Works on all devices</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Demo Links -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card" style="border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4">🎯 Quick Demo Links</h3>
                    <div class="row text-center">
                        <div class="col-md-4 mb-3">
                            <a href="index.php" class="btn btn-primary btn-lg w-100" style="border-radius: 15px;">
                                <i class="bi bi-house"></i> Homepage
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="route.php?from=Yangon&to=Mandalay&depart=<?= date('Y-m-d', strtotime('+1 day')) ?>&passenger=1 Person&ticket=Normal"
                                class="btn btn-success btn-lg w-100" style="border-radius: 15px;">
                                <i class="bi bi-search"></i> Search Routes
                            </a>
                        </div>
                        <div class="col-md-4 mb-3">
                            <a href="booking.php?route_id=1" class="btn btn-warning btn-lg w-100"
                                style="border-radius: 15px;">
                                <i class="bi bi-ticket"></i> Book Tickets
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- All Available Routes -->
    <div class="row">
        <div class="col-12">
            <h3 class="text-center mb-4">🚌 All Available Routes</h3>
            <?php foreach ($all_routes as $route): ?>
            <div class="card mb-3" style="border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.08);">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-3">
                            <h5 class="mb-1"><?= htmlspecialchars($route['origin']) ?> →
                                <?= htmlspecialchars($route['destination']) ?></h5>
                            <span class="badge bg-primary"><?= htmlspecialchars($route['type']) ?></span>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Bus</small><br>
                            <strong><?= htmlspecialchars($route['bus_name']) ?></strong>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted">Time</small><br>
                            <strong><?= date('H:i', strtotime($route['depart_time'])) ?> -
                                <?= date('H:i', strtotime($route['arrival_time'])) ?></strong>
                        </div>
                        <div class="col-md-2">
                            <small class="text-muted">Price</small><br>
                            <strong><?= number_format($route['price']) ?> MMK</strong>
                        </div>
                        <div class="col-md-2 text-end">
                            <a href="booking.php?route_id=<?= $route['route_id'] ?>"
                                class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-ticket"></i> Book
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Technical Details -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card" style="border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
                <div class="card-body p-4">
                    <h3 class="text-center mb-4">💻 Technical Implementation</h3>
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Backend Technologies:</h5>
                            <ul>
                                <li>PHP 8.0+ with PDO</li>
                                <li>MySQL Database</li>
                                <li>Session Management</li>
                                <li>Prepared Statements (Security)</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>Frontend Technologies:</h5>
                            <ul>
                                <li>Bootstrap 5 (Responsive)</li>
                                <li>Bootstrap Icons</li>
                                <li>Custom CSS (Glass Morphism)</li>
                                <li>JavaScript (Interactive Features)</li>
                            </ul>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-12">
                            <h5>Key Features Implemented:</h5>
                            <ul>
                                <li>✅ User Authentication & Registration</li>
                                <li>✅ Route Search & Filtering</li>
                                <li>✅ Interactive Seat Selection</li>
                                <li>✅ Booking Management</li>
                                <li>✅ Payment Integration</li>
                                <li>✅ Admin Dashboard</li>
                                <li>✅ Responsive Design</li>
                                <li>✅ Modern UI/UX</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>