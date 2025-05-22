<?php
session_start();
require_once '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("location: login.php");
    exit;
}

// Check if service ID is provided
if (!isset($_GET['id'])) {
    header("location: services.php");
    exit;
}

$service_id = $_GET['id'];
$error = '';
$success = '';

// Fetch service details
$sql = "SELECT * FROM services WHERE id = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, "i", $service_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $service = mysqli_fetch_assoc($result);
    
    if (!$service) {
        header("location: services.php");
        exit;
    }
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $booking_date = trim($_POST['booking_date']);
    $booking_time = trim($_POST['booking_time']);
    
    // Validate date and time
    $current_date = date('Y-m-d');
    if ($booking_date < $current_date) {
        $error = "Please select a future date";
    } else {
        // Check if the time slot is available
        $sql = "SELECT id FROM bookings WHERE service_id = ? AND booking_date = ? AND booking_time = ? AND status != 'cancelled'";
        if ($stmt = mysqli_prepare($conn, $sql)) {
            mysqli_stmt_bind_param($stmt, "iss", $service_id, $booking_date, $booking_time);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_store_result($stmt);
            
            if (mysqli_stmt_num_rows($stmt) > 0) {
                $error = "This time slot is already booked. Please choose another time.";
            } else {
                // Create booking
                $sql = "INSERT INTO bookings (user_id, service_id, booking_date, booking_time) VALUES (?, ?, ?, ?)";
                if ($stmt = mysqli_prepare($conn, $sql)) {
                    mysqli_stmt_bind_param($stmt, "iiss", $_SESSION['user_id'], $service_id, $booking_date, $booking_time);
                    
                    if (mysqli_stmt_execute($stmt)) {
                        $success = "Booking successful! You will receive a confirmation email shortly.";
                    } else {
                        $error = "Something went wrong. Please try again later.";
                    }
                }
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
    <title>Book Service - Health & Fitness Center</title>
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
        
        main {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .booking-container {
            width: 100%;
            max-width: 800px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            padding: 2.5rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            animation: fadeIn 0.5s ease;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .booking-header {
            grid-column: 1 / -1;
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .booking-header h2 {
            font-size: 2rem;
            color: var(--primary);
            margin-bottom: 0.5rem;
        }
        
        .service-details {
            background: var(--light);
            padding: 1.5rem;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.03);
        }
        
        .service-details h3 {
            font-size: 1.5rem;
            color: var(--dark);
            margin-bottom: 1rem;
        }
        
        .service-details p {
            color: var(--dark-gray);
            margin-bottom: 1rem;
            line-height: 1.6;
        }
        
        .service-price {
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--primary);
            margin-top: 1rem;
        }
        
        .booking-form {
            display: flex;
            flex-direction: column;
        }
        
        .form-group {
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
        
        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%232d3748' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 16px 12px;
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
        
        .btn-primary {
            background-color: var(--accent);
            color: white;
            box-shadow: 0 4px 15px rgba(74, 111, 165, 0.4);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(74, 111, 165, 0.6);
        }
        
        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1.5rem;
            font-weight: 500;
            grid-column: 1 / -1;
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
            .booking-container {
                grid-template-columns: 1fr;
                padding: 1.5rem;
            }
            
            .navbar {
                padding: 1rem;
            }
        }
        
        @media (max-width: 576px) {
            .booking-container {
                padding: 1.25rem;
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
                <a href="services.php" class="nav-item">Services</a>
                <a href="classes.php" class="nav-item">Classes</a>
                <a href="trainers.php" class="nav-item">Trainers</a>
                <a href="profile.php" class="nav-item">Profile</a>
                <a href="bookings.php" class="nav-item">My Bookings</a>
                <a href="logout.php" class="nav-item">Logout</a>
            </div>
        </nav>
    </header>

    <main>
        <div class="booking-container">
            <div class="booking-header">
                <h2>Book Service</h2>
            </div>
            
            <?php if(!empty($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if(!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <div class="service-details">
                <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                <p><?php echo htmlspecialchars($service['description']); ?></p>
                <p class="service-price">$<?php echo number_format($service['price'], 2); ?></p>
            </div>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $service_id); ?>" method="post" class="booking-form">
                <div class="form-group">
                    <label for="booking_date" class="form-label">Select Date</label>
                    <input type="date" name="booking_date" id="booking_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="booking_time" class="form-label">Select Time</label>
                    <select name="booking_time" id="booking_time" class="form-control" required>
                        <option value="">Choose a time</option>
                        <?php
                        // Generate time slots from 8 AM to 8 PM
                        $start = strtotime('08:00');
                        $end = strtotime('20:00');
                        for ($time = $start; $time <= $end; $time += 3600) {
                            $time_slot = date('H:i', $time);
                            echo "<option value='$time_slot'>$time_slot</option>";
                        }
                        ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Confirm Booking</button>
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
        // Form validation
        document.querySelector('.booking-form').addEventListener('submit', function(e) {
            const bookingDate = document.getElementById('booking_date').value;
            const bookingTime = document.getElementById('booking_time').value;
            
            if (!bookingDate || !bookingTime) {
                e.preventDefault();
                alert('Please select both date and time');
                return;
            }
            
            // Check if date is in the future
            const selectedDate = new Date(bookingDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            if (selectedDate < today) {
                e.preventDefault();
                alert('Please select a future date');
                return;
            }
        });
    </script>
</body>
</html>