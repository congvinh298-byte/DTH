<?php
/**
 * REST API v1: Thợ gửi Báo giá phát sinh
 * 
 * POST JSON or Form-data:
 *   id (int) — ID đơn dat_lich
 *   mota (string) — Mô tả phát sinh
 *   gia (int) — Số tiền phát sinh (VND)
 */
define("IN_SITE", true);
require_once(__DIR__."/../../v1/tho/_auth.php");

$user = requireThoAuth();
$tho_id = (int)$user['user_id'];

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$id   = (int)($input['id'] ?? 0);
$mota = check_string($input['mota'] ?? '');
$gia  = (int)($input['gia'] ?? 0);

if (!$id || empty($mota) || $gia <= 0) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Vui lòng nhập đầy đủ mô tả và số tiền hợp lệ']);
    exit;
}

$where = "`id` = '$id' AND `trangthai` = 'DANG_XU_LY'";
if ($user['level'] !== 'admin') {
    $where .= " AND `tho_id` = '$tho_id'";
}

$don = $DMH->get_row("SELECT * FROM `dat_lich` WHERE " . $where);
if (!$don) {
    echo json_encode(['status' => 'error', 'msg' => 'Đơn không khả dụng hoặc bạn không phải người đảm nhận']);
    exit;
}

$ok = $DMH->update("dat_lich", [
    'phatsinh_mota'  => $mota,
    'phatsinh_gia'   => $gia,
    'phatsinh_duyet' => 0
], "`id` = '$id'");

if ($ok) {
    $tho_name = $user['name'] ?: $user['username'];
    $text = "📝 BÁO GIÁ PHÁT SINH (App)\nĐơn #{$id}\nThợ: {$tho_name}\nMô tả: {$mota}\nGiá: " . number_format($gia) . "đ\nVui lòng duyệt trong admin.";
    send_tele($text);
    echo json_encode(['status' => 'success', 'msg' => 'Đã gửi báo giá phát sinh. Vui lòng chờ Admin duyệt!']);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Gửi báo giá thất bại']);
}
?>
