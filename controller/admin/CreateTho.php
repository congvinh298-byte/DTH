<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

header('Content-Type: application/json');

try {
    if (!isset($_COOKIE['token']) || empty($getUser) || !in_array($getUser['level'], ['admin','bct'])) {
        echo json_encode(['status' => 'error', 'msg' => 'Khong co quyen']);
        exit;
    }
    if (empty($_SESSION['loginadmin'])) {
        echo json_encode(['status' => 'error', 'msg' => 'Vui long dang nhap admin']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'msg' => 'Khong ho tro']);
        exit;
    }

    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    if ($username == '' || $password == '') {
        echo json_encode(['status' => 'error', 'msg' => 'Vui long nhap day du tai khoan va mat khau']);
        exit;
    }
    if (strlen($password) < 6) {
        echo json_encode(['status' => 'error', 'msg' => 'Mat khau phai co it nhat 6 ky tu']);
        exit;
    }

    $DMH->connect();
    $u = mysqli_real_escape_string($DMH->ketnoi, $username);
    $exists = $DMH->get_row("SELECT id FROM `users` WHERE `username` = '$u'");
    if ($exists) {
        echo json_encode(['status' => 'error', 'msg' => 'Tai khoan nay da ton tai']);
        exit;
    }

    $ok = $DMH->insert('users', [
        'username' => $username,
        'password' => md5($password),
        'name' => $name,
        'fullname' => $name,
        'phone' => $phone,
        'level' => 'tho',
        'banned' => 'ON',
        'money' => 0,
        'verify' => 0,
    ]);

    if ($ok) {
        echo json_encode(['status' => 'success', 'msg' => 'Da tao tai khoan tho thanh cong.']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Tao tai khoan that bai.']);
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Loi he thong: ' . $e->getMessage()]);
}
