<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'LỊCH SỬ MUA MIỀN';
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
CheckAdmin();
?>
<?php
if(isset($_GET['xoa']) && $getUser['level'] == 'admin')
{
    $user2 = $TUANORI->get_row(" SELECT * FROM `lichsumuamien` WHERE `id` = '".$_GET['xoa']."'");
    if(!$user2) {
        echo msg_admin("error","Lịch sử mua miền này không tồn tại", BASE_URL('Admin/Quanlymuamien'), 2000); die;
    }
    else
    {
        $dele = $TUANORI->remove("lichsumuamien", " `id` = '".$_GET['xoa']."' ");
        if($dele) {
            echo msg_admin("success", "Đã xóa lịch sử mua miền này thành công", BASE_URL('Admin/Quanlymuamien'), 2000);
        } else {
            echo msg_admin("error","Xóa thất bại. Lỗi hệ thống", BASE_URL('Admin/Quanlymuamien'), 2000);
        }
    }
}
?>
<h2>Lịch sử mua miền</h2>

<br />
<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>STT</th>
            <th width="7%">Username</th>
            <th width="10%">Tên Miền</th>
            <th width="5%">Tổng tiền</th>
            <th>Ngày Mua</th>
            <th>Ngày Hết</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
		</tr>
	</thead>
	<tbody>
    <?php $i = 1; foreach($TUANORI->get_list(" SELECT * FROM `lichsumuamien` ORDER BY id DESC LIMIT 50") as $row){ ?>
        <tr>
            <td><?=$i++;?></td>
            <td><a href="/pages/admin/EditQuanlythanhvien.php?id=<?=$TUANORI->getUser($row['username'])['id'];?>" target="_blank" style="color: #0099CC; font-weight: bold;"><?=$row['username'];?></a></td>
            <td style="font-weight: bold;"><a target="_blank" href="//<?=$row['domain'];?>"><?=$row['domain'];?></a></td>
            <td style="color: green; font-weight: bold;"><?=number_format($row['tongtien']);?>đ</td>
            <td  style="font-weight: bold;"><?=$row['timemua'];?></td>
            <td  style="font-weight: bold;">
                <?php if(strtotime($row['timedie']) < 0) {
                    echo '<b style="color: red">Chưa bắt đầu</b>';
                } else {
                    echo $row['timedie'];
                }
                ?>
            </td>
            <td><?=status($row['status']);?></td>
            <td>
                <a href="<?=BASE_URL('pages/admin/EditQuanlymuamien.php?id='.$row['id'].'');?>" class="btn btn-default btn-sm btn-icon icon-left">
					<i class="entypo-pencil"></i>
					Quản Lý
				</a>
                <a href="<?=BASE_URL('Admin/Quanlymuamien?xoa='.$row['id'].'');?>" class="btn btn-danger btn-sm btn-icon icon-left">
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