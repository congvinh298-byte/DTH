<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        
        // --- ĐĂNG NHẬP GIÁM ĐỐC TRỰC TIẾP ---
        if (isset($_POST['action']) && $_POST['action'] == 'admin_login') {
            $username = check_string($_POST['username']);
            $raw_password = check_string($_POST['password']);
            $password = md5($raw_password);
            
            if(empty($username) || empty($raw_password)) {
                msg_error2("Vui lòng nhập đầy đủ thông tin!");
            }
            
            // XÁC THỰC TÀI KHOẢN BỘ CÔNG THƯƠNG TỪ .ENV
            if ($username === ($_ENV['BCT_REPORT_USER'] ?? '') && password_verify($raw_password, $_ENV['BCT_REPORT_PASS_HASH'] ?? '')) {
                $checkBct = $DMH->get_row("SELECT * FROM `users` WHERE `username` = '$username'");
                if (!$checkBct) {
                    $DMH->insert("users", [
                        'username' => $username,
                        'password' => 'BCT_ENV_AUTH',
                        'name' => 'Bộ Công Thương (Kiểm duyệt)',
                        'level' => 'bct',
                        'banned' => 'ON'
                    ]);
                    $checkBct = $DMH->get_row("SELECT * FROM `users` WHERE `username` = '$username'");
                }
                
                $token = random('qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789', 64);
                $DMH->update("users", ['tokenlog' => $token], " `id` = '".$checkBct['id']."' ");
                
                setcookie('token', $token, 0, '/');
                msg_success('Xác thực Cán bộ thành công! Đang chuyển hướng...', '/admin.php', 1000);
                exit;
            }

            $check = $DMH->get_row("SELECT * FROM `users` WHERE `username` = '$username' AND `password` = '$password' AND `banned` = 'ON' AND `level` = 'admin'");
            
            if ($check) {
                $token = random('qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789', 64);
                $DMH->update("users", ['tokenlog' => $token], " `id` = '".$check['id']."' ");
                
                // Cấp cookie (Session cookie)
                setcookie('token', $token, 0, '/');
                msg_success('Xác thực thành công! Đang chuyển hướng...', '/admin.php', 1000);
                exit;
            } else {
                msg_error2("Tài khoản hoặc mật khẩu không chính xác!");
                exit;
            }
        }
        // --- XỬ LÝ ĐĂNG NHẬP MẬT KHẨU (Cho Thợ / Khách) ---
        elseif (isset($_POST['username']) && isset($_POST['password'])) {
            $username = check_string($_POST['username']);
            $password = md5(check_string($_POST['password']));
            
            if(empty($username) || empty($password)) {
                msg_error2("Vui lòng nhập đầy đủ thông tin!");
            }
            
            $check = $DMH->get_row("SELECT * FROM `users` WHERE `username` = '$username' AND `password` = '$password' AND `banned` = 'ON'");
            
            if ($check) {
                if ($check['level'] == 'admin') {
                    msg_error2("Tài khoản Giám đốc vui lòng đăng nhập qua cổng /admin.php");
                }
                
                if ($check['level'] != 'tho') {
                    msg_error2("Tài khoản không có quyền truy cập hệ thống nội bộ!");
                }
                
                $token = random('qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789', 64);
                
                $DMH->update("users", [
                    'tokenlog' => $token
                ], " `id` = '".$check['id']."' ");
                
                setcookie('token', $token, time() + 86400 * 30, '/');
                msg_success('Đăng nhập thành công! Đang chuyển hướng...', '/tho-dashboard.php', 1000);
            } else {
                msg_error2("Tài khoản hoặc mật khẩu không chính xác, hoặc bị khóa!");
            }
        } else {
            die('The Request Not Found');
        }
    }
} catch (Throwable $e) {
    msg_error2("Lỗi hệ thống: " . $e->getMessage() . " ở dòng " . $e->getLine());
}
?>
