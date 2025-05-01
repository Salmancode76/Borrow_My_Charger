<?php session_start(); ?>

<?php
// ✅ Restrict access if user is not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.phtml");
    exit;
}

require './Views/headers/admin_header.phtml';
require_once 'Models/Charger.php';

$charger = new Charger();
$data = $charger->getAllChargers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Charger Locations</title>
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
        h1 {
            text-align: center;
            margin-top: 20px;
            color: #333;
        }
        .logout {
            text-align: center;
            margin-top: 10px;
        }
        .logout a {
            text-decoration: none;
            color: white;
            background-color: #dc3545;
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: bold;
        }
        .logout a:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <h1>Charger Locations</h1>

    <div class="logout">
        <a href="logout.php">Logout</a>
    </div>

    <div id="map"></div>

    <script>
        const chargerData = <?php echo json_encode($data); ?>;
        console.log("Chargers (from PHP):", chargerData);
    </script>

    <script 
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCf6CIeUcXb6aIJNQO_Wg7idkbqbLRY63A&libraries=maps,marker&callback=LoadMap&loading=async" 
        async 
        defer>
    </script>
    <script src="Map.js"></script>
</body>
</html>
