<?php
session_start();
require_once '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error_message = '';
$success_message = '';

// Handle booking cancellation
if (isset($_POST['cancel_booking'])) {
    $booking_id = $_POST['booking_id'];
    
    // Verify that the booking belongs to the user
    $stmt = $conn->prepare("SELECT id FROM bookings WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $booking_id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Update booking status to cancelled
        $stmt = $conn->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
        $stmt->bind_param("i", $booking_id);
        
        if ($stmt->execute()) {
            $success_message = "Booking cancelled successfully.";
        } else {
            $error_message = "Error cancelling booking. Please try again.";
        }
    } else {
        $error_message = "Invalid booking ID.";
    }
}

// Fetch user's bookings
$sql = "SELECT b.*, s.name as service_name, s.price 
        FROM bookings b 
        JOIN services s ON b.service_id = s.id 
        WHERE b.user_id = ? 
        ORDER BY b.booking_date DESC, b.booking_time DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$bookings = $result->fetch_all(MYSQLI_ASSOC);
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
                <a href="../index.php">Home</a>
                <a href="services.php">Services</a>
                <a href="classes.php">Classes</a>
                <a href="trainers.php">Trainers</a>
                <a href="profile.php">Profile</a>
                <a href="bookings.php">My Bookings</a>
                <a href="logout.php">Logout</a>
            </div>
        </nav>
    </header>

    <main>
        <section class="bookings-hero">
            <h1>My Bookings</h1>
            <p>View and manage your fitness appointments</p>
        </section>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <section class="bookings-grid">
            <?php if (empty($bookings)): ?>
                <div class="no-bookings">
                    <p>You don't have any bookings yet.</p>
                    <a href="services.php" class="btn btn-primary">Book a Service</a>
                </div>
            <?php else: ?>
                <div class="grid">
                    <?php foreach ($bookings as $booking): ?>
                        <div class="card booking-card">
                            <div class="booking-info">
                                <h3><?php echo htmlspecialchars($booking['service_name']); ?></h3>
                                <p class="date">
                                    <i class="far fa-calendar"></i>
                                    <?php echo date('F j, Y', strtotime($booking['booking_date'])); ?>
                                </p>
                                <p class="time">
                                    <i class="far fa-clock"></i>
                                    <?php echo date('g:i A', strtotime($booking['booking_time'])); ?>
                                </p>
                                <p class="price">
                                    <i class="fas fa-dollar-sign"></i>
                                    <?php echo number_format($booking['price'], 2); ?>
                                </p>
                                <p class="status <?php echo strtolower($booking['status']); ?>">
                                    Status: <?php echo ucfirst($booking['status']); ?>
                                </p>
                                
                                <?php if ($booking['status'] === 'pending'): ?>
                                    <form method="POST" class="cancel-form" onsubmit="return confirm('Are you sure you want to cancel this booking?');">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <button type="submit" name="cancel_booking" class="btn btn-danger">
                                            Cancel Booking
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
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

        .no-bookings {
            text-align: center;
            padding: 3rem;
            background: #f8f9fa;
            border-radius: 8px;
            margin: 2rem auto;
            max-width: 600px;
        }

        .booking-card {
            padding: 2rem;
            margin-bottom: 1rem;
        }

        .booking-info h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .booking-info p {
            margin-bottom: 0.5rem;
            color: #666;
        }

        .booking-info i {
            margin-right: 0.5rem;
            color: #3498db;
        }

        .status {
            font-weight: bold;
            padding: 0.5rem;
            border-radius: 4px;
            margin: 1rem 0;
        }

        .status.pending {
            background: #fff3cd;
            color: #856404;
        }

        .status.confirmed {
            background: #d4edda;
            color: #155724;
        }

        .status.cancelled {
            background: #f8d7da;
            color: #721c24;
        }

        .cancel-form {
            margin-top: 1rem;
        }

        .btn-danger {
            background: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background: #c82333;
        }
    </style>
</body>
</html> 