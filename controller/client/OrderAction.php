<?php
error_reporting(0);
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

header('Content-Type: application/json');

$is_logged_in = isset($_COOKIE['token']);
$user_id = $getUser['id'] ?? 0;

if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if($action == 'confirm_received') {
        if(!$is_logged_in) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng đăng nhập']);
            exit;
        }

        $id = (int)$_POST['id'];
        
        // Cập nhật trạng thái
        $DMH->query("UPDATE `store_orders` SET `status` = 'completed' WHERE `id` = '$id' AND `user_id` = '$user_id'");
        
        // Báo cho Admin
        $order = $DMH->get_row("SELECT * FROM `store_orders` WHERE `id` = '$id'");
        if($order) {
            send_tele("✅ Khách hàng " . $order['customer_name'] . " ĐÃ NHẬN HÀNG THÀNH CÔNG cho đơn #" . $id . ".");
        }

        echo json_encode(['status' => 'success']);
        exit;
    }
}
?>
