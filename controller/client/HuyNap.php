<?php

define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
if(isset($_POST['magd']) && isset($_COOKIE['token'])) {
    $magd = check_string($_POST['magd']);
    // msg_error2();

    if($DMH->get_row(" SELECT * FROM `hoadon_vi` WHERE `magd` = '$magd' AND `status` = 'xuly'")) {
        $DMH->update("hoadon_vi", array(
            'status'    => 'huy',
        ), " `magd` = '$magd' ");
        msg_success("Đã hủy hóa đơn nạp tiền", "/Nap/Vi", 1000);
    } else {
        msg_error2("Không thể hủy");
    }
}
else
{
    require_once("../../pages/client/404.php");
}