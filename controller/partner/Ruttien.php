<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
    define("IN_SITE", true);
    require_once("../../core/config.php");
    require_once("../../core/function.php");
    if($_COOKIE['token']) {
        if($getUser['level'] != 'admin') {
            msg("error", "Bạn không phải là ADMIN");
        }
        $atm    = check_string($_POST['atm']);
        $stk    = check_string($_POST['stk']);
        $name   = check_string($_POST['name']);
        $sotien = check_string(numb($_POST['sotien']));
        if(!$atm || !$stk || !$name || !$sotien) {
            msg("error", "Vui lòng không bỏ trống thông tin");
        }
        if($sotien < 100000) {
            msg("error", "Số tiền rút tối thiểu là 100.000đ");
        }
        if($sotien > $getUser['money_partner']) {
            msg("error", "Số tiền của bạn không đủ để rút");
        }
        if($sotien+ 5000 > $getUser['money_partner']) {
            msg("error", "Số tiền không đủ để trả phí rút.");
        }
        $create = $TUANORI->insert("partner_ruttien", [
            'username'  => $getUser['username'],
            'atm'       => $atm,
            'stk'       => $stk,
            'name'      => $name,
            'sotien'    => $sotien,
            'status'    => 'xuly'
        ]);
        $sotien+=5000;
        $isMoney = $TUANORI->tru("users", "money_partner", $sotien, " `tokenlog` = '".$_COOKIE['token']."'");
        $TUANORI->insert("partner_biendongsodu", [
            'username'      => $getUser['username'],
            'usermua'       => $getUser['username'],
            'truoc'         => $getUser['money_partner'],
            'sau'           => $getUser['money_partner']- $sotien,
            'note'          => 'Đã yêu cầu rút tiền về ngân hàng '.$atm,
            'tongtien'      => $sotien,
            'id_code'       => 0, 
            'time'          => gettime()
        ]);
        if($create) {
            msg("success", "Bạn đã rút tiền thành công, vui lòng chờ admin duyệt");
        }
    } else {
        msg("error", "Vui lòng đăng nhập để truy cập admin","/",1000);
    }
