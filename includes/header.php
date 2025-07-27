<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'E-ticket Myanmar - Book Bus Tickets Online' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Loader -->
    <div id="loader" class="loader">
        <div class="spinner"></div>
        <p style="margin-top: 20px; color: white; font-weight: 600;">Loading E-ticket Myanmar...</p>
    </div>

    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div>
                <i class="bi bi-telephone"></i> +959965509210 | 
                <i class="bi bi-envelope"></i> e-ticketmyanmar@nonipoly.net
            </div>
            <div>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <i class="bi bi-person-circle"></i> Welcome, <?= htmlspecialchars($_SESSION['user']) ?> |
                    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
                <?php else: ?>
                    <a href="login.php"><i class="bi bi-box-arrow-in-right"></i> Login</a> |
                    <a href="register.php"><i class="bi bi-person-plus"></i> Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <div class="navbar">
        <div class="container">
            <div class="logo">
                <i class="bi bi-bus-front"></i> E-ticket Myanmar
            </div>
            <ul class="nav-links">
                <li><a href="index.php"><i class="bi bi-house"></i> Home</a></li>
                <li><a href="about.php"><i class="bi bi-info-circle"></i> About Us</a></li>
                <li class="dropdown">
                    <a href="#"><i class="bi bi-map"></i> Routes ▾</a>
                    <div class="dropdown-content">
                        <a href="routes.php?type=popular"><i class="bi bi-star"></i> Popular Routes</a>
                        <a href="routes.php?type=recent"><i class="bi bi-clock"></i> Recent Routes</a>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#"><i class="bi bi-question-circle"></i> Help ▾</a>
                    <div class="dropdown-content">
                        <a href="help.php?type=faq"><i class="bi bi-chat-quote"></i> FAQ</a>
                        <a href="help.php?type=contact"><i class="bi bi-envelope"></i> Contact Us</a>
                    </div>
                </li>
            </ul>
            <div class="navbar-right">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="My_Booking.php" class="bookings-btn">
                        <i class="bi bi-ticket"></i> My Bookings
                    </a>
                <?php endif; ?>
                <button onclick="toggleChatbot()" class="bookings-btn" style="background: linear-gradient(135deg, #10b981, #059669);">
                    <i class="bi bi-chat-dots"></i> Chatbot
                </button>
            </div>
        </div>
    </div> 