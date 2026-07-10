<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'LỊCH SỬ CHUYỂN TIỀN';
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
CheckAdmin();
?>
<h2>Lịch sử chuyển tiền</h2>

<br />
<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>STT</th>
            <th>Người Chuyển</th>
            <th>Người Nhận</th>
            <th>Số Tiền</th>
            <th>Thời Gian</th>
            <th>IP</th>
		</tr>
	</thead>
	<tbody>
        <?php $i = 1;  foreach($DMH->get_list(" SELECT * FROM `chuyentien` ORDER BY id DESC LIMIT 100") as $row){ ?>
        <tr>
            <td><?=$i++;?></td>
            <td><a href="/pages/admin/EditQuanlythanhvien.php?id=<?=$DMH->getUser($row['userchuyen'])['id'];?>" target="_blank" style="color: #0099CC; font-weight: bold;"><?=$row['userchuyen'];?></a></td>
            <td><a href="/pages/admin/EditQuanlythanhvien.php?id=<?=$DMH->getUser($row['usernhan'])['id'];?>" target="_blank" style="color: #333300; font-weight: bold;"><?=$row['usernhan'];?></a></td>
            <td><b style="color: green">+ <?=format_cash($row['sotien']);?>đ</b> </td>
            <td><b><?=gettime2($row['time']);?></b></td>
            <td><?=$row['ip'];?></td>
        </tr>
        <?php } ?>
	</tbody>
	
</table>

<?php

require_once("../../pages/admin/Footer.php");
?>