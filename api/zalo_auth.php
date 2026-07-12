<?php
define("IN_SITE", true);
require_once(__DIR__."/../core/config.php");
require_once(__DIR__."/../core/function.php");

// Set CORS cho Zalo Mini App
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit(0);
}

try {
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    if ($action == 'login_with_phone') {
        // Trong thực tế, bạn sẽ nhận được Access Token của Zalo từ client
        // và gọi Zalo Open API để lấy SĐT. Ở đây giả lập SĐT đã được lấy:
        $phone = check_string($_POST['phone'] ?? '');
        $zalo_id = check_string($_POST['zalo_id'] ?? '');
        
        if (empty($phone)) {
            echo json_encode(['status' => 'error', 'msg' => 'Không lấy được số điện thoại từ Zalo']);
            exit;
        }
        
        $check = $DMH->get_row("SELECT * FROM `users` WHERE `phone` = '$phone' AND `level` = 'user'");
        
        if ($check) {
            // Đã tồn tại, đăng nhập luôn
            $token = random('qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789', 64);
            $DMH->update("users", ['tokenlog' => $token, 'zalo_id' => $zalo_id], " `id` = '".$check['id']."' ");
            
            echo json_encode([
                'status' => 'success', 
                'msg' => 'Đăng nhập thành công',
                'token' => $token,
                'user' => [
                    'name' => $check['name'],
                    'phone' => $check['phone'],
                    'points' => $check['points'],
                    'first_login' => $check['first_login']
                ]
            ]);
        } else {
            // Đăng ký mới
            $token = random('qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789', 64);
            $password = md5(random('0123456789', 6)); // Mật khẩu ngẫu nhiên vì dùng Zalo auth
            
            $isInsert = $DMH->insert("users", [
                'username' => 'kh_' . $phone,
                'phone' => $phone,
                'password' => $password,
                'level' => 'user',
                'first_login' => 1,
                'banned' => 'ON',
                'points' => 0,
                'tokenlog' => $token,
                'zalo_id' => $zalo_id
            ]);
            
            if ($isInsert) {
                echo json_encode([
                    'status' => 'success', 
                    'msg' => 'Đăng ký thành công',
                    'token' => $token,
                    'user' => [
                        'name' => null,
                        'phone' => $phone,
                        'points' => 0,
                        'first_login' => 1
                    ]
                ]);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Lỗi tạo tài khoản!']);
            }
        }
        exit;
    }
    
    echo json_encode(['status' => 'error', 'msg' => 'Invalid action']);
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>
