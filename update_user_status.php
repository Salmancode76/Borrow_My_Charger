<?php
// Initialize session and include necessary files
session_start();
require_once 'Models/connectionDB.php';
require_once 'Models/User.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Unauthorized access'
    ]);
    exit;
}

// Get JSON data
$json = file_get_contents('php://input');
$data = json_decode($json, true);

// Validate input
if (!isset($data['user_id']) || !isset($data['action'])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request data'
    ]);
    exit;
}

$userId = intval($data['user_id']);
$action = $data['action'];

// Map action to status
$statusMap = [
    'approve' => 'Approved',
    'disapprove' => 'Disapproved'
];

if (!isset($statusMap[$action])) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Invalid action'
    ]);
    exit;
}

$status = $statusMap[$action];

// Update user status
$db = new connectionDB();
$conn = $db->connect();
$user = new User($conn);
$result = $user->updateUserStatus($userId, $status);

// Return response
header('Content-Type: application/json');
echo json_encode([
    'success' => $result,
    'message' => $result 
        ? 'User has been ' . ($action === 'approve' ? 'approved' : 'disapproved') . ' successfully.' 
        : 'Failed to update user status'
]);