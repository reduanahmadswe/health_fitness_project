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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
                        <a href="profile.php" class="nav-item active">Profile</a>
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
        <section class="hero">
            <div class="hero-content">
                <h1>My Profile</h1>
                <p>Manage your account and view your bookings</p>
            </div>
        </section>

        <section class="profile-section">
            <div class="container">
                <div class="profile-grid">
                    <div class="profile-card">
                        <div class="card">
                            <div class="card-header">
                                <h2><i class="fas fa-user-circle"></i> Personal Information</h2>
                            </div>
                            <div class="card-body">
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
                            </div>
                            <div class="card-footer">
                                <a href="edit-profile.php" class="btn btn-primary">Edit Profile</a>
                                <a href="change-password.php" class="btn btn-outline" style="margin-top: 10px;">Change Password</a>
                            </div>

                        </div>
                    </div>

                    <div class="bookings-card">
                        <div class="card">
                            <div class="card-header">
                                <h2><i class="fas fa-calendar-alt"></i> Recent Bookings</h2>
                                <a href="services.php" class="btn btn-primary">Book New Service</a>
                            </div>
                            <div class="card-body">
                                <?php if (!empty($bookings)): ?>
                                    <div class="bookings-grid">
                                        <?php foreach ($bookings as $booking): ?>
                                            <div class="booking-item">
                                                <div class="booking-icon">
                                                    <i class="fas fa-<?php echo ($booking['category'] == 'Fitness' ? 'dumbbell' : ($booking['category'] == 'Yoga' ? 'spa' : 'calendar-check')); ?>"></i>
                                                </div>
                                                <div class="booking-details">
                                                    <h3><?php echo htmlspecialchars($booking['service_name']); ?></h3>
                                                    <p class="category"><?php echo htmlspecialchars($booking['category']); ?></p>
                                                    <div class="booking-meta">
                                                        <p><i class="far fa-calendar"></i> <?php echo date('F j, Y', strtotime($booking['booking_date'])); ?></p>
                                                        <p><i class="far fa-clock"></i> <?php echo date('g:i A', strtotime($booking['booking_time'])); ?></p>
                                                    </div>
                                                    <p class="status status-<?php echo strtolower($booking['status']); ?>">
                                                        <i class="fas fa-<?php echo ($booking['status'] == 'Confirmed' ? 'check-circle' : ($booking['status'] == 'Pending' ? 'hourglass-half' : 'times-circle')); ?>"></i>
                                                        <?php echo htmlspecialchars($booking['status']); ?>
                                                    </p>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <i class="far fa-calendar-times"></i>
                                        <h3>No Bookings Yet</h3>
                                        <p>You haven't made any bookings yet.</p>
                                        <a href="services.php" class="btn btn-primary">Explore Services</a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
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

    <script>
        // Mobile menu toggle
        const hamburger = document.querySelector('.hamburger');
        const navLinks = document.querySelector('.nav-links');
        
        hamburger.addEventListener('click', () => {
            navLinks.classList.toggle('active');
        });
    </script>

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
            color: white;
        }
        
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../images/profile-hero-bg.jpg') no-repeat center center/cover;
            height: 50vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            border-radius: 0 0 20px 20px;
            margin-bottom: 4rem;
        }
        
        .hero-content {
            max-width: 800px;
            padding: 2rem;
        }
        
        .hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            animation: fadeInDown 1s ease;
        }
        
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease;
        }
        
        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .profile-grid {
            display: grid;
            grid-template-columns: 1fr 2fr;
            gap: 2rem;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 95%;
            display: flex;
            flex-direction: column;
        }
        
        .card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--gray);
        }
        
        .card-header h2 {
            font-size: 1.5rem;
            color: var(--dark);
            display: flex;
            align-items: center;
            gap: 0.8rem;
        }
        
        .card-header i {
            color: var(--accent);
        }
        
        .info-item {
            margin-bottom: 1.5rem;
        }
        
        .info-item label {
            display: block;
            color: var(--dark-gray);
            font-size: 0.9rem;
            margin-bottom: 0.3rem;
        }
        
        
        .info-item span {
            color: var(--dark);
            font-size: 1.1rem;
            font-weight: 500;
        }
        
        .card-footer {
            margin-top: auto;
            padding-top: 1.5rem;
        }
        
        .bookings-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }
        
        .booking-item {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            gap: 1.5rem;
            align-items: flex-start;
        }
        
        .booking-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }
        
        .booking-icon {
            background-color: rgba(79, 195, 161, 0.1);
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        
        .booking-icon i {
            color: var(--accent);
            font-size: 1.2rem;
        }
        
        .booking-details {
            flex-grow: 1;
        }
        
        .booking-details h3 {
            font-size: 1.2rem;
            color: var(--dark);
            margin-bottom: 0.3rem;
        }
        
        .category {
            color: var(--accent);
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 1rem;
        }
        
        .booking-meta {
            display: flex;
            gap: 1rem;
            margin-bottom: 1rem;
            flex-wrap: wrap;
        }
        
        .booking-meta p {
            color: var(--dark-gray);
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .booking-meta i {
            color: var(--primary);
        }
        
        .status {
            font-weight: 600;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        
        .status i {
            font-size: 1rem;
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
        
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
        }
        
        .empty-state i {
            font-size: 3rem;
            color: var(--dark-gray);
            margin-bottom: 1.5rem;
            opacity: 0.5;
        }
        
        .empty-state h3 {
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        
        .empty-state p {
            color: var(--dark-gray);
            margin-bottom: 1.5rem;
        }
        
        .btn {
            display: inline-block;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }
        
        .btn-primary {
            background-color: var(--accent);
            color: white;
            box-shadow: 0 4px 15px rgba(79, 195, 161, 0.4);
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(79, 195, 161, 0.6);
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
            .navbar {
                padding: 1rem;
            }
            
            .profile-grid {
                grid-template-columns: 1fr;
            }
            
            .hero h1 {
                font-size: 2.5rem;
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
            
            .hero {
                height: 60vh;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .hero p {
                font-size: 1rem;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .logo h1 {
                font-size: 1.5rem;
            }
            
            .hero {
                height: 50vh;
            }
            
            .hero h1 {
                font-size: 1.8rem;
            }
        }
    </style>
</body>
</html>