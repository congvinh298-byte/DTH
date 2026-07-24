<?php
/**
 * Controller: Duyệt / Từ chối báo giá phát sinh (Admin)
 *
 * POST params:
 *   id     (int)    — ID đơn trong dat_lich
 *   action (string) — 'duyet' hoặc 'tuchoi'
 *
 * RBAC: Chỉ admin.
 * Response: JSON { status, msg }
 */
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

header('Content-Type: application/json; charset=utf-8');

// RBAC Check — chỉ admin
if (empty($_SESSION['loginadmin']) && (empty($getUser) || $getUser['level'] != 'admin')) {
    echo json_encode(['status' => 'error', 'msg' => 'Không có quyền truy cập']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id']) || empty($_POST['action'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Thiếu tham số']);
    exit;
}

$id     = (int)$_POST['id'];
$action = trim($_POST['action']);

if (!in_array($action, ['duyet', 'tuchoi'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Hành động không hợp lệ']);
    exit;
}

try {
    // Kiểm tra đơn tồn tại và có báo giá chờ duyệt
    $don = $DMH->get_row(
        "SELECT d.*, u.name AS tho_name, u.username AS tho_username
         FROM `dat_lich` d
         LEFT JOIN `users` u ON u.id = d.tho_id
         WHERE d.id = '$id' AND d.phatsinh_gia > 0"
    );

    if (!$don) {
        echo json_encode(['status' => 'error', 'msg' => 'Không tìm thấy báo giá phát sinh cho đơn này']);
        exit;
    }

    if ($action === 'duyet') {
        // Duyệt báo giá — set phatsinh_duyet = 1
        $ok = $DMH->update('dat_lich', ['phatsinh_duyet' => 1], "`id` = '$id'");
        if ($ok) {
            $tho = $don['tho_name'] ?: $don['tho_username'];
            $gia = number_format((int)$don['phatsinh_gia'], 0, ',', '.');
            // Gửi Telegram thông báo đã duyệt
            send_tele("✅ DUYỆT BÁO GIÁ PHÁT SINH\nĐơn #{$id}\nThợ: {$tho}\nGiá đã duyệt: {$gia}đ\nKhách hàng sẽ được thông báo.");
            echo json_encode(['status' => 'success', 'msg' => "Đã duyệt báo giá phát sinh {$gia}đ cho đơn #{$id}"]);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Cập nhật thất bại, vui lòng thử lại']);
        }
    } else {
        // Từ chối — reset về 0 để thợ có thể gửi lại
        $ok = $DMH->update('dat_lich', [
            'phatsinh_duyet' => 0,
            'phatsinh_gia'   => 0,
            'phatsinh_mota'  => ''
        ], "`id` = '$id'");
        if ($ok) {
            $tho = $don['tho_name'] ?: $don['tho_username'];
            send_tele("❌ TỪ CHỐI BÁO GIÁ PHÁT SINH\nĐơn #{$id}\nThợ: {$tho}\nAdmin đã từ chối báo giá. Thợ có thể gửi lại.");
            echo json_encode(['status' => 'success', 'msg' => "Đã từ chối báo giá cho đơn #{$id}. Thợ có thể gửi lại."]);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Cập nhật thất bại, vui lòng thử lại']);
        }
    }

} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>
