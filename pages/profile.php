<?php
session_start();
require_once '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch user data
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);

// Fetch user's bookings
$sql = "SELECT b.*, s.name as service_name, s.category 
        FROM bookings b 
        JOIN services s ON b.service_id = s.id 
        WHERE b.user_id = ? 
        ORDER BY b.booking_date DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$bookings_result = mysqli_stmt_get_result($stmt);
$bookings = mysqli_fetch_all($bookings_result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - Health & Fitness Center</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="logo">
                <h1>Health & Fitness Center</h1>
            </div>
            <div class="nav-links">
                <!-- Main Navigation -->
                <div class="nav-main">
                    <a href="../index.php" class="nav-item">Home</a>
                    <a href="services.php" class="nav-item">Services</a>
                    <a href="classes.php" class="nav-item">Classes</a>
                    <a href="trainers.php" class="nav-item">Trainers</a>
                    <a href="feedback.php" class="nav-item">Feedback</a>
                    <a href="about.php" class="nav-item">About Us</a>
                    <a href="search.php" class="nav-item">Search</a>
                </div>

                <!-- User Navigation -->
                <div class="nav-user">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="profile.php" class="nav-item">Profile</a>
                        <a href="bookings.php" class="nav-item">My Bookings</a>
                        <a href="logout.php" class="nav-item">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="nav-item">Login</a>
                        <a href="register.php" class="nav-item">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="profile-hero">
            <h1>My Profile</h1>
            <p>Manage your account and view your bookings</p>
        </section>

        <section class="profile-content">
            <div class="profile-container">
                <div class="profile-info">
                    <h2>Personal Information</h2>
                    <div class="info-card">
                        <div class="info-item">
                            <label>Name:</label>
                            <span><?php echo htmlspecialchars($user['username']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Email:</label>
                            <span><?php echo htmlspecialchars($user['email']); ?></span>
                        </div>
                        <div class="info-item">
                            <label>Member Since:</label>
                            <span><?php echo date('F j, Y', strtotime($user['created_at'])); ?></span>
                        </div>
                        <a href="edit-profile.php" class="btn btn-primary">Edit Profile</a>
                    </div>
                </div>

                <div class="recent-bookings">
                    <h2>Recent Bookings</h2>
                    <?php if (!empty($bookings)): ?>
                        <div class="bookings-list">
                            <?php foreach ($bookings as $booking): ?>
                                <div class="booking-card">
                                    <h3><?php echo htmlspecialchars($booking['service_name']); ?></h3>
                                    <p class="category"><?php echo htmlspecialchars($booking['category']); ?></p>
                                    <p class="date">Date: <?php echo date('F j, Y', strtotime($booking['booking_date'])); ?></p>
                                    <p class="time">Time: <?php echo date('g:i A', strtotime($booking['booking_time'])); ?></p>
                                    <p class="status">Status: <span class="status-<?php echo strtolower($booking['status']); ?>"><?php echo htmlspecialchars($booking['status']); ?></span></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <p class="no-bookings">You haven't made any bookings yet.</p>
                        <a href="services.php" class="btn btn-primary">Book a Service</a>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p>Email: info@healthfitness.com</p>
                <p>Phone: (123) 456-7890</p>
                <p>Address: 123 Fitness Street, Health City</p>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <a href="about.php">About Us</a>
                <a href="services.php">Services</a>
                <a href="contact.php">Contact</a>
            </div>
            <div class="footer-section">
                <h3>Follow Us</h3>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2024 Health & Fitness Center. All rights reserved.</p>
        </div>
    </footer>

    <style>
        .profile-hero {
            text-align: center;
            padding: 4rem 2rem;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('../images/profile-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
        }

        .profile-hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .profile-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
        }

        .info-card {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .info-item {
            margin-bottom: 1.5rem;
        }

        .info-item label {
            display: block;
            color: #666;
            margin-bottom: 0.5rem;
        }

        .info-item span {
            color: #2c3e50;
            font-size: 1.1rem;
        }

        .bookings-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        .booking-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .booking-card h3 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .category {
            color: #3498db;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .date, .time {
            color: #666;
            margin-bottom: 0.5rem;
        }

        .status {
            margin-top: 1rem;
            font-weight: bold;
        }

        .status-confirmed {
            color: #27ae60;
        }

        .status-pending {
            color: #f39c12;
        }

        .status-cancelled {
            color: #e74c3c;
        }

        .no-bookings {
            text-align: center;
            color: #666;
            margin: 2rem 0;
        }

        @media (max-width: 768px) {
            .profile-container {
                grid-template-columns: 1fr;
            }
        }
    </style>
</body>
</html> 