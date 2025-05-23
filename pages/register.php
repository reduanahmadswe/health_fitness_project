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
   
    $formData['username'] = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $formData['email'] = trim($_POST['email'] ?? '');
    $formData['full_name'] = trim($_POST['full_name'] ?? '');
    $formData['phone'] = trim($_POST['phone'] ?? '');

   
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


    if (empty($errors)) {
      
        $sql = "SELECT id FROM users WHERE username = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $formData['username']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors['username'] = "This username is already taken";
        }
        mysqli_stmt_close($stmt);


        $sql = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, "s", $formData['email']);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        
        if (mysqli_stmt_num_rows($stmt) > 0) {
            $errors['email'] = "This email is already registered";
        }
        mysqli_stmt_close($stmt);

       
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

    <link rel="stylesheet" href="../css/index.css">
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
        
        document.querySelector('.register-form').addEventListener('submit', function(e) {
            let isValid = true;
            const form = this;
            
           
            document.querySelectorAll('.form-control.error').forEach(el => {
                el.classList.remove('error');
            });
            document.querySelectorAll('.error-message').forEach(el => {
                el.remove();
            });
            
           
            const username = form.querySelector('#username');
            if (username.value.trim().length < 4) {
                showError(username, "Username must be at least 4 characters");
                isValid = false;
            }
            
            l
            const email = form.querySelector('#email');
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email.value.trim())) {
                showError(email, "Please enter a valid email address");
                isValid = false;
            }
            
           
            const fullName = form.querySelector('#full_name');
            if (fullName.value.trim() === '') {
                showError(fullName, "Full name is required");
                isValid = false;
            }
            
           
            const password = form.querySelector('#password');
            if (password.value.length < 6) {
                showError(password, "Password must be at least 6 characters");
                isValid = false;
            }
            
           
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