<?php

    define("IN_SITE", true);
    require_once("../../core/config.php");
    require_once("../../core/function.php");
    if($_COOKIE['token']) {
        if($getUser['level'] != 'admin') {
            msg("error", "Bạn không phải là ADMIN");
        }
        $id_user    = check_string($_POST['id_user']);
        $nameatm    = check_string($_POST['nameatm']);
        $magd       = check_string($_POST['magd']);
        $money      = check_string(numb($_POST['sotien']));
        if(!$id_user || !$nameatm || !$magd || !$money) {
            msg("error", "Vui lòng nhập đầy đủ thông tin"); 
        }
        if($money <= 0) {
            msg("error", "Số tiền nạp không hợp lệ"); 
        }
        $check = $DMH->get_row(" SELECT * FROM `users` WHERE `id` = '$id_user'");
        if(!$check) {
            msg("error", "Thành viên nạp tiền không hợp lệ");
        }
        if($check['banned'] == 'OFF') {
            msg("error", "Thành viên này đã bị khóa"); 
        }
        if($DMH->get_row(" SELECT * FROM `napatm` WHERE `hinhthuc` = '$nameatm' AND `magd` = '$magd' ")) {
            msg("error", "Thông tin nạp tiền này đã có trong hệ thống"); 
        }
        $cr1 = $DMH->insert("biendongsodu", [
            'username'      => $check['username'],
            'truoc'         => $check['money'],
            'sau'           => $check['money'] + $money,
            'tongtien'      => $money,
            'note'          => "Nạp ".format_cash($money)."đ vào tài khoản qua $nameatm",
            'time'          => gettime()
        ]);
        $cr2 = $DMH->insert("napatm", [
            'username'       => $check['username'],
            'hinhthuc'       => $nameatm,
            'magd'           => $magd,
            'sotien'         => $money,
            'thoigian'       => gettime(),
            'ndnaptien'      => $DMH->site('nd_bank').' '.$id_user
        ]);
        if($cr1 && $cr2) {
            msg("success", "Đã thêm thông tin nạp tiền thành công.", "/Admin/LichsunaptienATM", 2000); 
        } else {
            msg("error", "Không thể thêm thông tin nạp tiền"); 
        }

    } else {
        msg("error", "Vui lòng đăng nhập để truy cập admin","",1000);
    }