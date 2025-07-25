<?php
$host     = 'localhost';
$db       = 'eticket_db';
$user     = 'root';          // MySQL username
$pass     = '';              // MySQL password
$charset  = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

/* ──────────────────────────────────────────────────────────
   Admin landing page, reused across the project
   ──────────────────────────────────────────────────────── */
define('ADMIN_HOME', 'admin/dashboard.php');   // ★ CHANGED (was admin/dashboard.php)

?>
