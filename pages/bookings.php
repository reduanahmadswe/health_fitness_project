<?php
session_start();
require_once '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Fetch user's bookings
$user_id = $_SESSION['user_id'];
$sql = "SELECT b.*, s.name as service_name, s.category, s.price 
        FROM bookings b 
        JOIN services s ON b.service_id = s.id 
        WHERE b.user_id = ? 
        ORDER BY b.booking_date DESC, b.booking_time DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$bookings = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Bookings - Health & Fitness Center</title>
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
        <section class="bookings-hero">
            <h1>My Bookings</h1>
            <p>View and manage your scheduled sessions</p>
        </section>

        <section class="bookings-content">
            <div class="bookings-container">
                <?php if (!empty($bookings)): ?>
                    <div class="bookings-grid">
                        <?php foreach ($bookings as $booking): ?>
                            <div class="booking-card">
                                <div class="booking-header">
                                    <h3><?php echo htmlspecialchars($booking['service_name']); ?></h3>
                                    <span class="status status-<?php echo strtolower($booking['status']); ?>">
                                        <?php echo htmlspecialchars($booking['status']); ?>
                                    </span>
                                </div>
                                <div class="booking-details">
                                    <p class="category">
                                        <i class="fas fa-tag"></i>
                                        <?php echo htmlspecialchars($booking['category']); ?>
                                    </p>
                                    <p class="date">
                                        <i class="fas fa-calendar"></i>
                                        <?php echo date('F j, Y', strtotime($booking['booking_date'])); ?>
                                    </p>
                                    <p class="time">
                                        <i class="fas fa-clock"></i>
                                        <?php echo date('g:i A', strtotime($booking['booking_time'])); ?>
                                    </p>
                                    <p class="price">
                                        <i class="fas fa-dollar-sign"></i>
                                        <?php echo number_format($booking['price'], 2); ?>
                                    </p>
                                </div>
                                <?php if ($booking['status'] === 'Pending'): ?>
                                    <div class="booking-actions">
                                        <a href="cancel-booking.php?id=<?php echo $booking['id']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel Booking</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="no-bookings">
                        <i class="fas fa-calendar-times fa-3x"></i>
                        <h2>No Bookings Found</h2>
                        <p>You haven't made any bookings yet.</p>
                        <a href="services.php" class="btn btn-primary">Book a Service</a>
                    </div>
                <?php endif; ?>
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
        .bookings-hero {
            text-align: center;
            padding: 4rem 2rem;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('../images/bookings-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
        }

        .bookings-hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .bookings-container {
            max-width: 1200px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .bookings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
        }

        .booking-card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .booking-header {
            padding: 1.5rem;
            background: #f8f9fa;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .booking-header h3 {
            color: #2c3e50;
            margin: 0;
        }

        .booking-details {
            padding: 1.5rem;
        }

        .booking-details p {
            margin: 0.75rem 0;
            color: #666;
        }

        .booking-details i {
            width: 20px;
            color: #3498db;
            margin-right: 0.5rem;
        }

        .booking-actions {
            padding: 1rem 1.5rem;
            background: #f8f9fa;
            border-top: 1px solid #eee;
            text-align: right;
        }

        .status {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: bold;
        }

        .status-confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status-pending {
            background: #fff3cd;
            color: #856404;
        }

        .status-cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .no-bookings {
            text-align: center;
            padding: 4rem 2rem;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .no-bookings i {
            color: #ddd;
            margin-bottom: 1rem;
        }

        .no-bookings h2 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .no-bookings p {
            color: #666;
            margin-bottom: 2rem;
        }

        @media (max-width: 768px) {
            .bookings-hero h1 {
                font-size: 2rem;
            }

            .booking-header {
                flex-direction: column;
                text-align: center;
                gap: 1rem;
            }
        }
    </style>
</body>
</html> 