<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'LỊCH SỬ MUA CODE';
require_once("../../pages/partner/Head.php");
require_once("../../pages/partner/Header.php");
CheckVeri();
if(isset($_GET['xoa']) && $getUser['verify'] == 1) {
    $row = $TUANORI->get_row(" SELECT * FROM `partner_code` WHERE `id` = '".$_GET['xoa']."' AND `username` = '".$getUser['username']."' ");
    if(!$row) {
        die(msg_admin("error", "Đơn bán code này không tồn tại",BASE_URL('Partner/HistoryCode'), 500));
    } else {
        $TUANORI->remove("partner_code", " `id` = '".$_GET['xoa']."' ");
        die(msg_admin("success", "Đã xóa thành công",BASE_URL('Partner/HistoryCode'), 500));
    }
}
?>

<h2>Lịch sử mua code</h2>

<br />
<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>#</th>
            <th>Mã code</th>
            <th>Tổng tiền nhận</th>
            <th>Thời gian</th>
            <th>Nội dung</th>
            <th>Người mua</th>
		</tr>
	</thead>
	<tbody>
        <?php $i = 0; foreach($TUANORI->get_list(" SELECT * FROM `partner_biendongsodu` WHERE `username` = '".$getUser['username']."' AND `id_code` != 0 ORDER BY id DESC LIMIT 10") as $row){ ?>
        <tr>
            <td><?=++$i;?></td>
            <td><a target="_blank" href="/mua-code/<?=$row['id_code'];?>"><span class="btn btn-info" style="padding: 4px 8px;"><?=$row['id_code'];?></span></a></td>
            <td><b style="color: green">+<?=format_cash($row['tongtien']);?>đ</b></td>
            <td><b><?=$row['time'];?></b></td>
            <td><b style="color: black"><?=$row['note'];?></b></td>
            <td><b style="color: green"><?=$row['usermua'];?></b></td>
        </tr>
        <?php } ?>
	</tbody>
	
</table>

<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
require_once("../../pages/partner/Footer.php");
?>