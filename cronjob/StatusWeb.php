<?php

/*CRON 1 NGÀY 1 LẦN*/
define("IN_SITE", true);
require_once("../core/config.php");
require_once("../core/function.php");
/*XỬ LÝ TRẠNG THÁI ON OFF SỰ KIỆN*/
if($DMH->site('timeskoff') < time() && $DMH->site('sukien') == 'ON') {
    $DMH->update("options", array(
            'value' => 'OFF'
        ), " `key` = 'sukien' ");
}

/*XỬ LÝ TẠO HÓA ĐƠN NẠP, NẾU QUÁ 2 TIẾNG MÀ CHƯA ĐƯỢC XỬ LÝ SẼ BỊ HỦY*/
foreach($DMH->get_list(" SELECT * FROM `hoadon_vi` WHERE `status` = 'xuly' ORDER BY id DESC") as $ok) {
    if(strtotime($ok['thoigian']) + 7200 < time()) {
        $DMH->update("hoadon_vi", array(
            'status'       => 'huy',
        ), " `id` = '".$ok['id']."' AND `status` = 'xuly' ");
    }
}

/*XỬ LÝ ON OFF THÀNH VIÊN*/
foreach($DMH->get_list(" SELECT * FROM `users` WHERE `banned` = 'ON' AND `online` = 'ONLINE'") as $data2) {
    $tt1 = gettime2(time() - 300);
    if($tt1 > $data2['timeon']) {
        $DMH->update("users", array(
            'online' => 'OFFLINE'
        ), " `username` = '".$data2['username']."' AND `banned` = 'ON' AND `online` = 'ONLINE' ");
        
    }
}

/*XỬ LÝ TÊN MIỀN HẾT HẠN*/
foreach($DMH->get_list(" SELECT * FROM `lichsumuamien` WHERE `status` = 'thanhcong' ") as $data3) {
    if(time() > strtotime($data3['timedie'])) {
        $DMH->update("lichsumuamien", array(
            'status' => 'hethan'
        ), " `id` = '".$data3['id']."' AND `status` = 'thanhcong' ");
    }
}