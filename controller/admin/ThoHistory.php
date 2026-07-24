<?php
/**
 * Controller: Lịch sử đơn hàng của 1 thợ cụ thể (Admin)
 *
 * GET params:
 *   tho_id (int)    — ID thợ
 *   limit  (int)    — Số đơn tối đa (default 30)
 *
 * RBAC: Chỉ admin.
 * Response: JSON { status, tho: {...}, orders: [...], stats: {...} }
 */
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

header('Content-Type: application/json; charset=utf-8');

// RBAC Check
if (empty($_SESSION['loginadmin']) && (empty($getUser) || $getUser['level'] != 'admin')) {
    echo json_encode(['status' => 'error', 'msg' => 'Không có quyền truy cập']);
    exit;
}

if (empty($_GET['tho_id'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Thiếu tham số tho_id']);
    exit;
}

$tho_id = (int)$_GET['tho_id'];
$limit  = min((int)($_GET['limit'] ?? 30), 100); // Tối đa 100

try {
    // Thông tin thợ
    $tho = $DMH->get_row(
        "SELECT id, username, name, fullname, phone, banned, money
         FROM `users`
         WHERE `id` = '$tho_id' AND `level` = 'tho'"
    );

    if (!$tho) {
        echo json_encode(['status' => 'error', 'msg' => 'Không tìm thấy thợ']);
        exit;
    }

    // Danh sách đơn gần nhất
    $orders = $DMH->get_list(
        "SELECT id, ten, sdt, dichvu, diachi, thoigian, trangthai,
                phatsinh_gia, phatsinh_duyet, danhgia_sao
         FROM `dat_lich`
         WHERE `tho_id` = '$tho_id'
         ORDER BY `id` DESC
         LIMIT $limit"
    );
    if (!is_array($orders)) $orders = [];

    // Thống kê tổng hợp
    $stats_row = $DMH->get_row(
        "SELECT
            COUNT(*) AS total,
            SUM(CASE WHEN trangthai = 'HOAN_THANH' THEN 1 ELSE 0 END) AS hoan_thanh,
            SUM(CASE WHEN trangthai = 'DANG_XU_LY' THEN 1 ELSE 0 END) AS dang_xu_ly,
            SUM(CASE WHEN trangthai = 'DA_HUY'     THEN 1 ELSE 0 END) AS da_huy,
            SUM(CASE WHEN trangthai = 'HOAN_THANH' AND phatsinh_duyet = 1 THEN phatsinh_gia ELSE 0 END) AS tong_phatsinh,
            AVG(CASE WHEN danhgia_sao > 0 THEN danhgia_sao END) AS diem_trung_binh
         FROM `dat_lich`
         WHERE `tho_id` = '$tho_id'"
    );

    echo json_encode([
        'status' => 'success',
        'tho'    => [
            'id'       => (int)$tho['id'],
            'name'     => $tho['name'] ?: $tho['fullname'] ?: $tho['username'],
            'username' => $tho['username'],
            'phone'    => $tho['phone'] ?? '-',
            'banned'   => $tho['banned'],
            'money'    => (int)$tho['money']
        ],
        'stats'  => [
            'total'          => (int)($stats_row['total'] ?? 0),
            'hoan_thanh'     => (int)($stats_row['hoan_thanh'] ?? 0),
            'dang_xu_ly'     => (int)($stats_row['dang_xu_ly'] ?? 0),
            'da_huy'         => (int)($stats_row['da_huy'] ?? 0),
            'tong_phatsinh'  => (int)($stats_row['tong_phatsinh'] ?? 0),
            'diem_tb'        => $stats_row['diem_trung_binh'] ? round((float)$stats_row['diem_trung_binh'], 1) : null
        ],
        'orders' => $orders
    ]);

} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>
