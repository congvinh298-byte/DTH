<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
    define("IN_SITE", true);
    require_once("../../core/config.php");
    require_once("../../core/function.php");
    if($_COOKIE['token']) {
        if($getUser['level'] != 'admin') {
            msg("error", "Bạn không phải là ADMIN");
        }
        
        foreach($_POST as $key => $value)
        {
            $TUANORI->update("domainclf", array(
                $key      => $value
            ), " `id` = '1' ");
        }
        msg("success","Đã lưu lại dữ liệu thành công","", 1000);
        

    } else {
        msg("error", "Vui lòng đăng nhập để truy cập admin","",1000);
    }