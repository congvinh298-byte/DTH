<?php
/**
 * REST API v1: Thợ đánh dấu hoàn thành đơn hàng
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

$where = "`id` = '$id' AND `trangthai` = 'DANG_XU_LY'";
if ($user['level'] !== 'admin') {
    $where .= " AND `tho_id` = '$tho_id'";
}

$don = $DMH->get_row("SELECT * FROM `dat_lich` WHERE " . $where);
if (!$don) {
    echo json_encode(['status' => 'error', 'msg' => 'Đơn hàng này không khả dụng hoặc bạn không phải người đảm nhận!']);
    exit;
}

$ok = $DMH->update("dat_lich", ['trangthai' => 'HOAN_THANH'], "`id` = '$id'");

if ($ok) {
    // Trừ phí nền tảng 20,000đ
    $DMH->tru("users", "money", 20000, "`id` = '$tho_id'");

    $tho_name = $user['name'] ?: $user['username'];
    $khach    = $don['ten']   ?? '?';
    $sdt      = $don['sdt']   ?? '?';
    $dichvu   = $don['dichvu'] ?? '?';
    $link_dg  = 'https://' . $_SERVER['SERVER_NAME'] . '/tra-cuu-don.php?sdt=' . urlencode($sdt);
    
    $text = "✅ ĐƠN HOÀN THÀNH (App)\nĐơn #{$id}\nDịch vụ: {$dichvu}\nKhách: {$khach} | SĐT: {$sdt}\nThợ: {$tho_name}\n—\n💬 Link đánh giá: {$link_dg}";
    send_tele($text);

    echo json_encode([
        'status' => 'success',
        'msg' => 'Tuyệt vời! Bạn đã hoàn thành đơn hàng. Đã trừ 20.000đ phí nền tảng.'
    ]);
} else {
    echo json_encode(['status' => 'error', 'msg' => 'Cập nhật trạng thái thất bại, vui lòng thử lại']);
}
?>
