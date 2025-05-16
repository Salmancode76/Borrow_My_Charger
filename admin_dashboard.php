<?php
session_start();
$is_admin = ($_SESSION['user_role'] === "Admin");
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) ||  !$is_admin) {
    header("Location: /Borrow_My_Charger/login.php");
    exit;
}
require 'Views/admin_dashboard.phtml';
