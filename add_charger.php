<?php

require_once 'Models/Charger.php';

$std = new stdClass();

require './Views/add-charge.phtml';

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
    } else {
        echo "<div class='alert alert-danger mt-3'>❌ Failed to add charger.</div>";
    }
}
