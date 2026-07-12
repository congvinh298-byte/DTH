<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'nhan_don') {
    
    // Bắt buộc đăng nhập với quyền thợ
    if(!isset($_COOKIE['token']) || empty($getUser)) {
        echo json_encode(['status' => 'error', 'msg' => 'Vui lòng đăng nhập!']);
        exit;
    }
    if($getUser['level'] != 'tho' && $getUser['level'] != 'admin') {
        echo json_encode(['status' => 'error', 'msg' => 'Bạn không có quyền thực hiện chức năng này!']);
        exit;
    }

    $id = (int)$_POST['id'];
    $my_id = $getUser['id'];

    if ((int)$getUser['money'] < 0) {
        echo json_encode(['status' => 'error', 'msg' => 'Bạn đang nợ phí nền tảng! Vui lòng thanh toán cho Giám đốc để nhận đơn mới.']);
        exit;
    }

    // Kiểm tra đơn hàng có tồn tại và đang ở trạng thái CHO_XU_LY không
    $check = $DMH->get_row("SELECT * FROM `dat_lich` WHERE `id` = '$id' AND `trangthai` = 'CHO_XU_LY'");
    
    if($check) {
        $update = $DMH->update("dat_lich", [
            'trangthai' => 'DANG_XU_LY',
            'tho_id' => $my_id
        ], " `id` = '$id' ");
        
        if($update) {
            echo json_encode(['status' => 'success', 'msg' => 'Nhận đơn thành công! Khách hàng sẽ chờ bạn liên hệ.']);
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Có lỗi xảy ra, vui lòng thử lại!']);
        }
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Đơn hàng này không tồn tại hoặc đã có thợ khác nhận mất rồi!']);
    }
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Request Not Found']);
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>
