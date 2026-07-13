<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
        echo json_encode(['status' => 'error', 'msg' => 'Thiếu thông tin đơn hàng']);
        exit;
    }

    $id = (int)$_POST['id'];

    // Chỉ cho phép hủy đơn đang ở trạng thái CHO_XU_LY
    $check = $DMH->get_row("SELECT * FROM `dat_lich` WHERE `id` = '$id' AND `trangthai` = 'CHO_XU_LY'");

    if (!$check) {
        echo json_encode(['status' => 'error', 'msg' => 'Đơn không tồn tại hoặc không thể hủy']);
        exit;
    }

    $update = $DMH->update("dat_lich", [
        'trangthai' => 'DA_HUY',
        'thoigian' => time()
    ], "`id` = '$id'");

    if ($update) {
        echo json_encode(['status' => 'success', 'msg' => 'Đã hủy đơn thành công.']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Không thể cập nhật trạng thái đơn.']);
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>
