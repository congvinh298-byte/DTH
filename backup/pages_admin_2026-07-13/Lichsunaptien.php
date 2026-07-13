<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'LỊCH SỬ NẠP THẺ CÀO';
CheckAdmin();
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
?>
<?php
if(isset($_GET['status']) && isset($_GET['id']) && $getUser['level'] == 'admin') {
    $user2 = $DMH->get_row(" SELECT * FROM `napcard` WHERE `id` = '".$_GET['id']."'");
    if(!$user2) {
        echo msg_admin("error","Thẻ nạp không tồn tại trong hệ thống", BASE_URL('Admin/Lichsunaptien'), 2000); die;
    } else if($user2['status'] == 'thanhcong') {
        echo msg_admin("error","Thẻ này đã được duyệt từ trước.", BASE_URL('Admin/Lichsunaptien'), 2000);
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
        
        echo msg_admin("success","Đã cập nhật trạng thái thẻ thành công", BASE_URL('Admin/Lichsunaptien'), 2000);
        
    }
}
?>
<h2>Lịch sử nạp thẻ cào</h2>

<br />
<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>STT</th>
            <th width="7%">Username</th>
            <th width="10%">Loại thẻ</th>
            <th width="5%">Số tiền</th>
            <th>Thực nhận</th>
            <th>Seri</th>
            <th>Mã thẻ</th>
            <th>STATUS</th>
            <th>Thời gian</th>
            <th>Trạng thái</th>
		</tr>
	</thead>
	<tbody>
    <?php $i = 1; foreach($DMH->get_list(" SELECT * FROM `napcard` ORDER BY id DESC LIMIT 100") as $row){ ?>
        <tr>
            <td><?=$i++;?></td>
            <td><a href="/pages/admin/EditQuanlythanhvien.php?id=<?=$DMH->getUser($row['username'])['id'];?>" target="_blank" style="color: #0099CC; font-weight: bold;"><?=$row['username'];?></a></td>
            <td><b style="color: red"><?=$row['loaithe'];?></b></td>
            <td><b style="color: green"><?=format_cash($row['menhgia']);?>đ</b></td>
            <td><b style="color: green"><?=format_cash($row['thucnhan']);?>đ</b></td>

            <td><b><?=$row['seri'];?></b></td>
            <td><b><?=$row['pin'];?></b></td>
            <td><?=napthestt($row['status']);?></td>
            <td><b><?=$row['thoigian'];?></b></td>
            <td>
                <a href="<?=BASE_URL('Admin/Lichsunaptien?status=thanhcong&id='.$row['id']);?>" class="btn btn-success btn-sm btn-icon icon-left">
                    <i class="entypo-check"></i>
                    Duyệt
                </a>
                <a href="<?=BASE_URL('Admin/Lichsunaptien?status=thatbai&id='.$row['id']);?>" class="btn btn-danger btn-sm btn-icon icon-left">
                    <i class="entypo-block"></i>
                    Thẻ sai
                </a>
            </td>
        </tr>
    <?php } ?>
	</tbody>
	
</table>

<?php

require_once("../../pages/admin/Footer.php");
?>