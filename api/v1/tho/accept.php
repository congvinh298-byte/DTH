<?php
/**
 * REST API v1: Thợ chốt nhận đơn hàng
 * 
 * POST JSON or Form-data:
 *   id (int) — ID đơn dat_lich
 */
define("IN_SITE", true);
require_once(__DIR__."/../../v1/tho/_auth.php");

$user = requireThoAuth();
$tho_id = (int)$user['user_id'];

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$id = (int)($input['id'] ?? 0);

if (!$id) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Thiếu ID đơn hàng']);
    exit;
}

// Kiểm tra số dư thợ
$fresh_user = $DMH->get_row("SELECT * FROM `users` WHERE `id` = '$tho_id'");
if ((int)$fresh_user['money'] < 0) {
    echo json_encode([
        'status' => 'error',
        'msg' => 'Tài khoản của bạn đang bị âm tiền (' . number_format($fresh_user['money']) . 'đ). Vui lòng nạp thêm tiền để tiếp tục nhận đơn!'
    ]);
    exit;
}

// Kiểm tra đơn hàng có sẵn không
$don = $DMH->get_row("SELECT * FROM `dat_lich` WHERE `id` = '$id' AND `trangthai` = 'CHO_XU_LY'");

if (!$don) {
    echo json_encode(['status' => 'error', 'msg' => 'Đơn hàng này đã có người nhận hoặc không khả dụng!']);
    exit;
}

// Nhận đơn
$ok = $DMH->update("dat_lich", [
    'trangthai' => 'DANG_XU_LY',
    'tho_id'    => $tho_id
], "`id` = '$id'");

if ($ok) {
    $tho_name = $fresh_user['name'] ?: $fresh_user['username'];
    send_tele("🛠 THỢ NHẬN ĐƠN (App)\nĐơn #{$id} - {$don['dichvu']}\nThợ: {$tho_name} (SĐT: {$fresh_user['phone']})\nKhách: {$don['ten']} - {$don['sdt']}");
    
    echo json_encode(['status' => 'success', 'msg' => 'Nhận đơn thành công! Vui lòng liên hệ khách hàng ngay.']);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Có lỗi xảy ra khi nhận đơn']);
}
?>
