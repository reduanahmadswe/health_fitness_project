
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
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .services-hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../images/services-bg.jpg') no-repeat center center/cover;
            height: 50vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            border-radius: 0 0 20px 20px;
            margin-bottom: 4rem;
        }
        
        .services-hero h1 {
            font-size: 3.5rem;
            margin-bottom: 1rem;
            animation: fadeInDown 1s ease;
        }
        
        .services-hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease;
        }
        
        .services-grid {
            margin-bottom: 4rem;
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
        
        .service-card {
            background: white;
            border-radius: 15px;
            padding: 2rem;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .service-icon {
            margin-bottom: 1.5rem;
        }
        
        .service-icon i {
            color: var(--accent);
        }
        
        .service-card h3 {
            font-size: 1.5rem;
            margin-bottom: 1rem;
            color: var(--secondary);
        }
        
        .service-card p {
            color: var(--dark-gray);
            margin-bottom: 1.5rem;
            flex-grow: 1;
        }
        
        .service-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
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
            
            .services-hero h1 {
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
            
            .services-hero {
                height: 40vh;
            }
            
            .services-hero h1 {
                font-size: 2rem;
            }
            
            .services-hero p {
                font-size: 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .logo h1 {
                font-size: 1.5rem;
            }
            
            .services-hero {
                height: 30vh;
            }
            
            .category-section h2 {
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

    <script src="../js/service.js"></script>

</body>

</html>