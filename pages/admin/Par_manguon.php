<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'Mã nguồn đang bán';
CheckAdmin();
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
?>
<?php
if(isset($_GET['xoa']) && $getUser['level'] == 'admin')
{
    $user2 = $DMH->get_row(" SELECT * FROM `partner_code` WHERE `id` = '".$_GET['xoa']."'");
    if(!$user2) {
        echo msg_admin("error", "Đơn đăng bán code không tồn tại", BASE_URL('Admin/Par_manguon'), 1000);
    } else {
        $dele = $DMH->remove("partner_code", " `id` = '".$_GET['xoa']."' ");
        if($dele) {
            echo msg_admin("success","Đã đơn bán code thành công", BASE_URL('Admin/Par_manguon'), 1000);
        } else {
            echo msg_admin("error", "Xóa thất bại. Lỗi hệ thống", BASE_URL('Admin/Par_manguon'), 1000);
        }
    }
}
?>

<h2>Mã nguồn của cộng tác viên</h2>
<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>STT</th>
            <th width="5%">Danh Mục</th>
            <th width="25%">Tên</th>
            <th>Giá Bán</th>
            <th>Lượt tải</th>
            <th>Thumbnail</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
		</tr>
	</thead>
	<?php $i = 1;  foreach($DMH->get_list(" SELECT * FROM `partner_code` ORDER BY id DESC LIMIT 50") as $row){ ?>
        <tr>
            <td><?=$i++;?></td>
            <td><a target="_blank" href="/pages/admin/EditDanhmucbancode.php?id=<?=$row['id_danhmuc'];?>"><span class="btn btn-info" style="padding: 4px 8px;"><?=$row['id_danhmuc'];?></span></a></td>
            <td><b style="font-size: 13px; color: black"><?=$row['name'];?></b></td>
            <td style="color: green; font-weight:bold;"><?=sotienmua(format_cash($row['sotien']));?></td>
            <td style="color: red; font-weight:bold;"><?=number_format($DMH->get_row(" SELECT * FROM `danhsachmuacode` WHERE `id` = '".$row['id_public']."' ")['luottai'] ?? 0);?> lượt</td>
            <td> <img class="rounded" src="<?=$row['img'];?>" style="width: 300px; height: 100px"> </td>
            <td><?=hoso($row['status']);?></td>
            <td>
                <a href="<?=BASE_URL('pages/admin/EditPar_manguon.php?id='.$row['id']);?>" class="btn btn-default btn-sm btn-icon icon-left">
					<i class="entypo-pencil"></i>
					Edit
				</a>
				<a href="<?=BASE_URL('Admin/Par_manguon?xoa='.$row['id'].'');?>" class="btn btn-danger btn-sm btn-icon icon-left">
					<i class="entypo-cancel"></i>
					Delete
				</a>
            </td>
        </tr>
    <?php } ?>
	</tbody>
	
</table>

<?php

require_once("../../pages/admin/Footer.php");
?>

