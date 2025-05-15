<?php
session_start();
require_once 'Models/Charger.php';

$std = new stdClass();
$is_admin = ($_SESSION['user_role'] === "admin");
if (isset($_SESSION['user_id']) && isset($_SESSION['user_role'])) {
    // Get the user role and convert to lowercase for case-insensitive comparison
    $user_role = strtolower($_SESSION['user_role']);
    
    // Check if user is admin
    if ($user_role === "admin") {
        require './Views/headers/admin_header.phtml';
    } else {
        // For any non-admin role, show the homeowner header
        require './Views/headers/home_owner_header.phtml';
    }
} else {
    // User is not logged in, redirect to login page
    header("Location: /Borrow_My_Charger/login.php");
    exit;
}

require './Views/add-charge.phtml';



$customer_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
// If no user is logged in, return error
if ($customer_id === 0) {
    header("Location: /Borrow_My_Charger/login.php");
    exit;
}


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['charge_name'])) {
    $charge_name = htmlentities(trim($_POST['charge_name'] ?? ''));
    $availability = htmlentities(trim($_POST['availability'] ?? ''));
    $priceperkwh = htmlentities(trim($_POST['priceperkwh'] ?? ''));
    $latitude = htmlentities(trim($_POST['latitude'] ?? ''));
    $longitude = htmlentities(trim($_POST['longitude'] ?? ''));
    $location = htmlentities(trim($_POST['charge_loca'] ?? '' ));
    $avaFrom = htmlentities(trim($_POST['available_from'] ?? '' ));
    $avaTo = htmlentities(trim($_POST['available_to'] ?? '' ));
    
    $imagePath = '';
    if (isset($_FILES['charger_image']) && $_FILES['charger_image']['error'] === UPLOAD_ERR_OK) {
        $imageTmp = $_FILES['charger_image']['tmp_name'];
        $imageName = basename($_FILES['charger_image']['name']);
        $uploadDir = __DIR__ . '/images/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
    $imagePath = 'images/' . $imageName;
        move_uploaded_file($imageTmp, $uploadDir . $imageName);
    }

    $params = [
        'name' => $charge_name,
        'availability' => $availability,
        'price' => $priceperkwh,
        'latitude' => $latitude,
        'longitude' => $longitude,
        'image' => $imagePath,
        'Location'=>$location,
        '$avaFrom' => $avaFrom,
        '$avaTo'=> $avaTo
        ];

    $charger = new Charger();
    if ($charger->insert_charger($params)) {
        echo "<div class='alert alert-success mt-3'>✅ Charger successfully added!</div>";
                    header("Location: homeowner_dashboard.php");

    } else {
        echo "<div class='alert alert-danger mt-3'>❌ Failed to add charger.</div>";
    }
}
