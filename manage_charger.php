<?php

require_once 'Models/Charger.php';

$std = new stdClass();

$charger = new Charger();

$result =  $charger->getChargerByID(2);

$std->charger_id = $result['charger_id'];
$std->charge_name = $result['charge_name'];
$std->cost = $result['cost'];
$std->availability = $result['availability'];
$std->latitude = $result['latitude'];
$std->longitude = $result['longitude'];
$std->user_id = $result['user_id'];
$std->picture = $result['picture'];
$std->location = $result['location'];
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

    if (isset($_FILES['charger_image']) && isset($_POST['picture']) ) {
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
        'charger_id' => $std->charger_id,
        'name' => $charge_name,
        'price' => $priceperkwh,
        'availability' => $std->availability ?? '',
        'latitude' => $latitude,
        'longitude' => $longitude,
        'image' => $imagePath,
        'location' => $location,
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

require './Views/add-charge.phtml';