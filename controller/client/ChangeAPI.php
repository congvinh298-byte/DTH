<?php
define("IN_SITE", true);

require_once("../../core/config.php");
require_once("../../core/function.php");
if(isset($_POST['type']) && $_POST['type'] == 'ChangeAPI')
{
    if($DMH->get_row(" SELECT * FROM `blockip` WHERE `ip` = '".myip()."'  ")) {
        msg_error2('Bạn đã bị chặn sử dụng tính năng của chúng tôi vĩnh viễn. Xin cảm ơn');
    }
    if(empty($_COOKIE['token'])) {
        msg_error('Vui lòng đăng nhập để sử dụng tính năng', BASE_URL(''), 1000);
    }
    // XỬ LÝ EDIT API
    
    $update = $DMH->update("users", array(
        'token_api'         => strtoupper(randomtoken2())
    ), " `username` = '".$getUser['username']."' ");
    if($update) {
        msg_success("Đã thay đổi token mới thành công", "", 1000);
    } else {
        msg_error2("Vui lòng thử lại");
    }
}
else
{
    require_once("../../pages/client/404.php");
}