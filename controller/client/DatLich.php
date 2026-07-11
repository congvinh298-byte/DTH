<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['type']) && $_POST['type'] == 'DatLich') {
    
    // Security and Anti-Spam Check
    if(isset($_SESSION['last_book'])) {
        if(time() - $_SESSION['last_book'] < 60) {
            msg_error2("Vui lòng đợi 1 phút trước khi gửi yêu cầu tiếp theo!");
        }
    }

    $ten = check_string($_POST['ten']);
    $sdt = check_string($_POST['sdt']);
    $diachi = check_string($_POST['diachi']);
    $yeucau = check_string($_POST['yeucau']);
    $dichvu = check_string($_POST['dichvu']);

    if(empty($ten) || empty($sdt) || empty($diachi) || empty($yeucau) || empty($dichvu)) {
        msg_error2("Vui lòng điền đầy đủ các thông tin bắt buộc!");
    }

    if(strlen($sdt) < 10 || strlen($sdt) > 15 || !is_numeric($sdt)) {
        msg_error2("Số điện thoại không hợp lệ!");
    }

    $create = $DMH->insert("dat_lich", [
        'ten'      => $ten,
        'sdt'      => $sdt,
        'diachi'   => $diachi,
        'yeucau'   => $yeucau,
        'dichvu'   => $dichvu,
        'trangthai'=> 'CHO_XU_LY',
        'thoigian' => time()
    ]);

    if($create) {
        $_SESSION['last_book'] = time();
        msg_success('Yêu cầu đã được gửi thành công! Kỹ thuật viên sẽ gọi lại cho bạn trong ít phút.', '', 3000);
    } else {
        msg_error2("Hệ thống đang bận, vui lòng thử lại sau!");
    }
} else {
    die('The Request Not Found');
}
?>
