<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'EDIT TÊN MIỀN';
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
CheckAdmin();
?>
<?php
if(isset($_GET['id']) && $getUser['level'] == 'admin')
{
    $row = $TUANORI->get_row(" SELECT * FROM `lichsumuamien` WHERE `id` = '".$_GET['id']."'");
    if(!$row) {
        echo msg_admin("error", "Lịch sử mua miền không tồn tại",BASE_URL('Admin/Quanlymuamien'), 1000); die;
    }
} else {
    echo msg_admin("error", "Đường link không hợp lệ",BASE_URL('Admin/Quanlymuamien'), 1000); die;
}
?>
<h2>Edit tên miền <a href="/Admin/Quanlymuamien" class="btn btn-info"><i class="fa fa-backward"></i> Quay Lại</a></h2>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <b>Edit tên miền</b>
                </div>
            </div>
            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered">
                  

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Tên miền</label>
                        <div class="col-sm-5">
                            <input type="text" id="domain" value="<?=$row['domain'];?>" class="form-control daterange"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Nameserver</label>
                        <div class="col-sm-5">
                            <textarea type="text" id="ns" class="form-control daterange"  rows="5"><?=$row['ns'];?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="color: green; font-weight: bold;">Năm mua</label>
                        <div class="col-sm-5">
                            <input type="text" id="thoihan" value="<?=$row['thoihan'];?>" class="form-control daterange"/>
                        </div>
                    </div>
                 
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Trạng thái</label>
                        <div class="col-sm-5">
                            <select id="status" class="selectboxit">
                                <option value="thanhcong" <?=($row['status'] == 'thanhcong') ? 'selected': ''?>>Thành công</option>
                                <option value="thatbai" <?=($row['status'] == 'thatbai') ? 'selected': ''?>>Thất Bại</option>
                                <option value="xuly" <?=($row['status'] == 'xuly') ? 'selected': ''?>>Chờ xử lý</option>
                                <option value="tamkhoa" <?=($row['status'] == 'tamkhoa') ? 'selected': ''?>>Tạm khóa</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-5 control-label">
                            <button type="submit" id="btnAdd" class="btn btn-success">Lưu thông tin</button>
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
                url: "<?=BASE_URL('controller/admin/EditDomain.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    id: "<?=$row['id'];?>",
                    type: 'EditDomain',
                    domain: $("#domain").val(),
                    ns: $("#ns").val(),
                    thoihan: $("#thoihan").val(),
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
                    $('#btnAdd').html('Lưu thông tin').prop('disabled', false);
                },
                error: function() {
                    cuteToast({
                        type: "error",
                        message: 'Không thể xử lý',
                        timer: 5000
                    });
                    $('#btnAdd').html('Lưu thông tin').prop('disabled', false);
                }

            });
        });
    </script>

</div>

<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
require_once("../../pages/admin/Footer.php");
?>

