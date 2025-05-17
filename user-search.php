<?php
require_once 'Models/Charger.php';
session_start();

$customer_id = isset($_SESSION['user_id']) ? intval($_SESSION['user_id']) : 0;
if ($customer_id === 0 && $_SESSION['user_role'] !="Customer") {
    header("Location: /Borrow_My_Charger/login.php");
    exit;
}


// Get and sanitize parameters
$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
$max_price = filter_var($max_price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

$min_price = isset($_GET['min_price']) ? $_GET['min_price'] : '';
$min_price = filter_var($min_price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);


$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$location = filter_var($location, FILTER_SANITIZE_STRING);

$availability = isset($_GET['availability']) ? trim($_GET['availability']) : '';
$availability = filter_var($availability, FILTER_SANITIZE_STRING);

$search_time = isset($_GET['search_time']) ? $_GET['search_time'] : '';
if ($search_time !== '' && strlen($search_time) === 5) {
    $search_time .= ':00';
}
// Initialize charger model
$chargerModel = new Charger();

// Search chargers based on filters
$matchedChargers = $chargerModel->searchChargers($min_price,$max_price, $location, $availability,$search_time);

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
            'longitude' => isset($point['longitude']) ? $point['longitude'] : null,
            'available_from' => $point['available_from'] ?? '00:00',
            'available_to' => $point['available_to'] ?? '23:59'
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
