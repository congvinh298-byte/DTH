<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'EDIT DANH MỤC BÁN CODE';
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
CheckAdmin();
?>
<?php
if(isset($_GET['id']) && $getUser['level'] == 'admin') {
    $row = $TUANORI->get_row(" SELECT * FROM `danhmucmuacode` WHERE `id` = '".$_GET['id']."'");
    if(!$row) {
        echo msg_admin("error","Danh mục không tồn tại",BASE_URL('Admin/Danhmucbancode'), 1000); die;
    }
} else {
    echo msg_admin("error","Đường link không hợp lệ",BASE_URL('Admin/Danhmucbancode'), 1000); die;
}
?>
<h2>EDIT Danh mục bán code <a href="/Admin/Danhmucbancode" class="btn btn-info"><i class="fa fa-backward"></i> Quay Lại</a></h2>
<div class="row">
    
    <div class="col-md-12">

        <div class="panel panel-primary" data-collapsed="0">

            <div class="panel-heading">
                <div class="panel-title">
                    <b>Edit danh mục bán code</b>
                </div>

            </div>
            
            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Tên Danh Mục</label>
                        <div class="col-sm-5">
                            <input type="text" id="name" value="<?=$row['title'];?>" class="form-control daterange"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Mô Tả Danh Mục</label>
                        <div class="col-sm-5">
                            <input type="text" id="mota" value="<?=$row['mota'];?>" class="form-control daterange"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Hình Ảnh (Thumbnail)</label>
                        <div class="col-sm-5">
                            <input type="url" id="img" value="<?=$row['img'];?>" class="form-control daterange" /> <br/>
                            <img src="<?=$row['img'];?>" class="form-control daterange" style="width: 100%; height: 200px">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Trạng thái</label>
                        <div class="col-sm-5">
                            <select id="status" class="selectboxit">
                                <option value="SHOW" <?=($row['status'] == 'SHOW') ? 'selected': ''?>>Hiển thị</option>
                                <option value="OFF" <?=($row['status'] == 'OFF') ? 'selected': ''?>>Ẩn đi</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-5 control-label">
                            <button type="submit" id="btnUpdate" class="btn btn-success">Lưu thông tin</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $("#btnUpdate").on("click", function() {
            $('#btnUpdate').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled',
                true);
            $.ajax({
                url: "<?=BASE_URL('controller/admin/DanhMucCode.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    type: 'DanhMucCode',
                    type2: 'EditDanhmuccode',
                    id: <?=$row['id'];?>,
                    name: $("#name").val(),
                    mota: $("#mota").val(),
                    img: $("#img").val(),
                    status: $("#status").val()
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
                    $('#btnUpdate').html('Lưu thông tin').prop('disabled', false);
                },
                error: function() {
                    cuteToast({
                        type: "error",
                        message: 'Không thể xử lý',
                        timer: 5000
                    });
                    $('#btnUpdate').html('Lưu thông tin').prop('disabled', false);
                }

            });
        });
    </script>

</div>

<br />

<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
require_once("../../pages/admin/Footer.php");
?>

