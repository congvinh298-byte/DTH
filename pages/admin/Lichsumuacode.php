<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'LỊCH SỬ MUA CODE';
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
CheckAdmin();
?>
<h2>Lịch sử mua code</h2>

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
    <?php $i = 1;  foreach($DMH->get_list(" SELECT * FROM `lichsumuacode` ORDER BY id DESC LIMIT 100") as $row){ ?>
        <tr>
            <td><?=$i++;?></td>
            <td><a href="/pages/admin/EditQuanlythanhvien.php?id=<?=$DMH->getUser($row['username'])['id'];?>" target="_blank" style="color: #0099CC; font-weight: bold;"><?=$row['username'];?></a></td>
            <?php
                if(isset($row['magd'])) {
                    $url = 'Admin/View/Muacode/'.$row['id_code'];
                } else {
                    $url = 'mua-code/'.$row['id_code'];
                }
            ?>
            <td><a href="<?=BASE_URL($url);?>" target="_blank" style="color: red; font-weight: bold;"><?=$row['id_code'];?></a></td>
            <td><?=$row['magiamgia'];?></td>
            <td><b style="color: green"><?=format_cash($row['tongtien']);?>đ</b></td>
            <td><b><?=$row['time'];?></b></td>
        </tr>
    <?php } ?>
	</tbody>
	
</table>

<?php

require_once("../../pages/admin/Footer.php");
?>