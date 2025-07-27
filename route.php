<?php
session_start();
require 'config.php';

/* collect filters */
$ticket    = $_GET['ticket']    ?? '';
$from      = $_GET['from']      ?? '';
$to        = $_GET['to']        ?? '';
$depart    = $_GET['depart']    ?? '';
$passenger = $_GET['passenger'] ?? '';
$group     = $_GET['group']     ?? '';
$promo     = $_GET['promo']     ?? '';

/* pull matching routes with enhanced data */
$sql = "
 SELECT r.route_id, r.origin, r.destination, r.depart_time, r.arrival_time, r.price,
        b.bus_id, b.bus_name, b.seat_layout, b.capacity, b.type, b.image_path,
        rb.departure_time as bus_departure, rb.arrival_time as bus_arrival, rb.price as bus_price,
        (SELECT COUNT(*) FROM bookings bk WHERE bk.route_id = r.route_id AND bk.bus_id = b.bus_id AND bk.status != 'cancelled') as booked_seats
 FROM   routes r
 JOIN   route_buses rb ON rb.route_id = r.route_id
 JOIN   buses  b ON b.bus_id = rb.bus_id
 WHERE  (:from = '' OR r.origin      LIKE CONCAT('%',:from,'%'))
   AND  (:to   = '' OR r.destination LIKE CONCAT('%',:to,'%'))
   AND  rb.status = 'active'
 ORDER BY r.depart_time ASC
";

$stmt = $pdo->prepare($sql);
$stmt->execute([
    ':from' => $from,
    ':to'   => $to,
    ':date' => $depart
]);
$routes = $stmt->fetchAll();

// Get unique bus types for filter
$busTypes = [];
foreach ($routes as $route) {
    if (!in_array($route['type'], $busTypes)) {
        $busTypes[] = $route['type'];
    }
}

// Get unique departure times for filter
$departureTimes = [];
foreach ($routes as $route) {
    $time = date('H:i', strtotime($route['depart_time']));
    if (!in_array($time, $departureTimes)) {
        $departureTimes[] = $time;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results - <?= htmlspecialchars($from) ?> to <?= htmlspecialchars($to) ?> | E-ticket Myanmar</title>
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

    .results-header {
        background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
        padding: 30px 20px;
        color: white;
        border-radius: 15px;
        margin-bottom: 30px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .bus-card {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        margin-bottom: 25px;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: 1px solid #e9ecef;
    }

    .bus-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .bus-card img {
        height: 180px;
        object-fit: cover;
        width: 100%;
    }

    .bus-info {
        padding: 20px;
    }

    .bus-type-badge {
        background: linear-gradient(45deg, #10b981, #059669);
        color: white;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .price-tag {
        background: linear-gradient(45deg, #f59e0b, #d97706);
        color: white;
        padding: 8px 16px;
        border-radius: 25px;
        font-weight: bold;
        font-size: 18px;
    }

    .filter-box {
        background: white;
        padding: 25px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: 1px solid #e9ecef;
        position: sticky;
        top: 20px;
    }

    .filter-section {
        margin-bottom: 25px;
    }

    .filter-section h6 {
        color: #374151;
        font-weight: 600;
        margin-bottom: 15px;
        border-bottom: 2px solid #e5e7eb;
        padding-bottom: 8px;
    }

    .form-check {
        margin-bottom: 10px;
    }

    .form-check-input:checked {
        background-color: #1e3a8a;
        border-color: #1e3a8a;
    }

    .btn-book {
        background: linear-gradient(45deg, #10b981, #059669);
        border: none;
        padding: 12px 30px;
        border-radius: 25px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-book:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(16, 185, 129, 0.4);
    }

    .seat-availability {
        background: #f3f4f6;
        padding: 8px 12px;
        border-radius: 20px;
        font-size: 14px;
        color: #374151;
    }

    .departure-time {
        font-size: 18px;
        font-weight: bold;
        color: #1e3a8a;
    }

    .duration {
        color: #6b7280;
        font-size: 14px;
    }

    .amenities {
        display: flex;
        gap: 15px;
        margin-top: 15px;
    }

    .amenity {
        display: flex;
        align-items: center;
        gap: 5px;
        color: #6b7280;
        font-size: 14px;
    }

    .amenity i {
        color: #10b981;
    }

    .no-results {
        text-align: center;
        padding: 60px 20px;
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .no-results i {
        font-size: 48px;
        color: #9ca3af;
        margin-bottom: 20px;
    }

    footer {
        background-color: #1e3a8a;
        color: white;
        padding: 30px 0;
        margin-top: 50px;
    }

    footer a {
        color: #ddd;
        text-decoration: none;
    }

    footer a:hover {
        color: white;
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
                    <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                    <li class="nav-item"><a class="nav-link" href="routes.php">Routes</a></li>
                    <li class="nav-item"><a class="nav-link" href="contact.php">Help</a></li>
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <li class="nav-item"><a class="nav-link" href="My_Booking.php">My Bookings</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <!-- Results Header -->
        <div class="results-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h3 class="mb-2">
                        <i class="bi bi-search"></i>
                        <?= htmlspecialchars($from) ?> <i class="bi bi-arrow-right"></i> <?= htmlspecialchars($to) ?>
                    </h3>
                    <p class="mb-0">
                        <i class="bi bi-calendar3"></i> <?= htmlspecialchars($depart) ?> |
                        <i class="bi bi-people"></i> <?= htmlspecialchars($passenger) ?> |
                        <i class="bi bi-ticket"></i> <?= htmlspecialchars($ticket) ?>
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <h4 class="mb-0"><?= count($routes) ?> Routes Found</h4>
                    <small>Available for booking</small>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Filters -->
            <div class="col-lg-3">
                <div class="filter-box">
                    <h5 class="mb-4"><i class="bi bi-funnel"></i> Filters</h5>

                    <!-- Bus Type Filter -->
                    <div class="filter-section">
                        <h6>Bus Type</h6>
                        <?php foreach ($busTypes as $type): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="type_<?= $type ?>" checked>
                            <label class="form-check-label" for="type_<?= $type ?>">
                                <?= htmlspecialchars($type) ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Departure Time Filter -->
                    <div class="filter-section">
                        <h6>Departure Time</h6>
                        <?php foreach ($departureTimes as $time): ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="time_<?= $time ?>" checked>
                            <label class="form-check-label" for="time_<?= $time ?>">
                                <?= $time ?>
                            </label>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Price Range -->
                    <div class="filter-section">
                        <h6>Price Range</h6>
                        <div class="mb-3">
                            <label class="form-label">Max Price (MMK)</label>
                            <input type="range" class="form-range" id="priceRange" min="0" max="50000" value="50000">
                            <div class="d-flex justify-content-between">
                                <small>0</small>
                                <small id="priceValue">50,000</small>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-primary w-100" onclick="applyFilters()">
                        <i class="bi bi-search"></i> Apply Filters
                    </button>
                </div>
            </div>

            <!-- Bus Results -->
            <div class="col-lg-9">
                <?php if (empty($routes)): ?>
                <div class="no-results">
                    <i class="bi bi-search"></i>
                    <h4>No routes found</h4>
                    <p>Try adjusting your search criteria or check back later for new routes.</p>
                    <a href="index.php" class="btn btn-primary">Search Again</a>
                </div>
                <?php else: ?>
                <?php foreach ($routes as $route): ?>
                <div class="bus-card" data-type="<?= htmlspecialchars($route['type']) ?>"
                    data-time="<?= date('H:i', strtotime($route['depart_time'])) ?>"
                    data-price="<?= $route['price'] ?>">
                    <div class="row g-0">
                        <div class="col-md-4">
                            <img src="<?= $route['image_path'] ?: 'images/bus-default.jpg' ?>"
                                alt="<?= htmlspecialchars($route['bus_name']) ?>">
                        </div>
                        <div class="col-md-8">
                            <div class="bus-info">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <span class="bus-type-badge"><?= htmlspecialchars($route['type']) ?></span>
                                        <h5 class="mt-2 mb-1"><?= htmlspecialchars($route['bus_name']) ?></h5>
                                        <p class="text-muted mb-0"><?= htmlspecialchars($route['seat_layout']) ?> •
                                            <?= $route['capacity'] ?> seats</p>
                                    </div>
                                    <div class="text-end">
                                        <div class="price-tag"><?= number_format($route['price']) ?> MMK</div>
                                        <div class="seat-availability mt-2">
                                            <?= $route['capacity'] - $route['booked_seats'] ?> seats available
                                        </div>
                                    </div>
                                </div>

                                <div class="row mb-3">
                                    <div class="col-6">
                                        <div class="departure-time">
                                            <i class="bi bi-clock"></i>
                                            <?= date('H:i', strtotime($route['depart_time'])) ?>
                                        </div>
                                        <div class="duration">Departure</div>
                                    </div>
                                    <div class="col-6">
                                        <div class="departure-time">
                                            <i class="bi bi-clock-fill"></i>
                                            <?= date('H:i', strtotime($route['arrival_time'])) ?>
                                        </div>
                                        <div class="duration">Arrival</div>
                                    </div>
                                </div>

                                <div class="amenities">
                                    <div class="amenity">
                                        <i class="bi bi-wifi"></i> WiFi
                                    </div>
                                    <div class="amenity">
                                        <i class="bi bi-snow"></i> AC
                                    </div>
                                    <div class="amenity">
                                        <i class="bi bi-cup-hot"></i> Refreshments
                                    </div>
                                    <div class="amenity">
                                        <i class="bi bi-phone"></i> USB Charging
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mt-3">
                                    <div>
                                        <small class="text-muted">
                                            <i class="bi bi-info-circle"></i>
                                            Estimated duration:
                                            <?= date_diff(date_create($route['depart_time']), date_create($route['arrival_time']))->format('%h hrs %i min') ?>
                                        </small>
                                    </div>
                                    <?php if (isset($_SESSION['user_id'])): ?>
                                    <a href="booking.php?route_id=<?= $route['route_id'] ?>" class="btn btn-book">
                                        <i class="bi bi-ticket-perforated"></i> Book Now
                                    </a>
                                    <?php else: ?>
                                    <a href="login.php?redirect=booking.php?route_id=<?= $route['route_id'] ?>"
                                        class="btn btn-book">
                                        <i class="bi bi-person"></i> Login to Book
                                    </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <p>&copy; 2025 E-ticket Myanmar. All Rights Reserved.</p>
                </div>
                <div class="col-md-6 text-end">
                    <a href="about.php">About Us</a> |
                    <a href="contact.php">Contact</a> |
                    <a href="TandC.php">Terms</a> |
                    <a href="privacy.php">Privacy</a>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Price range slider
    const priceRange = document.getElementById('priceRange');
    const priceValue = document.getElementById('priceValue');

    priceRange.addEventListener('input', function() {
        priceValue.textContent = Number(this.value).toLocaleString();
    });

    // Filter functionality
    function applyFilters() {
        const selectedTypes = Array.from(document.querySelectorAll('input[id^="type_"]:checked'))
            .map(cb => cb.id.replace('type_', ''));

        const selectedTimes = Array.from(document.querySelectorAll('input[id^="time_"]:checked'))
            .map(cb => cb.id.replace('time_', ''));

        const maxPrice = parseInt(priceRange.value);

        document.querySelectorAll('.bus-card').forEach(card => {
            const type = card.dataset.type;
            const time = card.dataset.time;
            const price = parseInt(card.dataset.price);

            const typeMatch = selectedTypes.length === 0 || selectedTypes.includes(type);
            const timeMatch = selectedTimes.length === 0 || selectedTimes.includes(time);
            const priceMatch = price <= maxPrice;

            if (typeMatch && timeMatch && priceMatch) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Auto-apply filters on change
    document.querySelectorAll('.filter-box input').forEach(input => {
        input.addEventListener('change', applyFilters);
    });
    </script>
</body>

</html>