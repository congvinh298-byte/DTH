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
            <th>STT</th>
            <th>Username</th>
            <th>Trước</th>
            <th>Số tiền</th>
            <th>Sau</th>
            <th>Note</th>
            <th>Thời Gian</th>
		</tr>
	</thead>
	<tbody>
        <?php $i = 1;  foreach($TUANORI->get_list(" SELECT * FROM `biendongsodu` ORDER BY id DESC LIMIT 100") as $row){ ?>
        <tr>
            <td><?=$i++;?></td>
            <td><a href="/pages/admin/EditQuanlythanhvien.php?id=<?=$TUANORI->getUser($row['username'])['id'];?>" target="_blank" style="color: #0099CC; font-weight: bold;"><?=$row['username'];?></a></td>
            <td style="font-weight: bold;"><?=sotienmua($row['truoc']);?></td>
            <?php if($row['sau'] >= $row['truoc']) { ?>
                <td style="color: green; font-weight: bold;" >+<?=number_format($row['tongtien']);?>₫</td>
            <?php } else { ?>
                <td style="color: red; font-weight: bold;">-<?=number_format($row['tongtien']);?>₫</td>
            <?php } ?>
            <td style="font-weight: bold;"><?=sotienmua($row['sau']);?></td>

            <td><b><?=inkq($row['note'], 'Không Có');?></b></td>
            <td><b><?=$row['time'];?></b></td>
        </tr>
        <?php } ?>
	</tbody>
	
</table>

<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
require_once("../../pages/admin/Footer.php");
?>