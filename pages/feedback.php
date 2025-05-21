<?php
session_start();
require_once '../includes/config.php';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $rating = $_POST['rating'] ?? 0;
    $message = $_POST['message'] ?? '';
    $user_id = $_SESSION['user_id'] ?? null;

    if (empty($message)) {
        $error_message = "Please enter your feedback message.";
    } elseif ($rating < 1 || $rating > 5) {
        $error_message = "Please select a valid rating.";
    } else {
        $sql = "INSERT INTO feedback (user_id, name, email, rating, message) VALUES (?, ?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "issis", $user_id, $name, $email, $rating, $message);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Thank you for your feedback!";
            // Clear form data
            $name = $email = $message = '';
            $rating = 0;
        } else {
            $error_message = "Error submitting feedback. Please try again.";
        }
    }
}

// Fetch user's previous feedback if logged in
$previous_feedback = [];
if (isset($_SESSION['user_id'])) {
    $sql = "SELECT * FROM feedback WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $previous_feedback = mysqli_fetch_all($result, MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback - Health & Fitness Center</title>
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
        <section class="feedback-hero">
            <h1>Feedback & Suggestions</h1>
            <p>Help us improve by sharing your experience</p>
        </section>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <section class="feedback-section">
            <div class="feedback-container">
                <form id="feedbackForm" method="POST" class="feedback-form">
                    <div class="rating-container">
                        <label>Your Rating:</label>
                        <div class="star-rating">
                            <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input type="radio" id="star<?php echo $i; ?>" name="rating" value="<?php echo $i; ?>" <?php echo ($rating == $i) ? 'checked' : ''; ?>>
                                <label for="star<?php echo $i; ?>"><i class="fas fa-star"></i></label>
                            <?php endfor; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="message">Your Feedback:</label>
                        <textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                    </div>

                    <button type="submit" class="btn btn-primary">Submit Feedback</button>
                </form>

                <?php if (!empty($previous_feedback)): ?>
                    <div class="previous-feedback">
                        <h2>Your Previous Feedback</h2>
                        <div class="feedback-list">
                            <?php foreach ($previous_feedback as $feedback): ?>
                                <div class="feedback-card">
                                    <div class="feedback-rating">
                                        <?php for ($i = 1; $i <= 5; $i++): ?>
                                            <i class="fas fa-star <?php echo $i <= $feedback['rating'] ? 'active' : ''; ?>"></i>
                                        <?php endfor; ?>
                                    </div>
                                    <p class="feedback-message"><?php echo htmlspecialchars($feedback['message']); ?></p>
                                    <p class="feedback-date"><?php echo date('F j, Y', strtotime($feedback['created_at'])); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
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
        .feedback-hero {
            text-align: center;
            padding: 4rem 2rem;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('../images/feedback-bg.jpg');
            background-size: cover;
            background-position: center;
            color: white;
        }

        .feedback-hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .feedback-container {
            max-width: 800px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .feedback-form {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .rating-container {
            margin-bottom: 1.5rem;
        }

        .star-rating {
            display: flex;
            flex-direction: row-reverse;
            justify-content: flex-end;
            gap: 0.5rem;
        }

        .star-rating input {
            display: none;
        }

        .star-rating label {
            cursor: pointer;
            font-size: 1.5rem;
            color: #ddd;
            transition: color 0.2s;
        }

        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: #ffd700;
        }

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .form-group textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            resize: vertical;
        }

        .previous-feedback {
            margin-top: 3rem;
        }

        .previous-feedback h2 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
        }

        .feedback-card {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            margin-bottom: 1rem;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .feedback-rating {
            margin-bottom: 1rem;
        }

        .feedback-rating i {
            color: #ddd;
            margin-right: 0.25rem;
        }

        .feedback-rating i.active {
            color: #ffd700;
        }

        .feedback-message {
            color: #666;
            margin-bottom: 1rem;
            line-height: 1.6;
        }

        .feedback-date {
            color: #999;
            font-size: 0.9rem;
        }

        /* Add search styles */
        .search-container {
            position: relative;
            margin: 0 1rem;
            display: inline-block;
        }

        .search-input {
            padding: 0.5rem 1rem;
            border: 1px solid #ddd;
            border-radius: 20px;
            width: 200px;
            transition: width 0.3s ease;
        }

        .search-input:focus {
            width: 300px;
            outline: none;
            border-color: #3498db;
        }

        .search-results {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border-radius: 4px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            z-index: 1000;
            max-height: 300px;
            overflow-y: auto;
        }

        .search-result-item {
            display: block;
            padding: 0.75rem 1rem;
            text-decoration: none;
            color: #333;
            border-bottom: 1px solid #eee;
        }

        .search-result-item:hover {
            background: #f8f9fa;
        }

        .result-title {
            font-weight: bold;
            margin-bottom: 0.25rem;
        }

        .result-category {
            font-size: 0.9rem;
            color: #666;
        }

        .no-results {
            padding: 1rem;
            text-align: center;
            color: #666;
        }
    </style>

    <!-- Add search script -->
    <script src="../js/search.js"></script>
    <script>
        document.getElementById('feedbackForm').addEventListener('submit', function(e) {
            const rating = document.querySelector('input[name="rating"]:checked');
            const message = document.getElementById('message').value.trim();

            if (!rating) {
                e.preventDefault();
                alert('Please select a rating.');
                return;
            }

            if (!message) {
                e.preventDefault();
                alert('Please enter your feedback message.');
                return;
            }
        });
    </script>
</body>
</html> 