<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
if(isset($_GET['magd']) && isset($_COOKIE['token'])) {
    $magd = check_string($_GET['magd']);
    $status = 0;
    if($row = $TUANORI->get_row(" SELECT * FROM `hoadon_vi` WHERE `magd` = '$magd' AND `username` = '".$getUser['username']."'")) {
        if($row['status'] == 'thanhcong') $status = 1;
        echo json_encode(['status' => $status, 'msg' => stnapvi($row['status'])]);
    } else {
        echo json_encode(['status' => 0, 'msg' => 'Hóa đơn lỗi']);
    }
} else {
    echo json_encode(['status' => 0, 'msg' => 'Hóa đơn lỗi']);
}