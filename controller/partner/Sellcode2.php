<?php

    define("IN_SITE", true);
    require_once("../../core/config.php");
    require_once("../../core/function.php");

    if(empty($_COOKIE['token'])) {
        msg("error", 'Vui lòng đăng nhập để chuyển tiền');
    }
    if(!$getUser['verify']) {
        msg("error", 'Bạn chưa xác minh tài khoản');
    }
    
    $sotien = check_string(numb($_POST['sotien']));
    $name   = check_string($_POST['name']);
    $mota   = check_string($_POST['mota']);
    $chude  = check_string($_POST['chude']);
    $link   = check_string($_POST['link']);
    $img    = check_string($_POST['img']);
    $img_c  = check_string($_POST['img_c']);
    $nd     = check_string($_POST['nd']);

    if(!$name || !$mota || !$nd || !$link || !$chude || !$img) {
        msg("error", "Vui lòng không bỏ trống thứ gì");
    }
    if($sotien < 0) {
        msg("error", "Số tiền bán phải >= 0");
    }
    if(str_word_count($name) <= 6) {
        msg("error", "Tên quá ngắn");
    }
    if(strlen($mota) <= 14) {
        msg("error", "Mô tả quá ngắn");
    }
    if($sotien > 100000000) {
        msg("error", "Số tiền quá lớn");
    }

    if(!$DMH->get_row(" SELECT * FROM `danhmucmuacode` WHERE `id` = '$chude' AND `status` = 'SHOW'")) {
        msg("error", "Chủ đề không hợp lệ");
    }
    if(!check_phone($getUser['zalo'])) {
        msg("error", "Số zalo của bạn không hợp lệ");
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
        msg("success", "Đã gửi code lên sàn. Vui lòng chờ admin duyệt", "", 1500);
    }



