<?php
/**
 * REST API v1: Danh sách đơn hàng cho Thợ
 * 
 * GET params:
 *   tab (string: 'cho' | 'cuatoi' | 'done', default 'cho')
 *   limit (int, default 50)
 */
define("IN_SITE", true);
require_once(__DIR__."/../../v1/tho/_auth.php");

$user = requireThoAuth();
$tho_id = (int)$user['user_id'];
$tab = check_string($_GET['tab'] ?? 'cho');
$limit = min((int)($_GET['limit'] ?? 50), 100);

if ($tab === 'cuatoi') {
    $where = "`trangthai` = 'DANG_XU_LY' AND `tho_id` = '$tho_id'";
} elseif ($tab === 'done') {
    $where = "`trangthai` = 'HOAN_THANH' AND `tho_id` = '$tho_id'";
} else { // 'cho'
    $where = "`trangthai` = 'CHO_XU_LY'";
}

$orders = $DMH->get_list("SELECT * FROM `dat_lich` WHERE {$where} ORDER BY `id` DESC LIMIT {$limit}");

if (!is_array($orders)) $orders = [];

// Thống kê số lượng badge cho các tab
$count_cho = (int)($DMH->get_row("SELECT COUNT(*) as c FROM `dat_lich` WHERE `trangthai` = 'CHO_XU_LY'")['c'] ?? 0);
$count_cuatoi = (int)($DMH->get_row("SELECT COUNT(*) as c FROM `dat_lich` WHERE `trangthai` = 'DANG_XU_LY' AND `tho_id` = '$tho_id'")['c'] ?? 0);

echo json_encode([
    'status' => 'success',
    'tab'    => $tab,
    'counts' => [
        'cho'    => $count_cho,
        'cuatoi' => $count_cuatoi,
    ],
    'orders' => array_map(function($o) {
        return [
            'id'             => (int)$o['id'],
            'ten'            => $o['ten'],
            'sdt'            => $o['sdt'],
            'diachi'         => $o['diachi'],
            'yeucau'         => $o['yeucau'],
            'dichvu'         => $o['dichvu'],
            'thoigian'       => (int)$o['thoigian'],
            'trangthai'      => $o['trangthai'],
            'tho_id'         => (int)$o['tho_id'],
            'phatsinh_mota'  => $o['phatsinh_mota'],
            'phatsinh_gia'   => (int)$o['phatsinh_gia'],
            'phatsinh_duyet' => (int)$o['phatsinh_duyet'],
            'nghiemthu_note' => $o['nghiemthu_note'],
            'nghiemthu_anh'  => $o['nghiemthu_anh'],
            'danhgia_sao'    => $o['danhgia_sao'] ? (int)$o['danhgia_sao'] : null,
            'danhgia_noidung'=> $o['danhgia_noidung'],
        ];
    }, $orders)
]);
?>
