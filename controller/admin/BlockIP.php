<?php

    define("IN_SITE", true);
    require_once("../../core/config.php");
    require_once("../../core/function.php");
    if($_COOKIE['token'])
    {
        if($getUser['level'] != 'admin') {
            msg("error", "Bạn không phải là ADMIN");
        }
        $user     = check_string($_POST['username']);
        if(!$user) {
            msg("error", "Vui lòng nhập đầy đủ thông tin để tiếp tục"); 
        }
        $check = $DMH->get_row(" SELECT * FROM `users` WHERE `ip` = '$user' or `username` = '$user'");
        if(!$check) {
            msg("error", "Tên người dùng này không tồn tại");
        }
        $check2 = $DMH->get_row(" SELECT * FROM `blockip` WHERE `ip` = '".$check['ip']."' ");
        if($check2) {
            msg("error", "Tài khoản này đã bị blockIP từ trước rồi");
        } else {
            $is = $DMH->num_rows("SELECT * FROM `users` WHERE `ip` = '".$check['ip']."' ");
            $update =  $DMH->update("users", array(
                    'banned'        => 'OFF',
                    'online'        => 'OFFLINE'
                ), " `ip` = '".$check['ip']."' ");
            $update2 =  $DMH->update("users", array(
                    'banned'        => 'OFF',
                    'online'        => 'OFFLINE'
                ), " `ip` = '".$check['ip']."' ");
            $create = $DMH->insert("blockip", [
                'ip'            => $check['ip'],
                'time'          => gettime()
            ]);
            if($update && $update2 && $create) {
                msg("success","Đã thêm dữ liệu block thành công. Chúng tôi đã block $is tài khoản của người này","/Admin/Blockip", 2000);
            } else {
                msg("error", "Thêm dữ liệu thất bại");
            }
        }
    }
    else
    {
        msg("error", "Vui lòng đăng nhập để truy cập admin","",1000);
    }
