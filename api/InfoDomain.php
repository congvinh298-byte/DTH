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
        $TUANORI->update("users", array(
            'online'       => 'ONLINE'
        ), " `id` = '$token' ");
        if($api_keys['buy_domain'] == 'ON') {
            $row = array (
                "status" => true,
                "message" => 'Kiểm tra thành công'
            );
            $tuan = [];
            foreach($TUANORI->get_list(" SELECT * FROM `danhsachmien` ORDER BY id DESC") as $row) {
                array_push($tuan, ['domain' => $row['domain'],'money' => $row['money'], 'giahan' => $row['giahan']]);
            }
            $row2 = array (
                "status"    => true,
                "message"   => 'Đã kiểm tra thành công',
                'data'      => $tuan
            );
            print_r(json_encode($row2));
        } else  {
            json_code(false, "Chức năng WHOIS của tài khoản này chưa được kích hoạt. Hãy truy cập vào link này: ".BASE_URL('Docs_api')." để kích hoạt nó");
        }
    } else {
        json_code(false, "Token của bạn chưa được Kích Hoạt. Hãy liên hệ admin để được Kích Hoạt token");
    }
}