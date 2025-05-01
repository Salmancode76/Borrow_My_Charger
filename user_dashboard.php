<?php
date_default_timezone_set('Asia/Bahrain');
require_once './Models/Booking.php';

$std = new stdClass();
$booking = new Booking();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    $last_check_time = isset($data['last_check_time']) ? $data['last_check_time'] : date('Y-m-d H:i:s', strtotime('-1 hour'));

    try {
        // Get any booking status updates since the last check time
        $updates = $booking->getBookingStatusUpdates(6, $last_check_time);

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

$std->bookingRequests = $booking->getBookingsByCustomer(6);

require_once './Views/user_dashboard.phtml';