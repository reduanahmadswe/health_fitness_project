<?php
session_start();
require_once '../includes/config.php';

// Get search query from URL parameter
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';
$results = [];

if (!empty($search_query)) {
    // Search in services table
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
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search - Health & Fitness Center</title>
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Search Page Specific Styles */
        .search-hero {
            text-align: center;
            padding: 4rem 2rem;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('../images/search-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
            margin-bottom: 2rem;
        }

        .search-hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .search-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .search-input-container {
            position: relative;
            margin-bottom: 2rem;
        }

        .search-input {
            width: 100%;
            padding: 1rem 1.5rem;
            font-size: 1.1rem;
            border: 2px solid #ddd;
            border-radius: 25px;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-input:focus {
            border-color: #3498db;
            box-shadow: 0 0 10px rgba(52, 152, 219, 0.2);
        }

        .search-icon {
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .search-results {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .result-item {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #eee;
            transition: background-color 0.3s ease;
        }

        .result-item:last-child {
            border-bottom: none;
        }

        .result-item:hover {
            background-color: #f8f9fa;
        }

        .result-title {
            font-size: 1.2rem;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .result-category {
            color: #666;
            font-size: 0.9rem;
        }

        .result-description {
            color: #666;
            margin-top: 0.5rem;
            line-height: 1.4;
        }

        .no-results {
            text-align: center;
            padding: 2rem;
            color: #666;
            font-style: italic;
        }

        .highlight {
            background-color: #fff3cd;
            padding: 2px;
            border-radius: 2px;
        }

        @media (max-width: 768px) {
            .search-hero h1 {
                font-size: 2rem;
            }

            .search-container {
                padding: 0 1rem;
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
        <section class="search-hero">
            <h1>Search Our Services</h1>
            <div class="search-container">
                <form action="search.php" method="GET" class="search-form">
                    <input type="text" name="q" id="searchInput" placeholder="Search services..." value="<?php echo htmlspecialchars($search_query); ?>" required>
                    <button type="submit" class="search-button">
                        <i class="fas fa-search"></i>
                    </button>
                </form>
            </div>
        </section>

        <?php if (!empty($search_query)): ?>
            <section class="search-results">
                <h2>Search Results for "<?php echo htmlspecialchars($search_query); ?>"</h2>
                <?php if (!empty($results)): ?>
                    <div class="results-grid">
                        <?php foreach ($results as $result): ?>
                            <div class="result-card">
                                <h3><?php echo htmlspecialchars($result['name']); ?></h3>
                                <p class="category"><?php echo htmlspecialchars($result['category']); ?></p>
                                <p class="description"><?php echo htmlspecialchars($result['description']); ?></p>
                                <p class="price">$<?php echo number_format($result['price'], 2); ?></p>
                                <?php if(isset($_SESSION['user_id'])): ?>
                                    <a href="book-service.php?id=<?php echo $result['id']; ?>" class="btn btn-primary">Book Now</a>
                                <?php else: ?>
                                    <a href="login.php" class="btn btn-primary">Login to Book</a>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="no-results">No results found for your search.</p>
                <?php endif; ?>
            </section>
        <?php endif; ?>
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
        document.addEventListener('DOMContentLoaded', function() {
            const searchInput = document.getElementById('searchInput');
            const searchResults = document.getElementById('searchResults');
            let searchTimeout;

            // Function to escape HTML
            function escapeHtml(unsafe) {
                return unsafe
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            }

            // Function to highlight matching text
            function highlightText(text, searchTerm) {
                if (!searchTerm) return text;
                const regex = new RegExp(`(${searchTerm})`, 'gi');
                return text.replace(regex, '<span class="highlight">$1</span>');
            }

            // Function to display results
            function displayResults(results) {
                if (!Array.isArray(results) || results.length === 0) {
                    searchResults.innerHTML = '<div class="no-results">No results found</div>';
                    return;
                }

                searchResults.innerHTML = results.map(result => `
                    <div class="result-item">
                        <div class="result-title">${highlightText(escapeHtml(result.name), searchInput.value)}</div>
                        <div class="result-category">${escapeHtml(result.category)}</div>
                        <div class="result-description">${escapeHtml(result.description || '')}</div>
                    </div>
                `).join('');
            }

            // Function to perform search
            function performSearch(query) {
                if (query.length < 2) {
                    searchResults.innerHTML = '';
                    return;
                }

                fetch(`../includes/search.php?q=${encodeURIComponent(query)}`)
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.error) {
                            console.error('Search error:', data.error);
                            searchResults.innerHTML = '<div class="no-results">Error performing search</div>';
                        } else {
                            displayResults(data);
                        }
                    })
                    .catch(error => {
                        console.error('Search error:', error);
                        searchResults.innerHTML = '<div class="no-results">Error performing search</div>';
                    });
            }

            // Add event listener for search input
            searchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const query = this.value.trim();

                if (query.length < 2) {
                    searchResults.innerHTML = '';
                    return;
                }

                searchTimeout = setTimeout(() => {
                    performSearch(query);
                }, 300);
            });

            // Close search results when clicking outside
            document.addEventListener('click', function(e) {
                if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.style.display = 'none';
                } else {
                    searchResults.style.display = 'block';
                }
            });
        });
    </script>
</body>
</html> 