<?php

    define("IN_SITE", true);
    require_once("../../core/config.php");
    require_once("../../core/function.php");
    if($_COOKIE['token']) {
        if($getUser['level'] != 'admin') {
            msg("error", "Bạn không phải là ADMIN");
        }
        // xử lý thêm danh mục tạo website
        if($_POST['type'] == 'AddMucWeb') {
            $name       = check_string($_POST['name']);
            $mota       = check_string($_POST['mota']);
            $img        = check_string($_POST['img']);
            // msg("error","Đã thêm thất bại");
            if(!$name || !$mota || !$img) {
                msg("error", "Vui lòng nhập đủ thông tin để tiếp tục");
            }
            if(!filter_var($img, FILTER_VALIDATE_URL))  {
                msg("error", "Hình ảnh phải là 1 url");
            } else {
                msg("error", "Xin chào các bạn nhé");
                /*XỬ LÝ THÊM DANH MỤC*/
                $create = $DMH->insert("danhmuctaoweb", [
                    'title'       => $name,
                    'mota'        => $mota,
                    'img'         => $img
                ]);
                if($create) {
                    msg("success","Đã thêm danh mục thành công","", 1000);
                } else {
                    msg("error","Đã thêm thất bại");
                }
            }
        }

        // xử lý edit danh mục
        if($_POST['type'] == 'EditMucWeb') {
            $name       = check_string($_POST['name']);
            $mota       = check_string($_POST['mota']);
            $img        = check_string($_POST['img']);
            $status     = check_string($_POST['status']);
            $id         = check_string($_POST['id']);
            if(!$name || !$mota || !$img || !$status) {
                msg("error", "Vui lòng nhập đủ thông tin để tiếp tục");
            }
            if(!filter_var($img, FILTER_VALIDATE_URL))  {
                msg("error", "Hình ảnh phải là 1 url");
            } else {
                /*XỬ LÝ THÊM DANH MỤC*/
                $create = $DMH->update("danhmuctaoweb", array(
                    'title'        => $name,
                    'mota'         => $mota,
                    'img'          => $img,
                    'status'       => $status
                ), " `id` = '$id' ");
                if($create) {
                    msg("success","Đã cập nhật danh mục thành công","", 1000);
                } else {
                    msg("error","Đã cập nhật thất bại");
                }
            }
        }

        // xử lý add mẫu website

        if($_POST['type'] == 'AddMauWeb') {
            $id_danhmuc = check_string($_POST['danhmuc']);
            $name       = check_string($_POST['name']);
            $mota       = check_string($_POST['mota']);
            $money      = check_string(numb($_POST['money']));
            $demo       = check_string($_POST['demo']);
            $img        = check_string($_POST['img']);
            $listimg    = check_string($_POST['listimg']);
            
            if(!$id_danhmuc || !$name || !$mota || !$demo || !$img) {
                msg("error","Vui lòng nhập đầy đủ thông tin để tiếp tục");
            }
            if(!filter_var($demo, FILTER_VALIDATE_URL))  {
                msg("error","Link demo phải là 1 url");
            }
            if(!filter_var($img, FILTER_VALIDATE_URL))  {
                msg("error","Hình ảnh phải là 1 url");
            }
            if(empty($listimg)) {
                $listimg = $img;
            }
            /*XỬ LÝ ĐĂNG BÁN MÃ NGUỒN*/
            if($_POST['type2'] == 'AddThemMauWeb') {
                $create = $DMH->insert("danhsachtaoweb", [
                    'id_danhmuc'        => $id_danhmuc,
                    'title'             => $name,
                    'mota'              => $mota,
                    'img'               => $img,
                    'listimg'           => $listimg,
                    'money'             => $money,
                    'demo'              => $demo
                ]);
                if($create) {
                    msg("success", "Đăng thêm mẫu mã website mới thành công","", 1000);
                } else {
                    msg("error","Thêm dữ liệu lỗi");
                }
            }
            
            /*EDIT PHẦN THÊM WEBSITE*/
            if($_POST['type2'] == 'EditThemMauWeb') {
                $DMH->update("danhsachtaoweb", array(
                    'id_danhmuc'        => $id_danhmuc,
                    'title'             => $name,
                    'mota'              => $mota,
                    'img'               => $img,
                    'listimg'           => $listimg,
                    'money'             => $money,
                    'demo'              => $demo,
                    'status'            => $_POST['status']
                ), " `id` = '".$_POST['id']."' ");
                msg("success","Đã cập nhật thành công","", 1000);
            }

        }
    } else {
        msg("error", "Vui lòng đăng nhập để truy cập admin","/",1000);
    }
