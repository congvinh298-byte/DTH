<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'HÓA ĐƠN TSR';
CheckAdmin();
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
?>
<?php
if(isset($_GET['xoa']) && $getUser['level'] == 'admin') {
    $user2 = $DMH->get_row(" SELECT * FROM `hoadon_vi` WHERE `id` = '".$_GET['xoa']."'");
    if(!$user2) {
        echo msg_admin("error","Hóa đơn không tồn tại trong hệ thống", BASE_URL('Admin/Hoadontsr'), 2000); die;
    } else {
        $dele = $DMH->remove("hoadon_vi", " `id` = '".$_GET['xoa']."' ");
        if($dele) {
            echo msg_admin("success","Đã xóa hóa đơn thành công", BASE_URL('Admin/Hoadontsr'), 2000);
        } else {
            echo msg_admin("error", "Xóa thất bại. Lỗi hệ thống", BASE_URL('Admin/Hoadontsr'), 2000);
        }
    }
}
if(isset($_GET['duyet']) && $getUser['level'] == 'admin') {
    $user2 = $DMH->get_row(" SELECT * FROM `hoadon_vi` WHERE `id` = '".$_GET['duyet']."'");
    if(!$user2) {
        echo msg_admin("error","Hóa đơn không tồn tại trong hệ thống", BASE_URL('Admin/Hoadontsr'), 2000); die;
    } else {
        $update = $DMH->update("hoadon_vi", array(
            'status'        => 'thanhcong'
        ), " `id` = '".$_GET['duyet']."' ");
        $add = $DMH->insert("biendongsodu", [
            'username'      => $user2['username'],
            'truoc'         => $DMH->getUser($user2['username'])['money'],
            'sau'           => $DMH->getUser($user2['username'])['money'] + $user2['sotien'],
            'note'          => 'Nạp thành công hóa đơn #'.$user2['magd'].' qua TSR',
            'tongtien'      => $user2['sotien'],
            'time'          => gettime()
        ]);
        $cong = $DMH->cong("users", "money", $user2['sotien'], " `username` = '".$user2['username']."' ");
        if($update && $add && $cong) {
            echo msg_admin("success","Đã duyệt hóa đơn thành công", BASE_URL('Admin/Hoadontsr'), 2000);
        } else {
            echo msg_admin("error", "Xóa thất bại. Lỗi hệ thống", BASE_URL('Admin/Hoadontsr'), 2000);
        }
    }
}
if(isset($_GET['huy']) && $getUser['level'] == 'admin') {
    $user2 = $DMH->get_row(" SELECT * FROM `hoadon_vi` WHERE `id` = '".$_GET['huy']."'");
    if(!$user2) {
        echo msg_admin("error","Hóa đơn không tồn tại trong hệ thống", BASE_URL('Admin/Hoadontsr'), 2000); die;
    } else {
        if($user2['status'] != 'xuly') {
            echo msg_admin("error","Hóa đơn này đã được xử lý!", BASE_URL('Admin/Hoadontsr'), 2000); die;
        }
        $update = $DMH->update("hoadon_vi", array(
            'status'        => 'huy'
        ), " `id` = '".$_GET['huy']."' ");
        if($update) {
            echo msg_admin("success","Đã hủy hóa đơn thành công", BASE_URL('Admin/Hoadontsr'), 2000);
        } else {
            echo msg_admin("error", "Xóa thất bại. Lỗi hệ thống", BASE_URL('Admin/Hoadontsr'), 2000);
        }
    }
}
?>
<h2>Hóa đơn TSR</h2>
<br />
<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>STT</th>
            <th>Username</th>
            <th>Mã GD</th>
            <th>Số Tiền</th>
            <th>Thời Gian</th>
            <th>Trạng Thái</th>
            <th>Thao tác</th>
		</tr>
	</thead>
	<tbody>
        <?php $i = 1;  foreach($DMH->get_list(" SELECT * FROM `hoadon_vi` ORDER BY id DESC LIMIT 100") as $row){ ?>
        <tr>
            <td><?=$i++;?></td>
            <td><a href="/pages/admin/EditQuanlythanhvien.php?id=<?=$DMH->getUser($row['username'])['id'];?>" target="_blank" style="color: #0099CC; font-weight: bold;"><?=$row['username'];?></a></td>
            <td><a style="color: green; font-weight: bold"><?=$row['magd'];?></td>
            <td><b style="color: green">+ <?=format_cash($row['sotien']);?>đ</b> </td>
            <td><b><?=$row['thoigian'];?></b></td>
            <td><?=stnapvi($row['status']);?></td>
            <td>
				<a href="<?=BASE_URL('Admin/Hoadontsr?xoa='.$row['id']);?>" class="btn btn-danger btn-sm btn-icon icon-left">
					<i class="entypo-cancel"></i>
					Delete
				</a>
                <a href="<?=BASE_URL('Admin/Hoadontsr?huy='.$row['id']);?>" class="btn btn-info btn-sm btn-icon icon-left">
					<i class="entypo-check"></i>
					Hủy
				</a>
                <a href="<?=BASE_URL('Admin/Hoadontsr?duyet='.$row['id']);?>" class="btn btn-success btn-sm btn-icon icon-left">
					<i class="entypo-check"></i>
					Duyệt
				</a>
            </td>
        </tr>
        <?php } ?>
	</tbody>
	
</table>

<?php

require_once("../../pages/admin/Footer.php");
?>