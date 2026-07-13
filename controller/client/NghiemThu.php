<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

header('Content-Type: application/json');

try {
    if (!isset($_COOKIE['token']) || empty($getUser)) {
        echo json_encode(['status' => 'error', 'msg' => 'Vui lòng đăng nhập']);
        exit;
    }
    if ($getUser['level'] != 'tho' && $getUser['level'] != 'admin') {
        echo json_encode(['status' => 'error', 'msg' => 'Không có quyền']);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id']) || empty($_POST['anh'])) {
        echo json_encode(['status' => 'error', 'msg' => 'Thiếu thông tin']);
        exit;
    }

    $id = (int)$_POST['id'];
    $anh = check_string($_POST['anh']);
    $note = check_string($_POST['note'] ?? '');
    $my_id = $getUser['id'];

    $where = "`id` = '$id' AND `trangthai` = 'DANG_XU_LY'";
    if ($getUser['level'] != 'admin') {
        $where .= " AND `tho_id` = '$my_id'";
    }

    $don = $DMH->get_row("SELECT * FROM `dat_lich` WHERE " . $where);
    if (!$don) {
        echo json_encode(['status' => 'error', 'msg' => 'Đơn không khả dụng']);
        exit;
    }

    $update = $DMH->update("dat_lich", [
        'nghiemthu_anh' => $anh,
        'nghiemthu_note' => $note
    ], "`id` = '$id'");

    if ($update) {
        echo json_encode(['status' => 'success', 'msg' => 'Đã lưu ảnh nghiệm thu.']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Lưu thất bại']);
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>
