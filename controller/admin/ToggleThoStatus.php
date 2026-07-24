<?php
/**
 * Controller: Khóa / Mở khóa tài khoản thợ (Admin)
 *
 * POST params:
 *   id (int) — ID user trong bảng users (level='tho')
 *
 * RBAC: Chỉ admin.
 * Response: JSON { status, msg, new_state: 'ON'|'OFF' }
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Thiếu tham số ID']);
    exit;
}

$id = (int)$_POST['id'];

try {
    // Lấy thông tin thợ — chỉ cho phép toggle tài khoản có level='tho'
    $tho = $DMH->get_row("SELECT id, username, name, banned FROM `users` WHERE `id` = '$id' AND `level` = 'tho'");

    if (!$tho) {
        echo json_encode(['status' => 'error', 'msg' => 'Không tìm thấy tài khoản thợ']);
        exit;
    }

    // Toggle trạng thái
    $new_state = ($tho['banned'] === 'ON') ? 'OFF' : 'ON';
    $label     = ($new_state === 'ON') ? 'Mở khóa' : 'Khóa';

    $ok = $DMH->update('users', ['banned' => $new_state], "`id` = '$id'");

    if ($ok) {
        $name = $tho['name'] ?: $tho['username'];
        echo json_encode([
            'status'    => 'success',
            'msg'       => "{$label} tài khoản thợ '{$name}' thành công.",
            'new_state' => $new_state
        ]);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Cập nhật thất bại, vui lòng thử lại']);
    }

} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>
