<?php

    define("IN_SITE", true);
    require_once("../../core/config.php");
    require_once("../../core/function.php");
    if($_COOKIE['token']) {
        if($getUser['level'] != 'admin') {
            msg("error", "Bạn không phải là ADMIN");
        }
        if($_POST['type'] == 'Themma') {
            $magiamgia      = check_string($_POST['magiamgia']);
            $giam           = check_string($_POST['giambaonhieu']);
            $theloai        = check_string($_POST['theloai']);
            $luotdung       = check_string($_POST['luotdung']);
            $hienthi        = check_string($_POST['hienthi']);
            if(!$magiamgia || !$giam || !$theloai || !$luotdung || !$hienthi) {
                msg("error", "Vui lòng nhập đầy đủ thông tin");
            }
            if($giam > 100) {
                msg("error", "Không thể giảm hơn 100%");
            }
            if($luotdung <= 0) {
                msg("error", "Lượt dùng không hợp lệ");
            }
            /*EDIT MÃ GIẢM GIÁ*/
            if($_POST['type2'] == 'EditThemma') {
                $check = $DMH->get_row(" SELECT * FROM `magiamgia` WHERE `id` = '".$_POST['id']."' ");
                if(!$check) {
                    msg("error", "Mã giảm giá không tồn tại trong hệ thống");
                }
                $update = $DMH->update("magiamgia", array(
                    'magiamgia'     => $magiamgia,
                    'giambaonhieu'  => $giam,
                    'theloai'       => $theloai,
                    'conlai'        => $luotdung,
                    'dasudung'      => 0,
                    'hienthi'       => $hienthi
                ), " `id` = '".$_POST['id']."' ");
                if($update) {
                    msg("success","Cập nhật mã giảm giá thành công", "", 2000);
                } else {
                    msg("error", "Cập nhật không thành công");
                }

            }
            else if($_POST['type2'] == 'Themma') {
                $check = $DMH->get_row(" SELECT * FROM `magiamgia` WHERE `magiamgia` = '$magiamgia' ");
                if($check) {
                    msg("error", "Mã giảm giá đã tồn tại trong hệ thống");
                }
                $create = $DMH->insert("magiamgia", [
                    'magiamgia'     => $magiamgia,
                    'giambaonhieu'  => $giam,
                    'theloai'       => $theloai,
                    'conlai'        => $luotdung,
                    'dasudung'      => 0,
                    'hienthi'       => $hienthi
                ]);
                if($create) {
                    msg("success", "Đã thêm mã giảm giá thành công", "", 1000);
                } else {
                    msg("error", "Thêm thất bại");
                }
            }
        }
    } else {
        msg("error", "Vui lòng đăng nhập để truy cập admin","/",1000);
    }
