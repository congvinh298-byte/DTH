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

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
        echo json_encode(['status' => 'error', 'msg' => 'Thiếu thông tin']);
        exit;
    }

    $id = (int)$_POST['id'];

    $don = $DMH->get_row("SELECT * FROM `dat_lich` WHERE `id` = '$id' AND `trangthai` IN ('CHO_XU_LY','DANG_XU_LY')");
    if (!$don) {
        echo json_encode(['status' => 'error', 'msg' => 'Đơn không khả dụng để hủy']);
        exit;
    }

    $update = $DMH->update("dat_lich", [
        'trangthai' => 'DA_HUY',
        'tho_id' => 0,
        'thoigian' => time()
    ], "`id` = '$id'");

    if ($update) {
        echo json_encode(['status' => 'success', 'msg' => 'Đã hủy đơn.']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Hủy thất bại.']);
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>
