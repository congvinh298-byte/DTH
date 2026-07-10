<?php

/*CRON 1P 1 LẦN*/   
define("IN_SITE", true);
require_once("../core/config.php");
require_once("../core/function.php");
if(!$DMH->site('token_bank') || !$DMH->site('stk_bank') || !$DMH->site('user_bank') || !$DMH->site('mk_bank') || !$DMH->site('loaibank')) {
    die('Dữ liệu chưa có để api');
}
// print("https://api.web2m.com/historyapimb/".$DMH->site('mk_bank')."/".$DMH->site('stk_bank')."/".$DMH->site('token_bank')); die;
$data = json_decode(curl_get("https://api.web2m.com/historyapimb/".$DMH->site('mk_bank')."/".$DMH->site('stk_bank')."/".$DMH->site('token_bank')), true);
// $data = json_decode(curl_get("https://dmh.tech/a.json"), true);
foreach($data['data'] as $mb) {
    $magd   = explode('\\', $mb['refNo'])[0];
    $sotien = $mb['creditAmount'];
    if($DMH->site('sukien') == 'ON' && $DMH->site('khuyenmai') >= 1) $sotien = $sotien + ($sotien * $DMH->site('khuyenmai')/ 100);
    $cmt    = $mb['description'];
    $tien2  = $mb['debitAmount'];
    $id     = get_id_bank($cmt);
    if(!$tien2 && is_numeric($id)) {
        $check = $DMH->get_row(" SELECT * FROM `users` WHERE `id` = '$id'");
        $check2 = $DMH->get_row(" SELECT * FROM `napatm` WHERE `hinhthuc` = 'MBBANK' AND `magd` = '$magd'");
        if($check && !$check2) {
            $isMoney = $DMH->cong("users", "money", $sotien, " `username` = '".$check['username']."'");
            $isMoney2 = $DMH->cong("users", "total_money", $sotien, " `username` = '".$check['username']."'");
            if($isMoney && $isMoney2) {
                $create = $DMH->insert("napatm", [
                    'username'       => $check['username'],
                    'hinhthuc'       => 'MBBANK',
                    'magd'           => $magd,
                    'sotien'         => $sotien,
                    'thoigian'       => gettime(),
                    'ndnaptien'      => $cmt
                ]);
                $DMH->insert("biendongsodu", [
                    'username'  => $check['username'],
                    'truoc'     => $check['money'],
                    'sau'       => $check['money'] + $sotien, 
                    'note'      => "Nạp ".format_cash($sotien)."đ vào tài khoản qua MBBANK",
                    'tongtien'  => $sotien,
                    'time'      => gettime()
                ]);
                pusher($check['username'], "success", "Bạn đã nạp thành công ".number_format($sotien)."đ vào tài khoản qua MBBANK");
            }
        }
    }
}