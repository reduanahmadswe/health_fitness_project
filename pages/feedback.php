<?php
session_start();
require_once '../includes/config.php';

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = $_POST['rating'] ?? 0;
    $message = $_POST['message'] ?? '';
    $user_id = $_SESSION['user_id'] ?? null;

    if (empty($message)) {
        $error_message = "Please enter your feedback message.";
    } elseif ($rating < 1 || $rating > 5) {
        $error_message = "Please select a valid rating.";
    } else {
        $sql = "INSERT INTO feedback (user_id, rating, message, created_at) VALUES (?, ?, ?, NOW())";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "iis", $user_id, $rating, $message);
        
        if (mysqli_stmt_execute($stmt)) {
            $success_message = "Thank you for your feedback!";
            $message = '';
            $rating = 0;
        } else {
            $error_message = "Error submitting feedback. Please try again.";
        }
    }
}


$previous_feedback = [];

if (isset($_SESSION['user_id'])) {
    $sql = "SELECT * FROM feedback WHERE user_id = ? ORDER BY created_at DESC";
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $_SESSION['user_id']);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if ($result) {
            $previous_feedback = mysqli_fetch_all($result, MYSQLI_ASSOC);
        }
    }
}



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
            --success: #48bb78;
            --error: #f56565;
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
        
        .feedback-hero {
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('../images/feedback-bg.jpg') no-repeat center center/cover;
            height: 60vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            border-radius: 0 0 20px 20px;
            margin-bottom: 3rem;
        }
        
        .feedback-hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            animation: fadeInDown 1s ease;
        }
        
        .feedback-hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem auto;
            max-width: 800px;
            text-align: center;
            font-weight: 500;
        }
        
        .alert-error {
            background-color: rgba(245, 101, 101, 0.2);
            border-left: 4px solid var(--error);
            color: var(--error);
        }
        
        .alert-success {
            background-color: rgba(72, 187, 120, 0.2);
            border-left: 4px solid var(--success);
            color: var(--success);
        }
        
        .feedback-container {
            max-width: 800px;
            margin: 0 auto 4rem;
        }
        
        .feedback-form {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 3rem;
        }
        
        .rating-container {
            margin-bottom: 2rem;
        }
        
        .rating-container label {
            display: block;
            margin-bottom: 1rem;
            font-weight: 500;
            color: var(--dark);
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
            font-size: 2rem;
            color: var(--gray);
            transition: all 0.2s ease;
            margin-bottom: 0;
        }
        
        .star-rating label:hover,
        .star-rating label:hover ~ label,
        .star-rating input:checked ~ label {
            color: #ffc107;
        }
        
        .form-group {
            margin-bottom: 2rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.75rem;
            font-weight: 500;
            color: var(--dark);
        }
        
        .form-group textarea {
            width: 100%;
            padding: 1rem;
            border: 1px solid var(--gray);
            border-radius: 8px;
            resize: vertical;
            min-height: 150px;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.3s ease;
        }
        
        .form-group textarea:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(79, 195, 161, 0.2);
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
        
        .previous-feedback {
            margin-top: 3rem;
        }
        
        .previous-feedback h2 {
            font-size: 1.8rem;
            color: var(--secondary);
            margin-bottom: 1.5rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
        
        .previous-feedback h2::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 80px;
            height: 3px;
            background-color: var(--accent);
        }
        
        .feedback-list {
            display: grid;
            gap: 1.5rem;
        }
        
        .feedback-card {
            background: white;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
        }
        
        .feedback-card:hover {
            transform: translateY(-5px);
        }
        
        .feedback-rating {
            margin-bottom: 1rem;
        }
        
        .feedback-rating i {
            font-size: 1.2rem;
            color: var(--gray);
            margin-right: 0.3rem;
        }
        
        .feedback-rating i.active {
            color: #ffc107;
        }
        
        .feedback-message {
            color: var(--dark-gray);
            margin-bottom: 1rem;
            line-height: 1.7;
        }
        
        .feedback-date {
            color: var(--dark-gray);
            font-size: 0.9rem;
            font-style: italic;
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
            
            .feedback-hero h1 {
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
            
            .feedback-hero {
                height: 35vh;
            }
            
            .feedback-hero h1 {
                font-size: 2rem;
            }
            
            .feedback-hero p {
                font-size: 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .logo h1 {
                font-size: 1.5rem;
            }
            
            .feedback-hero {
                height: 30vh;
            }
            
            .previous-feedback h2 {
                font-size: 1.5rem;
            }
            
            .star-rating label {
                font-size: 1.5rem;
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

        <?php

$rating = isset($_POST['rating']) ? $_POST['rating'] : '';
$message = isset($_POST['message']) ? $_POST['message'] : '';


?>
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
                <textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($message); ?></textarea>
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
            <p>&copy; 2025 Health & Fitness Center. All rights reserved.</p>
        </div>
    </footer>

    <script src="../js/feedback.js"></script>
</body>
</html>