<?php
/**
 * REST API v1: Polling kiểm tra đơn mới cho App Thợ
 * 
 * GET (yêu cầu Bearer Token)
 */
define("IN_SITE", true);
require_once(__DIR__."/../../v1/tho/_auth.php");

$user = requireThoAuth();

$don_moi = $DMH->get_list(
    "SELECT `id`, `dichvu`, `ten`, `sdt`, `diachi`, `thoigian`
     FROM `dat_lich`
     WHERE `trangthai` = 'CHO_XU_LY'
     ORDER BY `id` DESC
     LIMIT 50"
);

$count = is_array($don_moi) ? count($don_moi) : 0;
$max_id = $count > 0 ? (int)$don_moi[0]['id'] : 0;

echo json_encode([
    'status'    => 'success',
    'count'     => $count,
    'max_id'    => $max_id,
    'timestamp' => time(),
    'orders'    => $don_moi ?: []
]);
?>
