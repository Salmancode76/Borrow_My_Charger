<?php
require_once('Models/connectionDB.php');
require_once('Models/User.php');

$db = new connectionDB();
$conn = $db->connect();
$userModel = new User($conn);
session_start();
$is_admin = ($_SESSION['user_role'] === "Admin");
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role']) ||  !$is_admin) {
    header("Location: /Borrow_My_Charger/login.php");
    exit;
}

if (isset($_GET['delete'])) {
    $userModel->deleteUser($_GET['delete']);
} elseif (isset($_GET['deactivate'])) {
    $userModel->setStatus($_GET['deactivate'], 2);
} elseif (isset($_GET['activate'])) {
    $userModel->setStatus($_GET['activate'], 1);
}

header("Location: Views/admin_manage_users.php");
exit;
?>
