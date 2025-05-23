<?php

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'health_fitness_db');


$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);


if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}


$sql = "CREATE DATABASE IF NOT EXISTS " . DB_NAME;
if (mysqli_query($conn, $sql)) {
    mysqli_select_db($conn, DB_NAME);
} else {
    die("Error creating database: " . mysqli_error($conn));
}


$sql = "CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    reset_token VARCHAR(100) DEFAULT NULL,
    reset_token_expiry DATETIME DEFAULT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!mysqli_query($conn, $sql)) {
    die("Error creating users table: " . mysqli_error($conn));
}


$sql = "CREATE TABLE IF NOT EXISTS services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10,2),
    category VARCHAR(50),
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
)";

if (!mysqli_query($conn, $sql)) {
    die("Error creating services table: " . mysqli_error($conn));
}


$sql = "CREATE TABLE IF NOT EXISTS bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    service_id INT,
    booking_date DATE,
    booking_time TIME,
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'pending',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (service_id) REFERENCES services(id)
)";

if (!mysqli_query($conn, $sql)) {
    die("Error creating bookings table: " . mysqli_error($conn));
}


$sql = "CREATE TABLE IF NOT EXISTS feedback (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    message TEXT NOT NULL,
    rating INT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
)";

if (!mysqli_query($conn, $sql)) {
    die("Error creating feedback table: " . mysqli_error($conn));
}


$check_services = "SELECT COUNT(*) as count FROM services";
$result = mysqli_query($conn, $check_services);
$row = mysqli_fetch_assoc($result);

if ($row['count'] == 0) {
    $sample_services = [
        ['Personal Training', 'One-on-one training session with a professional trainer', 50.00, 'Personal Training'],
        ['Group Fitness', 'High-intensity group workout session', 25.00, 'Group Classes'],
        ['Yoga Class', 'Relaxing yoga session for all levels', 20.00, 'Group Classes'],
        ['Nutrition Consultation', 'Personalized nutrition plan and consultation', 75.00, 'Nutrition'],
        ['Spa Treatment', 'Relaxing spa and wellness treatment', 100.00, 'Spa & Wellness']
    ];

    $sql = "INSERT INTO services (name, description, price, category) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);

    foreach ($sample_services as $service) {
        mysqli_stmt_bind_param($stmt, "ssds", $service[0], $service[1], $service[2], $service[3]);
        mysqli_stmt_execute($stmt);
    }
}
?> 