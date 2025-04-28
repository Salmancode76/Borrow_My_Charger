<?php

require_once 'Models/Charger.php';

header('Content-Type: application/json');

$action = $_GET['action'] ?? '';

$charger = new Charger();

if ($action === 'search') {
//    $data = $charger->searchChargers($location, $max_price, $availability);
} else {
    $data = $charger->getAllChargers();
}

echo json_encode($data);
//require_once 'Views/user_dashboard.phtml';
exit;
//it wont reach the require once ..how can we process things from here

/* 
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/EmptyPHP.php to edit this template
 */

