<?php
session_start();
require_once 'Models/Charger.php';
$std = new stdClass();
$is_admin = ($_SESSION['user_role'] === "admin");

if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header("Location: /Borrow_My_Charger/login.php");
    exit;
}

$customer_id = intval($_SESSION['user_id']);
if ($customer_id === 0) {
    header("Location: /Borrow_My_Charger/login.php");
    exit;
}

// Process form submission
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
        $uploadDir = __DIR__ . '/images/'; // Note: Fixed DIR to __DIR__
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
        'Location' => $location,
        '$avaFrom' => $avaFrom,
        '$avaTo' => $avaTo
    ];
    
    $charger = new Charger();
    if ($charger->insert_charger($params)) {
        header("Location: homeowner_dashboard.php");
        exit();
    }
}

$user_role = strtolower($_SESSION['user_role']);
if ($user_role === "admin") {
    require './Views/headers/admin_header.phtml';
} else {
    require './Views/headers/home_owner_header.phtml';
}

require './Views/add-charge.phtml';
?>