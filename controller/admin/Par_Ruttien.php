<?php

    define("IN_SITE", true);
    require_once("../../core/config.php");
    require_once("../../core/function.php");
    if($_COOKIE['token']) {
        if($getUser['level'] != 'admin') {
            msg("error", "Bạn không phải là ADMIN");
        }
        $id     = check_string($_POST['id']);
        $atm    = check_string($_POST['atm']);
        $stk    = check_string($_POST['stk']);
        $name   = check_string($_POST['name']);
        $sotien = check_string(numb($_POST['sotien']));
        $note   = check_string($_POST['note']);
        $status   = check_string($_POST['status']);
        if(!$atm || !$stk || !$name || !$sotien || !$status) {
            msg("error", "Vui lòng không bỏ trống thông tin");
        }
        if(!$row = $DMH->get_row(" SELECT * FROM `partner_ruttien` WHERE `id` = '$id' ")) {
            msg("error", "Đơn rút tiền không tồn tại"); 
        }
        if($row['status'] == 'thatbai') {
            msg("error", "Đơn rút tiền này đã được xử lý từ trước"); 
        }
        $DMH->update("partner_ruttien", array(
            'atm'       => $atm,
            'stk'       => $stk,
            'name'      => $name,
            'sotien'    => $sotien,
            'note'      => $note,
            'status'    => $status
        ), " `id` = '$id' ");
        if($status == 'thatbai') {
            $userr = $DMH->getUser($row['username']);
            $sotien = $row['sotien'] + 5000;
            $isMoney = $DMH->cong("users", "money_partner", $sotien, " `username` = '".$row['username']."'");
            $DMH->insert("partner_biendongsodu", [
                'username'      => $row['username'],
                'usermua'       => $row['username'],
                'truoc'         => $userr['money_partner'],
                'sau'           => $userr['money_partner'] + $sotien,
                'note'          => 'Rút tiền thất bại và hoàn '.number_format($sotien).'đ số tiền về tài khoản website',
                'tongtien'      => $sotien ,
                'id_code'       => 0,
                'time'          => gettime()
            ]);
        }
        msg("success", "Đã cập nhật đơn rút tiền thành công");

    } else {
        msg("error", "Vui lòng đăng nhập để truy cập admin","/",1000);
    }
