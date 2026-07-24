<?php
/**
 * Helper: Xác thực Token cho API v1 Thợ (Hỗ trợ Multi-session & Bearer Token)
 */
if (!defined('IN_SITE')) {
    define('IN_SITE', true);
}
require_once(__DIR__."/../../../core/config.php");
require_once(__DIR__."/../../../core/function.php");

header('Content-Type: application/json; charset=utf-8');

// Tự động tạo bảng user_sessions nếu chưa có
$DMH->connect();
$DMH->query("CREATE TABLE IF NOT EXISTS `user_sessions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `token` varchar(64) NOT NULL,
  `platform` varchar(50) DEFAULT 'web',
  `created_at` int(11) NOT NULL,
  `last_active` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `token` (`token`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

function getBearerToken() {
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER["Authorization"]);
    } else if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $headers = trim($_SERVER["HTTP_AUTHORIZATION"]);
    } else if (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        $requestHeaders = array_combine(array_map('ucwords', array_keys($requestHeaders)), array_values($requestHeaders));
        if (isset($requestHeaders['Authorization'])) {
            $headers = trim($requestHeaders['Authorization']);
        }
    }
    if (!empty($headers)) {
        if (preg_match('/Bearer\s(\S+)/i', $headers, $matches)) {
            return $matches[1];
        }
    }
    if (!empty($_REQUEST['token'])) {
        return trim($_REQUEST['token']);
    }
    if (!empty($_COOKIE['token'])) {
        return trim($_COOKIE['token']);
    }
    return null;
}

function requireThoAuth() {
    global $DMH;
    $token = getBearerToken();
    if (empty($token)) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'msg' => 'Chưa cung cấp token xác thực']);
        exit;
    }

    $token_safe = mysqli_real_escape_string($DMH->ketnoi, $token);
    
    // 1. Kiểm tra trong user_sessions
    $session = $DMH->get_row("SELECT s.*, u.* FROM `user_sessions` s JOIN `users` u ON s.user_id = u.id WHERE s.token = '$token_safe' AND u.banned = 'ON'");
    
    // 2. Fallback: kiểm tra tokenlog trong users
    if (!$session) {
        $user = $DMH->get_row("SELECT * FROM `users` WHERE `tokenlog` = '$token_safe' AND `banned` = 'ON'");
        if ($user) {
            $session = $user;
            $session['user_id'] = $user['id'];
        }
    }

    if (!$session) {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'msg' => 'Token không hợp lệ hoặc phiên đăng nhập đã hết hạn']);
        exit;
    }

    if ($session['level'] !== 'tho' && $session['level'] !== 'admin') {
        http_response_code(403);
        echo json_encode(['status' => 'error', 'msg' => 'Tài khoản không có quyền thợ kỹ thuật']);
        exit;
    }

    // Cập nhật last_active nếu là session
    if (isset($session['id']) && isset($session['user_id'])) {
        $DMH->query("UPDATE `user_sessions` SET `last_active` = '" . time() . "' WHERE `token` = '$token_safe'");
    }

    return $session;
}
?>
