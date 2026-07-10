<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'BIẾN ĐỘNG SỐ DƯ';
require_once("../../pages/partner/Head.php");
require_once("../../pages/partner/Header.php");
CheckVeri();
if(isset($_GET['xoa']) && $getUser['verify'] == 1) {
    $row = $DMH->get_row(" SELECT * FROM `partner_code` WHERE `id` = '".$_GET['xoa']."' AND `username` = '".$getUser['username']."' ");
    if(!$row) {
        die(msg_admin("error", "Đơn bán code này không tồn tại",BASE_URL('Partner/HistoryCode'), 500));
    } else {
        $DMH->remove("partner_code", " `id` = '".$_GET['xoa']."' ");
        die(msg_admin("success", "Đã xóa thành công",BASE_URL('Partner/HistoryCode'), 500));
    }
}
?>

<h2>Biến động số dư</h2>

<br />
<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>#</th>
            <th>Tiền trước</th>
            <th>Tổng tiền</th>
            <th>Tiền sau</th>
            <th>Nội dung</th>
            <th>Thời gian</th>
		</tr>
	</thead>
	<tbody>
        <?php $i = 0; foreach($DMH->get_list(" SELECT * FROM `partner_biendongsodu` WHERE `username` = '".$getUser['username']."'  ORDER BY id DESC LIMIT 10") as $row){ ?>
        <tr>
            <td><?=++$i;?></td>
            <td><b><?=number_format($row['truoc']);?>đ</b></td>
            <td>
                <?php if($row['sau'] > $row['truoc']) {
                    echo '<b style="color: green">+'.format_cash($row['tongtien']).'đ</b>';
                }else {
                    echo '<b style="color: red">-'.format_cash($row['tongtien']).'đ</b>';
                } ?>
            </td>
            <td><b><?=number_format($row['sau']);?>đ</b></td>
            <td><b><?=$row['time'];?></b></td>
            <td><b style="color: black"><?=$row['note'];?></b></td>
        </tr>
        <?php } ?>
	</tbody>
	
</table>

<?php

require_once("../../pages/partner/Footer.php");
?>