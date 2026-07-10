<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
    define("IN_SITE", true);
    require_once("../../core/config.php");
    require_once("../../core/function.php");
    if($_COOKIE['token']) {
        if($getUser['level'] != 'admin') {
            msg("error", "Bạn không phải là ADMIN");
        }    
        $id   =  check_string($_POST['id']);
        $status = check_string($_POST['status']);
        $note   =  check_string($_POST['note']);
        if(!$id) {
            msg("error","Chưa nhận được ID hồ sơ");
        }
        
        if(!$row = $TUANORI->get_row(" SELECT * FROM `upload_hoso` WHERE `id` = '$id'")) {
            msg("error","ID hồ sơ không tồn tại");
        }
        if(!$status) {
            msg("error",'Bạn chưa chọn trạng thái cho hồ sơ');
        }
        $veri = 0;
        if($status == 'thanhcong') {
            $veri = 1;
        }
        $TUANORI->update("users", array(
            'verify'    => $veri
        ), " `id` = '".$row['id']."' AND `banned` = 'ON' ");
        $TUANORI->update("upload_hoso", array(
            'status'    => $status,
            'note'      => $note
        ), " `id` = '$id' ");
        msg("success",'Đã cập nhật hồ sơ thành công', "", 1000);

    
    } else {
        msg("error", "Vui lòng đăng nhập để truy cập admin","",1000);
    }