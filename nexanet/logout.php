<?php
require_once 'config/session.php';
session_destroy();
setcookie('nexanet_remember', '', time() - 3600, '/');
header('Location: login.php');
exit;
?>