<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'LỊCH SỬ NẠP ATM';
CheckAdmin();
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
?>
<h2>Lịch sử nạp ATM <a href="/Admin/AddNapAtm" class="btn btn-info"><i class="fa fa-cloud-upload"></i>  Thêm thông tin</a></h2>

<br />
<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>STT</th>
            <th>Username</th>
            <th>ND Nạp tiền</th>
            <th>Mã giao dịch</th>
            <th>Ngân Hàng</th>
            <th>Số tiền</th>
            <th>Thời gian</th>
            <th>Status</th>
		</tr>
	</thead>
	<tbody>
    <?php $i = 1;  foreach($DMH->get_list(" SELECT * FROM `napatm` ORDER BY id DESC LIMIT 100") as $row){ ?>
        <tr>
            <td><?=$i++;?></td>
            <td><a href="/pages/admin/EditQuanlythanhvien.php?id=<?=$DMH->getUser($row['username'])['id'];?>" target="_blank" style="color: #0099CC; font-weight: bold;"><?=$row['username'];?></a></td>
            <td style="font-weight: bold;"><?=$row['ndnaptien'];?></td>
            <td style="font-weight: bold; color: black"><?=$row['magd'];?></td>
            <td><?=$row['hinhthuc'];?></td>
            <td><b style="color: green"><?=format_cash($row['sotien']);?>đ</b></td>
            <td><?=$row['thoigian'];?></td>
            <td><span class="badge badge-success">Thành công</span></td>
        </tr>
    <?php } ?>
	</tbody>
	
</table>

<?php

require_once("../../pages/admin/Footer.php");
?>