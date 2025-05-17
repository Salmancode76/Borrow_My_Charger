<?php
session_start();
require_once('Models/connectionDB.php');
require_once('Models/User.php');
require_once('Models/Charger.php');

$db = new connectionDB();
$conn = $db->connect();
$userModel = new User($conn);
$charger = new Charger();

// If already logged in, redirect based on role
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    $user = $userModel->getUserById($_SESSION['user_id']);
    $status_name = strtolower($user['status_name']);
    
    $_SESSION['user_status'] = $status_name;
    
    switch (strtolower($_SESSION['user_role'])) {
    case 'admin':
        header("Location: admin_dashboard.php");
        break;
    case 'rental manager':
        $chargerResult = $charger->getChargerByID($_SESSION['user_id']);
        if ($chargerResult !== false && $chargerResult !== null) {
            header("Location: homeowner_dashboard.php");
        } else {
            header("Location: add_charger.php");
        }
        break;
    case 'customer':
    default:
        header("Location: user_dashboard.php");
        break;
    }
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['username']);
    $password = $_POST['password'];
    $result = $userModel->login($email, $password);
    
    if (isset($result['error'])) {
        $_SESSION['login_error'] = $result['error'];
        header("Location: login.php");
        exit;
    }
    
    $status_name = strtolower($result['status']);
    
    if ($status_name === 'pending' || $status_name === 'deactivated' || $status_name === 'disapproved') {
        switch ($status_name) {
            case 'pending':
                $_SESSION['login_error'] = "Your account is still pending approval";
                break;
            case 'deactivated':
                $_SESSION['login_error'] = "Your account has been deactivated by the admin";
                break;
            case 'disapproved':
                $_SESSION['login_error'] = "Your account request has been disapproved";
                break;
            default:
                break;
        }
            
        header("Location: login.php");
        exit();
    }

    // Set session variables
    $_SESSION['user_id'] = $result['id'];
    $_SESSION['user_role'] = $result['role'];
    $_SESSION['user_status'] = $status_name;
    $_SESSION['user_name'] = $result['name'];

    // Redirect based on role
    switch (strtolower($result['role'])) {
        case 'admin':
            header("Location: admin_dashboard.php");
            break;
        case 'rental manager':
            $chargerResult = $charger->getChargerByID($_SESSION['user_id']);
            if ($chargerResult !== false && $chargerResult !== null) {
                header("Location: homeowner_dashboard.php");
            } else {
                header("Location: add_charger.php");
            }
            break;
        case 'customer':
        default:
            header("Location: user_dashboard.php");
            break;
    }
    exit;
}

// Include the login view template
require_once './Views/login.phtml';