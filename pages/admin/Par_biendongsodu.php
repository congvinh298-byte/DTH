<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'BIẾN ĐỘNG SỐ DƯ';
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
CheckAdmin();
?>

<h2>Biến động số dư</h2>

<br />
<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>#</th>
            <th>Username</th>
            <th>Tiền trước</th>
            <th>Tổng tiền</th>
            <th>Tiền sau</th>
            <th>Nội dung</th>
            <th>Thời gian</th>
		</tr>
	</thead>
	<tbody>
        <?php $i = 0; foreach($DMH->get_list(" SELECT * FROM `partner_biendongsodu` WHERE `username` = '".$getUser['username']."'  ORDER BY id DESC LIMIT 100") as $row){ ?>
        <tr>
            <td><?=++$i;?></td>
            <td><a href="/pages/admin/EditQuanlythanhvien.php?id=<?=$DMH->getUser($row['username'])['id'];?>" target="_blank" style="color: #0099CC; font-weight: bold;"><?=$row['username'];?></a></td>
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