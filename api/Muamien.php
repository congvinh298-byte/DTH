<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
define("IN_SITE", true);
require_once("../core/config.php");
require_once("../core/function.php");
//https://localhost/api/Taoweb.php?token=25729BD0EACBFABFEB77C72037EF92&domain=tuanorii.com&nam=1&name1=213&name2=2
if(empty($_GET['token'])) {
    json_code(false, "Thiếu dữ liệu token gửi lên");
}
$token = check_string($_GET['token']);
$check_api = $TUANORI->get_row(" SELECT * FROM `users` WHERE `token_api` = '$token' AND `banned` = 'ON' ");
if(!$check_api || $token !== $check_api['token_api']) {
    json_code(false, "Token không tồn tại hoặc tài khoản đã bị đình chỉ");
} else {
    if($TUANORI->get_row(" SELECT * FROM `blockip` WHERE `ip` = '".myip()."' ")) {
        json_code(false, "Bạn đã bị chặn sử dụng tính năng của chúng tôi vĩnh viễn. Xin cảm ơn");
    }
    $api_keys = $TUANORI->get_row(" SELECT * FROM `key_apis` WHERE `username` = '".$check_api['username']."'");
    if($api_keys) {
        if($api_keys['buy_domain'] == 'ON') {
            if(empty($_GET['domain'])) {    
                json_code(false, "Thiếu dữ liệu Tên Miền");
            }
            if(empty($_GET['nam'])) {
                json_code(false, "Thiếu thờ gian mua");
            }
            if(empty($_GET['ns'])) {
                json_code(false, "Vui lòng điền nameserver");
            }
            if(empty($_GET['duoi'])) {
                json_code(false, "Vui lòng nhập đuôi miền");
            }
            if(count(explode('|', $_GET['ns'])) < 2) {
                json_code(false, "Tối thiểu là 2 nameserver");

                
            }
            $ten    = check_string(strtolower($_GET['domain']));
            $duoi   = check_string(strtolower($_GET['duoi']));
            $ns     = check_string($_GET['ns']);
            $nam    = check_string($_GET['nam']);
            if($nam < 1 || $nam > 9) {
                json_code(false, "Năm mua trong khoảng từ 1 đến 9 năm");
            }
            if(preg_match('/[#@! $%^&*()+=\-\[\]\';,.\/{}|":<>?~\\\\]/', $ten)) {
                json_code(false, "Tên miền không được chứa kí tự lạ");
            }
            $data   = $TUANORI->get_row(" SELECT * FROM `danhsachmien` WHERE `domain` = '".$duoi."' ");
            if(!$data) {
                json_code(false, "Chúng tôi không hỗ trợ đuôi miền $duoi");
            }
            // $sotien = $nam*$data['money'];
            // if($sotien > $check_api['money']) {
            //     json_code(false, "Tài khoản của bạn không đủ tiền để mua tên miền");
            // }
            $duoi1 = $TUANORI->get_row(" SELECT * FROM `danhsachmien` WHERE `domain` = '$duoi' ");
            $sotien = $duoi1['money'] + ($nam-1)*$duoi1['giahan'];
            if ($sotien < 1) {
                json_code(false, "Số tiền không hợp lệ");
            }
            if($sotien > $check_api['money']) {
                json_code(false, "Tài khoản của bạn không đủ tiền để mua tên miền");
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://nhanhoa.com/service/');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "view=check_whois&site=whois&domain=".$ten."&ext=.".$duoi."&is_tmtv=0&is_get_premium_price=1");
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
            $row = array();
            $row[] = 'Connection: keep-alive';
            $row[] = 'Sec-Ch-Ua: \"Google Chrome\";v=\"89\", \"Chromium\";v=\"89\", \";Not A Brand\";v=\"99\"';
            $row[] = 'Accept: */*';
            $row[] = 'X-Requested-With: XMLHttpRequest';
            $row[] = 'Sec-Ch-Ua-Mobile: ?0';
            $row[] = 'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.128 Safari/537.36';
            $row[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
            $row[] = 'Origin: https://nhanhoa.com';
            $row[] = 'Sec-Fetch-Site: same-origin';
            $row[] = 'Sec-Fetch-Mode: cors';
            $row[] = 'Sec-Fetch-Dest: empty';
            $row[] = 'Referer: https://nhanhoa.com/';
            $row[] = 'Accept-Language: vi-VN,vi;q=0.9,en-US;q=0.8,en;q=0.7,fr-FR;q=0.6,fr;q=0.5';

            curl_setopt($ch, CURLOPT_HTTPHEADER, $row);
            $result = curl_exec($ch);
            curl_close($ch);
            $check = json_decode($result, true);
            // SAU KHI ĐÃ KIỂM TRA XONG

            $domain = $ten.'.'.$duoi;

            // KIỂM TRA TÊN MIỀN
            if($check['status'] != '1') {
                $isMoney = $TUANORI->tru("users", "money", $sotien, " `token_api` = '$token'");
                if($isMoney) {
                    $nss = '';
                    $datans = [];
                    foreach(explode('|', $ns) as $okk) {
                        $nss .= $okk."\n";
                        array_push($datans, $okk);
                    }
                    $create = $TUANORI->insert("lichsumuamien", [
                        'username'      => $check_api['username'],
                        'domain'        => $domain,
                        'ns'            => $nss,
                        'thoihan'       => $nam,
                        'timemua'       => gettime(),
                        'timedie'       => 0,
                        'tongtien'      => $sotien,
                        'status'        => 'xuly',
                        'type'          => 'API'
                    ]);
                    if($create) {
                        $update = $TUANORI->insert("biendongsodu", [
                            'username'      => $check_api['username'],
                            'truoc'         => $check_api['money'],
                            'sau'           => $check_api['money'] - $sotien,
                            'note'          => 'Mua tên miền '.$domain.' với giá '.format_cash($sotien).'đ qua API',
                            'tongtien'      => $sotien,
                            'time'          => gettime()
                        ]);
                        if($update) {
                            // send_tele("Khách hàng ".$check_api['username']." vừa mua tên miền $domain với giá ".number_format($sotien)."đ");
                            $arrDataPost = array(
                                'status'        => true,
                                'messsage'      => "Mua miền $domain thành công với giá ".number_format($sotien)."đ",
                                'trangthai'     => 'xuly',
                                'timemua'       => gettime(),
                                'nammua'        => $nam,
                                'tongtien'      => $sotien,
                                'nameserver'    => $datans
                            );
                            die(json_encode($arrDataPost));
                        }
                    } else {
                        json_code(false, "Lỗi CSDL");
                    }
                } else {
                    json_code(false, "Trừ tiền lỗi. Không thể mua bây giờ");
                }
            } else {
                json_code(false, "Tên miền $domain đã được đăng ký");
            }
        } else {
            json_code(false, "Chức năng MUA MIỀN của tài khoản này chưa được kích hoạt. Hãy truy cập vào link này: ".BASE_URL('Docs_api')." để kích hoạt nó");
        }
    } else {
        json_code(false, "Token của bạn chưa được Kích Hoạt. Hãy liên hệ admin để được Kích Hoạt token");
    }
}