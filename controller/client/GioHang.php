<?php
    define("IN_SITE", true);
    require_once("../../core/config.php");
    require_once("../../core/function.php");
    if(empty($_COOKIE['token'])) die(json_encode(['status' => 'error', 'msg' => 'Đăng nhập để tiếp tục']));
    $id = check_string($_POST['id']);
    if(!$id) die(json_encode(['status' => 'error', 'msg' => 'Không nhận được dữ liệu sản phẩm']));
    $check = $TUANORI->get_row(" SELECT * FROM `danhsachmuacode` WHERE `id` = '$id' ");
    if($check['statusmua'] == 'OFF' || $check['hienthi'] != 'SHOW') die(json_encode(['status' => 'error', 'msg' => 'Sản phẩm này hiện đang không thể mua']));
    if($TUANORI->get_row(" SELECT * FROM `giohang` WHERE `id_code` = '$id' AND `username` = '".$getUser['username']."' "))  {
        die;
    }
    if($TUANORI->num_rows(" SELECT * FROM `giohang` WHERE `username` = '".$getUser['username']."' ") >= 10) 
        die(json_encode(['status' => 'error', 'msg' => 'Giỏ hàng chỉ có thể chứa tối đa 10 sản phẩm']));
    if($getUser['username']) {
        $create = $TUANORI->insert("giohang", [
            'username'  => $getUser['username'],
            'id_code'   => $id,
            'sotien'    => $check['money'],
            'time'      => time(),
            'thoigian'  => gettime(),
            'status'    => 'xuly'
        ]);
    }

    if($create) die(json_encode(['status' => 'success', 'msg' => 'Đã thêm vào giỏ hàng thành công']));
    else die(json_encode(['status' => 'error', 'msg' => 'Không thể thêm vào giỏ hàng bây giờ']));