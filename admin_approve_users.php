<?php
// Initialize session and include necessary files
require_once 'Models/connectionDB.php';
require_once 'Models/User.php';

session_start();

$db = new connectionDB();
$conn = $db->connect();

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || strtolower($_SESSION['user_role']) !== 'admin') {
    header('Location: login.php');
    exit;    
}

// Get all pending users
$user = new User($conn);
$pendingUsers = $user->getPendingUsers();

error_log(json_encode($pendingUsers));

require_once './Views/headers/admin_header.phtml'; 
require_once './Views/admin_approve_users.phtml';