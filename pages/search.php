<?php
session_start();
require_once '../includes/config.php';

// Get search query from URL parameter
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

if (!empty($search_query)) {
    // Search in services table with prepared statement
    $sql = "SELECT * FROM services WHERE 
            name LIKE ? OR 
            description LIKE ? OR 
            category LIKE ? 
            ORDER BY name 
            LIMIT 10";
    
    $search_term = "%{$search_query}%";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sss", $search_term, $search_term, $search_term);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $results = mysqli_fetch_all($result, MYSQLI_ASSOC);
}

// Get all categories for filter
$categories = [];
$category_query = "SELECT DISTINCT category FROM services ORDER BY category";
$category_result = mysqli_query($conn, $category_query);
if ($category_result) {
    $categories = mysqli_fetch_all($category_result, MYSQLI_ASSOC);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search - Health & Fitness Center</title>
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
        
        .nav-item.active {
            color: var(--accent);
        }
        
        .nav-item.active::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: var(--accent);
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
        
        .btn-outline {
            background: transparent;
            border: 2px solid white;
            color: white;
        }
        
        .btn-outline:hover {
            background: white;
            color: var(--primary);
        }
        
        /* Search Hero Section */
        .search-hero {
            background: linear-gradient(135deg, rgba(74, 111, 165, 0.9), rgba(22, 96, 136, 0.9)), url('../images/search-bg.jpeg');
            background-size: cover;
            background-position: top;
            color: white;
            padding: 6rem 0;
            text-align: center;
        }
        
        .search-hero h1 {
            font-size: 3rem;
            margin-bottom: 1.5rem;
            animation: fadeInDown 1s ease;
        }
        
        .search-hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            opacity: 0.9;
            animation: fadeInUp 1s ease;
        }
        
        .search-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .search-form {
            position: relative;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .search-input {
            width: 100%;
            padding: 1rem 1.5rem;
            font-size: 1rem;
            border: none;
            border-radius: 50px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            outline: none;
            transition: all 0.3s ease;
        }
        
        .search-input:focus {
            box-shadow: 0 0 0 3px rgba(79, 195, 161, 0.3);
        }
        
        .search-button {
            position: absolute;
            right: 5px;
            top: 5px;
            background: var(--accent);
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .search-button:hover {
            background: var(--secondary);
            transform: scale(1.05);
        }
        
        /* Search Content Section */
        .search-content {
            padding: 4rem 0;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }
        
        .section-header h2 {
            font-size: 2.5rem;
            color: var(--dark);
            margin-bottom: 1rem;
            position: relative;
            display: inline-block;
        }
        
        .section-header h2::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 50%;
            transform: translateX(-50%);
            width: 80px;
            height: 3px;
            background-color: var(--accent);
        }
        
        .section-header p {
            color: var(--dark-gray);
            max-width: 700px;
            margin: 0 auto;
        }
        
        /* Filter Section */
        .filter-section {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-bottom: 3rem;
            flex-wrap: wrap;
        }
        
        .filter-btn {
            padding: 0.5rem 1.5rem;
            background: white;
            border: 1px solid var(--gray);
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 0.9rem;
        }
        
        .filter-btn:hover, .filter-btn.active {
            background: var(--accent);
            color: white;
            border-color: var(--accent);
        }
        
        /* Results Grid */
        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 2rem;
        }
        
        .result-card {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .result-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .result-image {
            height: 200px;
            overflow: hidden;
        }
        
        .result-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .result-card:hover .result-image img {
            transform: scale(1.05);
        }
        
        .result-info {
            padding: 1.5rem;
        }
        
        .result-info h3 {
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
            color: var(--dark);
        }
        
        .result-category {
            display: inline-block;
            background: var(--gray);
            color: var(--secondary);
            padding: 0.3rem 1rem;
            border-radius: 50px;
            font-size: 0.8rem;
            font-weight: 600;
            margin-bottom: 1rem;
        }
        
        .result-description {
            color: var(--dark-gray);
            margin-bottom: 1.5rem;
            line-height: 1.6;
        }
        
        .result-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--accent);
            margin-bottom: 1.5rem;
        }
        
        .no-results {
            text-align: center;
            padding: 4rem;
            color: var(--dark-gray);
            font-size: 1.2rem;
            grid-column: 1 / -1;
        }
        
        .no-results a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
        }
        
        .no-results a:hover {
            text-decoration: underline;
        }
        
        .highlight {
            background-color: #fff3cd;
            padding: 2px;
            border-radius: 2px;
        }
        
        /* Footer Styles */
        footer {
            background-color: var(--dark);
            color: white;
            padding: 4rem 0 0;
        }
        
        .footer-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .footer-col h3 {
            font-size: 1.3rem;
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .footer-col h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 50px;
            height: 2px;
            background-color: var(--accent);
        }
        
        .footer-col p, .footer-col a {
            color: var(--gray);
            margin-bottom: 0.8rem;
            display: block;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .footer-col a:hover {
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
        
        /* Animations */
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
        
        /* Responsive Styles */
        @media (max-width: 992px) {
            .search-hero h1 {
                font-size: 2.5rem;
            }
            
            .section-header h2 {
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
            
            .search-hero {
                padding: 4rem 0;
            }
            
            .results-grid {
                grid-template-columns: 1fr;
            }
        }
        
        @media (max-width: 576px) {
            .search-hero h1 {
                font-size: 2rem;
            }
            
            .filter-section {
                justify-content: flex-start;
            }
        }
    </style>
</head>
<body>
<header class="header">
    <div class="navbar">
        <div class="logo">
            <a href="../index.php">
                <h1>Health & <span>Fitness</span></h1>
            </a>
        </div>

        <div class="hamburger" id="mobile-menu">
            <!-- Hamburger icon removed -->
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
                <a href="search.php" class="nav-item active">Search</a>
            </div>

            <!-- User Navigation -->
            <div class="nav-user">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="profile.php" class="nav-item">Profile</a>
                    <a href="bookings.php" class="nav-item">My Bookings</a>
                    
                <?php else: ?>
                    <a href="login.php" class="nav-item btn btn-outline">Login</a>
                    <a href="register.php" class="nav-item btn btn-primary">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>



    <main>
        <section class="search-hero">
            <div class="search-container">
                <h1>Find What You Need</h1>
                <p>Search our services, classes, and fitness programs</p>
                <form action="search.php" method="GET" class="search-form">
                    <input type="text" name="q" class="search-input" placeholder="Search services..." value="<?php echo htmlspecialchars($search_query); ?>" required>
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </section>

        <section class="search-content">
            <div class="container">
                <?php if (!empty($search_query)): ?>
                    <div class="section-header">
                        <h2>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h2>
                    </div>
                    
                    <?php if (!empty($categories)): ?>
                        <div class="filter-section">
                            <button class="filter-btn active" data-category="all">All Categories</button>
                            <?php foreach ($categories as $category): ?>
                                <button class="filter-btn" data-category="<?php echo htmlspecialchars($category['category']); ?>">
                                    <?php echo htmlspecialchars($category['category']); ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($results)): ?>
                        <div class="results-grid">
                            <?php foreach ($results as $result): ?>
                                <div class="result-card" data-category="<?php echo htmlspecialchars($result['category']); ?>">
                                    <div class="result-image">
                                        <img src="../images/services/<?php echo htmlspecialchars($result['image'] ?? 'default-service.jpg'); ?>" alt="<?php echo htmlspecialchars($result['name']); ?>">
                                    </div>
                                    <div class="result-info">
                                        <span class="result-category"><?php echo htmlspecialchars($result['category']); ?></span>
                                        <h3><?php echo highlightKeywords(htmlspecialchars($result['name']), $search_query); ?></h3>
                                        <p class="result-description"><?php echo highlightKeywords(htmlspecialchars($result['description']), $search_query); ?></p>
                                        <p class="result-price">$<?php echo number_format($result['price'], 2); ?></p>
                                        <?php if(isset($_SESSION['user_id'])): ?>
                                            <a href="book-service.php?id=<?php echo $result['id']; ?>" class="btn btn-primary">Book Now</a>
                                        <?php else: ?>
                                            <a href="login.php" class="btn btn-primary">Login to Book</a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="no-results">
                            <p>No results found for your search. Try different keywords or browse our <a href="services.php">services</a>.</p>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="section-header">
                        <h2>Browse Our Services</h2>
                        <p>Search for specific services or browse by category</p>
                    </div>
                    
                    <?php if (!empty($categories)): ?>
                        <div class="filter-section">
                            <?php foreach ($categories as $category): ?>
                                <a href="services.php?category=<?php echo urlencode($category['category']); ?>" class="filter-btn">
                                    <?php echo htmlspecialchars($category['category']); ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-grid">
            <div class="footer-col">
                <h3>Health & Fitness</h3>
                <p>Your journey to a healthier life starts here.</p>
                <div class="social-links">
                    <a href="#"><i class="fab fa-facebook-f"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-twitter"></i></a>
                    <a href="#"><i class="fab fa-youtube"></i></a>
                </div>
            </div>
            <div class="footer-col">
                <h3>Quick Links</h3>
                <a href="../index.php">Home</a>
                <a href="services.php">Services</a>
                <a href="classes.php">Classes</a>
                <a href="trainers.php">Trainers</a>
                <a href="about.php">About Us</a>
            </div>
            <div class="footer-col">
                <h3>Contact</h3>
                <p><i class="fas fa-map-marker-alt"></i> 123 Fitness Street, Health City</p>
                <p><i class="fas fa-phone"></i> (123) 456-7890</p>
                <p><i class="fas fa-envelope"></i> info@healthfitness.com</p>
            </div>
            <div class="footer-col">
                <h3>Newsletter</h3>
                <p>Subscribe to our newsletter for tips and offers.</p>
                <form class="newsletter-form">
                    <input type="email" placeholder="Your Email" class="search-input" style="margin-bottom: 1rem;">
                    <button type="submit" class="btn btn-primary">Subscribe</button>
                </form>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Health & Fitness Center. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Mobile menu toggle
        document.addEventListener('DOMContentLoaded', function() {
            const menuToggle = document.getElementById('mobile-menu');
            const navLinks = document.querySelector('.nav-links');

            menuToggle.addEventListener('click', function() {
                this.classList.toggle('active');
                navLinks.classList.toggle('active');
            });

            // Close menu when clicking on a nav item
            const navItems = document.querySelectorAll('.nav-item');
            navItems.forEach(item => {
                item.addEventListener('click', function() {
                    menuToggle.classList.remove('active');
                    navLinks.classList.remove('active');
                });
            });

            // Category filter functionality
            const filterBtns = document.querySelectorAll('.filter-btn');
            if (filterBtns.length > 0) {
                filterBtns.forEach(btn => {
                    btn.addEventListener('click', function(e) {
                        if (this.tagName === 'A') return;
                        
                        e.preventDefault();
                        const category = this.dataset.category;
                        
                        // Update active button
                        filterBtns.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        
                        // Filter results
                        if (category === 'all') {
                            document.querySelectorAll('.result-card').forEach(card => {
                                card.style.display = 'block';
                            });
                        } else {
                            document.querySelectorAll('.result-card').forEach(card => {
                                if (card.dataset.category === category) {
                                    card.style.display = 'block';
                                } else {
                                    card.style.display = 'none';
                                }
                            });
                        }
                    });
                });
            }

            // Highlight search terms in results
            function highlightKeywords(text, query) {
                if (!query) return text;
                const terms = query.split(' ').filter(term => term.length > 0);
                terms.forEach(term => {
                    const regex = new RegExp(term, 'gi');
                    text = text.replace(regex, match => `<span class="highlight">${match}</span>`);
                });
                return text;
            }
        });
    </script>
</body>
</html>

<?php
// Function to highlight search keywords in text
function highlightKeywords($text, $query) {
    if (empty($query)) return $text;
    $terms = explode(' ', $query);
    foreach ($terms as $term) {
        if (strlen($term) > 0) {
            $text = preg_replace("/(" . preg_quote($term) . ")/i", "<span class=\"highlight\">$1</span>", $text);
        }
    }
    return $text;
}
?>