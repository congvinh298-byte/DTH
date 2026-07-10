<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
    define("IN_SITE", true);
    require_once("../../core/config.php");
    require_once("../../core/function.php");
    if($_COOKIE['token']) {
        if($getUser['level'] != 'admin') {
            msg("error", "Bạn không phải là ADMIN");
        }
        // XỬ LÝ EDIT API
        $whois          = check_string($_POST['whois']);
        $list_code      = check_string($_POST['list_code']);
        $buy_code       = check_string($_POST['buy_code']);
        $buy_domain     = check_string($_POST['buy_domain']);
        $update = $TUANORI->update("key_apis", array(
            'whois'         => $whois,
            'list_code'     => $list_code,
            'buy_code'      => $buy_code,
            'buy_domain'    => $buy_domain
        ), " `id` = '".$_POST['id']."' ");
        if($update) {
            msg("success","Đã cập nhật thành công", "", 2000);
        } else {
            msg("error","Thao tác thất bại", "", 2000);
        }  
    } else {
        msg("error", "Vui lòng đăng nhập để truy cập admin","/",2000);
    }
