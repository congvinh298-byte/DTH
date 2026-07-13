<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

if (isset($_COOKIE['token'])) {
    setcookie('token', '', time() - 3600, '/');
    unset($_COOKIE['token']);
}

session_start();
unset($_SESSION['loginadmin']);
session_destroy();

header("Location: /pages/admin/LoginAdmin.php");
exit;
?>
