<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'LỊCH SỬ RÚT TIỀN';
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
CheckAdmin();
?>
<?php
if(isset($_GET['xoa']) && $getUser['level'] == 'admin')
{
    $user2 = $DMH->get_row(" SELECT * FROM `magiamgia` WHERE `id` = '".$_GET['xoa']."'");
    if(!$user2) {
        echo msg_admin("error", "Mã giảm giá này không tồn tại", BASE_URL('Admin/Magiamgia'), 1000);
    } else {
        $dele = $DMH->remove("magiamgia", " `id` = '".$_GET['xoa']."' ");
        if($dele) {
            echo msg_admin("success","Đã xóa mã giảm giá thành công", BASE_URL('Admin/Magiamgia'), 1000);
        } else {
            echo msg_admin("error", "Xóa thất bại. Lỗi hệ thống", BASE_URL('Admin/Magiamgia'), 1000);
        }
    }
}
?>

<h2>Lịch sử rút tiền</h2>
<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>STT</th>
            <th>Ngân Hàng</th>
            <th>Số tài khoản</th>
            <th>Tên chủ thẻ</th>
            <th>Số tiền rút</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>

		</tr>
	</thead>
	<?php $i = 1;  foreach($DMH->get_list(" SELECT * FROM `partner_ruttien` WHERE `username` = '".$getUser['username']."' ORDER BY id DESC") as $row){ ?>
        <tr>
            <td><?=$i++;?></td>
            <td><b style="color: green"><?=$row['atm'];?></b></td>
            <td><b><?=$row['stk'];?></b></td>
            <td><b><?=$row['name'];?></b></td>
            <td><b style="color: green"><?=format_cash($row['sotien']);?>đ</b></td>
            <td><b><?=status_partner($row['status']);?></b></td>
            <td>
                <a href="<?=BASE_URL('Admin/EditPar_ruttien?id='.$row['id']);?>" class="btn btn-default btn-sm btn-icon icon-left">
					<i class="entypo-pencil"></i>
					Edit
				</a>
            </td>
        </tr>
    <?php } ?>
	</tbody>
	
</table>

<?php

require_once("../../pages/admin/Footer.php");
?>

