<?php
session_start();
require_once '../includes/config.php';

// Fetch all classes from services table where category is 'Group Classes'
$sql = "SELECT * FROM services WHERE category = 'Group Classes' ORDER BY name";
$result = mysqli_query($conn, $sql);
$classes = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Classes - Health & Fitness Center</title>
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
        <section class="classes-hero">
            <h1>Our Fitness Classes</h1>
            <p>Join our expert-led classes and achieve your fitness goals</p>
        </section>

        <section class="classes-grid">
            <div class="grid">
                <?php foreach ($classes as $class): ?>
                    <div class="card class-card">
                        <div class="class-icon">
                            <i class="fas fa-dumbbell fa-3x"></i>
                        </div>
                        <h3><?php echo htmlspecialchars($class['name']); ?></h3>
                        <p><?php echo htmlspecialchars($class['description']); ?></p>
                        <div class="class-price">
                            <span>$<?php echo number_format($class['price'], 2); ?></span>
                        </div>
                        <?php if(isset($_SESSION['user_id'])): ?>
                            <a href="book-service.php?id=<?php echo $class['id']; ?>" class="btn btn-primary">Book Now</a>
                        <?php else: ?>
                            <a href="login.php" class="btn btn-primary">Login to Book</a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="class-schedule">
            <h2>Class Schedule</h2>
            <div class="schedule-grid">
                <div class="schedule-card">
                    <h3>Morning Classes</h3>
                    <ul>
                        <li>6:00 AM - Yoga</li>
                        <li>7:00 AM - Group Fitness</li>
                        <li>8:00 AM - HIIT</li>
                    </ul>
                </div>
                <div class="schedule-card">
                    <h3>Afternoon Classes</h3>
                    <ul>
                        <li>12:00 PM - Pilates</li>
                        <li>2:00 PM - Zumba</li>
                        <li>4:00 PM - Strength Training</li>
                    </ul>
                </div>
                <div class="schedule-card">
                    <h3>Evening Classes</h3>
                    <ul>
                        <li>6:00 PM - Yoga</li>
                        <li>7:00 PM - Group Fitness</li>
                        <li>8:00 PM - HIIT</li>
                    </ul>
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
        .classes-hero {
            text-align: center;
            padding: 4rem 2rem;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('../images/classes-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
        }

        .classes-hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .class-card {
            text-align: center;
            transition: transform 0.3s ease;
        }

        .class-card:hover {
            transform: translateY(-10px);
        }

        .class-icon {
            margin-bottom: 1rem;
            color: #3498db;
        }

        .class-price {
            font-size: 1.5rem;
            font-weight: bold;
            color: #2c3e50;
            margin: 1rem 0;
        }

        .schedule-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            padding: 2rem;
        }

        .schedule-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .schedule-card h3 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .schedule-card ul {
            list-style: none;
            padding: 0;
        }

        .schedule-card li {
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }

        .schedule-card li:last-child {
            border-bottom: none;
        }
    </style>
</body>
</html> 