<?php
session_start();

session_unset();

session_destroy();
$_SESSION['user_id'] = null;
$_SESSION['user_role'] = null;
$_SESSION['user_status'] = null;

header("Location: login.php");
exit;
?>
