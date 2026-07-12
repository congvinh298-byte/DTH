<?php
define("IN_SITE", true);
require_once(__DIR__."/../core/config.php");
require_once(__DIR__."/../core/function.php");

// Zalo sẽ gửi request POST dạng JSON tới Webhook này
$payload = file_get_contents('php://input');
$event = json_decode($payload, true);

if (!$event) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'msg' => 'Invalid payload']);
    exit;
}

// Log lại webhook để debug (tùy chọn)
file_put_contents(__DIR__.'/../webhook_log.txt', date('Y-m-d H:i:s') . " - " . $payload . "\n", FILE_APPEND);

// Xác thực Webhook (tuỳ chọn nhưng nên làm) bằng cách kiểm tra mac (mã hóa HMAC)
// $mac = hash_hmac('sha256', $event['app_id'] . $payload, $_ENV['ZALO_APP_SECRET']);
// if ($mac !== $event['mac']) {
//    http_response_code(401);
//    exit;
// }

try {
    // Xử lý các sự kiện từ Zalo
    $eventType = $event['event_name'] ?? '';
    
    switch ($eventType) {
        case 'user_submit_info': // Sự kiện khi người dùng gửi thông tin qua Mini App
        case 'user_send_message':
            // TODO: Lưu vào Database hoặc gửi thông báo cho Thợ
            // $DMH->insert("orders", [...]);
            break;
            
        case 'oa_send_message_status':
            // TODO: Cập nhật trạng thái tin nhắn
            break;
            
        default:
            // Các sự kiện khác
            break;
    }
    
    // Luôn trả về 200 OK để Zalo biết đã nhận được Webhook
    http_response_code(200);
    echo json_encode(['status' => 'success', 'msg' => 'Webhook received']);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'msg' => $e->getMessage()]);
}
?>
