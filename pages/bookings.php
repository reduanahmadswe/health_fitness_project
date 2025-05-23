<?php
session_start();
require_once '../includes/config.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}


if (isset($_SESSION['success'])) {
    echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
    unset($_SESSION['success']);
}

if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}


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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #4a6fa5;
            --secondary: #166088;
            --accent: #4fc3a1;
            --dark: #2d3748;
            --light: #f8f9fa;
            --gray: #e2e8f0;
            --dark-gray: #a0aec0;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Poppins', sans-serif;
            line-height: 1.6;
            color: var(--dark);
            background-color: #f5f7fa;
        }
        
        .header {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        
        .navbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 1rem 2rem;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .logo h1 {
            font-size: 1.8rem;
            font-weight: 700;
            background: linear-gradient(to right, white, var(--gray));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .nav-links {
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }
        
        .nav-main, .nav-user {
            display: flex;
            gap: 1.5rem;
        }
        
        .nav-item {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 0;
            position: relative;
            transition: all 0.3s ease;
        }
        
        .nav-item:hover, .nav-item.active {
            color: var(--accent);
        }
        
        .nav-item::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 0;
            height: 2px;
            background-color: var(--accent);
            transition: width 0.3s ease;
        }
        
        .nav-item:hover::after, .nav-item.active::after {
            width: 100%;
        }
        
        .hamburger {
            display: none;
            cursor: pointer;
            font-size: 1.5rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 0.9rem;
            border: none;
            cursor: pointer;
        }
        
        .btn-primary {
            background-color: var(--accent);
            color: white;
            box-shadow: 0 4px 15px rgba(79, 195, 161, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(79, 195, 161, 0.6);
            background-color: #3daa8a;
        }
        
        .btn-danger {
            background-color: #e74c3c;
            color: white;
            box-shadow: 0 4px 15px rgba(231, 76, 60, 0.4);
        }
        
        .btn-danger:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.6);
            background-color: #c0392b;
        }
        
       
        .bookings-hero {
            background: linear-gradient(135deg, rgba(74, 111, 165, 0.9), rgba(22, 96, 136, 0.9)), url('../images/my-booking.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            padding: 6rem 0;
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .bookings-hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            animation: fadeInDown 1s ease;
        }
        
        .bookings-hero p {
            font-size: 1.2rem;
            opacity: 0.9;
            animation: fadeInUp 1s ease;
        }
        
        
        .bookings-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .bookings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }
        
        .booking-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .booking-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .booking-header {
            padding: 1.5rem;
            background: var(--light);
            border-bottom: 1px solid var(--gray);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .booking-header h3 {
            font-size: 1.3rem;
            color: var(--dark);
            margin: 0;
        }
        
        .booking-details {
            padding: 1.5rem;
        }
        
        .booking-details p {
            display: flex;
            align-items: center;
            gap: 0.8rem;
            margin-bottom: 1rem;
            color: var(--dark-gray);
        }
        
        .booking-details i {
            color: var(--accent);
            width: 20px;
            text-align: center;
        }
        
        .booking-actions {
            padding: 1rem 1.5rem;
            background: var(--light);
            border-top: 1px solid var(--gray);
            text-align: right;
        }
        
        .status {
            padding: 0.4rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-confirmed {
            background-color: rgba(39, 174, 96, 0.1);
            color: #27ae60;
        }
        
        .status-pending {
            background-color: rgba(243, 156, 18, 0.1);
            color: #f39c12;
        }
        
        .status-cancelled {
            background-color: rgba(231, 76, 60, 0.1);
            color: #e74c3c;
        }
        
        .no-bookings {
            background: white;
            border-radius: 15px;
            padding: 4rem 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 4rem;
        }
        
        .no-bookings i {
            font-size: 3rem;
            color: var(--dark-gray);
            margin-bottom: 1.5rem;
            opacity: 0.3;
        }
        
        .no-bookings h2 {
            color: var(--dark);
            margin-bottom: 1rem;
        }
        
        .no-bookings p {
            color: var(--dark-gray);
            margin-bottom: 2rem;
        }
        
        
        footer {
            background-color: var(--dark);
            color: white;
            padding: 4rem 0 0;
        }
        
        .footer-content {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .footer-section {
            margin-bottom: 2rem;
        }
        
        .footer-section h3 {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .footer-section h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 2px;
            background-color: var(--accent);
        }
        
        .footer-section p, .footer-section a {
            color: var(--gray);
            margin-bottom: 0.8rem;
            display: block;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-section a:hover {
            color: var(--accent);
        }
        
        .social-links {
            display: flex;
            gap: 1rem;
        }
        
        .social-links a {
            color: white;
            font-size: 1.2rem;
            transition: transform 0.3s ease;
        }
        
        .social-links a:hover {
            transform: translateY(-3px);
            color: var(--accent);
        }
        
        .footer-bottom {
            text-align: center;
            padding: 1.5rem;
            background-color: rgba(0, 0, 0, 0.2);
            margin-top: 2rem;
        }

        .alert {
            padding: 1rem;
            margin: 1rem auto;
            max-width: 1200px;
            border-radius: 5px;
            text-align: center;
            animation: fadeInDown 0.5s ease;
        }

        .alert-success {
            background-color: rgba(39, 174, 96, 0.2);
            color: #27ae60;
            border: 1px solid #27ae60;
        }

        .alert-danger {
            background-color: rgba(231, 76, 60, 0.2);
            color: #e74c3c;
            border: 1px solid #e74c3c;
        }

        .booking-actions form {
            display: inline;
        }
        
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
       
        @media (max-width: 992px) {
            .bookings-hero h1 {
                font-size: 2.5rem;
            }
            
            .bookings-grid {
                grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .hamburger {
                display: block;
            }
            
            .nav-links {
                position: fixed;
                top: 70px;
                left: -100%;
                width: 100%;
                height: calc(100vh - 70px);
                background: linear-gradient(135deg, var(--primary), var(--secondary));
                flex-direction: column;
                align-items: center;
                padding: 2rem 0;
                transition: left 0.3s ease;
            }
            
            .nav-links.active {
                left: 0;
            }
            
            .nav-main, .nav-user {
                flex-direction: column;
                width: 100%;
                text-align: center;
            }
            
            .nav-item {
                padding: 1rem;
                width: 100%;
            }
            
            .bookings-hero {
                padding: 4rem 0;
            }
            
            .bookings-hero h1 {
                font-size: 2.2rem;
            }
            
            .booking-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .booking-actions {
                text-align: center;
            }
        }
        
        @media (max-width: 576px) {
            .bookings-hero h1 {
                font-size: 1.8rem;
            }
            
            .bookings-container {
                padding: 0 1rem;
            }
        }
    </style>
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="logo">
                <h1>Health & Fitness Center</h1>
            </div>
            <div class="hamburger">
                <i class="fas fa-bars"></i>
            </div>
            <div class="nav-links">
                
                <div class="nav-main">
                    <a href="../index.php" class="nav-item">Home</a>
                    <a href="services.php" class="nav-item">Services</a>
                    <a href="classes.php" class="nav-item">Classes</a>
                    <a href="trainers.php" class="nav-item">Trainers</a>
                    <a href="feedback.php" class="nav-item">Feedback</a>
                    <a href="about.php" class="nav-item">About Us</a>
                    <a href="search.php" class="nav-item">Search</a>
                </div>

                
                <div class="nav-user">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="profile.php" class="nav-item">Profile</a>
                        <a href="bookings.php" class="nav-item active">My Bookings</a>
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

        <section class="bookings-container">
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
                                <p>
                                    <i class="fas fa-tag"></i>
                                    <?php echo htmlspecialchars($booking['category']); ?>
                                </p>
                                <p>
                                    <i class="fas fa-calendar"></i>
                                    <?php echo date('F j, Y', strtotime($booking['booking_date'])); ?>
                                </p>
                                <p>
                                    <i class="fas fa-clock"></i>
                                    <?php echo date('g:i A', strtotime($booking['booking_time'])); ?>
                                </p>
                                <p>
                                    <i class="fas fa-dollar-sign"></i>
                                    <?php echo number_format($booking['price'], 2); ?>
                                </p>
                            </div>
                            <?php if ($booking['status'] === 'pending'): ?>
                                <div class="booking-actions">
                                    <form action="cancel-booking.php" method="POST" onsubmit="return confirm('Are you sure you want to cancel this booking?')">
                                        <input type="hidden" name="booking_id" value="<?php echo $booking['id']; ?>">
                                        <button type="submit" class="btn btn-danger">
                                            Cancel Booking
                                        </button>
                                    </form>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-bookings">
                    <i class="fas fa-calendar-times"></i>
                    <h2>No Bookings Found</h2>
                    <p>You haven't made any bookings yet.</p>
                    <a href="services.php" class="btn btn-primary">Book a Service</a>
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
            <p>&copy; <?php echo date('Y'); ?> Health & Fitness Center. All rights reserved.</p>
        </div>
    </footer>

    <script>
        
        const hamburger = document.querySelector('.hamburger');
        const navLinks = document.querySelector('.nav-links');
        
        hamburger.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
        
        
        const navItems = document.querySelectorAll('.nav-item');
        navItems.forEach(item => {
            item.addEventListener('click', () => {
                navLinks.classList.remove('active');
            });
        });
    </script>
</body>
</html>