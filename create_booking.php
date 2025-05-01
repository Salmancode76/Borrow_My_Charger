<?php
// Initialize session if needed
session_start();
require_once 'Models/Booking.php';

// Set the content type
header('Content-Type: application/json');

// Get the current user ID from session
$customer_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 6;

// If no user is logged in, return error
if ($customer_id === 0) {
    echo json_encode([
        'success' => false,
        'message' => 'You must be logged in to book a charging session'
    ]);
    exit;
}

// Get the request body
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validate input
$charger_id = isset($data['charger_id']) ? intval($data['charger_id']) : 0;
$booking_datetime = isset($data['booking_datetime']) ? $data['booking_datetime'] : '';

if ($charger_id <= 0 || empty($booking_datetime)) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid booking data'
    ]);
    exit;
}

// Check if the booking time is in the future
$booking_time = strtotime($booking_datetime);
$current_time = time();

if ($booking_time <= $current_time) {
    echo json_encode([
        'success' => false,
        'message' => 'Booking time must be in the future'
    ]);
    exit;
}

// Initialize booking model
$booking = new Booking();

// Check availability first
$is_available = $booking->checkAvailability($charger_id, $booking_datetime);

if (!$is_available) {
    echo json_encode([
        'success' => false,
        'message' => 'This charging point is already booked for the selected time'
    ]);
    exit;
}

// Create the booking
try {
    $booking_data = [
        'customer_id' => $customer_id,
        'charge_id' => $charger_id,
        'date' => $booking_datetime
    ];
    
    $booking_id = $booking->createBooking($booking_data);
    
    if ($booking_id) {
        echo json_encode([
            'success' => true,
            'booking_id' => $booking_id,
            'message' => 'Booking created successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to create booking'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}