<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

header('Content-Type: application/json');

try {
    if (isset($_COOKIE['token'])) {
        setcookie('token', '', time() - 3600, '/');
        unset($_COOKIE['token']);
    }
    if (isset($_SESSION)) {
        session_unset();
        session_destroy();
    }
    echo json_encode(['status' => 'success', 'msg' => 'Đã đăng xuất thành công.']);
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
}
?>
