<?php
/**
 * Controller: Admin Đặt / Reset mật khẩu cho Thợ
 * 
 * POST params:
 *   id (int) — ID thợ (level='tho')
 *   new_password (string) — Mật khẩu mới (ít nhất 6 ký tự)
 * 
 * RBAC: Chỉ Admin
 * Response: JSON { status, msg }
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

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id']) || empty($_POST['new_password'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Thiếu thông tin yêu cầu']);
    exit;
}

$id = (int)$_POST['id'];
$new_password = trim($_POST['new_password']);

if (strlen($new_password) < 6) {
    echo json_encode(['status' => 'error', 'msg' => 'Mật khẩu mới phải có ít nhất 6 ký tự']);
    exit;
}

try {
    $DMH->connect();
    $tho = $DMH->get_row("SELECT id, username, name FROM `users` WHERE `id` = '$id' AND `level` = 'tho'");
    if (!$tho) {
        echo json_encode(['status' => 'error', 'msg' => 'Không tìm thấy tài khoản thợ']);
        exit;
    }

    $hashed = md5($new_password);
    $ok = $DMH->update('users', [
        'password' => $hashed
    ], "`id` = '$id'");

    if ($ok) {
        $name = $tho['name'] ?: $tho['username'];
        echo json_encode([
            'status' => 'success',
            'msg' => "Đã đổi mật khẩu cho thợ '{$name}' thành công!"
        ]);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Cập nhật thất bại, vui lòng thử lại']);
    }

} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>
