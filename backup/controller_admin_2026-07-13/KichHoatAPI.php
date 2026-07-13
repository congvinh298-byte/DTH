<?php

    define("IN_SITE", true);
    require_once("../../core/config.php");
    require_once("../../core/function.php");
    if($_COOKIE['token']) {
        if($getUser['level'] != 'admin') {
            msg("error", "Bạn không phải là ADMIN");
        }
        //  XỬ LÝ PHẦN KÍCH HOẠT MÃ API
        $token = check_string($_POST['token']);
        if(empty($token)) {
            msg("error","Vui lòng nhập token để kích hoạt");
        }
        if(strlen($token) < 15) {
            msg("error","Độ dài token không chính xác");
        } else {
            $check = $DMH->get_row(" SELECT * FROM `users` WHERE `token_api` = '$token'");
            if(!$check) {
                msg("error","Token không tồn tại trong hệ thống");
            } else {
                $check2 = $DMH->get_row(" SELECT * FROM `key_apis` WHERE `username` = '".$check['username']."'");
                if(empty($check2))
                {
                    if($check['banned'] == 'OFF') {
                        msg("error","Tài khoản này của đối tác API đã bị khóa");
                    }
                    $create = $DMH->insert("key_apis", [
                        'username'      => $check['username']
                    ]);
                    if($create) {
                        msg("success","Đã kích hoạt đối tác API thành công", "", 1000);
                    } else {
                        msg("error","Kích hoạt thất bại");
                    }
                } else {
                    msg("error","Đối tác này đã đươc kích hoạt từ trước");
                }
            }
        }
        
    } else {
        msg("error", "Vui lòng đăng nhập để truy cập admin","/",1000);
    }
