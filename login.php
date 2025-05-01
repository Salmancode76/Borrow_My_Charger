<?php
session_start();
require_once('Models/connectionDB.php');
require_once('Models/User.php');

$db = new connectionDB();
$conn = $db->connect();
$userModel = new User($conn);

if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    // Redirect based on role
    switch (strtolower($_SESSION['user_role'])) {
        case 'admin':
            header("Location: admin_dashboard.php");
            break;
        case 'rental manager':
            header("Location: homeowner_dashboard.php");
            break;
        case 'customer':
        default:
            header("Location: user_dashboard.php");
            break;
    }
    exit;    
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['username']);
    $password = $_POST['password'];

    $result = $userModel->login($email, $password);

    if (isset($result['error'])) {
        // Redirect back to the login page with error
        header("Location: login.php?error=" . urlencode($result['error']));
        exit;
    }

    // Set session variables
    $_SESSION['user_id'] = $result['id'];
    $_SESSION['user_role'] = $result['role'];
    $_SESSION['user_name'] = $result['name'];

    // Redirect based on role
    switch (strtolower($result['role'])) {
        case 'admin':
            header("Location: admin_dashboard.php");
            break;
        case 'rental manager':
            header("Location: homeowner_dashboard.php");
            break;
        case 'customer':
        default:
            header("Location: user_dashboard.php");
            break;
    }
    exit;
}

require_once './Views/login.phtml';
