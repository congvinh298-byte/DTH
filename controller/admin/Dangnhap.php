<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

header('Content-Type: application/json');

if (!isset($_COOKIE['token']) || empty($getUser)) {
    echo json_encode(['status' => 'error', 'msg' => 'Vui long dang nhap']);
    exit;
}
if (!in_array($getUser['level'], ['admin','bct'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Khong co quyen']);
    exit;
}

$password = trim($_POST['password'] ?? '');
if (!$password || strlen($password) < 5) {
    echo json_encode(['status' => 'error', 'msg' => 'Mat khau cap 2 khong chinh xac']);
    exit;
}

$hash = md5($password);
if ($hash != md5('845409')) {
    echo json_encode(['status' => 'error', 'msg' => 'Mat khau cap 2 khong chinh xac']);
    exit;
}

$rowlog = $DMH->get_row("SELECT * FROM `users` WHERE `tokenlog` = '" . $_COOKIE['token'] . "' AND `level` IN ('admin','bct')");
if (!$rowlog) {
    echo json_encode(['status' => 'error', 'msg' => 'Thong tin dang nhap khong chinh xac']);
    exit;
}

$_SESSION['loginadmin'] = true;
echo json_encode(['status' => 'success', 'msg' => 'Dang nhap thanh cong', 'url' => '/pages/admin/Home.php', 'time' => 1000]);
