<?php
require_once './Models/Charger.php';
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

require './Views/homeowner_dashboard.phtml';





