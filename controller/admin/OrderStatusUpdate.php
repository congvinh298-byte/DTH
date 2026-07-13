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

    $updateData = ['status' => $status];

    if ($status == 'shipping') {
        $shipperName = check_string($_POST['shipper_name'] ?? '');
        $shipperPhone = check_string($_POST['shipper_phone'] ?? '');
        $trackingCode = check_string($_POST['tracking_code'] ?? '');
        $deliveryNote = check_string($_POST['delivery_note'] ?? '');

        if (empty($shipperName)) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui lòng nhập tên người giao / đơn vị vận chuyển']);
            exit;
        }

        $updateData['shipper_name'] = $shipperName;
        $updateData['shipper_phone'] = $shipperPhone;
        $updateData['tracking_code'] = $trackingCode;
        $updateData['delivery_note'] = $deliveryNote;
        $updateData['shipped_at'] = date('Y-m-d H:i:s');
    }

    if ($status == 'completed') {
        $updateData['delivered_at'] = date('Y-m-d H:i:s');
    }

    $update = $DMH->update("store_orders", $updateData, "`id` = '$id'");

    if ($update) {
        $statusText = [
            'pending' => 'Đang chuẩn bị',
            'shipping' => 'Đang giao hàng',
            'completed' => 'Đã giao xong',
            'cancelled' => 'Đã hủy'
        ][$status];
        
        $teleMsg = "📦 CẬP NHẬT ĐƠN HÀNG #".$id."\nKhách: ".$order['customer_name']."\nSĐT: ".$order['phone']."\nTrạng thái mới: ".$statusText;
        if ($status == 'shipping' && !empty($updateData['shipper_name'])) {
            $teleMsg .= "\nGiao bởi: ".$updateData['shipper_name'];
            if (!empty($updateData['shipper_phone'])) $teleMsg .= " (".$updateData['shipper_phone'].")";
            if (!empty($updateData['tracking_code'])) $teleMsg .= "\nMã vận đơn: ".$updateData['tracking_code'];
        }
        send_tele($teleMsg);
        
        echo json_encode(['status' => 'success', 'msg' => 'Đã cập nhật trạng thái: ' . $statusText]);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Cập nhật thất bại']);
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>
