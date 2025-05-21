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
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="profile.php">Profile</a>
                    <a href="bookings.php">My Bookings</a>
                    <a href="logout.php">Logout</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </div>
        </nav>
    </header>

    <main>
        <section class="trainers-hero">
            <h1>Our Expert Trainers</h1>
            <p>Meet our team of professional fitness trainers</p>
        </section>

        <section class="trainers-grid">
            <div class="grid">
                <?php foreach ($trainers as $trainer): ?>
                    <div class="card trainer-card">
                        <div class="trainer-image">
                            <img src="../images/<?php echo htmlspecialchars($trainer['image']); ?>" alt="<?php echo htmlspecialchars($trainer['name']); ?>">
                        </div>
                        <div class="trainer-info">
                            <h3><?php echo htmlspecialchars($trainer['name']); ?></h3>
                            <p class="specialization"><?php echo htmlspecialchars($trainer['specialization']); ?></p>
                            <p class="experience">Experience: <?php echo htmlspecialchars($trainer['experience']); ?></p>
                            <p class="bio"><?php echo htmlspecialchars($trainer['bio']); ?></p>
                            <?php if(isset($_SESSION['user_id'])): ?>
                                <a href="book-service.php?trainer=<?php echo urlencode($trainer['name']); ?>" class="btn btn-primary">Book Session</a>
                            <?php else: ?>
                                <a href="login.php" class="btn btn-primary">Login to Book</a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="trainer-cta">
            <div class="cta-content">
                <h2>Ready to Start Your Fitness Journey?</h2>
                <p>Book a session with one of our expert trainers today!</p>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="services.php" class="btn btn-primary">Book Now</a>
                <?php else: ?>
                    <a href="register.php" class="btn btn-primary">Join Now</a>
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
        .trainers-hero {
            text-align: center;
            padding: 4rem 2rem;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('../images/trainers-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
        }

        .trainers-hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .trainer-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            padding: 2rem;
        }

        .trainer-image {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .trainer-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .trainer-info {
            flex: 1;
        }

        .trainer-info h3 {
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .specialization {
            color: #3498db;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .experience {
            color: #666;
            margin-bottom: 1rem;
        }

        .bio {
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }

        .trainer-cta {
            text-align: center;
            padding: 4rem 2rem;
            background: #f8f9fa;
        }

        .cta-content h2 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .cta-content p {
            margin-bottom: 2rem;
            color: #666;
        }
    </style>
</body>
</html> 