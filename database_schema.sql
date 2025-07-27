-- E-ticket Myanmar Database Schema
-- This file contains the complete database structure for the booking system

-- Users table
CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    nrc VARCHAR(50),
    role ENUM('user', 'admin') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Routes table
CREATE TABLE IF NOT EXISTS routes (
    route_id INT AUTO_INCREMENT PRIMARY KEY,
    origin VARCHAR(100) NOT NULL,
    destination VARCHAR(100) NOT NULL,
    distance DECIMAL(8,2),
    duration VARCHAR(50),
    depart_time DATETIME NOT NULL,
    arrival_time DATETIME NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Buses table
CREATE TABLE IF NOT EXISTS buses (
    bus_id INT AUTO_INCREMENT PRIMARY KEY,
    bus_name VARCHAR(100) NOT NULL,
    type VARCHAR(50) NOT NULL, -- VIP, Normal, etc.
    seat_layout VARCHAR(20) NOT NULL, -- e.g., "2+2", "2+1"
    capacity INT NOT NULL,
    image_path VARCHAR(255),
    status ENUM('active', 'maintenance', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Route-Bus assignments
CREATE TABLE IF NOT EXISTS route_buses (
    route_bus_id INT AUTO_INCREMENT PRIMARY KEY,
    route_id INT NOT NULL,
    bus_id INT NOT NULL,
    departure_time TIME NOT NULL,
    arrival_time TIME NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    status ENUM('active', 'cancelled') DEFAULT 'active',
    FOREIGN KEY (route_id) REFERENCES routes(route_id) ON DELETE CASCADE,
    FOREIGN KEY (bus_id) REFERENCES buses(bus_id) ON DELETE CASCADE,
    UNIQUE KEY unique_route_bus (route_id, bus_id, departure_time)
);

-- Bookings table
CREATE TABLE IF NOT EXISTS bookings (
    booking_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    route_id INT NOT NULL,
    bus_id INT NOT NULL,
    seat_no VARCHAR(10) NOT NULL,
    passenger_name VARCHAR(100) NOT NULL,
    passenger_phone VARCHAR(20) NOT NULL,
    passenger_nrc VARCHAR(50),
    booking_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    travel_date DATE NOT NULL,
    status ENUM('pending', 'confirmed', 'cancelled', 'completed') DEFAULT 'pending',
    ticket_code VARCHAR(20) UNIQUE NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'mpu', 'wave', 'kpay') DEFAULT 'cash',
    payment_status ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    FOREIGN KEY (route_id) REFERENCES routes(route_id) ON DELETE CASCADE,
    FOREIGN KEY (bus_id) REFERENCES buses(bus_id) ON DELETE CASCADE,
    UNIQUE KEY unique_seat_booking (route_id, bus_id, seat_no, travel_date)
);

-- Payments table
CREATE TABLE IF NOT EXISTS payments (
    payment_id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('cash', 'mpu', 'wave', 'kpay') NOT NULL,
    payment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    transaction_id VARCHAR(100),
    notes TEXT,
    FOREIGN KEY (booking_id) REFERENCES bookings(booking_id) ON DELETE CASCADE
);

-- Feedback table
CREATE TABLE IF NOT EXISTS feedback (
    feedback_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Promotions table
CREATE TABLE IF NOT EXISTS promotions (
    promo_id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) UNIQUE NOT NULL,
    description TEXT,
    discount_type ENUM('percentage', 'fixed') NOT NULL,
    discount_value DECIMAL(10,2) NOT NULL,
    min_amount DECIMAL(10,2) DEFAULT 0,
    max_discount DECIMAL(10,2),
    valid_from DATE NOT NULL,
    valid_to DATE NOT NULL,
    usage_limit INT DEFAULT 0,
    used_count INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample data for testing

-- Insert sample users
INSERT INTO users (name, email, password, phone, role) VALUES
('Admin User', 'admin@eticket.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+959123456789', 'admin'),
('John Doe', 'john@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+959987654321', 'user'),
('Jane Smith', 'jane@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+959555666777', 'user');

-- Insert sample routes
INSERT INTO routes (origin, destination, distance, duration, depart_time, arrival_time, price) VALUES
('Yangon', 'Mandalay', 620.5, '10 hours', '2025-01-15 08:00:00', '2025-01-15 18:00:00', 29800.00),
('Yangon', 'Bagan', 450.2, '8 hours', '2025-01-15 09:00:00', '2025-01-15 17:00:00', 24800.00),
('Mandalay', 'Yangon', 620.5, '10 hours', '2025-01-15 20:00:00', '2025-01-16 06:00:00', 29800.00),
('Yangon', 'Taunggyi', 380.0, '7 hours', '2025-01-15 10:00:00', '2025-01-15 17:00:00', 25800.00),
('Mandalay', 'Bagan', 170.3, '3 hours', '2025-01-15 14:00:00', '2025-01-15 17:00:00', 15800.00);

-- Insert sample buses
INSERT INTO buses (bus_name, type, seat_layout, capacity, image_path) VALUES
('Super Express', 'VIP', '2+1', 40, 'images/bus1.jpg'),
('Comfort Plus', 'Normal', '2+2', 50, 'images/bus2.jpg'),
('Premium Deluxe', 'VIP', '1+1', 30, 'images/bus3.jpg'),
('Standard Express', 'Normal', '2+2', 45, 'images/bus4.jpg');

-- Insert sample route-bus assignments
INSERT INTO route_buses (route_id, bus_id, departure_time, arrival_time, price) VALUES
(1, 1, '08:00:00', '18:00:00', 29800.00),
(1, 2, '09:00:00', '19:00:00', 24800.00),
(2, 3, '09:00:00', '17:00:00', 24800.00),
(3, 1, '20:00:00', '06:00:00', 29800.00),
(4, 2, '10:00:00', '17:00:00', 25800.00);

-- Insert sample promotions
INSERT INTO promotions (code, description, discount_type, discount_value, min_amount, valid_from, valid_to, usage_limit) VALUES
('WELCOME10', 'Welcome discount 10%', 'percentage', 10.00, 10000.00, '2025-01-01', '2025-12-31', 100),
('SAVE500', 'Save 500 MMK', 'fixed', 500.00, 15000.00, '2025-01-01', '2025-06-30', 50);

-- Create indexes for better performance
CREATE INDEX idx_bookings_user_id ON bookings(user_id);
CREATE INDEX idx_bookings_route_id ON bookings(route_id);
CREATE INDEX idx_bookings_status ON bookings(status);
CREATE INDEX idx_bookings_travel_date ON bookings(travel_date);
CREATE INDEX idx_routes_origin_destination ON routes(origin, destination);
CREATE INDEX idx_payments_booking_id ON payments(booking_id);
CREATE INDEX idx_promotions_code ON promotions(code);
CREATE INDEX idx_promotions_valid_dates ON promotions(valid_from, valid_to); 