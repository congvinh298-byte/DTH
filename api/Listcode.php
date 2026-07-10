<?php
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
        $muc = '';
        if(isset($_GET['danhmuc'])) {
            $id_muc = check_string($_GET['danhmuc']);
            if(!$TUANORI->get_row(" SELECT * FROM `danhmucmuacode` WHERE `id` = '$id_muc' ")) {
                die(json_code(false, "Danh mục không tồn tại trong hệ thống"));
            }
            $muc = "AND `id_danhmuc` = '".$id_muc."' ";
        }
        /*KÍCH HOẠT THÀNH VIÊN ONLINE*/
        $TUANORI->update("users", array(
            'online'       => 'ONLINE'
        ), " `id` = '$token' ");
        if($api_keys['list_code'] == 'ON') {
            $get_list = $TUANORI->get_list(" SELECT * FROM `danhsachmuacode` WHERE `hienthi` = 'SHOW' $muc ORDER BY id DESC");
            $user = $check_api;
            $list = [];
            foreach($get_list as $row) {
                array_push($list, array(
                    "id_danhmuc"    => $row['id_danhmuc'],
                    "title"         => $row['title'],
                    "mota"          => $row['mota'],
                    "img"           => $row['img'],
                    "money"         => $row['money'],   
                    "demo"          => $row['demo'],
                    'luotxem'       => $row['luotxem'],
                    'luottai'       => $row['luottai']
                ));
            }
            $arrDataPost = array(
                'status'        => true,
                'messsage'      => 'Lấy dữ liệu thành công',
                'username'      => $user['username'],
                'sotien'        => $user['money'],
                'email'         => $user['email'],
                'ip_api'        => myip(),
                'agent'         => $_SERVER['HTTP_USER_AGENT'],
                'list_code'     => $list
            );
            die(json_encode($arrDataPost));
        } else {
            json_code(false, "Chức năng LIST CODE của tài khoản này chưa được kích hoạt. Hãy truy cập vào link này: ".BASE_URL('Docs_api')." để kích hoạt nó");
        }
    } else {
        json_code(false, "Token của bạn chưa được Kích Hoạt. Hãy liên hệ admin để được Kích Hoạt token");
    }

}