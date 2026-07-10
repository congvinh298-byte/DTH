<?php

define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
if(isset($_POST['type']))
{
    if($DMH->get_row(" SELECT * FROM `blockip` WHERE `ip` = '".myip()."'  "))
    {
        msg_error2('Bạn đã bị chặn sử dụng tính năng của chúng tôi vĩnh viễn. Xin cảm ơn');
    }
    if(empty($_COOKIE['token']))
    {
        msg_error('Vui lòng đăng nhập để sử dụng tính năng', BASE_URL(''), 1000);
    }
    if(
        $DMH->num_rows("SELECT * FROM `napcard` WHERE `status` = 'thatbai' AND `username` = '".$getUser['username']."' AND `thoigian` >= DATE(NOW()) AND `thoigian` < DATE(NOW()) + INTERVAL 1 DAY  ") - 
        $DMH->num_rows("SELECT * FROM `napcard` WHERE `status` = 'hoantat' AND `username` = '".$getUser['username']."' AND `thoigian` >= DATE(NOW()) AND `thoigian` < DATE(NOW()) + INTERVAL 1 DAY  ") >= 6)
    {
        msg_error2("Rất tiếc. Bạn đang có nhiều thẻ nạp sai. Vui lòng chờ hôm sau để nạp tiếp");
    }
    $seri           = check_string($_POST['seri']);
    $pin            = check_string($_POST['mathe']);
    $type           = check_string($_POST['loaithe']);
    $amount         = check_string($_POST['menhgia']);
    $tranid         = rand(111111111111,999999999);
    if(!$seri || !$pin || !$type || !$amount)
    {
        msg_error2("Vui lòng nhập đầy đủ thông tin");
    }
    if(strlen($pin) < 7)
    {
        msg_error2("Độ dài mã thẻ không đúng định dạng");
    }
    if(strlen($seri) < 7)
    {
        msg_error2("Độ dài seri không đúng định dạng");
    }
    if($DMH->num_rows(" SELECT * FROM `napcard` WHERE `username` = '".$getUser['username']."' AND `status` = 'xuly'") >= 3)
    {
        msg_error2("Bạn đang có 3 thẻ chờ duyệt. Vui lòng duyệt xong rồi nạp tiếp");
    }
    else
    {
        // $data = curl_get('https://thesieure.com/chargingws/v2?sign='.md5($DMH->site('partner_key').$pin.$seri).'&telco='.$type.'&code='.$pin.'&serial='.$seri.'&amount='.$amount.'&request_id='.$tranid.'&partner_id='.$DMH->site('partner_id').'&command=charging');
        $partner_id = '13113605845';
        $partner_key = '7899ea11f5d604645da8c64d17ba677e';
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_CONNECTTIMEOUT => 0,
            CURLOPT_TIMEOUT => 16,
          CURLOPT_URL => 'https://cardvip.vn/chargingws/v2',
            CURLOPT_USERAGENT => 'DMH CURL',
            CURLOPT_POST => 1,
            CURLOPT_SSL_VERIFYPEER => false, //Bỏ kiểm SSL
            CURLOPT_POSTFIELDS => http_build_query(array(
                'sign' => md5($partner_key.$pin.$seri),
                'telco' => $type,
                'code' => $pin,
                'serial' => $seri,
                'amount' => $amount,
                'request_id' => $tranid,
                'partner_id' => $partner_id,
                'command'   => 'charging'
            ))
        ));
        $resp = curl_exec($curl);
        curl_close($curl);
        $data = json_decode($resp, true);
        if(isset($data['status']))
        {
            if($data['status'] == 99)
            {
                $thucnhan = chietkhau($amount, $DMH->site('ckcard'));
                $create = $DMH->insert("napcard", [
                    'username'          => $getUser['username'],
                    'loaithe'           => $type,
                    'menhgia'           => $amount,
                    'seri'              => $seri,
                    'pin'               => $pin,
                    'thucnhan'          => $thucnhan,
                    'requestid'         => $tranid,
                    'status'            => 'xuly',
                    'thoigian'          => gettime()
                ]);
                msg_success("Đã gửi thẻ thành công. Mã thẻ $pin đang chờ xử lý. Vui lòng đợi 10 - 30s", BASE_URL('Profile/NapThe'), 1500);
            }
            else
            {
                msg_error2($data['message']);
            }
        }
        else
        {
            msg_error2('Liên hệ với ADMIN để nạp thẻ. Chức năng đang lỗi');
        }
        
        // $curl = curl_init();
        // curl_setopt_array($curl, array(
        //     CURLOPT_RETURNTRANSFER => 1,
        //     CURLOPT_CONNECTTIMEOUT => 0,
        //     CURLOPT_TIMEOUT => 16,
        //   CURLOPT_URL => 'https://api.cardvip.vn/api/createExchange',
        //     CURLOPT_USERAGENT => 'DMH CURL',
        //     CURLOPT_POST => 1,
        //     CURLOPT_SSL_VERIFYPEER => false, //Bỏ kiểm SSL
        //     CURLOPT_POSTFIELDS => http_build_query(array(
        //         'APIKey' => 'a8d107af-40c7-4de0-9d74-93b3c294c9c5',
        //         'NumberCard' => $pin,
        //         'SeriCard' => $seri,
        //         'NetworkCode' => $type,
        //         'PricesExchange' => $amount,
        //         'IsFast' => 'false',
        //         'RequestId' => $tranid,
        //         'UrlCallback' => 'https://tuanjsc.click/assets/ajaxs/Callback.php'
        
        //     ))
        // ));
        // $resp = curl_exec($curl);
        // curl_close($curl);
        // // print_r($resp);
        // $data = json_decode($resp, true);
        // if(isset($data['status']))
        // {
        //     if($data['status'] == 200)
        //     {
        //         $thucnhan = chietkhau($amount, $DMH->site('ckcard'));
        //         $create = $DMH->insert("napcard", [
        //             'username'          => $getUser['username'],
        //             'loaithe'           => $type,
        //             'menhgia'           => $amount,
        //             'seri'              => $seri,
        //             'pin'               => $pin,
        //             'thucnhan'          => $thucnhan,
        //             'requestid'         => $tranid,
        //             'status'            => 'xuly',
        //             'thoigian'          => gettime()
        //         ]);
        //         msg_success2("Đã gửi thẻ thành công. Mã thẻ $pin đang chờ xử lý. Vui lòng đợi 10 - 30s");
        //     }
        //     else
        //     {
        //         msg_error2($data['message']);
        //     }
        // }
        // else
        // {
        //     msg_error2('Liên hệ với ADMIN để nạp thẻ. Chức năng đang lỗi');
        // }
        
    }
}
else
{
    require_once("../../pages/client/404.php");
}