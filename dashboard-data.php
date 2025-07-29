<?php
require 'config.php';

header('Content-Type: application/json');

$response = [];
$salesData = $pdo->query("SELECT DATE_FORMAT(booking_date, '%b') AS month, SUM(total_price) AS revenue FROM bookings WHERE payment_status = 'paid' GROUP BY MONTH(booking_date) ORDER BY MONTH(booking_date)")->fetchAll(PDO::FETCH_ASSOC);
$response['sales_trend'] = $salesData;

// === Payment Methods Distribution ===
$paymentData = $pdo->query("SELECT payment_method, COUNT(*) AS count FROM bookings WHERE payment_status = 'paid' GROUP BY payment_method")->fetchAll(PDO::FETCH_ASSOC);
$response['payment_methods'] = $paymentData;

// === Top Routes ===
$topRoutes = $pdo->query("SELECT CONCAT(r.origin, ' → ', r.destination) AS route, COUNT(*) AS count FROM bookings b JOIN routes r ON b.route_id = r.route_id GROUP BY b.route_id ORDER BY count DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$response['top_routes'] = $topRoutes;

// === Top Users ===
$topUsers = $pdo->query("SELECT u.name, COUNT(*) AS count FROM bookings b JOIN users u ON b.user_id = u.user_id GROUP BY b.user_id ORDER BY count DESC LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
$response['top_users'] = $topUsers;

echo json_encode($response);
?>