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
                <a href="../index.php">Home</a>
                <a href="services.php">Services</a>
                <a href="classes.php">Classes</a>
                <a href="trainers.php">Trainers</a>
                <a href="profile.php">Profile</a>
                <a href="bookings.php">My Bookings</a>
                <a href="logout.php">Logout</a>
            </div>
        </nav>
    </header>

    <main>
        <div class="form-container fade-in">
            <h2>Book Service</h2>
            
            <div class="service-details">
                <h3><?php echo htmlspecialchars($service['name']); ?></h3>
                <p><?php echo htmlspecialchars($service['description']); ?></p>
                <p class="price">Price: $<?php echo number_format($service['price'], 2); ?></p>
            </div>

            <?php if(!empty($error)): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if(!empty($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"] . "?id=" . $service_id); ?>" method="post" class="booking-form">
                <div class="form-group">
                    <label for="booking_date">Select Date *</label>
                    <input type="date" name="booking_date" id="booking_date" class="form-control" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="booking_time">Select Time *</label>
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
            <p>&copy; 2024 Health & Fitness Center. All rights reserved.</p>
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