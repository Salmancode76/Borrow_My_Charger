<?php

require_once 'Models/Charger.php'; 

$max_price = isset($_GET['max_price']) ? $_GET['max_price'] : '';
$max_price = filter_var($max_price, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

$location = isset($_GET['location']) ? trim($_GET['location']) : '';
$location = filter_var($location, FILTER_SANITIZE_STRING);

$chargerModel = new Charger();
$matchedChargers = $chargerModel->searchChargers($max_price,$location);

$response = [];
if (!empty($matchedChargers)) {
    foreach ($matchedChargers as $point) {
        $response[] = [
            'charger_id' => isset($point['charger_id']) ? $point['charger_id'] : 'Unknown Location',
            'Location' => isset($point['Location']) ? $point['Location'] : 'Unknown Location',
            'cost' => isset($point['cost']) ? $point['cost'] : 'N/A',
            'availability' => isset($point['availability']) ? $point['availability'] : 'Unknown Status',
            'image_url' => isset($point['image_url']) ? $point['image_url'] : 'default_image.jpg'
        ];
}

    }

echo json_encode($response);
?>
