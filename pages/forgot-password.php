<?php
session_start();
require_once '../includes/config.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = "Please enter your email address";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address";
    } else {
        // Check if email exists
        $sql = "SELECT id, username FROM users WHERE email = ?";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "s", $email);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) == 1) {
                mysqli_stmt_bind_result($stmt, $id, $username);
                mysqli_stmt_fetch($stmt);
                
                // Generate reset token
                $token = bin2hex(random_bytes(32));
                $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
                
                // Store token in database
                $sql = "UPDATE users SET reset_token = ?, reset_expiry = ? WHERE id = ?";
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "ssi", $token, $expiry, $id);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        // Send reset email
                        $reset_link = "https://" . $_SERVER['HTTP_HOST'] . "/health_fitness_project/pages/reset-password.php?token=" . $token;
                        $to = $email;
                        $subject = "Password Reset Request - Health & Fitness Center";
                        
                        // HTML email content
                        $message = '
                        <html>
                        <head>
                            <style>
                                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                                .header { background-color: #4a6fa5; padding: 10px; color: white; text-align: center; }
                                .content { padding: 20px; background-color: #f9f9f9; }
                                .button { display: inline-block; padding: 10px 20px; background-color: #4a6fa5; color: white; text-decoration: none; border-radius: 4px; }
                                .footer { margin-top: 20px; padding: 10px; text-align: center; font-size: 12px; color: #777; }
                            </style>
                        </head>
                        <body>
                            <div class="container">
                                <div class="header">
                                    <h2>Health & Fitness Center</h2>
                                </div>
                                <div class="content">
                                    <p>Hello ' . htmlspecialchars($username) . ',</p>
                                    <p>You have requested to reset your password. Click the button below to reset your password:</p>
                                    <p><a href="' . $reset_link . '" class="button">Reset Password</a></p>
                                    <p>Or copy and paste this link into your browser:<br>' . $reset_link . '</p>
                                    <p>This link will expire in 1 hour.</p>
                                    <p>If you did not request this, please ignore this email.</p>
                                </div>
                                <div class="footer">
                                    <p>Best regards,<br>Health & Fitness Center Team</p>
                                </div>
                            </div>
                        </body>
                        </html>
                        ';
                        
                        $headers = "MIME-Version: 1.0" . "\r\n";
                        $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
                        $headers .= "From: Health & Fitness Center <noreply@healthfitness.com>" . "\r\n";
                        $headers .= "Reply-To: support@healthfitness.com" . "\r\n";
                        
                        if (mail($to, $subject, $message, $headers)) {
                            $success = "Password reset instructions have been sent to your email. Please check your inbox (and spam folder).";
                        } else {
                            $error = "Failed to send reset email. Please try again later.";
                            // Log the error for debugging
                            error_log("Failed to send password reset email to: " . $email);
                        }
                    } else {
                        $error = "Something went wrong. Please try again later.";
                        error_log("Database error when updating reset token: " . mysqli_error($conn));
                    }
                } else {
                    $error = "Database error. Please try again later.";
                    error_log("Database prepare statement error: " . mysqli_error($conn));
                }
            } else {
                // Don't reveal whether email exists or not for security
                $success = "If an account exists with this email, a password reset link has been sent.";
            }
        } else {
            $error = "Database error. Please try again later.";
            error_log("Database prepare statement error: " . mysqli_error($conn));
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - Health & Fitness Center</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary: #4a6fa5;
            --secondary: #166088;
            --accent: #4fc3a1;
            --dark: #2d3748;
            --light: #f8f9fa;
            --gray: #e2e8f0;
            --dark-gray: #a0aec0;
            --danger: #e53e3e;
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
            min-height: 100vh;
            display: flex;
            flex-direction: column;
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
        
        .hamburger {
            display: none;
            cursor: pointer;
            font-size: 1.5rem;
        }
        
        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .auth-container {
            width: 100%;
            max-width: 500px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 2.5rem;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .auth-header h2 {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .auth-header p {
            color: var(--dark-gray);
        }
        
        .auth-form .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid var(--gray);
            border-radius: 8px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(74, 111, 165, 0.2);
        }
        
        .btn {
            display: inline-block;
            padding: 0.8rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            cursor: pointer;
            text-align: center;
            border: none;
            font-size: 1rem;
        }
        
        .btn-block {
            display: block;
            width: 100%;
        }
        
        .btn-primary {
            background-color: var(--primary);
            color: white;
            box-shadow: 0 4px 15px rgba(74, 111, 165, 0.4);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 111, 165, 0.6);
        }
        
        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--dark-gray);
        }
        
        .auth-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .auth-footer a:hover {
            color: var(--secondary);
            text-decoration: underline;
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
        }
        
        .alert-error {
            background-color: rgba(229, 62, 62, 0.1);
            color: var(--danger);
            border-left: 4px solid var(--danger);
        }
        
        .alert-success {
            background-color: rgba(56, 161, 105, 0.1);
            color: var(--success);
            border-left: 4px solid var(--success);
        }
        
        footer {
            background-color: var(--dark);
            color: white;
            padding: 2rem 0;
            text-align: center;
        }
        
        .footer-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 2rem;
        }
        
        @media (max-width: 768px) {
            .navbar {
                padding: 1rem;
            }
            
            .auth-container {
                padding: 1.5rem;
            }
            
            .auth-header h2 {
                font-size: 1.5rem;
            }
        }
        
        @media (max-width: 576px) {
            .logo h1 {
                font-size: 1.5rem;
            }
            
            .auth-container {
                padding: 1.25rem;
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
                <h2>Forgot Password?</h2>
                <p>Enter your email to receive a password reset link</p>
            </div>
            
            <?php if(!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if(!empty($success)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="auth-form">
                <div class="form-group">
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Send Reset Link</button>
                </div>
                
                <div class="auth-footer">
                    Remember your password? <a href="login.php">Login here</a>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> Health & Fitness Center. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Enhanced form validation
        document.querySelector('.auth-form').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (!email) {
                e.preventDefault();
                alert('Please enter your email address');
                document.getElementById('email').focus();
                return;
            }
            
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address');
                document.getElementById('email').focus();
                return;
            }
        });
    </script>
</body>
</html>