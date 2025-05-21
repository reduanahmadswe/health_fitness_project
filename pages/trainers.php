<?php
session_start();
require_once '../includes/config.php';

// Sample trainers data (in a real application, this would come from a database)
$trainers = [
    [
        'name' => 'John Smith',
        'specialization' => 'Strength Training',
        'experience' => '10 years',
        'bio' => 'Certified personal trainer specializing in strength and conditioning. Former professional athlete with a passion for helping others achieve their fitness goals.',
        'image' => 'trainer1.jpg'
    ],
    [
        'name' => 'Sarah Johnson',
        'specialization' => 'Yoga & Pilates',
        'experience' => '8 years',
        'bio' => 'Experienced yoga and pilates instructor with a focus on mindfulness and body awareness. Helps clients improve flexibility and mental well-being.',
        'image' => 'trainer2.jpg'
    ],
    [
        'name' => 'Mike Wilson',
        'specialization' => 'HIIT & Cardio',
        'experience' => '6 years',
        'bio' => 'HIIT specialist with a background in competitive sports. Creates high-energy workouts that maximize results in minimal time.',
        'image' => 'trainer3.jpg'
    ],
    [
        'name' => 'Emily Brown',
        'specialization' => 'Nutrition & Wellness',
        'experience' => '12 years',
        'bio' => 'Certified nutritionist and wellness coach. Combines exercise science with nutritional expertise to help clients achieve optimal health.',
        'image' => 'trainer4.jpg'
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trainers - Health & Fitness Center</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
    <header class="header">
        <nav class="navbar">
            <div class="logo">
                <h1>Health<span>&Fitness</span></h1>
            </div>
            
            <div class="hamburger" id="mobile-menu">
                <i class="fas fa-bars"></i>
            </div>
            
            <div class="nav-links">
                <!-- Main Navigation -->
                <div class="nav-main">
                    <a href="../index.php" class="nav-item">Home</a>
                    <a href="services.php" class="nav-item">Services</a>
                    <a href="classes.php" class="nav-item">Classes</a>
                    <a href="trainers.php" class="nav-item active">Trainers</a>
                    <a href="feedback.php" class="nav-item">Feedback</a>
                    <a href="about.php" class="nav-item">About</a>
                </div>

                <!-- User Navigation -->
                <div class="nav-user">
                    <?php if(isset($_SESSION['user_id'])): ?>
                        <a href="profile.php" class="nav-item"><i class="fas fa-user"></i> Profile</a>
                        <a href="bookings.php" class="nav-item"><i class="fas fa-calendar-alt"></i> Bookings</a>
                        <a href="logout.php" class="nav-item"><i class="fas fa-sign-out-alt"></i> Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="nav-item"><i class="fas fa-sign-in-alt"></i> Login</a>
                        <a href="register.php" class="nav-item"><i class="fas fa-user-plus"></i> Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <section class="trainers-hero">
            <h1>Our Expert Trainers</h1>
            <p>Professional guidance tailored to your fitness journey</p>
        </section>

        <section class="trainers-grid">
            <div class="category-section">
                <h2>Meet Our Team</h2>
                <div class="grid">
                    <?php foreach ($trainers as $trainer): ?>
                        <div class="trainer-card">
                            <div class="trainer-image">
                                <img src="../images/<?php echo htmlspecialchars($trainer['image']); ?>" alt="<?php echo htmlspecialchars($trainer['name']); ?>">
                            </div>
                            <div class="trainer-info">
                                <h3><?php echo htmlspecialchars($trainer['name']); ?></h3>
                                <div class="trainer-specialization">
                                    <i class="fas fa-dumbbell"></i> <?php echo htmlspecialchars($trainer['specialization']); ?>
                                </div>
                                <div class="trainer-experience">
                                    <i class="fas fa-award"></i> <?php echo htmlspecialchars($trainer['experience']); ?> experience
                                </div>
                                <p class="trainer-bio"><?php echo htmlspecialchars($trainer['bio']); ?></p>
                                <div class="trainer-actions">
                                    <?php if(isset($_SESSION['user_id'])): ?>
                                        <a href="book-service.php?trainer=<?php echo urlencode($trainer['name']); ?>" class="btn btn-primary">Book Session</a>
                                    <?php else: ?>
                                        <a href="login.php" class="btn btn-primary">Login to Book</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <section class="trainers-cta">
            <div class="cta-content">
                <h2>Start Your Transformation Today</h2>
                <p>Get personalized training from our certified professionals</p>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="services.php" class="btn btn-primary">Book a Session</a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary">Join Now</a>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-content">
            <div class="footer-section">
                <h3>Health<span>&Fitness</span></h3>
                <p>Your journey to a healthier life starts here. We provide professional guidance and support for your fitness goals.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="footer-section">
                <h3>Quick Links</h3>
                <a href="../index.php">Home</a>
                <a href="services.php">Services</a>
                <a href="classes.php">Classes</a>
                <a href="trainers.php">Trainers</a>
                <a href="about.php">About Us</a>
            </div>
            <div class="footer-section">
                <h3>Contact Us</h3>
                <p><i class="fas fa-map-marker-alt"></i> 123 Fitness Street, Health City</p>
                <p><i class="fas fa-phone"></i> (123) 456-7890</p>
                <p><i class="fas fa-envelope"></i> info@healthfitness.com</p>
            </div>
            <div class="footer-section">
                <h3>Newsletter</h3>
                <p>Subscribe to get updates on new classes and special offers.</p>
                <form class="newsletter-form">
                    <input type="email" placeholder="Your Email">
                    <button type="submit" class="btn btn-primary">Subscribe</button>
                </form>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Health & Fitness Center. All rights reserved.</p>
            <div class="footer-links">
                <a href="#">Privacy Policy</a>
                <a href="#">Terms of Service</a>
            </div>
        </div>
    </footer>

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
        
        .logo h1 span {
            color: var(--accent);
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
        
        .nav-item.active {
            color: var(--accent);
        }
        
        .nav-item.active::after {
            width: 100%;
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
            color: white;
        }
        
        main {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .trainers-hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../images/trainers-bg.jpg') no-repeat center center/cover;
            height: 55vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            border-radius: 0 0 20px 20px;
            margin-bottom: 4rem;
        }
        
        .trainers-hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            animation: fadeInDown 1s ease;
        }
        
        .trainers-hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease;
        }
        
        .category-section {
            margin-bottom: 3rem;
        }
        
        .category-section h2 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
            color: var(--secondary);
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .category-section h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 3px;
            background-color: var(--accent);
        }
        
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .trainer-card {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .trainer-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .trainer-image {
            height: 250px;
            overflow: hidden;
        }
        
        .trainer-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .trainer-card:hover .trainer-image img {
            transform: scale(1.1);
        }
        
        .trainer-info {
            padding: 1.5rem;
        }
        
        .trainer-info h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: var(--secondary);
        }
        
        .trainer-specialization, .trainer-experience {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .trainer-specialization i {
            color: var(--accent);
        }
        
        .trainer-bio {
            color: var(--dark-gray);
            margin: 1rem 0;
        }
        
        .trainer-actions {
            margin-top: 1.5rem;
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
            background-color: #3da98a;
        }
        
        .trainers-cta {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            padding: 4rem 2rem;
            text-align: center;
            border-radius: 15px;
            margin: 4rem 0;
        }
        
        .trainers-cta h2 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .trainers-cta p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
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
        
        .footer-section h3 span {
            color: var(--accent);
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
            margin-top: 1rem;
        }
        
        .social-links a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 36px;
            height: 36px;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: white;
            transition: all 0.3s ease;
        }
        
        .social-links a:hover {
            background-color: var(--accent);
            transform: translateY(-3px);
        }
        
        .newsletter-form {
            display: flex;
            margin-top: 1rem;
        }
        
        .newsletter-form input {
            flex: 1;
            padding: 0.8rem 1rem;
            border: none;
            border-radius: 50px 0 0 50px;
            font-family: inherit;
        }
        
        .newsletter-form button {
            border-radius: 0 50px 50px 0;
            padding: 0 1.5rem;
        }
        
        .footer-bottom {
            text-align: center;
            padding: 1.5rem;
            background-color: rgba(0, 0, 0, 0.2);
            margin-top: 2rem;
        }
        
        .footer-bottom p {
            margin-bottom: 0.5rem;
        }
        
        .footer-links {
            display: flex;
            justify-content: center;
            gap: 1.5rem;
        }
        
        .footer-links a {
            color: var(--gray);
            font-size: 0.9rem;
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
            
            .trainers-hero h1 {
                font-size: 2.5rem;
            }
            
            .trainers-cta h2 {
                font-size: 2rem;
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
            
            .trainers-hero {
                height: 40vh;
            }
            
            .trainers-hero h1 {
                font-size: 2rem;
            }
            
            .trainers-hero p {
                font-size: 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .logo h1 {
                font-size: 1.5rem;
            }
            
            .trainers-hero {
                height: 30vh;
            }
            
            .trainers-hero h1 {
                font-size: 1.8rem;
            }
            
            .trainers-cta h2 {
                font-size: 1.8rem;
            }
            
            .trainers-cta p {
                font-size: 1rem;
            }
            
            .grid {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <script>
        // Mobile menu toggle
        document.getElementById('mobile-menu').addEventListener('click', function() {
            document.querySelector('.nav-links').classList.toggle('active');
        });
    </script>
</body>
</html>