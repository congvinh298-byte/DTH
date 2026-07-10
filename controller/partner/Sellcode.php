<?php

    define("IN_SITE", true);
    require_once("../../core/config.php");
    require_once("../../core/function.php");

    if(empty($_COOKIE['token'])) {
        msg_error2('Vui lòng đăng nhập để chuyển tiền');
    }
    if(!$getUser['verify']) {
        msg_error2('Bạn chưa xác minh tài khoản');
    }
    $sotien = check_string($_POST['sotien']);
    $name   = check_string($_POST['name']);
    $mota   = check_string($_POST['mota']);
    $chude  = check_string($_POST['chude']);
    $link   = check_string($_POST['link']);
    $img    = check_string($_POST['img']);
    $img_c  = check_string($_POST['img_c']);
    $nd     = check_string($_POST['nd']);
    $check  = check_string($_POST['check']);
    if(!$name || !$mota || !$nd || !$link || !$chude || !$img) {
        msg_error2("Vui lòng không bỏ trống thứ gì");
    }
    if($sotien < 0) {
        msg_error2("Số tiền bán phải >= 0");
    }
    if(str_word_count($name) <= 6) {
        msg_error2("Tên quá ngắn");
    }
    if(strlen($mota) <= 14) {
        msg_error2("Mô tả quá ngắn");
    }
    if($sotien > 100000000) {
        msg_error2("Số tiền quá lớn");
    }
    if(!$DMH->get_row(" SELECT * FROM `danhmucmuacode` WHERE `id` = '$chude' AND `status` = 'SHOW'")) {
        msg_error2("Chủ đề không hợp lệ");
    }
    if(!check_phone($getUser['zalo'])) {
        msg_error2("Số zalo của bạn không hợp lệ");
    }
    if(!$check) {
        msg_error2("Vui lòng xác nhận đã tuân thủ Nội Quy");
    }
    $create = $DMH->insert("partner_code", [
        'username'      => $getUser['username'],
        'name'          => $name,
        'mota'          => $mota,
        'sotien'        => $sotien,
        'id_danhmuc'    => $chude,
        'img'           => $img_c,
        'list_img'      => $img,
        'link'          => $link,
        'hdsd'          => $nd,
        'zalo'          => $getUser['zalo'],
        'thoigian'      => gettime()
    ]);
    if($create) {
        msg_success("Đã gửi code lên sàn. Vui lòng chờ admin duyệt", "", 1500);
    }



