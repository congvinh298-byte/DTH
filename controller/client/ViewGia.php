<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
if(isset($_GET['duoi']) && isset($_GET['thanhtoan'])) {
    $duoi = check_string($_GET['duoi']);
    $nam  = check_string($_GET['thanhtoan']);
    if($row = $TUANORI->get_row(" SELECT * FROM `danhsachmien` WHERE `id` = '$duoi' ")) {
        if($nam>=1 && $nam < 10) {
            echo number_format($row['money'] + ($nam-1)*$row['giahan']);
            die;
        }
        echo 0; die;
    }
    echo 0; die;

}
else
{
    require_once("../../pages/client/404.php");
}