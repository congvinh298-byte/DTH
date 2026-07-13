<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['status' => 'error', 'msg' => 'Phương thức không hợp lệ']);
        exit;
    }

    $name = check_string($_POST['name'] ?? '');
    $phone = check_string($_POST['phone'] ?? '');
    $password = check_string($_POST['password'] ?? '');
    $confirm = check_string($_POST['confirm_password'] ?? '');
    $address = check_string($_POST['address'] ?? '');

    if (empty($name) || empty($phone) || empty($password) || empty($confirm)) {
        echo json_encode(['status' => 'error', 'msg' => 'Vui lòng điền đầy đủ thông tin bắt buộc']);
        exit;
    }

    if (!preg_match('/^[0-9]{10,11}$/', $phone)) {
        echo json_encode(['status' => 'error', 'msg' => 'Số điện thoại không hợp lệ (10-11 số)']);
        exit;
    }

    if (strlen($password) < 6) {
        echo json_encode(['status' => 'error', 'msg' => 'Mật khẩu phải có ít nhất 6 ký tự']);
        exit;
    }

    if ($password !== $confirm) {
        echo json_encode(['status' => 'error', 'msg' => 'Mật khẩu xác nhận không khớp']);
        exit;
    }

    // Kiểm tra SĐT đã tồn tại chưa
    $check = $DMH->get_row("SELECT * FROM `users` WHERE `phone` = '$phone' AND `level` = 'user'");
    if ($check) {
        echo json_encode(['status' => 'error', 'msg' => 'Số điện thoại này đã được đăng ký. Vui lòng đăng nhập.']);
        exit;
    }

    // Tạo tài khoản
    $username = 'kh_' . $phone;
    $isInsert = $DMH->insert("users", [
        'username' => $username,
        'phone' => $phone,
        'password' => md5($password),
        'name' => $name,
        'address' => $address,
        'level' => 'user',
        'first_login' => 0,
        'banned' => 'ON',
        'points' => 0,
        'money' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    if (!$isInsert) {
        echo json_encode(['status' => 'error', 'msg' => 'Lỗi tạo tài khoản, vui lòng thử lại']);
        exit;
    }

    // Tự động đăng nhập
    $newUser = $DMH->get_row("SELECT * FROM `users` WHERE `phone` = '$phone' AND `level` = 'user' AND `banned` = 'ON'");
    if ($newUser) {
        $token = random('qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789', 64);
        $DMH->update("users", ['tokenlog' => $token], " `id` = '".$newUser['id']."' ");
        setcookie('token', $token, time() + 86400 * 30, '/');
    }

    echo json_encode([
        'status' => 'success',
        'msg' => 'Đăng ký thành công! Đang chuyển hướng...',
        'redirect' => '/goi-tho.php'
    ]);

} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>
