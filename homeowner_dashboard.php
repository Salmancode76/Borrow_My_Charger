<?php
session_start();
require_once './Models/Charger.php';
require_once './Models/Booking.php';
$std = new stdClass();
$charger = new Charger();
$booking = new Booking();

$homeowner_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

if ($homeowner_id === 0) {
    header("Location: /Borrow_My_Charger/login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    // Check if this is a status update request
    if (isset($data['action']) && $data['action'] === 'update_status') {
        // Extract the data
        $booking_id = isset($data['booking_id']) ? intval($data['booking_id']) : 0;
        $status = isset($data['status']) ? $data['status'] : '';
        
        // Validate the data
        if ($booking_id <= 0) {
            echo json_encode(['success' => false, 'message' => 'Invalid booking ID']);
            exit;
        }
        
        // Map the frontend status values to database values
        $statusMap = [
            'approve' => 'Approved',
            'reject' => 'Declined'
        ];
        
        if (!isset($statusMap[$status])) {
            echo json_encode(['success' => false, 'message' => 'Invalid status value']);
            exit;
        }
        
        $dbStatus = $statusMap[$status];
        
        // Update the booking status in the database
        try {
            $result = $booking->updateBookingStatus($booking_id, $dbStatus);
            
            if ($result) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to update booking status']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
        
        exit; // Important to exit after handling the AJAX request
    }
}

// Get the homeowner's charger
$result = $charger->getChargerByID($homeowner_id);

// Check if the homeowner has a charger
if ($result === false) {
    // No charger found - redirect to add charger page
    header("Location: /Borrow_My_Charger/add_charger.php");
    exit;
}

// If we get here, the homeowner has a charger, so continue with the dashboard
$bookingRequests = $booking->getBookingsByChargerOwner($homeowner_id);
$std->charger_id = $result['charger_id'];
$std->charge_name = $result['charge_name'];
$std->cost = $result['cost'];
$std->availability = $result['availability'];
$std->latitude = $result['latitude'];
$std->longitude = $result['longitude'];
$std->user_id = $result['user_id'];
$std->picture = $result['picture'];
$std->Location = $result['Location'];
$std->available_from = $result['available_from'];
$std->available_to = $result['available_to'];
$std->bookingRequests = $bookingRequests;

require './Views/headers/home_owner_header.phtml'; 
require './Views/homeowner_dashboard.phtml';
?>