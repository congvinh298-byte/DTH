<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'THÀNH VIÊN ĐANG BỊ KHÓA';
CheckAdmin();
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
?>
<?php
if(isset($_GET['mokhoa']) && $getUser['level'] == 'admin') {
    $user2 = $DMH->get_row(" SELECT * FROM `users` WHERE `id` = '".$_GET['mokhoa']."'");
    if(!$user2) {
        echo msg_admin("error", "Tài khoản này không tồn tại trong hệ thống", BASE_URL('Admin/QuanlythanhvienKhoa'), 2000); die;
    } else {
        $DMH->update("users", array(
            'banned'   => 'ON'
        ), " `id` = '".$_GET['mokhoa']."' ");
        echo msg_admin("success", "Đã mở khóa thành công", BASE_URL('Admin/QuanlythanhvienKhoa'), 2000);
    }
}
?>
<h2>Tổng thành viên bị Khóa</h2>

<br />
<table class="table table-bordered responsive">
	<thead>
		<tr>
			
			<th>STT</th>
			<th>Username</th>
			<th>Email</th>
			<th>Số tiền</th>
			<th>Tổng nạp</th>
			<th>Ngày Đăng Ký</th>
			<th>Trạng Thái</th>
			<th>Thao tác</th>
		</tr>
	</thead>
	<tbody>
		<?php $i = 0;  foreach($DMH->get_list(" SELECT * FROM `users` WHERE `banned` = 'OFF' ORDER BY id DESC LIMIT 50") as $row){ ?>
		<tr>
			<td><?=++$i;?></td>
			<td><b style="color: green"><?=$row['username'];?></b></td>
			<td><b style="color: green"><?=$row['email'];?></b></td>
			<td><b style="color: green"><?=format_cash($row['money']);?>đ</b></td>
			<td><b style="color: green"><?=format_cash($row['total_money']);?>đ</b></td>
			<td><?=$row['timereg'];?></td>
			<td><?=online($row['online']);?></td>
			<td>
				<a href="<?=BASE_URL('pages/admin/EditQuanlythanhvien.php?id='.$row['id'].'');?>" class="btn btn-default btn-sm btn-icon icon-left">
					<i class="entypo-pencil"></i>
					Edit
				</a>
				<a href="<?=BASE_URL('pages/admin/Quanlythanhvien.php?xoa='.$row['id'].'');?>" class="btn btn-danger btn-sm btn-icon icon-left">
					<i class="entypo-cancel"></i>
					Delete
				</a>
				<a href="<?=BASE_URL('pages/admin/QuanlythanhvienKhoa.php?mokhoa='.$row['id'].'');?>" class="btn btn-info btn-sm btn-icon icon-left">
					<i class="fa fa-unlock"></i>
					Mở IP
				</a>
			</td>
		</tr>
		<?php } ?>
	</tbody>
	
</table>

<?php

require_once("../../pages/admin/Footer.php");
?>