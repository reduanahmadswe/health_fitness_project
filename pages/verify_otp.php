<?php
session_start();
require_once '../includes/config.php';

// Check if user has requested password reset
if (!isset($_SESSION['reset_otp']) || !isset($_SESSION['reset_email']) || !isset($_SESSION['reset_time'])) {
    header("Location: forgot-password.php");
    exit;
}

// Check if OTP has expired (10 minutes)
if (time() - $_SESSION['reset_time'] > 600) {
    session_destroy();
    header("Location: forgot-password.php?error=expired");
    exit;
}

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $otp = trim($_POST['otp']);
    
    if (empty($otp)) {
        $error = "Please enter the OTP";
    } elseif (!is_numeric($otp) || strlen($otp) != 6) {
        $error = "Please enter a valid 6-digit OTP";
    } elseif ($otp != $_SESSION['reset_otp']) {
        $error = "Invalid OTP. Please try again.";
    } else {
        // OTP is valid, redirect to reset password page
        header("Location: reset_password.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - Health & Fitness Center</title>
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
        
        main {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
            min-height: calc(100vh - 200px);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .auth-container {
            width: 100%;
            max-width: 500px;
            background: white;
            border-radius: 15px;
            padding: 3rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            margin: 4rem 0;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .auth-container:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .auth-header h2 {
            font-size: 2rem;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }
        
        .auth-header p {
            color: var(--dark-gray);
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
            text-align: center;
            letter-spacing: 0.5rem;
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
            width: 100%;
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
        
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--dark-gray);
        }
        
        .auth-footer a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
        }
        
        .auth-footer a:hover {
            text-decoration: underline;
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
        
        .timer {
            text-align: center;
            color: var(--dark-gray);
            margin-bottom: 1rem;
            font-size: 0.9rem;
        }
        
        .resend-link {
            text-align: center;
            margin-top: 1rem;
        }
        
        .resend-link a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
        }
        
        .resend-link a:hover {
            text-decoration: underline;
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
        
        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
            }
            
            .auth-container {
                padding: 2rem;
                margin: 2rem 0;
            }
        }
        
        @media (max-width: 576px) {
            .auth-container {
                padding: 1.5rem;
            }
            
            .auth-header h2 {
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
            <div class="nav-links">
                <a href="../index.php" class="nav-item">Home</a>
                <a href="login.php" class="nav-item">Login</a>
                <a href="register.php" class="nav-item">Register</a>
            </div>
        </nav>
    </header>

    <main>
        <div class="auth-container">
            <div class="auth-header">
                <h2>Verify OTP</h2>
                <p>Enter the 6-digit code sent to your email</p>
            </div>
            
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
            
            <div class="timer">
                Time remaining: <span id="countdown">10:00</span>
            </div>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="form-group">
                    <label for="otp">Enter OTP</label>
                    <input type="text" name="otp" id="otp" class="form-control" required 
                           maxlength="6" pattern="\d{6}" inputmode="numeric"
                           value="<?php echo isset($_POST['otp']) ? htmlspecialchars($_POST['otp']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Verify OTP</button>
                </div>
                
                <div class="resend-link">
                    <a href="forgot-password.php">Resend OTP</a>
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
            <p>&copy; 2024 Health & Fitness Center. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Countdown timer
        function startTimer(duration, display) {
            let timer = duration, minutes, seconds;
            const countdown = setInterval(function () {
                minutes = parseInt(timer / 60, 10);
                seconds = parseInt(timer % 60, 10);

                minutes = minutes < 10 ? "0" + minutes : minutes;
                seconds = seconds < 10 ? "0" + seconds : seconds;

                display.textContent = minutes + ":" + seconds;

                if (--timer < 0) {
                    clearInterval(countdown);
                    display.textContent = "00:00";
                    window.location.href = "forgot-password.php?error=expired";
                }
            }, 1000);
        }

        window.onload = function () {
            const tenMinutes = 60 * 10,
                display = document.querySelector('#countdown');
            startTimer(tenMinutes, display);
        };

        // OTP input formatting
        document.getElementById('otp').addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    </script>
</body>
</html> 