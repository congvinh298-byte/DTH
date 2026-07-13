<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
session_start();
$_SESSION = [];
if (isset($_COOKIE[session_name()])) {
    setcookie(session_name(), '', time() - 3600, '/');
}
session_destroy();
setcookie('token', '', time() - 3600, '/');
header("Location: /pages/admin/LoginAdmin.php");
exit;
?>
