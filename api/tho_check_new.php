<?php
/**
 * API: Kiểm tra đơn mới cho thợ (AJAX Polling)
 * 
 * Endpoint này được gọi mỗi 30 giây từ tho-dashboard.php
 * Trả về số lượng đơn CHO_XU_LY để so sánh và phát alert nếu có đơn mới.
 * 
 * RBAC: Chỉ tho/admin mới được gọi.
 * Response: JSON { count: int, timestamp: int, orders: [{id, dichvu, ten, thoigian}] }
 */
define("IN_SITE", true);
require_once(__DIR__."/../core/config.php");

header('Content-Type: application/json; charset=utf-8');

// RBAC Check
if (!isset($_COOKIE['token']) || empty($getUser)) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'msg' => 'Chưa đăng nhập']);
    exit;
}
if ($getUser['level'] != 'tho' && $getUser['level'] != 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'msg' => 'Không có quyền truy cập']);
    exit;
}

try {
    // Lấy danh sách đơn CHO_XU_LY (chỉ lấy các trường cần thiết)
    $don_moi = $DMH->get_list(
        "SELECT `id`, `dichvu`, `ten`, `sdt`, `diachi`, `thoigian`
         FROM `dat_lich`
         WHERE `trangthai` = 'CHO_XU_LY'
         ORDER BY `id` DESC
         LIMIT 50"
    );

    $count = is_array($don_moi) ? count($don_moi) : 0;

    // Lấy ID lớn nhất để client so sánh (nếu max_id tăng → có đơn mới)
    $max_id = 0;
    if ($count > 0) {
        $max_id = (int)$don_moi[0]['id'];
    }

    echo json_encode([
        'status'    => 'success',
        'count'     => $count,
        'max_id'    => $max_id,
        'timestamp' => time(),
        'orders'    => $don_moi ?: []
    ]);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống']);
}
?>
