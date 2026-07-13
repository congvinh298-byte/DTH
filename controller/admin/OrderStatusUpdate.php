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

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id']) || empty($_POST['status'])) {
        echo json_encode(['status' => 'error', 'msg' => 'Thieu thong tin']);
        exit;
    }

    $id = (int)$_POST['id'];
    $status = $_POST['status'];
    $validStatuses = ['pending', 'shipping', 'completed', 'cancelled'];

    if (!in_array($status, $validStatuses)) {
        echo json_encode(['status' => 'error', 'msg' => 'Trang thai khong hop le']);
        exit;
    }

    $order = $DMH->get_row("SELECT * FROM `store_orders` WHERE `id` = '$id'");
    if (!$order) {
        echo json_encode(['status' => 'error', 'msg' => 'Don hang khong ton tai']);
        exit;
    }

    $updateData = ['status' => $status];

    if ($status == 'shipping') {
        $shipperName = check_string($_POST['shipper_name'] ?? '');
        $shipperPhone = check_string($_POST['shipper_phone'] ?? '');
        $trackingCode = check_string($_POST['tracking_code'] ?? '');
        $deliveryNote = check_string($_POST['delivery_note'] ?? '');

        if (empty($shipperName)) {
            echo json_encode(['status' => 'error', 'msg' => 'Vui long nhap nguoi giao / don vi van chuyen']);
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
            'pending' => 'Dang chuan bi',
            'shipping' => 'Dang giao hang',
            'completed' => 'Da giao xong',
            'cancelled' => 'Da huy'
        ][$status];
        echo json_encode(['status' => 'success', 'msg' => 'Da cap nhat trang thai: ' . $statusText]);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Cap nhat that bai']);
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Loi he thong: ' . $e->getMessage()]);
}
