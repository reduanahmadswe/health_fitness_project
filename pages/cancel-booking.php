<?php
session_start();
require_once '../includes/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Check if booking ID is provided via POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['booking_id']) || empty($_POST['booking_id'])) {
    $_SESSION['error'] = "Invalid request";
    header('Location: bookings.php');
    exit;
}

$booking_id = $_POST['booking_id'];
$user_id = $_SESSION['user_id'];

// Verify the booking belongs to the user and is pending
$sql = "SELECT id FROM bookings WHERE id = ? AND user_id = ? AND status = 'pending'";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "ii", $booking_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    $_SESSION['error'] = "Booking not found or cannot be cancelled";
    header('Location: bookings.php');
    exit;
}


$sql = "UPDATE bookings SET status = 'cancelled' WHERE id = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $booking_id);
mysqli_stmt_execute($stmt);

$_SESSION['success'] = "Booking cancelled successfully";
header('Location: bookings.php');
exit;
?>