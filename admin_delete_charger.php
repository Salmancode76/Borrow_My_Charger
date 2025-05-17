<?php
require_once 'Models/Charger.php';
session_start();
$is_admin = ($_SESSION['user_role'] === "Admin");
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) ||  !$is_admin) {
    header("Location: /Borrow_My_Charger/login.php");
    exit;
}


// Get the charger ID from the URL parameter
if (isset($_GET['id'])) {
    $charger_id = intval($_GET['id']);
} else {
    // Redirect with error if no ID was provided
    header("Location: admin_chargers.php?error=1");
    exit();
}

// Make sure we have a valid ID
if ($charger_id <= 0) {
    header("Location: admin_chargers.php?error=1");
    exit();
}

try {
    $charger = new Charger();
    $result = $charger->deleteCharger($charger_id);
    
    // Fixed missing colon here
    header("Location: admin_manage_chargers_controller.php");
    exit();
   
} catch (Exception $e) {
    header("Location: admin_chargers.php?error=1");
}
exit();
?>