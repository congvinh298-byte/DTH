<?php

    define("IN_SITE", true);
    require_once("../../core/config.php");
    require_once("../../core/function.php");
    if($_COOKIE['token'])
    {
        if($getUser['level'] != 'admin') {
            msg("error", "Bạn không phải là ADMIN");
        }
        $mk     = check_string($_POST['password']);
        if(!$mk) {
            msg("error", "Vui lòng nhập đầy đủ thông tin để tiếp tục");
        }
        if(strlen($mk) < 5)
        {
            msg("error", "Độ dài mật khẩu không chính xác");
        }
       
        else
        {
            $mk = md5($mk);
            $rowlog = $DMH->get_row(" SELECT * FROM `users` WHERE `tokenlog` = '".$_COOKIE['token']."' AND `passwordc2` = '$mk' AND `level` = 'admin'");
            if(!$rowlog)
            {
                msg("error", "Thông tin đăng nhập không chính xác. Vui lòng kiểm tra lại");
            }
            else
            {
                $_SESSION['loginadmin'] = true;
                msg("success", "Đăng nhập thành công",BASE_URL('Admin'),1000);
            }
        }
    }
    else
    {
        msg("error", "Vui lòng đăng nhập để truy cập admin","",1000);
    }
