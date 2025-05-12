<?php

session_start();
date_default_timezone_set('Asia/Bahrain');
require_once './Models/Booking.php';

$std = new stdClass();
$booking = new Booking();

// Get the current user ID from session
$customer_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

if ($customer_id === 0) {
    header("Location: /Borrow_My_Charger/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $last_check_time = isset($data['last_check_time']) ? $data['last_check_time'] : date('Y-m-d H:i:s', strtotime('-1 hour'));

    try {
        // Get any booking status updates since the last check time
        $updates = $booking->getBookingStatusUpdates($customer_id, $last_check_time);

        // Return the updates and current time
        echo json_encode([
            'success' => true,
            'updates' => $updates,
            'current_time' => date('Y-m-d H:i:s')
        ]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }

    exit;    
}    

$std->bookingRequests = $booking->getBookingsByCustomer($customer_id);
    require_once './Views/headers/users_header.phtml';

require_once './Views/user_dashboard.phtml';