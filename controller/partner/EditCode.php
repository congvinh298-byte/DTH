<?php

    define("IN_SITE", true);
    require_once("../../core/config.php");
    require_once("../../core/function.php");
    if($_COOKIE['token']) {
        if($getUser['level'] != 'admin') {
            msg("error", "Bạn không phải là ADMIN");
        }
        
        // xử lý phần thêm mã nguồn để bán
        if($_POST['type'] == 'EditThemCode') {
            $id         = check_string($_POST['id']);
            $danhmuc    = check_string($_POST['danhmuc']);
            $name       = check_string($_POST['name']);
            $mota       = check_string($_POST['mota']);
            $money      = check_string(numb($_POST['money']));
            $download   = check_string($_POST['download']);
            $img        = check_string($_POST['img']);
            $listimg    = check_string($_POST['listimg']);
            if(!$danhmuc || !$name || !$mota || !$download || !$img) {
                msg("error","Vui lòng nhập đầy đủ thông tin để tiếp tục");
            }
            if(!filter_var($img, FILTER_VALIDATE_URL))  {
                msg("error","Hình ả nh phải là 1 url");
            }
            if(empty($listimg)) {
                $listimg = $img;
            }
            if(!$row = $DMH->get_row(" SELECT * FROM `partner_code` WHERE `id` = '$id'")) {
                msg("error","ID mã nguồn không tồn tại");
            }
            if($row['status'] != 'xuly') {
                msg("error","Đơn này đã được ADMIN duyệt, bạn không thể thay đổi");
            }
            $DMH->update("partner_code", array(
                'username'      => $getUser['username'],
                'name'          => $name,
                'mota'          => $mota,
                'img'           => $img,
                'list_img'      => $listimg,
                'sotien'        => $money,
                'id_danhmuc'    => $danhmuc,
                'link'          => $download,
                'timeupdate'    => gettime()
            ), " `id` = '$id' ");
            msg("success","Đã cập nhật thành công","", 1000);
        }
    } else {
        msg("error", "Vui lòng đăng nhập để truy cập admin","/",1000);
    }
