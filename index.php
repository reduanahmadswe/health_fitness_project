<?php
session_start();
require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health & Fitness Center</title>
    <link rel="stylesheet" href="css/style.css">
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
                    <a href="index.php" class="nav-item">Home</a>
                    <a href="pages/services.php" class="nav-item">Services</a>
                    <a href="pages/classes.php" class="nav-item">Classes</a>
                    <a href="pages/trainers.php" class="nav-item">Trainers</a>
                    <a href="pages/feedback.php" class="nav-item">Feedback</a>
                    <a href="pages/about.php" class="nav-item">About Us</a>
                    <a href="pages/search.php" class="nav-item">Search</a>
                </div>

                <!-- User Navigation -->
                <div class="nav-user">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="pages/profile.php" class="nav-item">Profile</a>
                        <a href="pages/bookings.php" class="nav-item">My Bookings</a>
                        <a href="pages/logout.php" class="nav-item">Logout</a>
                    <?php else: ?>
                        <a href="pages/login.php" class="nav-item">Login</a>
                        <a href="pages/register.php" class="nav-item">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="hero">
            <div class="hero-content">
                <h1>Transform Your Life</h1>
                <p>Join our state-of-the-art fitness center and start your journey to a healthier lifestyle</p>
                <a href="pages/register.php" class="btn btn-primary">Get Started</a>
            </div>
        </section>

        <section class="features grid">
            <div class="card">
                <i class="fas fa-dumbbell fa-3x"></i>
                <h3>Modern Equipment</h3>
                <p>Access to the latest fitness equipment and technology</p>
            </div>
            <div class="card">
                <i class="fas fa-users fa-3x"></i>
                <h3>Expert Trainers</h3>
                <p>Professional trainers to guide your fitness journey</p>
            </div>
            <div class="card">
                <i class="fas fa-calendar-alt fa-3x"></i>
                <h3>Flexible Classes</h3>
                <p>Wide range of classes to suit your schedule</p>
            </div>
        </section>

        <section class="cta">
            <div class="cta-content">
                <h2>Ready to Start Your Fitness Journey?</h2>
                <p>Join us today and get your first week free!</p>
                <a href="pages/register.php" class="btn btn-primary">Sign Up Now</a>
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
                <a href="pages/about.php">About Us</a>
                <a href="pages/services.php">Services</a>
                <a href="pages/contact.php">Contact</a>
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

    <script src="js/main.js"></script>
</body>
</html> 