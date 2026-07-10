<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
    define("IN_SITE", true);
    require_once("../../core/config.php");
    require_once("../../core/function.php");
    if($_COOKIE['token']) {
        if($getUser['level'] != 'admin') {
            msg("error", "Bạn không phải là ADMIN");
        }
        $row = $TUANORI->get_row(" SELECT * FROM `users` WHERE `id` = '".check_string($_POST['id'])."'");
        if(!$row) {
            msg("error", "Users không tồn tại");
        }

        // xử lý cập nhật thông tin
        if($_POST['type'] == 'UpdateInfo') {
            $email      = check_string($_POST['email']);
            $sotien     = check_string(numb($_POST['sotien']));
            $tongtien   = check_string(numb($_POST['tongtien']));
            $status     = check_string($_POST['status']);
            $veri       = check_string($_POST['veri']);
            if(check_email($email) != 'True') {
                msg("error", "Email không hợp lệ");
            }
            if($sotien < 0 || $tongtien < 0) {
                msg("error", "Số tiền không thể âm");
            }
            if($sotien != $row['money']) {
                $TUANORI->insert("biendongsodu", [
                    'username'      => $row['username'],
                    'truoc'         => $row['money'], 
                    'sau'           => $sotien,
                    'tongtien'      => abs($sotien - $row['money']),
                    'note'          => 'Admin thay đổi số dư của thành viên',
                    'time'          => gettime()
                ]);
            }
            $TUANORI->update("users", array(
                'email'         => $email,
                'money'         => $sotien,
                'total_money'   => $tongtien,
                'banned'        => $status,
                'verify'        => $veri
            ), " `id` = '".$row['id']."' ");
            if($status == 'OFF') {
                /*THAY ĐỔI TOKEN LOG = LOGOUT*/
                $TUANORI->update("users", array(
                    'tokenlog'         => rand(111,999)
                ), " `id` = '".$row['id']."' ");
            }
            msg("success", "Đã cập nhật thông tin thành công","",1000);
        }

        // xử lý cộng tiền
        if($_POST['type'] == 'Congtien') {
            $sotien = check_string(numb($_POST['sotien']));
            $ghichu = check_string($_POST['ghichu']);
            if($sotien <= 0 ) {
                msg("error", "Số tiền cộng không hợp lệ");
            }
            $create = $TUANORI->insert("biendongsodu", [
                'username'      => $row['username'],
                'note'          => $ghichu,
                'truoc'         => $row['money'],
                'sau'           => $row['money'] + $sotien,
                'tongtien'      => $sotien,
                'time'          => gettime()
            ]);
            
            if($create) {
                $TUANORI->cong("users", "money", $sotien, " `username` = '".$row['username']."' ");
                $TUANORI->cong("users", "total_money", $sotien, " `username` = '".$row['username']."' ");
                msg("success","Cộng tiền thành công!", "", 2000);
            } else {
                msg("error", "Thêm dữ liệu lỗi!");
            }
        }

        // xử lý trừ tiền
        if($_POST['type'] == 'Trutien') {
            $sotien = check_string(numb($_POST['sotien']));
            $ghichu = check_string($_POST['ghichu']);
            if($sotien <= 0 ) {
                msg("error", "Số tiền cộng không hợp lệ");
            }
            if($row['money'] < $sotien) {
                msg("error", "Số tiền bị âm sau khi trừ");
            }
            $create = $TUANORI->insert("biendongsodu", [
                'username'      => $row['username'],
                'note'          => $ghichu,
                'truoc'         => $row['money'],
                'sau'           => $row['money'] - $sotien,
                'tongtien'      => $sotien,
                'time'          => gettime()
            ]);
            if($create) {
                $TUANORI->tru("users", "money", $sotien, " `username` = '".$row['username']."' ");
                msg("success","Trừ tiền thành công!", "", 2000);
            } else {
                msg("error", "Thêm dữ liệu lỗi!");
            }
        }
    } else {
        msg("error", "Vui lòng đăng nhập để truy cập admin","/",1000);
    }
