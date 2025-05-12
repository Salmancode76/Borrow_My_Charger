<?php
session_start();

session_unset();

session_destroy();
$_SESSION['user_id'] = null;
$_SESSION['user_role'] = null;

header("Location: login.php");
exit;
?>
