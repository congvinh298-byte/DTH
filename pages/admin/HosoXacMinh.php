<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'HỒ SƠ XÁC MINH';
CheckAdmin();
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
?>
<?php
if(isset($_GET['status']) && isset($_GET['id']) && $getUser['level'] == 'admin') {
    $user2 = $DMH->get_row(" SELECT * FROM `napcard` WHERE `id` = '".$_GET['id']."'");
    if(!$user2) {
        echo msg_admin("error","Thẻ nạp không tồn tại trong hệ thống", BASE_URL('Admin/HosoXacMinh'), 2000); die;
    } else if($user2['status'] == 'thanhcong') {
        echo msg_admin("error","Thẻ này đã được duyệt từ trước.", BASE_URL('Admin/HosoXacMinh'), 2000);
    } else {
        $status = check_string($_GET['status']);
        $check_user = $DMH->getUser($user2['username']);
        $update = $DMH->update("napcard", array(
            'status'        => $status,
            'uptime'        => gettime()
        ), " `id` = '".$_GET['id']."' ");
        if($status == 'thanhcong') {
            $add = $DMH->insert("biendongsodu", [
                'username'      => $user2['username'],
                'truoc'         => $check_user['money'],
                'sau'           => $check_user['money'] + $user2['thucnhan'],
                'note'          => "Nạp thẻ cào mệnh giá ".format_cash($user2['menhgia'])." thành công. Nhận ".format_cash($user2['thucnhan'])." đ. Có số mã thẻ là ".$user2['pin'],
                'tongtien'      => $user2['thucnhan'],
                'time'          => gettime(),
            ]);
            $cong = $DMH->cong("users", "money", $user2['thucnhan'], " `username` = '".$user2['username']."' ");
            $cong = $DMH->cong("users", "total_money", $user2['thucnhan'], " `username` = '".$user2['username']."' ");
        }
        
        echo msg_admin("success","Đã cập nhật trạng thái thẻ thành công", BASE_URL('Admin/HosoXacMinh'), 2000);
        
    }
}
?>
<h2>Tất cả hồ sơ</h2>

<br />
<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>STT</th>
            <th width="7%">Username</th>
            <th>Mặt trước</th>
            <th>Mặt sau</th>
            <th>Chân dung</th>
            <th>Thời gian</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
		</tr>
	</thead>
	<tbody>
    <?php $i = 1; foreach($DMH->get_list(" SELECT * FROM `upload_hoso` ORDER BY id DESC LIMIT 100") as $row){ ?>
        <tr>
            <td><?=$i++;?></td>
            <td><a href="/pages/admin/EditQuanlythanhvien.php?id=<?=$DMH->getUser($row['username'])['id'];?>" target="_blank" style="color: #0099CC; font-weight: bold;"><?=$row['username'];?></a></td>
            <td> <a target="_blank" href="<?=$row['mattruoc'];?>"><img class="rounded" src="<?=$row['mattruoc'];?>" style="width: 300px; height: 100px"></a> </td>
            <td> <a target="_blank" href="<?=$row['matsau'];?>"><img class="rounded" src="<?=$row['matsau'];?>" style="width: 300px; height: 100px"></a> </td>
            <td> <a target="_blank" href="<?=$row['chandung'];?>"><img class="rounded" src="<?=$row['chandung'];?>" style="width: 300px; height: 100px"></a> </td>
            <td><?=hoso($row['status']);?></td>
            <td><b><?=$row['thoigian'];?></b></td>
            <td>
                <a href="<?=BASE_URL('pages/admin/EditHosoXacMinh.php?id='.$row['id']);?>" class="btn btn-default btn-sm btn-icon icon-left">
                    <i class="entypo-pencil"></i>
                    Chỉnh sửa
                </a>
            </td>
        </tr>
    <?php } ?>
	</tbody>
	
</table>

<?php

require_once("../../pages/admin/Footer.php");
?>