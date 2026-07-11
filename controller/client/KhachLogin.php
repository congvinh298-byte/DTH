<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
        
        $action = $_POST['action'];
        
        if ($action == 'check_phone') {
            $phone = check_string($_POST['phone']);
            if (empty($phone)) {
                echo json_encode(['status' => 'error', 'msg' => 'Số điện thoại không hợp lệ!']);
                exit;
            }
            
            $check = $DMH->get_row("SELECT * FROM `users` WHERE `phone` = '$phone' AND `level` = 'user'");
            
            if ($check) {
                echo json_encode(['status' => 'exists']);
                exit;
            } else {
                // Create new account with OTP
                $otp = rand(100000, 999999);
                $password = md5((string)$otp);
                
                $isInsert = $DMH->insert("users", [
                    'username' => 'kh_' . $phone,
                    'phone' => $phone,
                    'password' => $password,
                    'level' => 'user',
                    'first_login' => 1,
                    'banned' => 'ON',
                    'points' => 0
                ]);
                
                if ($isInsert) {
                    echo json_encode(['status' => 'new', 'otp' => $otp]);
                    exit;
                } else {
                    echo json_encode(['status' => 'error', 'msg' => 'Lỗi tạo tài khoản!']);
                    exit;
                }
            }
        }
        
        if ($action == 'login') {
            $phone = check_string($_POST['phone']);
            $password = md5(check_string($_POST['password']));
            
            $check = $DMH->get_row("SELECT * FROM `users` WHERE `phone` = '$phone' AND `password` = '$password' AND `level` = 'user' AND `banned` = 'ON'");
            
            if ($check) {
                $token = random('qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789', 64);
                
                $DMH->update("users", [
                    'tokenlog' => $token
                ], " `id` = '".$check['id']."' ");
                
                setcookie('token', $token, time() + 86400 * 30, '/');
                
                if ($check['first_login'] == 1) {
                    msg_success('Đăng nhập thành công! Vui lòng cập nhật thông tin...', '/khach-update.php', 1000);
                } else {
                    msg_success('Đăng nhập thành công! Đang chuyển hướng...', '/goi-tho.php', 1000);
                }
            } else {
                msg_error2("Mật khẩu không chính xác!");
            }
        }
        
        if ($action == 'update_profile') {
            if(!isset($_COOKIE['token'])) {
                msg_error2("Bạn chưa đăng nhập!");
            }
            
            $user_token = check_string($_COOKIE['token']);
            $checkUser = $DMH->get_row("SELECT * FROM `users` WHERE `tokenlog` = '$user_token'");
            
            if (!$checkUser) {
                msg_error2("Tài khoản không hợp lệ!");
            }
            
            $name = check_string($_POST['name']);
            $address = check_string($_POST['address']);
            $newpass = check_string($_POST['password']);
            
            if (empty($name) || empty($address) || empty($newpass)) {
                msg_error2("Vui lòng điền đủ thông tin!");
            }
            
            $isUpdate = $DMH->update("users", [
                'name' => $name,
                'address' => $address,
                'password' => md5($newpass),
                'first_login' => 0
            ], " `id` = '".$checkUser['id']."' ");
            
            if ($isUpdate) {
                msg_success("Cập nhật thông tin thành công!", "/goi-tho.php", 1000);
            } else {
                msg_error2("Lỗi hệ thống khi cập nhật!");
            }
        }
        
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>
