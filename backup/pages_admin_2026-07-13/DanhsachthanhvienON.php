<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'THÀNH VIÊN ĐANG ONLINE';
CheckAdmin();
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
?>
<h2>Thành viên đang ONLINE</h2>

<br />

<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>STT</th>
            <th>Username</th>
            <th>Email</th>
            <th>Số tiền</th>
            <th>Tổng nạp</th>
            <th>Online gần nhất</th>
            <th>Trạng Thái</th>
            <th>Thao tác</th>
		</tr>
	</thead>
	<tbody>
		<?php $i = 0;  foreach($DMH->get_list(" SELECT * FROM `users` WHERE `banned` = 'ON' AND `online` = 'ONLINE' ORDER BY id DESC") as $row){ ?>
		<tr>
            <td><?=++$i;?></td>
			<td><b style="color: green"><?=$row['username'];?></b></td>
			<td><b style="color: green"><?=$row['email'];?></b></td>
			<td><b style="color: green"><?=format_cash($row['money']);?>đ</b></td>
			<td><b style="color: green"><?=format_cash($row['total_money']);?>đ</b></td>
			<td><?=$row['timeon'];?></td>
			<td><?=online($row['online']);?></td>
            <td>
				<a href="<?=BASE_URL('pages/admin/EditQuanlythanhvien.php?id='.$row['id'].'');?>" class="btn btn-default btn-sm btn-icon icon-left">
					<i class="entypo-pencil"></i>
					Edit
				</a>
				
            </td>
		</tr>
		<?php } ?>
	</tbody>
	
</table>

<?php

require_once("../../pages/admin/Footer.php");
?>