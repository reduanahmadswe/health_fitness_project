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
    <style>
        .about-hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('../images/about-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            text-align: center;
            padding: 6rem 2rem;
            margin-bottom: 3rem;
        }

        .about-hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .about-section {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .mission-vision {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-bottom: 4rem;
        }

        .mission-card, .vision-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            text-align: center;
        }

        .mission-card i, .vision-card i {
            font-size: 3rem;
            color: #3498db;
            margin-bottom: 1rem;
        }

        .team-section {
            margin-bottom: 4rem;
        }

        .team-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .team-member {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }

        .team-member:hover {
            transform: translateY(-5px);
        }

        .team-member img {
            width: 100%;
            height: 250px;
            object-fit: cover;
        }

        .team-info {
            padding: 1.5rem;
            text-align: center;
        }

        .team-info h3 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .team-info p {
            color: #666;
            font-size: 0.9rem;
        }

        .facilities-section {
            background: #f8f9fa;
            padding: 4rem 2rem;
            margin-bottom: 4rem;
        }

        .facilities-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin-top: 2rem;
        }

        .facility-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .facility-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .facility-info {
            padding: 1.5rem;
        }

        .facility-info h3 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .stats-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 2rem;
            text-align: center;
            margin-bottom: 4rem;
        }

        .stat-card {
            padding: 2rem;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .stat-card i {
            font-size: 2.5rem;
            color: #3498db;
            margin-bottom: 1rem;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        @media (max-width: 768px) {
            .about-hero h1 {
                font-size: 2rem;
            }

            .about-section {
                padding: 1rem;
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
            <h1>About Us</h1>
            <p>Your journey to a healthier lifestyle starts here</p>
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
                        <img src="../images/team1.jpg" alt="John Doe">
                        <div class="team-info">
                            <h3>John Doe</h3>
                            <p>Founder & CEO</p>
                        </div>
                    </div>
                    <div class="team-member">
                        <img src="../images/team2.jpg" alt="Jane Smith">
                        <div class="team-info">
                            <h3>Jane Smith</h3>
                            <p>Head of Training</p>
                        </div>
                    </div>
                    <div class="team-member">
                        <img src="../images/team3.jpg" alt="Mike Johnson">
                        <div class="team-info">
                            <h3>Mike Johnson</h3>
                            <p>Fitness Director</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="facilities-section">
                <h2>Our Facilities</h2>
                <div class="facilities-grid">
                    <div class="facility-card">
                        <img src="../images/gym.jpg" alt="Modern Gym">
                        <div class="facility-info">
                            <h3>Modern Gym</h3>
                            <p>State-of-the-art equipment and spacious workout areas</p>
                        </div>
                    </div>
                    <div class="facility-card">
                        <img src="../images/pool.jpg" alt="Swimming Pool">
                        <div class="facility-info">
                            <h3>Swimming Pool</h3>
                            <p>Olympic-sized pool for swimming and water aerobics</p>
                        </div>
                    </div>
                    <div class="facility-card">
                        <img src="../images/yoga.jpg" alt="Yoga Studio">
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
            <p>&copy; 2024 Health & Fitness Center. All rights reserved.</p>
        </div>
    </footer>

    <script src="../js/main.js"></script>
</body>
</html> 