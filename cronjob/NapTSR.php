<?php

/*CRON 1P 1 LẦN*/
define("IN_SITE", true);
require_once("../core/config.php");
require_once("../core/function.php");
if($DMH->site('time_thesieure') > time()) {
  	die('Vui lòng cron chậm lại');
}
$DMH->update("options", ['value' => time()+ 15], " `key` = 'time_thesieure' ");
if($DMH->site('status_tsr') == 'OFF' || !$DMH->site('tk_tsr')) {
    die('Dữ liệu chưa có để api');
}
$data = json_decode(curl_get(BASE_URL('/api/Thesieure/')), true);
// $data = json_decode(curl_get('https://dmh.tech/b.json'), true);
foreach($data['tranList'] as $tsr) {
    if($tsr['amount'] < 0) {
        continue;
    } else {
        $amount = $tsr['amount'];
        $magd   = $tsr['description'];
        $cmt    = $tsr['description'];
        $id     = get_id_bank($cmt);
        if($row = $DMH->get_row(" SELECT * FROM `hoadon_vi` WHERE `magd` = '$magd' AND `sotien` = '$amount' AND `status` = 'xuly' ORDER BY id DESC")) {
            $check = $DMH->getUser($row['username']);
            // echo $magd;
            /*CẬP NHẬT HÓA ĐƠN*/
            $DMH->update("hoadon_vi", array(
                'status'       => 'thanhcong',
            ), " `id` = '".$row['id']."' ");

            /*THÊM DỮ LIỆU BIẾN ĐỘNG SỐ DƯ*/
            $DMH->insert("biendongsodu", [
                'username'      => $check['username'],
                'truoc'         => $check['money'],
                'sau'           => $check['money'] + $amount,
                'note'          => "Nạp ".format_cash($amount)."đ vào tài khoản qua THESIEURE",
                'tongtien'      => $amount,
                'time'          => gettime()
            ]);
            pusher($check['username'], "success", "Bạn đã nạp thành công ".number_format($amount)."đ và thực nhận ".number_format($row['thucnhan'])."đ vào tài khoản qua THESIEURE");
            $isMoney1 = $DMH->cong("users", "money", $row['thucnhan'], " `username` = '".$check['username']."'");
            $isMoney2 = $DMH->cong("users", "total_money", $row['thucnhan'], " `username` = '".$check['username']."'");
        }
    }
}