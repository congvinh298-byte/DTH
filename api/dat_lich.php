<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

define("IN_SITE", true);
require_once(__DIR__."/../core/config.php");
require_once(__DIR__."/../core/function.php");

if (!isset($DMH)) {
    $DMH = new DMH();
}

$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    // Fallback to form data
    $input = $_POST;
}

$ten = isset($input['ten']) ? check_string($input['ten']) : '';
$sdt = isset($input['sdt']) ? check_string($input['sdt']) : '';
$diachi = isset($input['diachi']) ? check_string($input['diachi']) : '';
$dichvu = isset($input['dichvu']) ? check_string($input['dichvu']) : '';
$yeucau = isset($input['yeucau']) ? check_string($input['yeucau']) : '';

if (empty($ten) || empty($sdt) || empty($diachi)) {
    echo json_encode([
        'status' => 'error',
        'msg' => 'Vui lòng nhập đầy đủ thông tin: Tên, SĐT, Địa chỉ.'
    ]);
    exit;
}

$thoigian = time();
$trangthai = 'CHO_XU_LY';

$isInsert = $DMH->insert("dat_lich", [
    'ten' => $ten,
    'sdt' => $sdt,
    'diachi' => $diachi,
    'dichvu' => $dichvu,
    'yeucau' => $yeucau,
    'thoigian' => $thoigian,
    'trangthai' => $trangthai,
    'tho_id' => 0
]);

if ($isInsert) {
    // (Tùy chọn) Gửi thông báo Telegram cho Admin
    $admin_telegram_id = isset($_ENV['ADMIN_TELEGRAM_ID']) ? $_ENV['ADMIN_TELEGRAM_ID'] : null;
    if ($admin_telegram_id) {
        $msg = "🔔 CÓ KHÁCH HÀNG MỚI ĐẶT LỊCH TỪ ZALO MINI APP:\n";
        $msg .= "- Tên: $ten\n";
        $msg .= "- SĐT: $sdt\n";
        $msg .= "- Dịch vụ: $dichvu\n";
        $msg .= "- Địa chỉ: $diachi\n";
        $msg .= "- Yêu cầu: $yeucau\n";
        sendTelegram($admin_telegram_id, $msg);
    }
    
    echo json_encode([
        'status' => 'success',
        'msg' => 'Đặt lịch thành công! Chúng tôi sẽ liên hệ lại ngay.'
    ]);
} else {
    echo json_encode([
        'status' => 'error',
        'msg' => 'Đã xảy ra lỗi khi lưu vào cơ sở dữ liệu. Vui lòng thử lại.'
    ]);
}
exit;
