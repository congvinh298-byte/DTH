<?php

/*CRON 1 NGÀY 1 LẦN*/
define("IN_SITE", true);
require_once("../core/config.php");
require_once("../core/function.php");
require_once("../class/Theme1.php");
require_once('../class/class.smtp.php');
require_once('../class/PHPMailerAutoload.php');
require_once('../class/class.phpmailer.php');

/*XỬ LÝ TRẠNG THÁI WEBSITE*/
foreach($DMH->get_list(" SELECT * FROM `lichsutaoweb` WHERE `buoc` IN (4,5)") as $row) {
    // kiểm tra trong 7 ngày
    if($row['buoc'] == 4) {
        if($row['ngayhethan'] <= time() + 24 * 60 * 60 * 7) {
            $DMH->update("lichsutaoweb", array(
                'buoc'  => 5
            ), " `id` = '".$row['id']."' ");
            $bcc = 'dienmayhieu.com';
            $subject = 'Website của bạn sắp hết hạn';
            $hoten = 'Điện Máy Hiếu';
            $img =  $DMH->get_row(" SELECT * FROM `danhsachmuacode` WHERE `id` = '".$row['id_code']."' ");
            $noi_dung = theme1($row['username'], date('d/m/Y H:i:s', $row['ngayhethan']), $img['img'], BASE_URL('QuanLy/TrangWeb/'.$row['id']), $row['tenmien']);
            sendCSM($DMH->getUser($row['username'])['email'], $hoten, $subject, $noi_dung, $bcc);
        }
    } else {
        if($row['ngayhethan'] <= time()) {
            $DMH->update("lichsutaoweb", array(
                'buoc'  => 6
            ), " `id` = '".$row['id']."' ");
        }
    }
}