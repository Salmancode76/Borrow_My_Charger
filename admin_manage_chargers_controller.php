<?php

require_once 'Models/Charger.php';

$std = new stdClass();

$charger = new Charger();

$std->Chargers = $charger->getAllChargers();

require './Views/headers/admin_header.phtml'; 

require 'Views/admin_manage_chargepoints.phtml';