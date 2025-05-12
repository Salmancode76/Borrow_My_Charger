<?php

require_once 'Models/Charger.php';
session_start();
$std = new stdClass();

$charger = new Charger();
$homeowner_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

if ($homeowner_id === 0) {
    header("Location: /Borrow_My_Charger/login.php");
    exit;
}
$result =  $charger->getChargerByID($homeowner_id);

$std->charger_id = $result['charger_id'];
$std->charge_name = $result['charge_name'];
$std->cost = $result['cost'];
$std->availability = $result['availability'];
$std->latitude = $result['latitude'];
$std->longitude = $result['longitude'];
$std->user_id = $result['user_id'];
$std->picture = $result['picture'];
    $std->Location = ($result['Location']);
    $std->available_from = $result['available_from'];
$std->available_to = $result['available_to'];


if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['charge_name'])) {
    $charge_name = htmlentities(trim($_POST['charge_name'] ?? ''));
    $priceperkwh = htmlentities(trim($_POST['priceperkwh'] ?? ''));
    $latitude = htmlentities(trim($_POST['latitude'] ?? ''));
    $longitude = htmlentities(trim($_POST['longitude'] ?? ''));
    $location = htmlentities(trim($_POST['charge_loca'] ?? ''));
    $avaFrom = htmlentities(trim($_POST['available_from'] ?? ''));
    $avaTo = htmlentities(trim($_POST['available_to'] ?? ''));

    $imagePath = $std->picture;

 if (isset($_FILES['charger_image']) && $_FILES['charger_image']['size'] > 0) {
        $imageTmp = $_FILES['charger_image']['tmp_name'];
        $imageName = time() . '_' . basename($_FILES['charger_image']['name']);
        
        // FIXED: Correct directory path syntax
        $uploadDir = __DIR__ . '/images/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $imagePath = 'images/' . $imageName;
        
        // Try to upload and provide error details if it fails
        if (!move_uploaded_file($imageTmp, $uploadDir . $imageName)) {
            echo "<div class='alert alert-danger mt-3'>Failed to upload image. Error code: " . 
                 $_FILES['charger_image']['error'] . "</div>";
        }
    }

    $params = [
        'charger_id' => $std->charger_id,
        'name' => $charge_name,
        'price' => $priceperkwh,
        'availability' => $std->availability ?? '',
        'latitude' => $latitude,
        'longitude' => $longitude,
        'image' => $imagePath,
        'Location' => $location,
        'available_from' => $avaFrom,
        'available_to' => $avaTo
    ];

    if ($charger->updateCharger($params)) {

        header("Location: homeowner_dashboard.php");
        exit();
    } else {
        echo "<div class='alert alert-danger mt-3'>❌ Failed to update charger.</div>";
    }
}
require './Views/headers/home_owner_header.phtml'; 

require './Views/add-charge.phtml';