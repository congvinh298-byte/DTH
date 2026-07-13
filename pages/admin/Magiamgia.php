<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'THÊM MÃ GIẢM GIÁ MỚI';
CheckAdmin();
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
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
<h2>Thêm mã giảm giá</h2>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <b>Thêm mã giảm giá</b>
                </div>
            </div>
            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Mã giảm giá</label>
                        <div class="col-sm-5">
                            <input type="text" id="magiamgia" class="form-control daterange"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Phần trăm giảm (%)</label>
                        <div class="col-sm-5">
                            <input type="number" id="giambaonhieu" class="form-control daterange"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Trạng thái</label>
                        <div class="col-sm-5">
                            <select id="theloai" class="selectboxit">
                                <option value="muacode">TẠO WEB</option>
                                <option value="taoweb">MUA CODE</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="color: green">Lượt dùng</label>
                        <div class="col-sm-5">
                            <input type="number" id="luotdung" class="form-control daterange" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Trạng thái</label>
                        <div class="col-sm-5">
                            <select id="hienthi" class="selectboxit">
                                <option value="SHOW">Hiển thị</option>
                                <option value="OFF">Ẩn đi</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-5 control-label">
                            <button type="submit" id="btnAdd" class="btn btn-success">Thêm thông tin</button>
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
                url: "<?=BASE_URL('controller/admin/Magiamgia.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    type: 'Themma',
                    type2: 'Themma',
                    magiamgia: $("#magiamgia").val(),
                    giambaonhieu: $("#giambaonhieu").val(),
                    theloai: $("#theloai").val(),
                    luotdung: $("#luotdung").val(),
                    hienthi: $("#hienthi").val()
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

<br />

<h2>Mã giảm giá</h2>
<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>STT</th>
            <th width="10%">Mã giảm giá</th>
            <th width="25%">Giảm</th>
            <th width="25%">Thể loại</th>
            <th>Còn lại</th>
            <th width="5%">Đã sử dụng</th>
            <th>STATUS</th>
            <th>Thao tác</th>
		</tr>
	</thead>
	<?php $i = 1;  foreach($DMH->get_list(" SELECT * FROM `magiamgia` ORDER BY id DESC") as $row){ ?>
        <tr>
            <td><?=$i++;?></td>
            <td><b style="color: green"><?=$row['magiamgia'];?></b></td>
            <td><b style="color: red"><?=$row['giambaonhieu'];?>%</b></td>
            <td><b><?=magiamgia($row['theloai']);?></b></td>
            <td><b style="color: green"><?=format_cash($row['conlai']);?> lần</b></td>
            <td><b style="color: red"><?=format_cash($row['dasudung']);?> lần</b></td>
            
            <td><?=danhmuc($row['hienthi']);?></td>
            <td>
                <a href="<?=BASE_URL('pages/admin/EditMagiamgia.php?id='.$row['id'].'');?>" class="btn btn-default btn-sm btn-icon icon-left">
					<i class="entypo-pencil"></i>
					Edit
				</a>
				<a href="<?=BASE_URL('Admin/Magiamgia?xoa='.$row['id'].'');?>" class="btn btn-danger btn-sm btn-icon icon-left">
					<i class="entypo-cancel"></i>
					Delete
				</a>
            </td>
        </tr>
    <?php } ?>
	</tbody>
	
</table>

<?php

require_once("../../pages/admin/Footer.php");
?>

