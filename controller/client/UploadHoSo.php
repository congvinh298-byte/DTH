<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
    define("IN_SITE", true);
    require_once("../../core/config.php");
    require_once("../../core/function.php");

    if(empty($_COOKIE['token'])) {
        msg_error2('Vui lòng đăng nhập để chuyển tiền');
    }
    if($getUser['verify']) {
        msg_error2('Tài khoản của bạn đã được xác minh rồi.');
    }
    if($getUser['total_money'] < 0) {
        msg_error2('Phải phải nạp ít nhất 1đ mới có thể gửi đơn.');
    }
    if(!check_string($_POST['check'])) {
        msg_error2("Vui lòng tích vào ô: Xác nhận đã gửi đúng thông tin.");
    }
    if(empty($_FILES['file1']) || empty($_FILES['file2']) || empty($_FILES['file3'])) {
        msg_error2('Vui lòng gửi ảnh lên cho đầy đủ.');
    }
    if($TUANORI->get_row(" SELECT * FROM `upload_hoso` WHERE `username` = '".$getUser['username']."' AND `status` = 'xuly'  ")) {
        msg_error2('Bạn đang có hồ sơ đang chờ xử lý.');
    }
    $arr = [];
    $resultCreate       = upload_imgur($_FILES['file1']['tmp_name']);
    array_push($arr, json_decode($resultCreate, true)['data']['link']);
    $resultCreate       = upload_imgur($_FILES['file2']['tmp_name']);
    array_push($arr, json_decode($resultCreate, true)['data']['link']);
    $resultCreate       = upload_imgur($_FILES['file3']['tmp_name']);
    array_push($arr, json_decode($resultCreate, true)['data']['link']);
    /*XỬ LÝ THÔNG TIN KHI ĐÃ HOÀN THÀNH */
    $create = $TUANORI->insert("upload_hoso", [
        'username'  => $getUser['username'],
        'mattruoc'  => $arr[0],
        'matsau'    => $arr[1],
        'chandung'  => $arr[2],
        'thoigian'  => gettime()
    ]);
    msg_success2('Đã gửi thông tin thành công, vui lòng chờ ADMIN.');


