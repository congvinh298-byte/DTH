<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
    define("IN_SITE", true);
    require_once("../../core/config.php");
    require_once("../../core/function.php");
    if($_COOKIE['token']) {
        if($getUser['level'] != 'admin') {
            msg("error", "Bạn không phải là ADMIN");
        }
        //  XỬ LÝ PHẦN DANH MỤC CODE
        if($_POST['type'] == 'DanhMucCode')
        {
            $name       = check_string($_POST['name']);
            $mota       = check_string($_POST['mota']);
            $img        = check_string($_POST['img']);
            if(!$name || !$mota || !$img) {
                msg("error","Vui lòng nhập đủ thông tin để tiếp tục");
            }
            if(!filter_var($img, FILTER_VALIDATE_URL))  {
                msg("error","Hình ảnh phải là 1 url");
            } else {
                /*XỬ LÝ THÊM DANH MỤC*/
                if($_POST['type2'] == 'AddDanhmuccode')
                {
                    $create = $TUANORI->insert("danhmucmuacode", [
                        'title'       => $name,
                        'mota'        => $mota,
                        'img'         => $img
                    ]);
                    if($create) {
                        msg("success","Thêm thành công","", 1000);
                    } else {
                        msg("error","Thêm dữ liệu lỗi");
                    }
                }
                /*XỬ LÝ EDIT DANH MỤC*/
                if($_POST['type2'] == 'EditDanhmuccode') {
                    $status     = check_string($_POST['status']);
                    if(!$status) {
                        msg("error","Vui lòng chọn status");
                    }
                    $TUANORI->update("danhmucmuacode", array(
                        'title'        => $name,
                        'mota'         => $mota,
                        'img'          => $img,
                        'status'       => $status
                    ), " `id` = '".$_POST['id']."' ");
                    msg("success","Đã lưu thay đổi thành công",BASE_URL('pages/admin/Danhmucbancode.php'), 1000);
                }
            }
        }


        // xử lý phần thêm mã nguồn để bán
        if($_POST['type'] == 'ThemCode') {
            $id_danhmuc = check_string($_POST['danhmuc']);
            $name       = check_string($_POST['name']);
            $mota       = check_string($_POST['mota']);
            $money      = check_string(numb($_POST['money']));
            $download   = check_string($_POST['download']);
            $demo       = check_string($_POST['demo']);
            $img        = check_string($_POST['img']);
            $listimg    = check_string($_POST['listimg']);
            $partner    = check_string($_POST['partner']);
            if(!$id_danhmuc || !$name || !$mota || !$download || !$demo || !$img || !$partner)
            {
                msg("error","Vui lòng nhập đầy đủ thông tin để tiếp tục");
            }
            if(!filter_var($demo, FILTER_VALIDATE_URL)) 
            {
                msg("error","Link demo phải là 1 url");
            }
            if(!filter_var($img, FILTER_VALIDATE_URL)) 
            {
                msg("error","Hình ảnh phải là 1 url");
            }
            if(empty($listimg))
            {
                $listimg = $img;
            }
                /*XỬ LÝ ĐĂNG BÁN MÃ NGUỒN*/

            if($_POST['type2'] == 'AddThemCode') {
                $create = $TUANORI->insert("danhsachmuacode", [
                    'id_danhmuc'        => $id_danhmuc,
                    'title'             => $name,
                    'mota'              => $mota,
                    'img'               => $img,
                    'listimg'           => $listimg,
                    'money'             => $money,
                    'demo'              => $demo,
                    'download_link1s'   => curl_get("https://link1s.com/api?api=433a9ae8e5f78c9eed17f7405d0cdc48b1853366&url=$download&format=text"),
                    'download'          => $download,
                    'ngaydang'          => gettime(),
                    'partner'           => $partner
                ]);
                if($create) {
                    msg("success","Đăng bán mã nguồn thành công","", 1000);
                } else {
                    msg("error","Thêm dữ liệu lỗi");
                }
            }
            
            /*EDIT PHẦN THÊM CODE*/
            
            if($_POST['type2'] == 'EditThemCode')
            {
                // msg("error","Thêm dữ liệu lỗi");
                $TUANORI->update("danhsachmuacode", array(
                    'id_danhmuc'        => $id_danhmuc,
                    'title'             => $name,
                    'mota'              => $mota,
                    'img'               => $img,
                    'listimg'           => $listimg,
                    'money'             => $money,
                    'demo'              => $demo,
                    'download'          => $download,
                    'download_link1s'   => check_string($_POST['download_link1s']),
                    'hienthi'           => check_string($_POST['hienthi']),
                    'statusmua'         => check_string($_POST['statusmua']),
                    'timeupdate'        => gettime(),
                    'partner'           => $partner
                ), " `id` = '".$_POST['id']."' ");
                msg("success","Đã cập nhật thành công","", 1000);
            }
        
        }
    } else {
        msg("error", "Vui lòng đăng nhập để truy cập admin","/",1000);
    }
