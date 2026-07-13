<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'ĐƠN GIA HẠN WEBSITE';
CheckAdmin();
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
?>
<?php
if(isset($_GET['status']) && isset($_GET['id']) && $getUser['level'] == 'admin') {
    $status = check_string($_GET['status']);
    $user2 = $DMH->get_row(" SELECT * FROM `lichsugiahan` WHERE `id` = '".$_GET['id']."'");
    if(!$user2) {
        echo msg_admin("error","Lịch sử gia hạn này không tồn tại", BASE_URL('Admin/Lichsugiahan'), 1000); die;
    } else {
        if($user2['status'] == 'thatbai') {
            echo msg_admin("error","Đã cập nhật trạng thái từ trước", BASE_URL('Admin/Lichsugiahan'), 1000); die;
        }
        $check_user = $DMH->getUser($user2['username']);
        if($status == 'thatbai') {
            $add = $DMH->insert("biendongsodu", [
                'username'      => $user2['username'],
                'truoc'         => $check_user['money'],
                'sau'           => $check_user['money'] + $user2['tongtien'],
                'note'          => "Hoàn tiền gia hạn website ".$user2['tenmien'],
                'tongtien'      => $user2['tongtien'],
                'time'          => gettime()
            ]);
            $cong = $DMH->cong("users", "money", $user2['tongtien'], " `username` = '".$user2['username']."' ");
        }
        $update = $DMH->update("lichsugiahan", array(
            'status'        => $status
        ), " `id` = '".$_GET['id']."' ");
        $DMH->cong("lichsutaoweb", "ngayhethan", $user2['thoigian']*$onethang, " `id` = '".$user2['id_web']."'");
        $update = $DMH->update("lichsutaoweb", array(
            'buoc'        => 4
        ), " `id` = '".$user2['id_web']."' AND `username` = '".$user2['username']."' ");
        if($update) {
            echo msg_admin("success","Đã cập nhật trạng thái thành công", BASE_URL('Admin/Lichsugiahan'), 1000);
        } else {
            echo msg_admin("error","Cập nhật thất bại. Lỗi hệ thống", BASE_URL('Admin/Lichsugiahan'), 1000);
        }
    }
}
?>
<h2>Lịch sử gia hạn website</h2>

<br />
<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>STT</th>
            <th width="7%">Username</th>
            <th>ID Website</th>
            <th width="10%">Tên Miền</th>
            <th width="5%">Tổng tiền</th>
            <th>Thời gian thao tác</th>
            <th>Thời gian gia hạn</th>
            <th>STATUS</th>
            <th>Thao tác</th>
		</tr>
	</thead>
	<tbody>
    <?php $i = 1;  foreach($DMH->get_list(" SELECT * FROM `lichsugiahan` ORDER BY id DESC LIMIT 20") as $row){
        $id_web = $DMH->get_row(" SELECT * FROM `lichsutaoweb` WHERE `id` = '".$row['id_web']."'")['id_code'];
        ?>
        <tr>
            <td><?=$i++;?></td>
            <td><a href="/pages/admin/EditQuanlythanhvien.php?id=<?=$DMH->getUser($row['username'])['id'];?>" target="_blank" style="color: #0099CC; font-weight: bold;"><?=$row['username'];?></a></td>
            <td><a target="_blank" href="/tao-web/<?=$id_web;?>"><span class="btn btn-info" style="padding: 4px 8px;"><?=$id_web;?></span></a></td>
            <td><a style="color: red; font-weight: bold;" target="_blank" href="//<?=$row['tenmien'];?>"><?=$row['tenmien'];?></td>
            <td style="color: green; font-weight: bold;"><?=format_cash($row['tongtien']);?>đ</td>
            <td style="font-weight: bold;"><?=$row['time'];?></td>
            <td><b style="color: green">+ <?=$row['thoigian'];?> Tháng</b></td>
            <?php $check = $DMH->get_row(" SELECT * FROM `lichsutaoweb` WHERE `id` = '".$row['id_web']."'"); ?>
            <td><?=sttgiahan($row['status']);?></td>
            <td>
                <?php if($row['status'] == 'xuly') { ?>
                <a href="<?=BASE_URL('Admin/Lichsugiahan?status=thanhcong&id='.$row['id'].'');?>" class="btn btn-success btn-sm btn-icon icon-left">
					<i class="entypo-check"></i>
					Duyệt
				</a>
                <a href="<?=BASE_URL('Admin/Lichsugiahan?status=thatbai&id='.$row['id'].'');?>" class="btn btn-danger btn-sm btn-icon icon-left">
					<i class="entypo-cancel"></i>
					Hủy
				</a>
                <a href="<?=BASE_URL('Admin/Lichsugiahan?status=xuly&id='.$row['id'].'');?>" class="btn btn-warning btn-sm btn-icon icon-left">
                    <i class="entypo-block"></i>
                    Chờ
                </a>
                <?php } ?>
                <a href="<?=BASE_URL('pages/admin/EditQuanlytaoweb.php?id='.$row['id_web']);?>" class="btn btn-default btn-sm btn-icon icon-left">
					<i class="entypo-pencil"></i>
					Quản lý
				</a>
            </td>
        </tr>
    <?php } ?>
	</tbody>
	
</table>

<?php

require_once("../../pages/admin/Footer.php");
?>