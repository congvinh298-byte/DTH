<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'LỊCH SỬ TẠO WEBSITE';
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
CheckAdmin();
?>
<?php
if(isset($_GET['xoa']) && $getUser['level'] == 'admin')
{
    $user2 = $TUANORI->get_row(" SELECT * FROM `lichsutaoweb` WHERE `id` = '".$_GET['xoa']."'");
    if(!$user2) {
        echo msg("error", "Lịch sử tạo website không tồn tại", BASE_URL('Admin/Quanlytaoweb'), 1000); die;
    } else {
        $dele = $TUANORI->remove("lichsutaoweb", " `id` = '".$_GET['xoa']."' ");
        if($dele) {
            echo msg_admin("success","Đã xóa lịch sử tạo website  thành công", BASE_URL('Admin/Quanlytaoweb'), 1000);
        } else {
            echo msg("error", "Xóa thất bại. Lỗi hệ thống", BASE_URL('Admin/Quanlytaoweb'), 1000);
        }
    }
}
?>
<h2>Lịch sử tạo website</h2>

<br />
<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>STT</th>
            <th width="5%">Username</th>
            <th width="7%">Tên Miền</th>
            <th width="5%">Mã Tạo</th>
            <th>Ngày Tạo</th>
            <th>Ngày Duyệt</th>
            <th>Ngày Hết Hạn</th>
            <th>STATUS</th>
            <th>Thao tác</th>
		</tr>
	</thead>
	<tbody>
    <?php $i = 1;  foreach($TUANORI->get_list(" SELECT * FROM `lichsutaoweb` ORDER BY id DESC LIMIT 100") as $row){ ?>
        <tr>
            <td><?=$i++;?></td>
            <td><a href="/pages/admin/EditQuanlythanhvien.php?id=<?=$TUANORI->getUser($row['username'])['id'];?>" target="_blank" style="color: #0099CC; font-weight: bold;"><?=$row['username'];?></a></td>
            <td><a href="//<?=$row['tenmien'];?>" target="_blank" style="color: green; font-weight: bold;"><?=$row['tenmien'];?></a></td>
            <td><a target="_blank" href="/tao-web/<?=$row['id_code'];?>"><span class="btn btn-info" style="padding: 4px 8px;"><?=$row['id_code'];?></span></a></td>
            <td style="font-weight: bold;"><?=format_date($row['ngaytao']);?></td>
            <td style="font-weight: bold;"><?=timeran($row['ngayduyet']);?></td>
            <td style="font-weight: bold;"><?=timeran($row['ngayhethan']);?></td>
            <td><?=statustaoweb($row['buoc']);?></td>
            <td>
                <!-- <a type="button" href="<?=BASE_URL('pages/admin/EditQuanlytaoweb.php?id='.$row['id'].'');?>" class="btn btn-primary"><i class="fa fa-edit"></i><span> QUẢN LÝ</span></a>
                <a type="button" href="<?=BASE_URL('pages/admin/Quanlytaoweb.php?xoa='.$row['id'].'');?>" class="btn btn-danger"><i class="fa fa-trash"></i><span> XÓA</span></a> -->
                <a href="<?=BASE_URL('pages/admin/EditQuanlytaoweb.php?id='.$row['id'].'');?>" class="btn btn-default btn-sm btn-icon icon-left">
					<i class="entypo-pencil"></i>
					Quản Lý
				</a>
				<a href="<?=BASE_URL('pages/admin/Quanlytaoweb.php?xoa='.$row['id'].'');?>" class="btn btn-danger btn-sm btn-icon icon-left">
					<i class="entypo-cancel"></i>
					Delete
				</a>

            </td>
        </tr>
    <?php } ?>
	</tbody>
	
</table>

<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
require_once("../../pages/admin/Footer.php");
?>