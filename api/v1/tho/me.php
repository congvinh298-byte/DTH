<?php
/**
 * REST API v1: Thông tin cá nhân & Thống kê của Thợ
 * 
 * GET (yêu cầu Bearer Token)
 */
define("IN_SITE", true);
require_once(__DIR__."/../../v1/tho/_auth.php");

$user = requireThoAuth();
$tho_id = (int)$user['user_id'];

// Lấy thông tin mới nhất từ DB
$fresh_user = $DMH->get_row("SELECT * FROM `users` WHERE `id` = '$tho_id'");

// Thống kê nhanh
$stats = $DMH->get_row(
    "SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN trangthai = 'HOAN_THANH' THEN 1 ELSE 0 END) AS hoan_thanh,
        SUM(CASE WHEN trangthai = 'DANG_XU_LY' THEN 1 ELSE 0 END) AS dang_xu_ly,
        AVG(CASE WHEN danhgia_sao > 0 THEN danhgia_sao END) AS diem_tb
     FROM `dat_lich`
     WHERE `tho_id` = '$tho_id'"
);

echo json_encode([
    'status' => 'success',
    'user' => [
        'id'       => (int)$fresh_user['id'],
        'username' => $fresh_user['username'],
        'name'     => $fresh_user['name'] ?: $fresh_user['fullname'] ?: $fresh_user['username'],
        'phone'    => $fresh_user['phone'] ?? '',
        'level'    => $fresh_user['level'],
        'money'    => (int)$fresh_user['money'],
    ],
    'stats' => [
        'total'      => (int)($stats['total'] ?? 0),
        'hoan_thanh' => (int)($stats['hoan_thanh'] ?? 0),
        'dang_xu_ly' => (int)($stats['dang_xu_ly'] ?? 0),
        'diem_tb'    => $stats['diem_tb'] ? round((float)$stats['diem_tb'], 1) : null
    ]
]);
?>
