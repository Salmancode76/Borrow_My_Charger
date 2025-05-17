<?php
require_once 'Models/Charger.php';
$std = new stdClass();
session_start();
$is_admin = ($_SESSION['user_role'] === "Admin");
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) ||  !$is_admin) {
    header("Location: /Borrow_My_Charger/login.php");
    exit;
}

$charger = new Charger();

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$itemsPerPage = 6;


    $allChargers = $charger->getAllChargers();
    
    $totalItems = count($allChargers);
    $totalPages = ceil($totalItems / $itemsPerPage);
    
    $page = max(1, min($page, $totalPages > 0 ? $totalPages : 1));
    
    $offset = ($page - 1) * $itemsPerPage;
    $std->Chargers = array_slice($allChargers, $offset, $itemsPerPage);
    
    $std->paginationData = [
        'chargers' => $std->Chargers,
        'pagination' => [
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'totalItems' => $totalItems,
            'itemsPerPage' => $itemsPerPage
        ]
    ];


require './Views/headers/admin_header.phtml'; 
require 'Views/admin_manage_chargepoints.phtml';
?>