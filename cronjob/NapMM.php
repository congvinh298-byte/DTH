<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
/*CRON 1P 1 LẦN*/
define("IN_SITE", true);
require_once("../core/config.php");
require_once("../core/function.php");
if($TUANORI->site('time_momo') > time()) {
  	die('Vui lòng cron chậm lại');
}
$TUANORI->update("options", ['value' => time()+ 15], " `key` = 'time_momo' ");
if(!$TUANORI->site('token_momo')) {
    die('Dữ liệu chưa có để api');
}
// print_r("https://api.web2m.com/historyapimomo/".$TUANORI->site('token_momo'));
// die;
$data = json_decode(curl_get("https://api.web2m.com/historyapimomo/".$TUANORI->site('token_momo')), true);
// $data = json_decode(curl_get("https://tuanori.tech/a.json"), true);
foreach($data['momoMsg']['tranList'] as $mm) {
    $sotien = $mm['amount'];
    if($TUANORI->site('sukien') == 'ON' && $TUANORI->site('khuyenmai') >= 1) $sotien = $sotien + ($sotien * $TUANORI->site('khuyenmai')/ 100);
    $cmt    = $mm['comment'];
    $io     = $mm['io'];
    $magd   = $mm['tranId'];
    $id     = get_id_bank($cmt);
    $tg     = $mm['amount'];
    if($io == 1 && is_numeric($id)) {
        $check = $TUANORI->get_row(" SELECT * FROM `users` WHERE `id` = '$id'");
        $check2 = $TUANORI->get_row(" SELECT * FROM `napatm` WHERE `hinhthuc` = 'MOMO' AND `magd` = '$magd'");
        if($check && !$check2) {
            $create = $TUANORI->insert("napatm", [
                'username'       => $check['username'],
                'hinhthuc'       => 'MOMO',
                'magd'           => $magd,
                'sotien'         => $sotien,
                'thoigian'       => gettime(),
                'ndnaptien'      => $cmt
            ]);
            $create2 = $TUANORI->insert("biendongsodu", [
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
                $is = $TUANORI->cong("users", "money", $sotien, " `username` = '".$check['username']."'");
                $is2 = $TUANORI->cong("users", "total_money", $sotien, " `username` = '".$check['username']."'");
            }
        }
    }
}