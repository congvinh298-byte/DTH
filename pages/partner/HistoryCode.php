<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'LỊCH SỬ ĐĂNG BÁN CODE';
require_once("../../pages/partner/Head.php");
require_once("../../pages/partner/Header.php");
CheckVeri();
if(isset($_GET['xoa']) && $getUser['verify'] == 1) {
    $row = $TUANORI->get_row(" SELECT * FROM `partner_code` WHERE `id` = '".$_GET['xoa']."' AND `username` = '".$getUser['username']."' ");
    if(!$row) {
        die(msg_admin("error", "Đơn bán code này không tồn tại",BASE_URL('Partner/HistoryCode'), 500));
    } else {
        $TUANORI->remove("partner_code", " `id` = '".$_GET['xoa']."' ");
        die(msg_admin("success", "Đã xóa thành công",BASE_URL('Partner/HistoryCode'), 500));
    }
}
?>

<h2>Lịch sử đăng bán code</h2>

<br />
<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>#</th>
            <th>ID danh mục</th>
            <th>Tên</th>
            <th>Mô tả</th>
            <th>Số tiền</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
		</tr>
	</thead>
	<tbody>
        <?php $i = 0; foreach($TUANORI->get_list(" SELECT * FROM `partner_code` WHERE `username` = '".$getUser['username']."' ORDER BY id DESC LIMIT 100") as $row){ ?>
        <tr>
            <td><?=++$i;?></td>
            <td><a target="_blank" href="/danh-muc-code/<?=$row['id_danhmuc'];?>"><span class="btn btn-info" style="padding: 4px 8px;"><?=$row['id_danhmuc'];?></span></a></td>

            <td><b style="color: black"><?=$row['name'];?></b></td>
            <td><b style="color: black"><?=$row['mota'];?></b></td>
            <td><b style="color: green"><?=number_format($row['sotien']);?>đ</b></td>
            <td><?=status_partner($row['status']);?></td>
            <td>
                <a href="<?=BASE_URL('Partner/EditCode?id='.$row['id']);?>" class="btn btn-default btn-sm btn-icon icon-left">
                    <i class="entypo-pencil"></i>
                    Chỉnh sửa
                </a>
                <a href="<?=BASE_URL('Partner/HistoryCode?xoa='.$row['id']);?>" class="btn btn-danger btn-sm btn-icon icon-left">
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
require_once("../../pages/partner/Footer.php");
?>