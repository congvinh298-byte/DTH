<?php
/**
 * Controller: Khách hàng đánh giá dịch vụ sau khi đơn HOAN_THANH
 *
 * POST params:
 *   id            (int)    — ID đơn dat_lich
 *   danhgia_sao   (int)    — 1 đến 5
 *   danhgia_noidung (string) — Nội dung nhận xét (optional)
 *
 * Auth: Không bắt buộc đăng nhập — khách dùng link tra cứu đơn.
 *       Tuy nhiên nếu đã đăng nhập, sẽ validate SĐT khớp.
 * Response: JSON { status, msg }
 */
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['id']) || !isset($_POST['danhgia_sao'])) {
    echo json_encode(['status' => 'error', 'msg' => 'Thiếu thông tin đánh giá']);
    exit;
}

$id   = (int)$_POST['id'];
$sao  = (int)$_POST['danhgia_sao'];
$noidung = isset($_POST['danhgia_noidung']) ? trim(strip_tags($_POST['danhgia_noidung'])) : '';

// Validate số sao
if ($sao < 1 || $sao > 5) {
    echo json_encode(['status' => 'error', 'msg' => 'Số sao không hợp lệ (1–5)']);
    exit;
}

// Giới hạn độ dài nhận xét
if (mb_strlen($noidung) > 500) {
    echo json_encode(['status' => 'error', 'msg' => 'Nội dung nhận xét quá dài (tối đa 500 ký tự)']);
    exit;
}

try {
    // Lấy đơn và kiểm tra trạng thái HOAN_THANH
    $don = $DMH->get_row("SELECT * FROM `dat_lich` WHERE `id` = '$id' AND `trangthai` = 'HOAN_THANH'");

    if (!$don) {
        echo json_encode(['status' => 'error', 'msg' => 'Đơn hàng không tồn tại hoặc chưa hoàn thành']);
        exit;
    }

    // Không cho đánh giá lại nếu đã có điểm
    if (!empty($don['danhgia_sao'])) {
        echo json_encode(['status' => 'error', 'msg' => 'Đơn hàng này đã được đánh giá rồi. Cảm ơn bạn!']);
        exit;
    }

    // Nếu đã đăng nhập — kiểm tra SĐT khớp với đơn (bảo vệ không đánh giá đơn người khác)
    if (!empty($getUser) && $getUser['level'] === 'user') {
        $userPhone = $getUser['phone'] ?? $getUser['username'];
        if ($userPhone && $don['sdt'] !== $userPhone) {
            echo json_encode(['status' => 'error', 'msg' => 'Bạn không có quyền đánh giá đơn này']);
            exit;
        }
    }

    // Lưu đánh giá
    $ok = $DMH->update('dat_lich', [
        'danhgia_sao'     => $sao,
        'danhgia_noidung' => $noidung
    ], "`id` = '$id'");

    if ($ok) {
        // Gửi Telegram thông báo cho Admin/thợ
        $stars  = str_repeat('⭐', $sao);
        $thoMsg = $noidung ? "\nNhận xét: {$noidung}" : '';
        send_tele("📣 ĐÁNH GIÁ MỚI\nĐơn #{$id}\nDịch vụ: {$don['dichvu']}\nKhách: {$don['ten']}\nĐiểm: {$stars} ({$sao}/5){$thoMsg}");
        echo json_encode(['status' => 'success', 'msg' => 'Cảm ơn bạn đã đánh giá! Ý kiến của bạn giúp chúng tôi cải thiện dịch vụ.']);
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Lỗi lưu đánh giá, vui lòng thử lại']);
    }

} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>
