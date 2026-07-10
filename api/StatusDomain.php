<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
define("IN_SITE", true);
require_once("../core/config.php");
require_once("../core/function.php");
if(empty($_GET['token'])) {
    json_code(false, "Thiếu dữ liệu token gửi lên");
}
$token = check_string($_GET['token']);
$check_api = $TUANORI->get_row(" SELECT * FROM `users` WHERE `token_api` = '$token' AND `banned` = 'ON' ");
if(!$check_api || $token !== $check_api['token_api']) {
    json_code(false, "Token không tồn tại hoặc tài khoản đã bị đình chỉ");
} else {
    if($TUANORI->get_row(" SELECT * FROM `blockip` WHERE `ip` = '".myip()."' ")) {
        json_code(false, "Bạn đã bị chặn sử dụng tính năng của chúng tôi vĩnh viễn. Xin cảm ơn");
    }
    $api_keys = $TUANORI->get_row(" SELECT * FROM `key_apis` WHERE `username` = '".$check_api['username']."'");
    if($api_keys) {
        if(empty($_GET['domain'])) {
            json_code(false, "Thiếu dữ liệu Tên Miền");
        }
        $domain = check_string($_GET['domain']);
        $arr_domain = $TUANORI->get_row(" SELECT * FROM `lichsumuamien` WHERE `username` = '".$check_api['username']."' AND `domain` =  '$domain' ");
        if(!$arr_domain) {
            json_code(false, "Lịch sử mua miền này không tồn tại");
        }
        $ns = [];
        foreach(explode("\n", $arr_domain['ns']) as $okk) {
            array_push($ns, $okk);
        }
        $arrDataPost = array(
            'status'        => true,
            'messsage'      => "Kiểm tra thành công",
            'trangthai'     => $arr_domain['status'],
            'timemua'       => $arr_domain['timemua'],
            'nammua'        => $arr_domain['thoihan'],
            'tongtien'      => $arr_domain['tongtien'],
            'nameserver'    => $ns
        );
        die(json_encode($arrDataPost));
    } else {
        json_code(false, "Token của bạn chưa được Kích Hoạt. Hãy liên hệ admin để được Kích Hoạt token");
    }
}