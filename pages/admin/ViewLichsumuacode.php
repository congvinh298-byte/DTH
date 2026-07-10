<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'LỊCH SỬ MUA CODE';
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
CheckAdmin();
?>
<?php
if(isset($_GET['magd']) && $getUser['level'] == 'admin')
{
    $magd = check_string($_GET['magd']);
    echo 'Mã gd '.$magd;
    $row = $DMH->get_row(" SELECT * FROM `lichsumuacode` WHERE `magd` = '$magd'");
    if(!$row) {
        echo msg_admin("error","Lịch sử mua code không tồn tại",BASE_URL('Admin/Lichsumuacode'), 2000);
        die;
    }
}
else
{
    echo msg_admin("error","Đường link không hợp lệ",BASE_URL('Admin/Lichsumuacode'), 2000);
    die;
}
?>
<h2>Lịch sử mua code của đơn hàng <b style="color: green">#<?=$magd;?></b> <a href="/Admin/Lichsumuacode" class="btn btn-info"><i class="fa fa-backward"></i> Quay Lại</a></h2>

<br />
<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>STT</th>
            <th>Username</th>
            <th>Mã Code</th>
            <th>Mã giảm giá</th>
            <th>Số Tiền</th>
            <th>Thời Gian</th>
		</tr>
	</thead>
	<tbody>
    <?php $i = 1;  foreach($DMH->get_list(" SELECT * FROM `lichsumuacode2` WHERE `magd` = '$magd' ORDER BY id DESC LIMIT 100") as $row){ ?>
        <tr>
            <td><?=$i++;?></td>
            <td><a href="/pages/admin/EditQuanlythanhvien.php?id=<?=$DMH->getUser($row['username'])['id'];?>" target="_blank" style="color: #0099CC; font-weight: bold;"><?=$row['username'];?></a></td>
            <td><a target="_blank" href="/mua-code/<?=$row['id_code'];?>"><span class="btn btn-info" style="padding: 4px 8px;"><?=$row['id_code'];?></span></a></td>
            <td><?=$row['magiamgia'];?></td>
            <td><b style="color: green"><?=format_cash($row['tongtien']);?>đ</b></td>
            <td><b><?=gettime2($row['time']);?></b></td>
        </tr>
    <?php } ?>
	</tbody>
	
</table>

<?php

require_once("../../pages/admin/Footer.php");
?>