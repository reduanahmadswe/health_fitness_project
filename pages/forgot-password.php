<?php
session_start();
require_once '../includes/config.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = "Please enter your email address";
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
                        $reset_link = "http://" . $_SERVER['HTTP_HOST'] . "/health_fitness_project/pages/reset-password.php?token=" . $token;
                        $to = $email;
                        $subject = "Password Reset Request";
                        $message = "Hello " . $username . ",\n\n";
                        $message .= "You have requested to reset your password. Click the link below to reset your password:\n\n";
                        $message .= $reset_link . "\n\n";
                        $message .= "This link will expire in 1 hour.\n\n";
                        $message .= "If you did not request this, please ignore this email.\n\n";
                        $message .= "Best regards,\nHealth & Fitness Center";
                        $headers = "From: noreply@healthfitness.com";
                        
                        if (mail($to, $subject, $message, $headers)) {
                            $success = "Password reset instructions have been sent to your email";
                        } else {
                            $error = "Failed to send reset email. Please try again later.";
                        }
                    } else {
                        $error = "Something went wrong. Please try again later.";
                    }
                }
            } else {
                $error = "No account found with that email address";
            }
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

    <main>
        <div class="form-container fade-in">
            <h2>Forgot Password</h2>
            
            <?php if(!empty($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if(!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="forgot-password-form">
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" name="email" id="email" class="form-control" required>
                    <small>Enter the email address associated with your account</small>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Send Reset Link</button>
                </div>
                
                <p>Remember your password? <a href="login.php">Login here</a></p>
            </form>
        </div>
    </main>

    <footer>
        <div class="footer-content">
            <p>&copy; 2024 Health & Fitness Center. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Form validation
        document.querySelector('.forgot-password-form').addEventListener('submit', function(e) {
            const email = document.getElementById('email').value.trim();
            
            if (!email) {
                e.preventDefault();
                alert('Please enter your email address');
                return;
            }
            
            // Email validation
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                e.preventDefault();
                alert('Please enter a valid email address');
                return;
            }
        });
    </script>
</body>
</html> 