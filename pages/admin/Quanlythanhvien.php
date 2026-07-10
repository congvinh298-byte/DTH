<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'QUẢN LÝ THÀNH VIÊN';
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
CheckAdmin();
?>
<?php
if(isset($_GET['xoa']) && $getUser['level'] == 'admin')
{
    $user2 = $TUANORI->get_row(" SELECT * FROM `users` WHERE `id` = '".$_GET['xoa']."'");
    if(!$user2) {
        echo msg_admin("error","Tài khoản này không tồn tại trong hệ thống", BASE_URL('Admin/Quanlythanhvien'), 2000); die;
    }
    else
    {
        $dele = $TUANORI->remove("users", " `id` = '".$_GET['xoa']."' ");
		$dele = true;
        if($dele) {
			echo msg_admin("success","Đã xóa tài khoản thành công", BASE_URL('Admin/Quanlythanhvien'), 2000);
        }
        else {
			echo msg_admin("error","Xóa thất bại. Lỗi hệ thốn", BASE_URL('Admin/Quanlythanhvien'), 2000);

        }
    }
}
if(isset($_GET['khoa']) && $getUser['level'] == 'admin')
{
    $user2 = $TUANORI->get_row(" SELECT * FROM `users` WHERE `id` = '".$_GET['khoa']."'");
    if(!$user2) {
        echo msg_admin("error", "Tài khoản này không tồn tại trong hệ thống", BASE_URL('Admin/Quanlythanhvien'), 2000);
    } else {
        $is = $TUANORI->num_rows("SELECT * FROM `users` WHERE `ip` = '".$user2['ip']."' ");
        $update =  $TUANORI->update("users", array(
                'banned'        => 'OFF',
                'online'        => 'OFFLINE'
            ), " `ip` = '".$user2['ip']."' ");
     
        $create = $TUANORI->insert("blockip", [
            'ip'            => $user2['ip'],
            'time'          => gettime()
        ]);
        if(true) {
            echo msg_admin("success", "Đã khóa $is tài khoản của thành viên này", BASE_URL('Admin/Quanlythanhvien'), 2000);
        } else {
            echo msg_admin("Khóa thất bại", BASE_URL('Admin/Quanlythanhvien'), 2000);
        }
    }
}
$tim = '';
if(isset($_POST['Search']) && $getUser['level'] == 'admin') {
	$user = check_string($_POST['user']);
	$tim = "AND `username` LIKE '%$user%' ";
}
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <b>Tìm kiếm thành viên</b>
                </div>
            </div>
            <div class="panel-body">
                <form role="form" method="POST" class="form-horizontal form-groups-bordered">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Tên username</label>
                        <div class="col-sm-5">
                            <input type="text" name="user" value="<?=($user) ?? '';?>" class="form-control daterange"/>
                        </div>
                    </div>
					<div class="form-group">
                        <div class="col-sm-5 control-label">
                            <button type="submit" name="Search" class="btn btn-success">Tìm kiếm</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $("#btnAdd").on("click", function() {
            $('#btnAdd').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled',
                true);
            $.ajax({
                url: "<?=BASE_URL('controller/admin/DanhMucCode.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    type: 'ThemCode',
                    type2: 'AddThemCode',
                    danhmuc: $("#danhmuc").val(),
                    name: $("#name").val(),
                    mota: $("#mota").val(),
                    download: $("#download").val(),
                    money: $("#money").val(),
                    img: $("#img").val(),
                    listimg: $("#listimg").val(),
                    demo: $("#demo").val()
                },
                success: function(respone) {
                    cuteToast({
                        type: respone.status,
                        message: respone.msg,
                        timer: 5000
                    });
                    if(respone.url != '-1') {
                        setTimeout("location.href = '" + respone.url + "';", respone.time);
                    }
                    $('#btnAdd').html('Thêm thông tin').prop('disabled', false);
                },
                error: function() {
                    cuteToast({
                        type: "error",
                        message: 'Không thể xử lý',
                        timer: 5000
                    });
                    $('#btnAdd').html('Thêm thông tin').prop('disabled', false);
                }

            });
        });
    </script>

</div>

<h2>Tổng thành viên</h2>

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
		<?php $i = 0;  foreach($TUANORI->get_list(" SELECT * FROM `users` WHERE `banned` = 'ON' $tim ORDER BY id DESC LIMIT 50") as $row){ ?>
		<tr>
			<td><?=$i++;?></td>
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
				<a href="<?=BASE_URL('Admin/Quanlythanhvien?xoa='.$row['id'].'');?>" class="btn btn-danger btn-sm btn-icon icon-left">
					<i class="entypo-cancel"></i>
					Delete
				</a>
				<a href="<?=BASE_URL('Admin/Quanlythanhvien?khoa='.$row['id'].'');?>" class="btn btn-info btn-sm btn-icon icon-left">
					<i class="fa fa-lock"></i>
					Khóa IP
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