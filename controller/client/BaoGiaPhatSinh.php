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

    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id']) || empty($_POST['mota']) || !isset($_POST['gia'])) {
        echo json_encode(['status' => 'error', 'msg' => 'Thiếu thông tin']);
        exit;
    }

    $id = (int)$_POST['id'];
    $mota = check_string($_POST['mota']);
    $gia = (int)$_POST['gia'];
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
        'phatsinh_mota' => $mota,
        'phatsinh_gia' => $gia,
        'phatsinh_duyet' => 0
    ], "`id` = '$id'");

    if ($update) {
        $text = "📝 BÁO GIÁ PHÁT SINH\nĐơn #".$id."\nThợ: ".($getUser['name'] ?: $getUser['username'])."\nMô tả: ".$mota."\nGiá: ".number_format($gia)."đ\nVui lòng duyệt trong admin.";
        send_tele($text);
        echo json_encode(['status' => 'success', 'msg' => 'Đã gửi báo giá phát sinh. Chờ duyệt.']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Gửi báo giá thất bại']);
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>
