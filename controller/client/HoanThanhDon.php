<?php
define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'hoan_thanh') {
        
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

        $where = " `id` = '$id' AND `trangthai` = 'DANG_XU_LY' ";
        if ($getUser['level'] != 'admin') {
            $where .= " AND `tho_id` = '$my_id' ";
        }
        
        $check = $DMH->get_row("SELECT * FROM `dat_lich` WHERE " . $where);
        
        if($check) {
            $update = $DMH->update("dat_lich", [
                'trangthai' => 'HOAN_THANH'
            ], " `id` = '$id' ");
            
            if($update) {
                // Trừ phí nền tảng 20,000 VND
                $DMH->tru("users", "money", 20000, " `id` = '$my_id' ");
                echo json_encode(['status' => 'success', 'msg' => 'Tuyệt vời! Bạn đã hoàn thành đơn hàng này. Trừ 20,000đ phí nền tảng.']);
            } else {
                echo json_encode(['status' => 'error', 'msg' => 'Có lỗi khi cập nhật CSDL, vui lòng thử lại!']);
            }
        } else {
            echo json_encode(['status' => 'error', 'msg' => 'Đơn hàng này không khả dụng hoặc bạn không phải là người nhận đơn!']);
        }
    } else {
        echo json_encode(['status' => 'error', 'msg' => 'Request Not Found']);
    }
} catch (Throwable $e) {
    echo json_encode(['status' => 'error', 'msg' => 'Lỗi hệ thống: ' . $e->getMessage()]);
}
?>
