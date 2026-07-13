<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'SETTING TÀI KHOẢN CLOUDFARE';
CheckAdmin();
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
?>
<?php
if($getUser['level'] == 'admin') {
    $row = $DMH->get_row(" SELECT * FROM `domainclf` WHERE `status` = 'ON' LIMIT 1");
}
?>
<h2>Setting Tài Khoản Cloudfare</h2>
<div class="row">
    <div class="col-md-12">

        <div class="panel panel-primary" data-collapsed="0">

            <div class="panel-heading">
                <div class="panel-title">
                    <b>Setting Tài Khoản Cloudfare</b>
                </div>

            </div>

            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Email Cloudfare</label>
                        <div class="col-sm-5">
                            <input type="text" id="email" class="form-control daterange" value="<?=$row['email'];?>"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Mã Khóa Tài Khoản Cloudfare</label>
                        <div class="col-sm-5">
                            <input type="text" id="auth" class="form-control daterange" value="<?=$row['auth'];?>"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Account ID</label>
                        <div class="col-sm-5">
                            <input type="text" id="accountid" class="form-control daterange" value="<?=$row['accountid'];?>"/>
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
                url: "<?=BASE_URL('controller/admin/SettingClf.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    email: $("#email").val(),
                    auth: $("#auth").val(),
                    accountid: $("#accountid").val()
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


<?php

require_once("../../pages/admin/Footer.php");
?>

