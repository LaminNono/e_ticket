<?php
require 'config.php';

echo "<h2>Database Status Check</h2>";

// Check if tables exist
$tables = ['users', 'routes', 'buses', 'route_buses', 'bookings', 'payments', 'feedback', 'promotions'];
$existing_tables = [];

foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            $existing_tables[] = $table;
            echo "✅ Table '$table' exists<br>";
        } else {
            echo "❌ Table '$table' does not exist<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error checking table '$table': " . $e->getMessage() . "<br>";
    }
}

echo "<h3>Data Counts:</h3>";

// Check data in each table
foreach ($existing_tables as $table) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) as count FROM $table");
        $count = $stmt->fetch()['count'];
        echo "📊 $table: $count records<br>";
    } catch (Exception $e) {
        echo "❌ Error counting $table: " . $e->getMessage() . "<br>";
    }
}

echo "<h3>Sample Routes Data:</h3>";
try {
    $stmt = $pdo->query("SELECT * FROM routes LIMIT 5");
    $routes = $stmt->fetchAll();
    if (count($routes) > 0) {
        foreach ($routes as $route) {
            echo "📍 Route: {$route['origin']} → {$route['destination']} | {$route['depart_time']} | {$route['price']} MMK<br>";
        }
    } else {
        echo "❌ No routes found in database<br>";
    }
} catch (Exception $e) {
    echo "❌ Error fetching routes: " . $e->getMessage() . "<br>";
}

echo "<h3>Sample Route-Buses Data:</h3>";
try {
    $stmt = $pdo->query("SELECT * FROM route_buses LIMIT 5");
    $route_buses = $stmt->fetchAll();
    if (count($route_buses) > 0) {
        foreach ($route_buses as $rb) {
            echo "🚌 Route-Bus: Route ID {$rb['route_id']} | Bus ID {$rb['bus_id']} | {$rb['departure_time']} | {$rb['price']} MMK<br>";
        }
    } else {
        echo "❌ No route-buses found in database<br>";
    }
} catch (Exception $e) {
    echo "❌ Error fetching route_buses: " . $e->getMessage() . "<br>";
}

echo "<h3>Import Schema:</h3>";
if (count($existing_tables) < count($tables)) {
    echo "⚠️ Some tables are missing. Please import the database schema:<br>";
    echo "<a href='database_schema.sql' target='_blank'>View Schema File</a><br>";
    echo "Or run: mysql -u root -p e_ticket < database_schema.sql<br>";
} else {
    echo "✅ All tables exist!<br>";
}
?>