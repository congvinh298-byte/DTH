<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'EDIT TẠO MẪU WEBSITE';
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
CheckAdmin();
?>
<?php
if(isset($_GET['id']) && $getUser['level'] == 'admin') {
    $row = $TUANORI->get_row(" SELECT * FROM `lichsutaoweb` WHERE `id` = '".$_GET['id']."'");
    $tuanitv2 = $TUANORI->get_row(" SELECT * FROM `danhsachtaoweb` WHERE `id` = '".$row['id_code']."'");
    $drivev2 = $TUANORI->get_row(" SELECT * FROM `danhsachmuacode` WHERE `img` = '".$tuanitv2['img']."' ");
    if(!$row)
    {
        echo msg_admin("error","Đơn tạo web này không tồn tại",BASE_URL('Admin/Quanlytaoweb'), 1000); die;
    }
} else {
    echo msg_admin("error","Đường link không hợp lệ",BASE_URL('Admin/Quanlytaoweb'), 1000); die;
}
?>
<h2>Chỉnh sửa lịch sử tạo web <a href="/Admin/Quanlytaoweb" class="btn btn-info"><i class="fa fa-backward"></i> Quay Lại</a></h2>
<div class="row">
    <div class="col-md-12">

        <div class="panel panel-primary" data-collapsed="0">

            <div class="panel-heading">
                <div class="panel-title">
                    <b>Chỉnh sửa lịch sử tạo web </b>
                </div>

            </div>

            <div class="panel-body">
            <form role="form" class="form-horizontal form-groups-bordered">
                <div class="form-group">
                    <label class="col-sm-3 control-label" style="color: green">Username</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control daterange" id="username" value="<?=$row['username'];?>"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Email người dùng</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control daterange" value="<?=$TUANORI->getUser($row['username'])['email'];?>" disabled/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" style="color: green">Tên Miền</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control daterange" id="tenmien" value="<?=$row['tenmien'];?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" style="color: green">Mẫu web</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control daterange" id="id_code" value="<?=$row['id_code'];?>"/>
                        <img src="<?=$tuanitv2['img'];?>" class="form-control daterange" style="width: 100%; height: 20%">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" style="color: red">Tài Khoản admin</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control daterange" id="tkadmin" value="<?=$row['taikhoan'];?>"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" style="color: red">Mật Khẩu admin</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control daterange" id="mkadmin" value="<?=$row['matkhau'];?>"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" style="color: green">Số tháng mua</label>
                    <div class="col-sm-5">
                        <?php $st = ''; if($row['thangmua'] != 0) {
                            $st = 'disabled';
                        } ?>
                        <input type="text" class="form-control daterange" id="thangmua" value="<?=$row['thangmua'];?>" <?=$st;?>/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Thời Gian Tạo</label>
                    <div class="col-sm-5">
                        <b class="form-control" disabled><?=gettime2($row['ngaytao']);?></b>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Thời Gian Duyệt</label>
                    <div class="col-sm-5">
                        <b class="form-control" disabled><?=timeran($row['ngayduyet']);?></b>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Thời Gian Hết Hạn</label>
                    <div class="col-sm-5">
                        <b class="form-control" disabled><?=timeran($row['ngayhethan']);?></b>

                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label" style="color: green">Tiền Gia Hạn Mỗi 3 Tháng</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control daterange fnum" id="giahanweb" value="<?=format_cash($row['moneygiahan']);?>"/>
                    </div>
                </div>
                <hr/>
                <div class="form-group">
                    <label class="col-sm-3 control-label" style="color: green">Link login hosting</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control daterange" id="linklogin" value="<?=$row['linklogin'];?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" style="color: green">Mật khẩu hosting</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control daterange" id="tkhs" value="<?=$row['tkhs'];?>"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label" style="color: green">Mật khẩu hosting</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control daterange" id="mkhs" value="<?=$row['mkhs'];?>"/>
                    </div>
                </div>
                <hr/>

                <div class="form-group">
                    <label class="col-sm-3 control-label" style="color: green">Tổng tiền tạo website</label>
                    <div class="col-sm-5">
                        <input type="text" class="form-control daterange fnum" id="tongtien" value="<?=format_cash($row['tongtien']);?>"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label">Trạng thái</label>
                    <div class="col-sm-5">
                        <select id="status" class="selectboxit">
                            <option value="1" <?=($row['buoc'] == 1) ? 'selected': ''?>>B1: Chọn thời gian hoạt động</option>
                            <option value="2" <?=($row['buoc'] == 2) ? 'selected': ''?>>B2: Trỏ tên miền về hệ thống</option>
                            <option value="3" <?=($row['buoc'] == 3) ? 'selected': ''?>>B3: Chờ ADMIN xử lý đơn hàng</option>
                            <option value="4" <?=($row['buoc'] == 4) ? 'selected': ''?>>B4: Wesbite đang hoạt động</option>
                            <option value="5" <?=($row['buoc'] == 5) ? 'selected': ''?>>B5: Wesbite sắp hoặc đang hết hạn</option>
                            <option value="6" <?=($row['buoc'] == 6) ? 'selected': ''?>>B6: Wesbite chấm dứt hoạt động</option>
                            <option value="7" <?=($row['buoc'] == 7) ? 'selected': ''?>>B7: Wesbite đã bị hủy bởi ADMIN</option>
                            <option value="8" <?=($row['buoc'] == 8) ? 'selected': ''?>>B8: Liên hệ ADMIN</option>

                        </select> <br/>
                        <?=statustaoweb($row['buoc']);?>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-3 control-label"><b style="background: -webkit-linear-gradient(19deg, #FF0000 0%, #7F12E2 100%);-webkit-background-clip: text;-webkit-text-fill-color: transparent;">Có sử dụng mã giảm giá không?</b></label>
                    <div class="col-sm-5">
                        <b class="form-control"><?=magiamgiav2($row['magiamgia']);?></b>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><b style="color: red">Link download</b></label>
                    <div class="col-sm-5">
                        <b class="form-control"><?=$drivev2['download'];?></b>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label"><b style="color: red">Ghi chú từ ADMIN</b></label>
                    <div class="col-sm-5">
                        <textarea type="text" class="form-control daterange" id="note" rows="5"><?=$row['note'];?></textarea>

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
                url: "<?=BASE_URL('controller/admin/UpdateTaoWeb.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    type: 'UpdateAll',
                    id_web: '<?=$row['id'];?>',
                    username: $("#username").val(),
                    id_code: $("#id_code").val(),
                    tenmien: $("#tenmien").val(),
                    tkadmin: $("#tkadmin").val(),
                    mkadmin: $("#mkadmin").val(),
                    status: $("#status").val(),
                    giahanweb: $("#giahanweb").val(),
                    thangmua: $("#thangmua").val(),
                    tongtien: $("#tongtien").val(),
                    note: $("#note").val(),
                    linklogin: $("#linklogin").val(),
                    tkhs: $("#tkhs").val(),
                    mkhs: $("#mkhs").val()
                    
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
    <div class="col-md-6">

        <div class="panel panel-primary" data-collapsed="0">

            <div class="panel-heading">
                <div class="panel-title">
                    <b>Cộng thêm tháng</b>
                </div>
            </div>

            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered">

                    <div class="form-group">
                        <label class="col-sm-3 control-label"  style="color: green">Tháng</label>
                        <div class="col-sm-5">
                            <input type="number" class="form-control daterange" id="thangcong" value="0"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Trừ tiền thành viên</label>
                        <div class="col-sm-5">
                            <select id="trutien" class="selectboxit">
                                <option value="YES" >YES</option>
                                <option value="NO" >NO</option>
                            </select>
                            <i style="font-weight: bold">Trừ tiền thành viên để gia hạn</i>
                        </div>
                    </div>
                
                    <div class="form-group">
                        <div class="col-sm-5 control-label">
                            <button type="submit" id="btnCong" class="btn btn-success">Cộng</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $("#btnCong").on("click", function() {
            $('#btnCong').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled',
                true);
            $.ajax({
                url: "<?=BASE_URL('controller/admin/UpdateTaoWeb.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    type: 'Cong',
                    id_web: <?=$row['id'];?>,
                    thangcong: $("#thangcong").val(),
                    trutien: $("#trutien").val()
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
                    $('#btnCong').html('Cộng').prop('disabled', false);
                },
                error: function() {
                    cuteToast({
                        type: "error",
                        message: 'Không thể xử lý',
                        timer: 5000
                    });
                    $('#btnCong').html('Cộng').prop('disabled', false);
                }

            });
        });
    </script>


    <div class="col-md-6">

        <div class="panel panel-primary" data-collapsed="0">

            <div class="panel-heading">
                <div class="panel-title">
                    <b>Trừ bớt thời gian</b>
                </div>
            </div>

            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered">

                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="color: red">Tháng</label>
                        <div class="col-sm-5">
                            <input type="number" class="form-control daterange" id="thangtru" value="0"/>
                        </div>
                    </div>

                
                    <div class="form-group">
                        <div class="col-sm-5 control-label">
                            <button type="submit" id="btnTru" class="btn btn-danger">Trừ</button>
                        </div>
                    </div>
                </form>

            </div>

        </div>

    </div>
    <script type="text/javascript">
        $("#btnTru").on("click", function() {
            $('#btnTru').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled',
                true);
            $.ajax({
                url: "<?=BASE_URL('controller/admin/UpdateTaoWeb.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    type: 'Tru',
                    id_web: <?=$row['id'];?>,
                    thangtru: $("#thangtru").val()
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
                    $('#btnTru').html('Trừ').prop('disabled', false);
                },
                error: function() {
                    cuteToast({
                        type: "error",
                        message: 'Không thể xử lý',
                        timer: 5000
                    });
                    $('#btnTru').html('Trừ').prop('disabled', false);
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
