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
                                .header { background-color: #4CAF50; padding: 10px; color: white; text-align: center; }
                                .content { padding: 20px; background-color: #f9f9f9; }
                                .button { display: inline-block; padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 4px; }
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
    <link rel="stylesheet" href="../css/style.css">
    <style>
        .forgot-password-container {
            max-width: 500px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .forgot-password-form {
            margin-top: 1.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }
        
        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1rem;
        }
        
        .form-control:focus {
            outline: none;
            border-color: #4CAF50;
            box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
        }
        
        .btn-primary {
            background-color: #4CAF50;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }
        
        .btn-primary:hover {
            background-color: #3e8e41;
        }
        
        .text-center {
            text-align: center;
        }
        
        .small {
            font-size: 0.875rem;
            color: #666;
            margin-top: 0.25rem;
            display: block;
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
                <a href="../index.php">Home</a>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            </div>
        </nav>
    </header>

    <main class="main-content">
        <div class="forgot-password-container fade-in">
            <h2 class="text-center">Forgot Password</h2>
            <p class="text-center">Enter your email to receive a password reset link</p>
            
            <?php if(!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <?php if(!empty($success)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="forgot-password-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" required 
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    <small class="small">Enter the email address associated with your account</small>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn-primary btn-block">Send Reset Link</button>
                </div>
                
                <p class="text-center">Remember your password? <a href="login.php">Login here</a></p>
            </form>
        </div>
    </main>

    <footer class="footer">
        <div class="footer-content">
            <p>&copy; <?php echo date('Y'); ?> Health & Fitness Center. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Enhanced form validation
        document.querySelector('.forgot-password-form').addEventListener('submit', function(e) {
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