<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'EDIT THÔNG TIN HỒ SƠ';
CheckAdmin();
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
?>
<?php
if(isset($_GET['id']) && $getUser['level'] == 'admin')
{
    $id = check_string($_GET['id']);
    $row = $DMH->get_row(" SELECT * FROM `upload_hoso` WHERE `id` = '".$_GET['id']."'");
    if(!$row) {
        echo msg_admin("error","Mã nguồn không tồn tại",BASE_URL('Admin/HosoXacMinh'), 1000); die;
    }
} else {
    echo msg_admin("error","Đường link không hợp lệ",BASE_URL('Admin/HosoXacMinh'), 1000); die;
}
?>
<h2>Edit hồ sơ xác minh <a href="/Admin/HosoXacMinh" class="btn btn-info"><i class="fa fa-backward"></i> Quay Lại</a></h2>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <b>Edit hồ sơ xác minh</b>
                </div>
            </div>
            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered">

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Username</label>
                        <div class="col-sm-5">
                            <input type="text" id="name" value="<?=$row['username'];?>" class="form-control daterange" disabled/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Mặt trước CCCD</label>
                        <div class="col-sm-5">
                            <input type="text" value="<?=$row['mattruoc'];?>" class="form-control daterange" disabled />
                            <img src="<?=$row['mattruoc'];?>" class="form-control daterange" style="width: 100%; height: 200px">

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Mặt sau CCCD</label>
                        <div class="col-sm-5">
                            <input type="text" value="<?=$row['matsau'];?>" class="form-control daterange" disabled />
                            <img src="<?=$row['matsau'];?>" class="form-control daterange" style="width: 100%; height: 200px">

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Ảnh chân dung kèm thông tin</label>
                        <div class="col-sm-5">
                            <input type="text" value="<?=$row['chandung'];?>" class="form-control daterange" disabled />
                            <img src="<?=$row['chandung'];?>" class="form-control daterange" style="width: 100%; height: 200px">

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Ghi chú</label>
                        <div class="col-sm-5">
                            <textarea type="text" class="form-control daterange" rows="4" placeholder="Link ảnh mô tả" id="note"><?=$row['note'];?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Trạng thái</label>
                        <div class="col-sm-5">
                            <select id="status" class="selectboxit">
                                <option value="xuly" <?=($row['status'] == 'xuly') ? 'selected': ''?>>Chờ xử lý</option>
                                <option value="thatbai" <?=($row['status'] == 'thatbai') ? 'selected': ''?>>Từ chối</option>
                                <option value="thanhcong" <?=($row['status'] == 'thanhcong') ? 'selected': ''?>>Chấp thuận</option>

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
                url: "<?=BASE_URL('controller/admin/EditHoSo.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    id: "<?=$_GET['id'];?>",
                    note: $("#note").val(),
                    status: $("#status").val(),
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

<?php

require_once("../../pages/admin/Footer.php");
?>

