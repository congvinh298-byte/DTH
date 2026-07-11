<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");

$redirect = '/';

if(isset($_SERVER['HTTP_REFERER'])) {
    if(strpos($_SERVER['HTTP_REFERER'], 'admin') !== false) {
        $redirect = '/admin.php'; // Will automatically redirect to admin-login.php
    } elseif(strpos($_SERVER['HTTP_REFERER'], 'tho') !== false) {
        $redirect = '/tho-login.php';
    } else {
        $redirect = '/login.php';
    }
} else {
    $redirect = '/login.php';
}

setcookie("token", "", time()-3600, "/");
header('Location: ' . $redirect);
