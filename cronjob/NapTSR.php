<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
/*CRON 1P 1 LẦN*/
define("IN_SITE", true);
require_once("../core/config.php");
require_once("../core/function.php");
if($TUANORI->site('time_thesieure') > time()) {
  	die('Vui lòng cron chậm lại');
}
$TUANORI->update("options", ['value' => time()+ 15], " `key` = 'time_thesieure' ");
if($TUANORI->site('status_tsr') == 'OFF' || !$TUANORI->site('tk_tsr')) {
    die('Dữ liệu chưa có để api');
}
$data = json_decode(curl_get(BASE_URL('/api/Thesieure/')), true);
// $data = json_decode(curl_get('https://tuanori.tech/b.json'), true);
foreach($data['tranList'] as $tsr) {
    if($tsr['amount'] < 0) {
        continue;
    } else {
        $amount = $tsr['amount'];
        $magd   = $tsr['description'];
        $cmt    = $tsr['description'];
        $id     = get_id_bank($cmt);
        if($row = $TUANORI->get_row(" SELECT * FROM `hoadon_vi` WHERE `magd` = '$magd' AND `sotien` = '$amount' AND `status` = 'xuly' ORDER BY id DESC")) {
            $check = $TUANORI->getUser($row['username']);
            // echo $magd;
            /*CẬP NHẬT HÓA ĐƠN*/
            $TUANORI->update("hoadon_vi", array(
                'status'       => 'thanhcong',
            ), " `id` = '".$row['id']."' ");

            /*THÊM DỮ LIỆU BIẾN ĐỘNG SỐ DƯ*/
            $TUANORI->insert("biendongsodu", [
                'username'      => $check['username'],
                'truoc'         => $check['money'],
                'sau'           => $check['money'] + $amount,
                'note'          => "Nạp ".format_cash($amount)."đ vào tài khoản qua THESIEURE",
                'tongtien'      => $amount,
                'time'          => gettime()
            ]);
            pusher($check['username'], "success", "Bạn đã nạp thành công ".number_format($amount)."đ và thực nhận ".number_format($row['thucnhan'])."đ vào tài khoản qua THESIEURE");
            $isMoney1 = $TUANORI->cong("users", "money", $row['thucnhan'], " `username` = '".$check['username']."'");
            $isMoney2 = $TUANORI->cong("users", "total_money", $row['thucnhan'], " `username` = '".$check['username']."'");
        }
    }
}