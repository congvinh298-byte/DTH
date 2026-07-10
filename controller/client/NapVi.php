<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
if(isset($_COOKIE['token'])) {
    $sotien = check_string(numb($_POST['sotien']));
    if($sotien < 10000) {
        msg_error2("Số tiền nạp tối thiểu là 10.000đ");
    }
    if($TUANORI->num_rows(" SELECT * FROM `hoadon_vi` WHERE`username` = '".$getUser['username']."' AND `status` IN ('xuly', 'huy') AND `thoigian` >= DATE(NOW()) AND `thoigian` < DATE(NOW()) + INTERVAL 1 DAY") > 5) {
        msg_error2("Bạn có nhiều hóa đơn trong hôm nay. Vui lòng quay lại vào ngày mai.");
    }
    $rand = random('QWERTYUIOPASDFGHJKLZXCVBNM', 8);
    $thucnhan = $sotien;
    if($TUANORI->site('sukien') == 'ON' && $TUANORI->site('khuyenmai') > 0) {
        $thucnhan +=$sotien*$TUANORI->site('khuyenmai')/100;
    }
    $TUANORI->insert("hoadon_vi", [
        'username'      => $getUser['username'],
        'magd'          => $rand,
        'sotien'        => $sotien,
        'thucnhan'      => $thucnhan,
        'status'        => 'xuly',
        'thoigian'      => gettime()
    ]);
    msg_success("Đã tạo hóa đơn thành công", "/Nap/Vi/$rand", 500);
}
else
{
    require_once("../../pages/client/404.php");
}