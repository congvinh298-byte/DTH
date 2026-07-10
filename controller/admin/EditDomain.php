<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
    define("IN_SITE", true);
    require_once("../../core/config.php");
    require_once("../../core/function.php");
    if($_COOKIE['token']) {
        if($getUser['level'] != 'admin') {
            msg("error", "Bạn không phải là ADMIN");
        }
        // xử lý thêm danh mục tạo website
        if($_POST['type'] == 'EditDomain')
        {
            $id     = check_string($_POST['id']);
            $domain = check_string($_POST['domain']);
            $ns    = check_string($_POST['ns']);
            $tgian  = check_string($_POST['thoihan']);
            $status = check_string($_POST['status']);
            
            if(!$domain || !$ns || !$tgian || !$status) {
                msg("error","Vui lòng nhập đủ thông tin");
            }
            if($tgian < 1 || $tgian >=10) {
                msg("error","Thời gian mua từ 1 đến 9 năm");
            }
            if(count( explode("\n", $ns) ) < 2) {
                msg("error","Tối thiểu có 2 nameserver");
            }
            $check = $TUANORI->get_row(" SELECT * FROM `lichsumuamien` WHERE `id` = '$id'");
            if(!$check) {
                msg("error","Tên miền không tồn tại trong hệ thống");
            }
            else
            {
                if($status == 'thatbai') {
                    if($check['status'] == 'thatbai') {
                        msg("error","Đơn này đã được hoàn tiền rồi. Nên không thể hoàn tiền nữa");
                    }
                    $isMoney    = $TUANORI->cong("users", "money", $check['tongtien'], " `username` = '".$check['username']."'");
                    if($isMoney) {
                        $TUANORI->insert("biendongsodu", [
                            'username'      => $check['username'],
                            'truoc'         => $TUANORI->getUser($check['username'])['money'] - $check['tongtien'],
                            'sau'           => $TUANORI->getUser($check['username'])['money'],
                            'tongtien'      => $check['tongtien'],
                            'note'          => 'Hệ thống hoàn tiền mua tên miền '.$check['domain'].' vì thất bại',
                            'time'          => gettime()
                        ]);
                    }
                }
                $timedie = '-62169987208'; // chỉnh về 0000-00-00 00:00:00
                if(strtotime($check['timedie']) > 0) {
                    $timedie = $check['timedie'];
                }
                else if($status == 'thanhcong') {
                    $timedie = gettime2(time() + $onethang*12*$tgian);
                }
                $update = $TUANORI->update("lichsumuamien", array(
                    'domain'    => $domain,
                    'ns'        => $ns,
                    'thoihan'   => $tgian,
                    'status'    => $status,
                    'timedie'   => $timedie
                ), " `id` = '$id' ");
                if($update){
                    msg("success","Đã cập nhật thành công");
                } else {
                    msg("error","Lỗi hệ thống cơ sỡ dữ liệu");
                }
            }
        }
    } else {
        msg("error", "Vui lòng đăng nhập để truy cập admin","/",1000);
    }