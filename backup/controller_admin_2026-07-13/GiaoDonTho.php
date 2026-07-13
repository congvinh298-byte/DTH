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

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id']) || empty($_POST['tho_id'])) {
        echo json_encode(['status' => 'error', 'msg' => 'Thiếu thông tin']);
        exit;
    }

    $id = (int)$_POST['id'];
    $tho_id = (int)$_POST['tho_id'];

    // Kiểm tra thợ tồn tại
    $tho = $DMH->get_row("SELECT id FROM `users` WHERE `id` = '$tho_id' AND `level` = 'tho'");
    if (!$tho) {
        echo json_encode(['status' => 'error', 'msg' => 'Thợ không tồn tại']);
        exit;
    }

    $don = $DMH->get_row("SELECT * FROM `dat_lich` WHERE `id` = '$id' AND `trangthai` IN ('CHO_XU_LY','DANG_XU_LY')");
    if (!$don) {
        echo json_encode(['status' => 'error', 'msg' => 'Đơn không khả dụng']);
        exit;
    }

    $update = $DMH->update("dat_lich", [
        'trangthai' => 'DANG_XU_LY',
        'tho_id' => $tho_id
    ], "`id` = '$id'");

    if ($update) {
        echo json_encode(['status' => 'success', 'msg' => 'Đã giao đơn cho thợ.']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Cập nhật thất bại.']);
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>
