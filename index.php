<?php
session_start();

// Handle search form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $ticket = $_POST['ticket'] ?? '';
    $from = $_POST['from'] ?? '';
    $to = $_POST['to'] ?? '';
    $depart = $_POST['depart'] ?? '';
    $passenger = $_POST['passenger'] ?? '';
    $promo = $_POST['promo'] ?? '';

    // Validate required fields
    $errors = [];
    if (empty($from)) $errors[] = 'Please select departure city';
    if (empty($to)) $errors[] = 'Please select destination city';
    if (empty($passenger)) $errors[] = 'Please select number of passengers';
    if ($from === $to && !empty($from)) $errors[] = 'Departure and destination cannot be the same';

    if (empty($errors)) {
        // Build query string
        $params = http_build_query([
            'ticket' => $ticket,
            'from' => $from,
            'to' => $to,
            'depart' => $depart,
            'passenger' => $passenger,
            'promo' => $promo
        ]);
        
        header("Location: route.php?$params");
        exit();
    }
}

// Set minimum date to today
$minDate = date('Y-m-d');
$maxDate = date('Y-m-d', strtotime('+3 months'));

// Set page title
$pageTitle = 'E-ticket Myanmar - Book Bus Tickets Online';

// Include header
include 'includes/header.php';

// Include search form
include 'includes/search-form.php';

// Include news section
include 'includes/news-section.php';

// Include footer
include 'includes/footer.php';
?>