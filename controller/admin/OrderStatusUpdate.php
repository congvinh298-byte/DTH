<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

header('Content-Type: application/json');

try {
    if (!isset($_COOKIE['token']) || empty($getUser) || $getUser['level'] != 'admin') {
        echo json_encode(['status' => 'error', 'msg' => 'Không có quyền']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id']) || empty($_POST['status'])) {
        echo json_encode(['status' => 'error', 'msg' => 'Thiếu thông tin']);
        exit;
    }

    $id = (int)$_POST['id'];
    $status = $_POST['status'];
    $validStatuses = ['pending', 'shipping', 'completed', 'cancelled'];

    if (!in_array($status, $validStatuses)) {
        echo json_encode(['status' => 'error', 'msg' => 'Trạng thái không hợp lệ']);
        exit;
    }

    $order = $DMH->get_row("SELECT * FROM `store_orders` WHERE `id` = '$id'");
    if (!$order) {
        echo json_encode(['status' => 'error', 'msg' => 'Đơn hàng không tồn tại']);
        exit;
    }

    $update = $DMH->update("store_orders", [
        'status' => $status
    ], "`id` = '$id'");

    if ($update) {
        $statusText = [
            'pending' => 'Chờ xử lý',
            'shipping' => 'Đang giao hàng',
            'completed' => 'Đã hoàn thành',
            'cancelled' => 'Đã hủy'
        ][$status];
        
        send_tele("📦 CẬP NHẬT ĐƠN HÀNG #".$id."\nKhách: ".$order['customer_name']."\nSĐT: ".$order['phone']."\nTrạng thái mới: ".$statusText);
        
        echo json_encode(['status' => 'success', 'msg' => 'Đã cập nhật trạng thái: ' . $statusText]);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Cập nhật thất bại']);
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>
