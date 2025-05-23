<?php
session_start();
require_once '../includes/config.php';


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = trim($_POST['current_password']);
    $new_password = trim($_POST['new_password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    
    $sql = "SELECT password FROM users WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $hashed_password);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    
    if (!password_verify($current_password, $hashed_password)) {
        $error = "Current password is incorrect";
    } elseif (empty($new_password)) {
        $error = "Please enter a new password";
    } elseif (strlen($new_password) < 8) {
        $error = "Password must be at least 8 characters long";
    } elseif ($new_password !== $confirm_password) {
        $error = "Passwords do not match";
    }
    
    
    if (empty($error)) {
        $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "si", $new_hashed_password, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            $success = "Password changed successfully!";
        } else {
            $error = "Something went wrong. Please try again later.";
        }
        mysqli_stmt_close($stmt);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - Health & Fitness Center</title>
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
            --error: #e74c3c;
            --success: #38a169;
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
        
        .hero {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('../images/TransformYourLifeTheIncrediblePowerofFitness.jpg') no-repeat center center/cover;
            height: 40vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            color: white;
            border-radius: 0 0 20px 20px;
            margin-bottom: 4rem;
        }
        
        .hero-content {
            max-width: 800px;
            padding: 2rem;
        }
        
        .hero h1 {
            font-size: 3rem;
            margin-bottom: 1rem;
            animation: fadeInDown 1s ease;
        }
        
        .hero p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
            animation: fadeInUp 1s ease;
        }
        
        main {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        .password-form-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            padding: 2rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin-bottom: 4rem;
            transition: transform 0.3s ease;
        }
        
        .password-form-container:hover {
            transform: translateY(-5px);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--dark);
            font-weight: 500;
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--gray);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
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
        }
        
        .btn-outline {
            background: transparent;
            border: 2px solid var(--accent);
            color: var(--accent);
            margin-left: 1rem;
        }
        
        .btn-outline:hover {
            background: var(--accent);
            color: white;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        
        .alert-error {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--error);
            border-left: 4px solid var(--error);
        }
        
        .alert-success {
            background-color: rgba(56, 161, 105, 0.1);
            color: var(--success);
            border-left: 4px solid var(--success);
        }
        
        .password-container {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: var(--dark-gray);
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
        
        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
            }
            
            .hero h1 {
                font-size: 2rem;
            }
            
            .password-form-container {
                padding: 1.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .hero h1 {
                font-size: 1.8rem;
            }
            
            .btn {
                width: 100%;
                margin: 0.5rem 0;
            }
            
            .btn-outline {
                margin-left: 0;
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
        <section class="hero">
            <div class="hero-content">
                <h1>Change Password</h1>
                <p>Update your account security settings</p>
            </div>
        </section>

        <div class="password-form-container">
            <?php if(!empty($error)): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if(!empty($success)): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i> <?php echo $success; ?>
                </div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <div class="password-container">
                        <input type="password" name="current_password" id="current_password" class="form-control" required>
                        <i class="fas fa-eye password-toggle" id="toggleCurrentPassword"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <div class="password-container">
                        <input type="password" name="new_password" id="new_password" class="form-control" required>
                        <i class="fas fa-eye password-toggle" id="toggleNewPassword"></i>
                    </div>
                    <small style="color: var(--dark-gray);">Must be at least 8 characters long</small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <div class="password-container">
                        <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
                        <i class="fas fa-eye password-toggle" id="toggleConfirmPassword"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Change Password</button>
                    <a href="profile.php" class="btn btn-outline">Cancel</a>
                </div>
            </form>
        </div>
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

    <script>
        
        const toggleCurrentPassword = document.querySelector('#toggleCurrentPassword');
        const toggleNewPassword = document.querySelector('#toggleNewPassword');
        const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
        
        const currentPassword = document.querySelector('#current_password');
        const newPassword = document.querySelector('#new_password');
        const confirmPassword = document.querySelector('#confirm_password');
        
        toggleCurrentPassword.addEventListener('click', function() {
            const type = currentPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            currentPassword.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
        
        toggleNewPassword.addEventListener('click', function() {
            const type = newPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            newPassword.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
        
        toggleConfirmPassword.addEventListener('click', function() {
            const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPassword.setAttribute('type', type);
            this.classList.toggle('fa-eye-slash');
        });
        
        
        document.querySelector('form').addEventListener('submit', function(e) {
            const current = currentPassword.value.trim();
            const newPass = newPassword.value.trim();
            const confirmPass = confirmPassword.value.trim();
            
            if (!current) {
                e.preventDefault();
                alert('Please enter your current password');
                return;
            }
            
            if (!newPass) {
                e.preventDefault();
                alert('Please enter a new password');
                return;
            }
            
            if (newPass.length < 8) {
                e.preventDefault();
                alert('Password must be at least 8 characters long');
                return;
            }
            
            if (newPass !== confirmPass) {
                e.preventDefault();
                alert('Passwords do not match');
                return;
            }
        });
    </script>
</body>
</html>