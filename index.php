
    <?php require './Views/headers/admin_header.phtml'; ?>

<?php
require_once 'Models/Charger.php';

$charger = new Charger();
$data = $charger->getAllChargers();

echo "<h1>Charger Locations</h1>";
?>

<script>
    const chargerData = <?php echo json_encode($data); ?>;
    console.log("Chargers (from PHP):", chargerData);
</script>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Map Demo</title>
     <style>
        #map {
            height: 400px;
            width: 100%;
            max-width: 800px;
            margin: 40px auto;
            border: 2px solid #4CAF50;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        body {
            background-color: #f9f9f9;
            font-family: Arial, sans-serif;
        }
    </style>
</head>
<body>

<div id="map"></div>
<script 
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCf6CIeUcXb6aIJNQO_Wg7idkbqbLRY63A&libraries=maps,marker&callback=LoadMap&loading=async" 
    async 
    defer>
</script>
<script src="Map.js"></script>



</body>

<?php

?>
</html>
