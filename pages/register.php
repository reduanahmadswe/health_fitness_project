<?php
session_start();
require_once '../includes/config.php';

$errors = [];
$success = '';
$formData = [
    'username' => '',
    'email' => '',
    'full_name' => '',
    'phone' => ''
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and validate inputs
    $formData['username'] = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $formData['email'] = trim($_POST['email'] ?? '');
    $formData['full_name'] = trim($_POST['full_name'] ?? '');
    $formData['phone'] = trim($_POST['phone'] ?? '');

    // Validate required fields
    if (empty($formData['username'])) {
        $errors['username'] = "Username is required";
    } elseif (strlen($formData['username']) < 4) {
        $errors['username'] = "Username must be at least 4 characters";
    }

    if (empty($formData['email'])) {
        $errors['email'] = "Email is required";
    } elseif (!filter_var($formData['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }

    if (empty($formData['full_name'])) {
        $errors['full_name'] = "Full name is required";
    }

    if (empty($password)) {
        $errors['password'] = "Password is required";
    } elseif (strlen($password) < 6) {
        $errors['password'] = "Password must be at least 6 characters";
    }

    if (empty($confirm_password)) {
        $errors['confirm_password'] = "Please confirm your password";
    } elseif ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match";
    }

    // Only proceed if no validation errors
    if (empty($errors)) {
        // Check if username exists
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $formData['username']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors['username'] = "This username is already taken";
        }
        mysqli_stmt_close($stmt);

        // Check if email exists
        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $formData['email']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors['email'] = "This email is already registered";
        }
        mysqli_stmt_close($stmt);

        // If still no errors, insert new user
        if (empty($errors)) {
            $sql = "INSERT INTO users (username, password, email, full_name, phone) VALUES (?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            mysqli_stmt_bind_param($stmt, "sssss", 
                $formData['username'], 
                $hashed_password, 
                $formData['email'], 
                $formData['full_name'], 
                $formData['phone']
            );
            
            if (mysqli_stmt_execute($stmt)) {
                $success = "Registration successful! You can now login.";
                // Clear form data
                $formData = [
                    'username' => '',
                    'email' => '',
                    'full_name' => '',
                    'phone' => ''
                ];
            } else {
                $errors['database'] = "Registration failed. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Health & Fitness Center</title>
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
            --success: #27ae60;
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
            margin: 2rem auto;
            padding: 0 2rem;
            min-height: calc(100vh - 200px);
        }
        
        .form-container {
            max-width: 600px;
            margin: 2rem auto;
            background: white;
            border-radius: 15px;
            padding: 2.5rem;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
        }
        
        .form-container h2 {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--primary);
            font-size: 2rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--dark);
        }
        
        .form-group label.required::after {
            content: ' *';
            color: var(--error);
        }
        
        .form-control {
            width: 100%;
            padding: 0.8rem 1rem;
            border: 1px solid var(--gray);
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s ease;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(79, 195, 161, 0.2);
        }
        
        .form-control.error {
            border-color: var(--error);
        }
        
        .error-message {
            color: var(--error);
            font-size: 0.85rem;
            margin-top: 0.3rem;
            display: block;
        }
        
        .btn {
            display: inline-block;
            padding: 0.8rem 2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
            font-size: 1rem;
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
            background-color: #3daa8a;
        }
        
        .btn-block {
            display: block;
            width: 100%;
        }
        
        .form-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--dark-gray);
        }
        
        .form-footer a {
            color: var(--primary);
            text-decoration: none;
            font-weight: 500;
        }
        
        .form-footer a:hover {
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
            background-color: rgba(39, 174, 96, 0.1);
            color: var(--success);
            border-left: 4px solid var(--success);
        }
        
        footer {
            background-color: var(--dark);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        @media (max-width: 768px) {
            .form-container {
                padding: 1.5rem;
            }
            
            .navbar {
                padding: 1rem;
            }
            
            .logo h1 {
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
            </div>
        </nav>
    </header>

    <main>
        <div class="form-container">
            <h2>Create Your Account</h2>
            
            <?php if(!empty($errors['database'])): ?>
                <div class="alert alert-error"><?php echo $errors['database']; ?></div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="register-form">
                <div class="form-group">
                    <label for="username" class="required">Username</label>
                    <input type="text" name="username" id="username" 
                           class="form-control <?php echo isset($errors['username']) ? 'error' : ''; ?>" 
                           value="<?php echo htmlspecialchars($formData['username']); ?>" required>
                    <?php if(isset($errors['username'])): ?>
                        <span class="error-message"><?php echo $errors['username']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="email" class="required">Email</label>
                    <input type="email" name="email" id="email" 
                           class="form-control <?php echo isset($errors['email']) ? 'error' : ''; ?>" 
                           value="<?php echo htmlspecialchars($formData['email']); ?>" required>
                    <?php if(isset($errors['email'])): ?>
                        <span class="error-message"><?php echo $errors['email']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="full_name" class="required">Full Name</label>
                    <input type="text" name="full_name" id="full_name" 
                           class="form-control <?php echo isset($errors['full_name']) ? 'error' : ''; ?>" 
                           value="<?php echo htmlspecialchars($formData['full_name']); ?>" required>
                    <?php if(isset($errors['full_name'])): ?>
                        <span class="error-message"><?php echo $errors['full_name']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" name="phone" id="phone" 
                           class="form-control" 
                           value="<?php echo htmlspecialchars($formData['phone']); ?>">
                </div>
                
                <div class="form-group">
                    <label for="password" class="required">Password</label>
                    <input type="password" name="password" id="password" 
                           class="form-control <?php echo isset($errors['password']) ? 'error' : ''; ?>" required>
                    <?php if(isset($errors['password'])): ?>
                        <span class="error-message"><?php echo $errors['password']; ?></span>
                    <?php endif; ?>
                    <small style="color: var(--dark-gray); display: block; margin-top: 0.3rem;">
                        Password must be at least 6 characters long
                    </small>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password" class="required">Confirm Password</label>
                    <input type="password" name="confirm_password" id="confirm_password" 
                           class="form-control <?php echo isset($errors['confirm_password']) ? 'error' : ''; ?>" required>
                    <?php if(isset($errors['confirm_password'])): ?>
                        <span class="error-message"><?php echo $errors['confirm_password']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary btn-block">Register</button>
                </div>
                
                <div class="form-footer">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> Health & Fitness Center. All rights reserved.</p>
    </footer>

    <script>
        // Client-side validation
        document.querySelector('.register-form').addEventListener('submit', function(e) {
            let isValid = true;
            const form = this;
            
            // Clear previous errors
            document.querySelectorAll('.form-control.error').forEach(el => {
                el.classList.remove('error');
            });
            document.querySelectorAll('.error-message').forEach(el => {
                el.remove();
            });
            
            // Validate username
            const username = form.querySelector('#username');
            if (username.value.trim().length < 4) {
                showError(username, "Username must be at least 4 characters");
                isValid = false;
            }
            
            // Validate email
            const email = form.querySelector('#email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value.trim())) {
                showError(email, "Please enter a valid email address");
                isValid = false;
            }
            
            // Validate full name
            const fullName = form.querySelector('#full_name');
            if (fullName.value.trim() === '') {
                showError(fullName, "Full name is required");
                isValid = false;
            }
            
            // Validate password
            const password = form.querySelector('#password');
            if (password.value.length < 6) {
                showError(password, "Password must be at least 6 characters");
                isValid = false;
            }
            
            // Validate confirm password
            const confirmPassword = form.querySelector('#confirm_password');
            if (confirmPassword.value !== password.value) {
                showError(confirmPassword, "Passwords do not match");
                isValid = false;
            }
            
            if (!isValid) {
                e.preventDefault();
            }
        });
        
        function showError(input, message) {
            input.classList.add('error');
            const errorElement = document.createElement('span');
            errorElement.className = 'error-message';
            errorElement.textContent = message;
            input.parentNode.appendChild(errorElement);
        }
    </script>
</body>
</html>