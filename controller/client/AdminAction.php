<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

// Auth Check for Admin
if(!isset($_COOKIE['token'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Unauthorized']);
    exit;
}
$user_token = check_string($_COOKIE['token']);
$checkAdmin = $DMH->get_row("SELECT * FROM `users` WHERE `tokenlog` = '$user_token' AND `level` = 'admin'");
if (!$checkAdmin) {
    echo json_encode(['status' => 'error', 'msg' => 'Forbidden']);
    exit;
}

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
        
        $action = $_POST['action'];
        
        if ($action == 'find_customer') {
            $phone = check_string($_POST['phone']);
            $customer = $DMH->get_row("SELECT * FROM `users` WHERE `phone` = '$phone' AND `level` = 'user'");
            
            if ($customer) {
                echo json_encode([
                    'status' => 'success', 
                    'data' => [
                        'name' => $customer['name'],
                        'address' => $customer['address'],
                        'points' => (int)$customer['points']
                    ]
                ]);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Not found']);
            }
            exit;
        }
        
        if ($action == 'add_points') {
            $phone = check_string($_POST['phone']);
            $points = (int)$_POST['points'];
            
            if ($points <= 0) {
                echo json_encode(['status' => 'error', 'msg' => 'Số điểm không hợp lệ']);
                exit;
            }
            
            $customer = $DMH->get_row("SELECT * FROM `users` WHERE `phone` = '$phone' AND `level` = 'user'");
            
            if ($customer) {
                $new_points = (int)$customer['points'] + $points;
                $update = $DMH->update("users", ['points' => $new_points], "`id` = '".$customer['id']."'");
                
                if ($update) {
                    echo json_encode(['status' => 'success']);
                } else {
                    echo json_encode(['status' => 'error', 'msg' => 'Lỗi DB']);
                }
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Không tìm thấy KH']);
            }
            exit;
        }
        
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Exception: ' . $e->getMessage()]);
}
?>
