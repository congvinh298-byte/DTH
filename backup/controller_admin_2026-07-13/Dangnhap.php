<?php

define("IN_SITE", true);
require_once(__DIR__."/../../core/config.php");
require_once(__DIR__."/../../core/function.php");

if(isset($_COOKIE['token']))
{
    if($getUser['level'] != 'admin') {
        msg("error", "Bạn không phải là ADMIN");
    }
    $mk = check_string($_POST['password']);
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
        if($mk != md5('845409'))
        {
            msg("error", "Mật khẩu cấp 2 không chính xác");
        }
        
        $rowlog = $DMH->get_row(" SELECT * FROM `users` WHERE `tokenlog` = '".$_COOKIE['token']."' AND `level` = 'admin'");
        if(!$rowlog)
        {
            msg("error", "Thông tin đăng nhập không chính xác. Vui lòng kiểm tra lại");
        }
        else
        {
            $_SESSION['loginadmin'] = true;
            msg("success", "Đăng nhập thành công", BASE_URL('pages/admin/QuanLyDonHang.php'), 1000);
        }
    }
}
else
{
    msg("error", "Vui lòng đăng nhập để truy cập admin","",1000);
}
?>
