<?php
include_once __DIR__.'/libs/simple_html_dom.php';
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
/*MÃ NGUỒN ĐƯỢC VIẾT LẠI BỞI TUANORI.COM*/
$username = $TUANORI->site('tk_tsr');
$password = $TUANORI->site('mk_tsr');
if($TUANORI->site('status_tsr') != 'ON')
{
    die();
}
$token = $TUANORI->site('cookie_thesieure');
$url = "https://thesieure.com/wallet/transfer";
    $head = array(
        "Host:thesieure.com",
        "referer:https://thesieure.com/",
        "cookie:$token"
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Linux; Android 10; SM-J600G) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/83.0.4103.106 Mobile Safari/537.36");
    curl_setopt($ch, CURLOPT_HTTPHEADER, $head);
    $mr2 = curl_exec($ch);
    curl_close($ch);
    $rs = str_get_html($mr2);
    $lol = $rs->find('tbody', 1);
    $array = [];
    if ($lol) {
        foreach ($lol->find('tr') as $article) {
            // $ma_GD = $article->find('td', 0)->plaintext;
            $so_tien = $article->find('td', 1);
            $txt_sotien = $so_tien->find('span', 0)->plaintext;
            $nguoigui_nhan = $article->find('td', 2);
            $txt_nguoiguinhan = $nguoigui_nhan->find('p', 1)->plaintext;
            $ngay_tao = $article->find('td', 3)->plaintext;
            $trang_thai = $article->find('td', 4);
            $txt_trangthai = $trang_thai->find('span', 0)->plaintext;
            $noi_dung = $article->find('td', 5)->plaintext;
            $sotien = intval(preg_replace("/đ|,|\-|\+/i", '', $txt_sotien));
            if($txt_sotien[0] == "-") {
                $sotien = -$sotien; 
            }
            $array[] = [
                    "transId" => substr(strtoupper(md5($txt_nguoiguinhan.$ngay_tao)), 0, 10),
                    // "amount" => $txt_sotien,
                    "amount"    => $sotien,
                    "username" => $txt_nguoiguinhan,
                    "date" => $ngay_tao,
                    "status" => $txt_trangthai,
                    "description" => trim($noi_dung),
            ];
        }

    }
    die(json_encode(array("status" => true,"msg" => "Thành công","tranList" =>$array)));