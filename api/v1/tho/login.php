<?php
/**
 * REST API v1: Đăng nhập Thợ (Multi-platform & Multi-session)
 * 
 * POST JSON or Form-data:
 *   username (string)
 *   password (string)
 *   platform (string, optional: 'zalo_miniapp', 'app_mobile', 'web')
 * 
 * Response:
 *   JSON { status, msg, token, user }
 */
define("IN_SITE", true);
require_once(__DIR__."/../../v1/tho/_auth.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'msg' => 'Method Not Allowed']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    $input = $_POST;
}

$username = check_string($input['username'] ?? '');
$raw_password = check_string($input['password'] ?? '');
$platform = check_string($input['platform'] ?? 'app_mobile');

if (empty($username) || empty($raw_password)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Vui lòng nhập đầy đủ tên đăng nhập và mật khẩu']);
    exit;
}

$password = md5($raw_password);

$user = $DMH->get_row("SELECT * FROM `users` WHERE `username` = '$username' AND `password` = '$password'");

if (!$user) {
    http_response_code(401);
    echo json_encode(['status' => 'error', 'msg' => 'Tên đăng nhập hoặc mật khẩu không chính xác']);
    exit;
}

if ($user['banned'] !== 'ON') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'msg' => 'Tài khoản thợ của bạn đang bị khóa. Vui lòng liên hệ Admin.']);
    exit;
}

if ($user['level'] !== 'tho' && $user['level'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'msg' => 'Tài khoản không phải là Thợ kỹ thuật']);
    exit;
}

// Tạo session token mới (Multi-session support)
$token = random('qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM0123456789', 64);
$now = time();

$DMH->insert("user_sessions", [
    'user_id'     => $user['id'],
    'token'       => $token,
    'platform'    => $platform,
    'created_at'  => $now,
    'last_active' => $now,
]);

// Cập nhật tokenlog dự phòng cho single-session web
$DMH->update("users", ['tokenlog' => $token], "`id` = '{$user['id']}'");

echo json_encode([
    'status' => 'success',
    'msg' => 'Đăng nhập thành công',
    'token' => $token,
    'user' => [
        'id'       => (int)$user['id'],
        'username' => $user['username'],
        'name'     => $user['name'] ?: $user['fullname'] ?: $user['username'],
        'phone'    => $user['phone'] ?? '',
        'level'    => $user['level'],
        'money'    => (int)$user['money'],
    ]
]);
?>
