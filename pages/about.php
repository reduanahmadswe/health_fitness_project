<?php
session_start();
require_once '../includes/config.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Health & Fitness Center</title>
    <link rel="stylesheet" href="../css/style.css">
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
        
        .nav-item:hover {
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
        
        .nav-item:hover::after {
            width: 100%;
        }
        
        .hamburger {
            display: none;
            cursor: pointer;
            font-size: 1.5rem;
        }
        
        main {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 2rem;
        }
        
        .about-hero {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('../images/about-bg.jpg') no-repeat center center/cover;
            height: 50vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            border-radius: 15px;
            margin-bottom: 4rem;
            animation: fadeIn 1s ease;
        }
        
        .about-hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            animation: fadeInDown 1s ease;
        }
        
        .about-hero p {
            font-size: 1.2rem;
            animation: fadeInUp 1s ease;
        }
        
        .about-section {
            margin-bottom: 4rem;
        }
        
        .mission-vision {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }
        
        .mission-card, .vision-card {
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .mission-card:hover, .vision-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .mission-card i, .vision-card i {
            color: var(--accent);
            font-size: 3rem;
            margin-bottom: 1.5rem;
        }
        
        .mission-card h2, .vision-card h2 {
            font-size: 1.8rem;
            margin-bottom: 1rem;
            color: var(--secondary);
        }
        
        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }
        
        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        
        .stat-card i {
            color: var(--accent);
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .team-section, .facilities-section {
            margin-bottom: 4rem;
        }
        
        .team-section h2, .facilities-section h2 {
            font-size: 2.2rem;
            margin-bottom: 2rem;
            color: var(--secondary);
            text-align: center;
            position: relative;
        }
        
        .team-section h2::after, .facilities-section h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: var(--accent);
        }
        
        .team-grid, .facilities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 2rem;
        }
        
        .team-member, .facility-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .team-member:hover, .facility-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .team-member img, .facility-card img {
            width: 100%;
            height: 300px;
            object-fit: cover;
        }
        
        .team-info, .facility-info {
            padding: 1.5rem;
            text-align: center;
        }
        
        .team-info h3, .facility-info h3 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        
        .team-info p, .facility-info p {
            color: var(--dark-gray);
        }
        
        .facilities-section {
            background: linear-gradient(135deg, #f8f9fa, #e2e8f0);
            padding: 4rem 2rem;
            border-radius: 15px;
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
        
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
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
            
            .about-hero h1 {
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
            
            .about-hero {
                height: 40vh;
            }
            
            .about-hero h1 {
                font-size: 2rem;
            }
            
            .about-hero p {
                font-size: 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .logo h1 {
                font-size: 1.5rem;
            }
            
            .stats-section {
                grid-template-columns: 1fr 1fr;
            }
            
            .team-section h2, .facilities-section h2 {
                font-size: 1.8rem;
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
        <section class="about-hero">
            <div>
                <h1>About Us</h1>
                <p>Your journey to a healthier lifestyle starts here</p>
            </div>
        </section>

        <section class="about-section">
            <div class="mission-vision">
                <div class="mission-card">
                    <i class="fas fa-bullseye"></i>
                    <h2>Our Mission</h2>
                    <p>To inspire and empower individuals to achieve their fitness goals through personalized training, state-of-the-art facilities, and a supportive community environment.</p>
                </div>
                <div class="vision-card">
                    <i class="fas fa-eye"></i>
                    <h2>Our Vision</h2>
                    <p>To be the leading fitness center that transforms lives by promoting health, wellness, and personal growth through innovative fitness solutions.</p>
                </div>
            </div>

            <div class="stats-section">
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <div class="stat-number">5000+</div>
                    <p>Happy Members</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-dumbbell"></i>
                    <div class="stat-number">50+</div>
                    <p>Expert Trainers</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-calendar-check"></i>
                    <div class="stat-number">100+</div>
                    <p>Weekly Classes</p>
                </div>
                <div class="stat-card">
                    <i class="fas fa-trophy"></i>
                    <div class="stat-number">15+</div>
                    <p>Years Experience</p>
                </div>
            </div>

            <div class="team-section">
                <h2>Our Leadership Team</h2>
                <div class="team-grid">
                    <div class="team-member">
                        <img src="../images/trainers/trainer1.jpg" alt="John Doe">
                        <div class="team-info">
                            <h3>Reduan Ahmad</h3>
                            <p>Founder & CEO</p>
                        </div>
                    </div>
                    <div class="team-member">
                        <img src="../images/trainers/trainer2.jpg" alt="Jane Smith">
                        <div class="team-info">
                            <h3>Humaira Jannat</h3>
                            <p>Head of Training</p>
                        </div>
                    </div>
                    <div class="team-member">
                        <img src="../images/trainers/trainer4.jpg" alt="Mike Johnson">
                        <div class="team-info">
                            <h3>Rabbi Hossain</h3>
                            <p>Fitness Director</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="facilities-section">
                <h2>Our Facilities</h2>
                <div class="facilities-grid">
                    <div class="facility-card">
                        <img src="../images/facilities/GYM-01.png" alt="Modern Gym">
                        <div class="facility-info">
                            <h3>Modern Gym</h3>
                            <p>State-of-the-art equipment and spacious workout areas</p>
                        </div>
                    </div>
                    <div class="facility-card">
                        <img src="../images/facilities/pool.png" alt="Swimming Pool">
                        <div class="facility-info">
                            <h3>Swimming Pool</h3>
                            <p>Olympic-sized pool for swimming and water aerobics</p>
                        </div>
                    </div>
                    <div class="facility-card">
                        <img src="../images/facilities/yoga.jpg" alt="Yoga Studio">
                        <div class="facility-info">
                            <h3>Yoga Studio</h3>
                            <p>Peaceful environment for yoga and meditation</p>
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
            <p>&copy; 2025 Health & Fitness Center. All rights reserved.</p>
        </div>
    </footer>

    <script src="../js/main.js"></script>
    <script>
        
        document.querySelector('.hamburger').addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('active');
        });
    </script>
</body>
</html>