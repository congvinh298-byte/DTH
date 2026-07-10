<?php

/*CRON 1P 1 LẦN*/
define("IN_SITE", true);
require_once("../core/config.php");
require_once("../core/function.php");
if($DMH->site('time_momo') > time()) {
  	die('Vui lòng cron chậm lại');
}
$DMH->update("options", ['value' => time()+ 15], " `key` = 'time_momo' ");
if(!$DMH->site('token_momo')) {
    die('Dữ liệu chưa có để api');
}
// print_r("https://api.web2m.com/historyapimomo/".$DMH->site('token_momo'));
// die;
$data = json_decode(curl_get("https://api.web2m.com/historyapimomo/".$DMH->site('token_momo')), true);
// $data = json_decode(curl_get("https://dmh.tech/a.json"), true);
foreach($data['momoMsg']['tranList'] as $mm) {
    $sotien = $mm['amount'];
    if($DMH->site('sukien') == 'ON' && $DMH->site('khuyenmai') >= 1) $sotien = $sotien + ($sotien * $DMH->site('khuyenmai')/ 100);
    $cmt    = $mm['comment'];
    $io     = $mm['io'];
    $magd   = $mm['tranId'];
    $id     = get_id_bank($cmt);
    $tg     = $mm['amount'];
    if($io == 1 && is_numeric($id)) {
        $check = $DMH->get_row(" SELECT * FROM `users` WHERE `id` = '$id'");
        $check2 = $DMH->get_row(" SELECT * FROM `napatm` WHERE `hinhthuc` = 'MOMO' AND `magd` = '$magd'");
        if($check && !$check2) {
            $create = $DMH->insert("napatm", [
                'username'       => $check['username'],
                'hinhthuc'       => 'MOMO',
                'magd'           => $magd,
                'sotien'         => $sotien,
                'thoigian'       => gettime(),
                'ndnaptien'      => $cmt
            ]);
            $create2 = $DMH->insert("biendongsodu", [
                'username'      => $check['username'],
                'truoc'         => $check['money'],
                'sau'           => $check['money'] + $sotien,
                'note'          => "Nạp ".format_cash($sotien)."đ vào tài khoản qua MOMO",
                'tongtien'      => $sotien,
                'time'          => gettime()
            ]);
            $create = $create2 = true;
            if($create && $create2) {
                pusher($check['username'], "success", "Bạn đã nạp thành công ".number_format($sotien)."đ vào tài khoản qua MOMO");
                $is = $DMH->cong("users", "money", $sotien, " `username` = '".$check['username']."'");
                $is2 = $DMH->cong("users", "total_money", $sotien, " `username` = '".$check['username']."'");
            }
        }
    }
}