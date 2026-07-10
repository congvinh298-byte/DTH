<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
define("IN_SITE", true);
require_once("../core/config.php");
require_once("../core/function.php");
//https://localhost/api/Muacode.php?token=FA38C5A5D441B94FC3A6262D6C06BF&id=5&mgg=tuanne
if(empty($_GET['token'])) {
    json_code(false, "Thiếu dữ liệu token gửi lên");
}
$token = check_string($_GET['token']);
$check_api = $TUANORI->get_row(" SELECT * FROM `users` WHERE `token_api` = '$token' AND `banned` = 'ON' ");
if(!$check_api || $token !== $check_api['token_api'])
{
    json_code(false, "Token không tồn tại hoặc tài khoản đã bị đình chỉ");
} else {
    if($TUANORI->get_row(" SELECT * FROM `blockip` WHERE `ip` = '".myip()."' ")) {
        json_code(false, "Bạn đã bị chặn sử dụng tính năng của chúng tôi vĩnh viễn. Xin cảm ơn");
    }
    $api_keys = $TUANORI->get_row(" SELECT * FROM `key_apis` WHERE `username` = '".$check_api['username']."'");
    if($api_keys) {
        if($api_keys['buy_code'] == 'ON') {
            /*1. XỬ LÝ KHI CÓ MÃ GIẢM GIÁ*/
            $mgg = NULL;
            if(isset($_GET['mgg'])) {
                $mgg = check_string($_GET['mgg']);
                $check_mgg = $TUANORI->get_row(" SELECT * FROM `magiamgia` WHERE `magiamgia` = '$mgg' AND `conlai` >  0 AND `theloai` = 'muacode' ");
                if(!$check_mgg) {
                    json_code(false, "Mã giảm giá không tồn tại hoặc đã hết lượt sử dụng");
                }
            }
            /*1. KẾT THÚC XỬ LÝ MÃ GIẢM GIÁ*/

            if(empty($_GET['id'])) {
                json_code(false, "Vui lòng chọn ID code cần mua");
            }

            $id_code = check_string($_GET['id']);
            $check_code = $TUANORI->get_row(" SELECT * FROM `danhsachmuacode` WHERE `id` = '$id_code' AND `hienthi` = 'SHOW' ");
            if(!$check_code) {
                json_code(false, "ID code bạn mua không hợp lệ");
            }

            /*2.XỬ LÝ NẾU THỎA MÃN THÔNG TIN TRÊN*/
            if(isset($mgg)) {
                $sotien = $check_code['money'] - ($check_code['money'] * $check_mgg['giambaonhieu'] / 100);
                if($check_code['money'] <= 0) {
                    json_code(false, "Code miễn phí không sử dụng mã giảm giá");
                }
            } else {
                $sotien = $check_code['money'];
            }
            if($sotien > $check_api['money']) {
                json_code(false, "Số tiền bạn không đủ để mua code này");
            }
            if($block = $TUANORI->get_row(" SELECT * FROM `lichsumuacode` WHERE `username` = '".$check_api['username']."' ORDER BY id DESC ")) {
                if($block['tongtien'] == 0 && $block['time2'] + 30 > time()) {
                    $s = $block['time2'] +30 - time();
                    json_code(false, "Vui lòng chờ $s"."s để mua tiếp");
                }
            }
            $isMoney = $TUANORI->tru("users", "money", $sotien, " `token_api` = '$token'");
            if($isMoney) {
                $create = $TUANORI->insert("lichsumuacode", [
                    'username'          => $check_api['username'],
                    'id_code'           => $id_code,
                    'magiamgia'         => $mgg,
                    'tongtien'          => $sotien,
                    'hinhthuc'          => 'API',
                    'time'              => gettime(),
                    'time2'             => time()
                ]);
                if($create) {
                    if(isset($mgg)) {
                        $IsMgg  = $TUANORI->tru("magiamgia", "conlai", 1, " `magiamgia` = '$mgg'");
                        $IsMgg2 = $TUANORI->cong("magiamgia", "dasudung", 1, " `magiamgia` = '$mgg'");
                    }
                    $IsMgg3 = $TUANORI->cong("danhsachmuacode", "luottai", 1, " `id` = '$id_code'");
                    $IsMgg4 = $TUANORI->cong("danhsachmuacode", "luotxem", 1, " `id` = '$id_code'");
                    $arrDataPost = array(
                        'status'        => true,
                        'messsage'      => 'Mua code thành công mã #'.$id_code.' giá '.format_cash($sotien).' đ',
                        'title'         => $check_code['title'],
                        'mota'          => $check_code['mota'],
                        'id_code'       => $id_code,
                        'link_download' => $check_code['download'],
                        'timemua'       => gettime()
                    );
                    if($sotien > 0) {
                        $TUANORI->insert("biendongsodu", [
                            'username'      => $getUser['username'],
                            'truoc'         => $check_api['money'],
                            'sau'           => $check_api['money'] - $sotien,
                            'note'          => 'Mua code thành công mã #'.$id_code.' giá '.format_cash($sotien).' đ qua API',
                            'tongtien'      => $sotien,
                            'time'          => gettime()
                        ]);
                    }
                    die(json_encode($arrDataPost));
                }
                else {
                    json_code(false, "Lỗi lưu lịch sử mua code trên hệ thống. Không thể mua bây giờ");
                }

            } else {
                json_code(false, "Lỗi trừ tiền hệ thống. Không thể mua bây giờ");
            }
            /*2. KẾT THÚC XỬ LÝ THÔNG TIN TRÊN*/


        } else {
            json_code(false, "Chức năng MUA CODE của tài khoản này chưa được kích hoạt. Hãy truy cập vào link này: ".BASE_URL('Docs_api')." để kích hoạt nó");
        }
    } else {
        json_code(false, "Token của bạn chưa được Kích Hoạt. Hãy liên hệ admin để được Kích Hoạt token");
    }
}