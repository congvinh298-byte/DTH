<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'DANH MỤC TẠO WEBSITE';
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
CheckAdmin();
?>
<?php
if(isset($_GET['xoa']) && $getUser['level'] == 'admin') {
    $user2 = $DMH->get_row(" SELECT * FROM `danhmuctaoweb` WHERE `id` = '".$_GET['xoa']."'");
    if(!$user2) {
        echo msg_admin("error","Danh mục này không tồn tại", BASE_URL('Admin/Danhmuctaoweb'), 2000); die;
    } else {
        $dele = $DMH->remove("danhmuctaoweb", " `id` = '".$_GET['xoa']."' ");
        if($dele) {
            echo msg_admin("success","Đã xóa danh mục thành công", BASE_URL('Admin/Danhmuctaoweb'), 2000);
        } else {
            echo msg_admin("error", "Xóa thất bại. Lỗi hệ thống", BASE_URL('Admin/Danhmuctaoweb'), 2000);
        }
    }
}
?>
<h2>Danh mục tạo web</h2>
<div class="row">
    <div class="col-md-12">

        <div class="panel panel-primary" data-collapsed="0">

            <div class="panel-heading">
                <div class="panel-title">
                    <b>Thêm danh mục tạo web</b>
                </div>

            </div>

            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Tên Danh Mục</label>
                        <div class="col-sm-5">
                            <input type="text" id="name" class="form-control daterange"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Mô Tả Danh Mục</label>
                        <div class="col-sm-5">
                            <input type="text" id="mota" class="form-control daterange"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Hình Ảnh (Thumbnail)</label>
                        <div class="col-sm-5">
                            <input type="url" id="img" class="form-control daterange" />
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
                url: "<?=BASE_URL('controller/admin/DanhMucWeb.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    type: 'AddMucWeb',
                    name: $("#name").val(),
                    mota: $("#mota").val(),
                    img: $("#img").val()
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

<h2>Các mẫu website</h2>
<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>STT</th>
            <th>Tên</th>
            <th>Mô Tả</th>
            <th>IMG</th>
            <th>Status</th>
            <th>Thao tác</th>
		</tr>
	</thead>
	<tbody>
        <?php $i = 1;  foreach($DMH->get_list(" SELECT * FROM `danhmuctaoweb` ORDER BY id DESC") as $row){ ?>
        <tr>
            <td><?=$i++;?></td>
            <td><b style="font-size: 15px"><?=$row['title'];?></b></td>
            <td><b style="font-size: 13px"><?=$row['mota'];?></b></td>
            <td> <img class="rounded" src="<?=$row['img'];?>" style="width: 300px; height: 100px"> </td>
            <td><?=danhmuc($row['status']);?></td>
            <td>
                <a href="<?=BASE_URL('pages/admin/EditDanhmuctaoweb.php?id='.$row['id'].'');?>" class="btn btn-default btn-sm btn-icon icon-left">
					<i class="entypo-pencil"></i>
					Edit
				</a>
				<a href="<?=BASE_URL('Admin/Danhmuctaoweb?xoa='.$row['id'].'');?>" class="btn btn-danger btn-sm btn-icon icon-left">
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

