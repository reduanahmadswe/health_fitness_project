<?php
session_start();
require_once '../includes/config.php';

// Fetch all services from database
$sql = "SELECT * FROM services ORDER BY category, name";
$result = mysqli_query($conn, $sql);
$services = mysqli_fetch_all($result, MYSQLI_ASSOC);

// Group services by category
$grouped_services = [];
foreach ($services as $service) {
    $category = $service['category'];
    if (!isset($grouped_services[$category])) {
        $grouped_services[$category] = [];
    }
    $grouped_services[$category][] = $service;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services - Health & Fitness Center</title>
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
        <section class="services-hero">
            <h1>Our Services</h1>
            <p>Discover our wide range of fitness and wellness services</p>
        </section>

        <section class="services-grid">
            <?php foreach ($grouped_services as $category => $category_services): ?>
                <div class="category-section">
                    <h2><?php echo htmlspecialchars($category); ?></h2>
                    <div class="grid">
                        <?php foreach ($category_services as $service): ?>
                            <div class="card service-card">
                                <div class="service-icon">
                                    <?php
                                    // Set icon based on category
                                    $icon = 'fa-dumbbell';
                                    switch ($category) {
                                        case 'Personal Training':
                                            $icon = 'fa-user-friends';
                                            break;
                                        case 'Group Classes':
                                            $icon = 'fa-users';
                                            break;
                                        case 'Nutrition':
                                            $icon = 'fa-apple-alt';
                                            break;
                                        case 'Spa & Wellness':
                                            $icon = 'fa-spa';
                                            break;
                                    }
                                    ?>
                                    <i class="fas <?php echo $icon; ?> fa-3x"></i>
                                </div>
                                <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                                <p><?php echo htmlspecialchars($service['description']); ?></p>
                                <div class="service-price">
                                    <span>$<?php echo number_format($service['price'], 2); ?></span>
                                </div>
                                <?php if(isset($_SESSION['user_id'])): ?>
                                    <a href="book-service.php?id=<?php echo $service['id']; ?>" class="btn btn-primary">Book Now</a>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-primary">Login to Book</a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
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
        // Add smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                document.querySelector(this.getAttribute('href')).scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });
    </script>
</body>
</html> 