<?php
require_once 'Models/Charger.php';
session_start();
$std = new stdClass();
$charger = new Charger();



$user_id = intval($_SESSION['user_id']);
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';



// Check if this is a charger edit request
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // We're editing a specific charger
    $charger_id = intval($_GET['id']);
    $result = $charger->getChargerByChargerID($charger_id);
    
    if (!$result) {
        // Charger not found
        header("Location: admin_manage_chargers.php?error=charger_not_found");
        exit;
    }
    
    // Set up the data for the edit form
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
    
    // Process form submission for edit
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['charge_name'])) {
        $charge_name = htmlentities(trim($_POST['charge_name'] ?? ''));
        $priceperkwh = htmlentities(trim($_POST['priceperkwh'] ?? ''));
        $latitude = htmlentities(trim($_POST['latitude'] ?? ''));
        $longitude = htmlentities(trim($_POST['longitude'] ?? ''));
        $location = htmlentities(trim($_POST['charge_loca'] ?? ''));
        $avaFrom = htmlentities(trim($_POST['available_from'] ?? ''));
        $avaTo = htmlentities(trim($_POST['available_to'] ?? ''));
        $availability = htmlentities(trim($_POST['availability'] ?? $std->availability));
        $imagePath = $std->picture;
        
        if (isset($_FILES['charger_image']) && $_FILES['charger_image']['size'] > 0) {
            $imageTmp = $_FILES['charger_image']['tmp_name'];
            $imageName = time() . '_' . basename($_FILES['charger_image']['name']);
            
            // Fix the directory path syntax
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
            'availability' => $availability,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'image' => $imagePath,
            'Location' => $location,
            'available_from' => $avaFrom,
            'available_to' => $avaTo
        ];
        
        if ($charger->updateCharger($params)) {
            header("Location: admin_manage_chargers_controller.php");
            exit();
        } else {
            echo "<div class='alert alert-danger mt-3'>❌ Failed to update charger.</div>";
        }
    }
    
    // Load the edit view
    require './Views/headers/admin_header.phtml'; 
require './Views/admin-edit-charger.phtml';

        } else {
    // We're showing the list of all chargers
    $std->Chargers = $charger->getAllChargers();
    
    // Load the view for the charger list
    require './Views/headers/admin_header.phtml'; 
    require './Views/manage_chargers.phtml';
}
?>