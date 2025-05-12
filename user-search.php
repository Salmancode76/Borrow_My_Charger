<?php
require_once 'Models/Charger.php';
session_start();

$customer_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;

if ($customer_id === 0) {
    header("Location: /Borrow_My_Charger/login.php");
    exit;
}


// Get and sanitize parameters
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
$max_price = filter_var($max_price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$location = filter_var($location, FILTER_SANITIZE_STRING);

$availability = isset($_GET['availability']) ? trim($_GET['availability']) : '';
$availability = filter_var($availability, FILTER_SANITIZE_STRING);

// Initialize charger model
$chargerModel = new Charger();

// Search chargers based on filters
$matchedChargers = $chargerModel->searchChargers($max_price, $location, $availability);

// Format response
$response = [];
if (!empty($matchedChargers)) {
    foreach ($matchedChargers as $point) {
        $response[] = [
            'charger_id' => isset($point['charger_id']) ? $point['charger_id'] : 0,
            'Location' => isset($point['Location']) ? $point['Location'] : 'Unknown Location',
            'cost' => isset($point['cost']) ? $point['cost'] : 'N/A',
            'availability' => isset($point['availability']) ? $point['availability'] : 'Unknown Status',
'image_url' => isset($point['picture']) ? $point['picture'] : 'images/default_image.jpg',     
            'latitude' => isset($point['latitude']) ? $point['latitude'] : null,
            'longitude' => isset($point['longitude']) ? $point['longitude'] : null
        ];
    }
}

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode($response);
    exit;
} else {
    $chargers = $response;
    require_once './Views/headers/users_header.phtml';
    require_once './Views/user_search.phtml';
}
?>