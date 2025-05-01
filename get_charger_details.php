<?php
// Initialize session if needed
session_start();
require_once 'Models/Charger.php';

// Set the content type
header('Content-Type: application/json');

// Get the charger ID
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid charger ID'
    ]);
    exit;
}

// Get charger details
try {
    $charger = new Charger();
    $details = $charger->getChargerByChargerID($id);
    
    if ($details) {
        echo json_encode([
            'success' => true,
            'charger' => $details
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Charger not found'
        ]);
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage()
    ]);
}