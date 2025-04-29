<?php
// AJAX.php - This file returns all charger data with coordinates for the map
require_once 'Models/Charger.php';

// Initialize charger model
$chargerModel = new Charger();

// Get all chargers (or apply any global filters if needed)
$allChargers = $chargerModel->getAllChargers();

// Format response
$response = [];
if (!empty($allChargers)) {
    foreach ($allChargers as $charger) {
        $response[] = [
            'charger_id' => isset($charger['charger_id']) ? $charger['charger_id'] : 0,
            'charge_name' => isset($charger['charge_name']) ? $charger['Location'] : 'Unknown Location',
            'Location' => isset($charger['Location']) ? $charger['Location'] : 'Unknown Location',
            'cost' => isset($charger['cost']) ? $charger['cost'] : 'N/A',
            'availability' => isset($charger['availability']) ? $charger['availability'] : 'Unknown Status',
            'latitude' => isset($charger['latitude']) ? $charger['latitude'] : null,
            'longitude' => isset($charger['longitude']) ? $charger['longitude'] : null,
            'image_url' => isset($charger['image_url']) ? $charger['image_url'] : 'default_image.jpg'
        ];
    }
}

// Set header and return JSON
header('Content-Type: application/json');
echo json_encode($response);
?>