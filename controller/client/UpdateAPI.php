<?php

define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
if(isset($_POST['type']) && $_POST['type'] == 'UpDate')
{
    if($DMH->get_row(" SELECT * FROM `blockip` WHERE `ip` = '".myip()."'  ")) {
        msg_error2('Bạn đã bị chặn sử dụng tính năng của chúng tôi vĩnh viễn. Xin cảm ơn');
    }
    if(empty($_COOKIE['token'])) {
        msg_error('Vui lòng đăng nhập để sử dụng tính năng', BASE_URL(''), 1000);
    }
    // XỬ LÝ EDIT API
    $whois          = check_string($_POST['whois']);
    $list_code      = check_string($_POST['list_code']);
    $buy_code       = check_string($_POST['buy_code']);
    $buy_domain     = check_string($_POST['buy_domain']);
    $update = $DMH->update("key_apis", array(
        'whois'         => $whois,
        'list_code'     => $list_code,
        'buy_code'      => $buy_code,
        'buy_domain'    => $buy_domain
    ), " `username` = '".$getUser['username']."' ");
    if($update) {
        msg_success("Đã cập nhật thành công", "", 1000);
    } else {
        msg_error2("Cập nhật thất bại");

    }
}
else
{
    require_once("../../pages/client/404.php");
}