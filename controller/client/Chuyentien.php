<?php

    define("IN_SITE", true);
    require_once("../../core/config.php");
    require_once("../../core/function.php");

    if(empty($_COOKIE['token'])) {
        msg_error2('Vui lòng đăng nhập để chuyển tiền');
    }
    $user       = check_string($_POST['username']);
    $sotien     = check_string($_POST['sotien']);
    if(!$sotien || !$user){
        msg_error2("Vui lòng nhập đầy đủ thông tin");
    }
    if($sotien > $my_money)
    {
        msg_error2("Số tiền bạn không đủ để thực hiện chuyển tiền");
    }
    if($sotien < 10000) msg_error2("Số tiền chuyển tối thiểu là 10.000đ");
    if($sotien > 100000000) msg_error2("Số tiền chuyển tối đa là 1000.000.000đ");
    if($getUser['username'] == $user) {
        msg_error2("Không thể tự chuyển cho chính mình");
    }
    $check = $DMH->get_row(" SELECT * FROM `users` WHERE `username` = '$user' AND `banned` = 'ON'");
    if(!$check)
    {
        msg_error2("Người nhận không tồn tại hoặc người dùng đã bị đình chỉ");
    }
    $job = 'Chuyển '.format_cash($sotien).'đ cho thành viên '.$user.'';
    $isMoney = $DMH->tru("users", "money", $sotien, " `tokenlog` = '".$_COOKIE['token']."'");
    if($isMoney)
    {
        $data = $DMH->insert("chuyentien", [
            'userchuyen'    => $getUser['username'],
            'usernhan'      => $user,
            'sotien'        => $sotien,
            'time'          => time(),
            'ip'            => myip()
        ]);
        if($data) {
            $DMH->insert("biendongsodu", [
                'username'      => $getUser['username'],
                'truoc'         => $my_money,
                'sau'           => $my_money - $sotien, 
                'note'          => 'Chuyển '.format_cash($sotien).'đ cho thành viên '.$user.' ',
                'tongtien'      => $sotien,
                'time'          => gettime()
            ]);
            $DMH->insert("biendongsodu", [
                'username'      => $user,
                'truoc'         => $DMH->getUser($user)['money'],
                'sau'           => $DMH->getUser($user)['money'] + $sotien, 
                'note'          => 'Nhận '.format_cash($sotien).'đ từ thành viên '.$getUser['username'].'',
                'tongtien'      => $sotien,
                'time'          => gettime()
            ]);
            $isMoney2 = $DMH->cong("users", "money", $sotien, " `username` = '$user'");
            msg_success("Đã thực hiện chuyển tiền thành công.", "", 1000);
        }
    }
    