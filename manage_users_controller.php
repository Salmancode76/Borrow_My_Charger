<?php
require_once('Models/connectionDB.php');
require_once('Models/User.php');

$db = new connectionDB();
$conn = $db->connect();
$userModel = new User($conn);

if (isset($_GET['delete'])) {
    $userModel->deleteUser($_GET['delete']);
} elseif (isset($_GET['deactivate'])) {
    $userModel->setStatus($_GET['deactivate'], 5);
} elseif (isset($_GET['activate'])) {
    $userModel->setStatus($_GET['activate'], 4);
}

header("Location: Views/admin_manage_users.php");
exit;
?>
