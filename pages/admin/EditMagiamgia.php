<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'EDIT MÃ GIẢM GIÁ';
CheckAdmin();
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
?>
<?php
if(isset($_GET['id']) && $getUser['level'] == 'admin') {
    $magg = check_string($_GET['id']);
    $row = $DMH->get_row(" SELECT * FROM `magiamgia` WHERE `id` = '$magg'");
    if(!$row) {
        echo msg_admin("error","Đã giảm giá không tồn tại.",BASE_URL('Admin/Magiamgia'), 1000); die;
    }
} else {
    echo msg_admin("error","Đường link không hợp lệ",BASE_URL('Admin/Magiamgia'), 1000); die;
}
?>
<h2>EDIT Mã giảm giá <a href="/Admin/Magiamgia" class="btn btn-info"><i class="fa fa-backward"></i> Quay Lại</a></h2>
<div class="row">
    
    <div class="col-md-12">

        <div class="panel panel-primary" data-collapsed="0">

            <div class="panel-heading">
                <div class="panel-title">
                    <b>Edit Mã giảm giá</b>
                </div>

            </div>
            
            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Mã giảm giá</label>
                        <div class="col-sm-5">
                            <input type="text" id="magiamgia" value="<?=$row['magiamgia'];?>" class="form-control daterange"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Phần trăm giảm (%)</label>
                        <div class="col-sm-5">
                            <input type="number" id="giambaonhieu" value="<?=$row['giambaonhieu'];?>" class="form-control daterange"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Trạng thái</label>
                        <div class="col-sm-5">
                            <select id="theloai" class="selectboxit">
                                <option value="taoweb" <?=($row['theloai'] == 'taoweb') ? 'selected' : '';?>>TẠO WEB</option>
                                <option value="muacode" <?=($row['theloai'] == 'muacode') ? 'selected' : '';?>>MUA CODE</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="color: green">Lượt dùng</label>
                        <div class="col-sm-5">
                            <input type="number" id="luotdung" value="<?=$row['conlai'];?>" class="form-control daterange" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Trạng thái</label>
                        <div class="col-sm-5">
                            <select id="hienthi" class="selectboxit">
                                <option value="SHOW" <?=($row['hienthi'] == 'SHOW') ? 'selected' : '';?>>Hiển thị</option>
                                <option value="OFF" <?=($row['hienthi'] == 'OFF') ? 'selected' : '';?>>Ẩn đi</option>
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
                url: "<?=BASE_URL('controller/admin/Magiamgia.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    id: <?=$row['id'];?>,
                    type: 'Themma',
                    type2: 'EditThemma',
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

require_once("../../pages/admin/Footer.php");
?>

