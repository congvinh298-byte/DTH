<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'EDIT THÀNH VIÊN';
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
CheckAdmin();
if(isset($_GET['id']) && $getUser['level'] == 'admin')
{
    $row = $TUANORI->get_row(" SELECT * FROM `users` WHERE `id` = '".$_GET['id']."'");
    if(!$row)
    {
        die(msg_admin("error", "Thành viên không tồn tại",BASE_URL('Admin/Quanlythanhvien'), 1000));
    }
}
else
{
    die(msg_admin("error", "Đường link không hợp lệ",BASE_URL('Admin/Quanlythanhvien'), 1000));
}
?>
<h2>Edit thành viên <b style="color: green"><?=$row['username'];?></b></h2>
<br />



<div class="row">
    <div class="col-md-12">

        <div class="panel panel-primary" data-collapsed="0">

            <div class="panel-heading">
                <div class="panel-title">
                    Edit thành viên <b style="color: green"><?=$row['username'];?></b>
                </div>

            </div>

            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered">

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Username</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control daterange" value="<?=$row['username'];?>" disabled/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Email</label>
                        <div class="col-sm-5">
                            <input type="text" id="email" class="form-control daterange" value="<?=$row['email'];?>"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="color: green">Số tiền hiện có</label>
                        <div class="col-sm-5">
                            <input type="text" id="sotien" class="form-control daterange fnum" value="<?=format_cash($row['money']);?>"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="color: green">Số tiền đã nạp</label>
                        <div class="col-sm-5">
                            <input type="text" id="tongtien" class="form-control daterange" value="<?=format_cash($row['total_money']);?>" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Thời gian đăng ký</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control daterange" value="<?=$row['timereg'];?>" disabled />
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Thời gian on gần đây</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control daterange" value="<?=$row['timeon'];?>" disabled/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">IP</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control daterange" value="<?=$row['ip'];?>" disabled>
                        </div>
                    </div>
                    
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Tình Trạng</label>
                        <div class="col-sm-5">
                            <select id="status" class="selectboxit">
                                <option value="ON" <?=($row['banned'] == 'ON') ? 'selected' : '';?>>Hoạt động</option>
                                <option value="OFF" <?=($row['banned'] == 'OFF') ? 'selected' : '';?>>Tạm ngưng</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="color: green">Cộng tác viên</label>
                        <div class="col-sm-5">
                            <select id="veri" class="selectboxit">
                                <option value="1" <?=($row['verify'] == '1') ? 'selected' : '';?>>Chấp thuận</option>
                                <option value="0" <?=($row['verify'] == '0') ? 'selected' : '';?>>Chưa chấp thuận</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Số zalo</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control daterange" value="<?=$row['zalo'];?>" disabled/>
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
                url: "<?=BASE_URL('controller/admin/Users.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    type: 'UpdateInfo',
                    id: <?=$row['id'];?>,
                    email: $("#email").val(),
                    sotien: $("#sotien").val(),
                    tongtien: $("#tongtien").val(),
                    status: $("#status").val(),
                    veri: $("#veri").val()
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
                    <b>Cộng tiền thành viên</b>
                </div>
            </div>

            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered">

                    <div class="form-group">
                        <label class="col-sm-3 control-label"  style="color: green">Số tiền muốn cộng</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control daterange fnum" id="sotiencong" value="0"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Ghi chú cộng tiền</label>
                        <div class="col-sm-5">
                            <textarea type="text" id="ghichu" class="form-control daterange" rows="4" placeholder="Ghi chú cộng tiền (Nếu có)"></textarea>
                        </div>
                    </div>
                
                    <div class="form-group">
                        <div class="col-sm-5 control-label">
                            <button type="submit" id="btnCongtien" class="btn btn-success">Cộng tiền</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $("#btnCongtien").on("click", function() {
            $('#btnCongtien').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled',
                true);
            $.ajax({
                url: "<?=BASE_URL('controller/admin/Users.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    type: 'Congtien',
                    id: <?=$row['id'];?>,
                    sotien: $("#sotiencong").val(),
                    ghichu: $("#ghichu").val()
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
                    $('#btnCongtien').html('Cộng tiền').prop('disabled', false);
                },
                error: function() {
                    cuteToast({
                        type: "error",
                        message: 'Không thể xử lý',
                        timer: 5000
                    });
                    $('#btnCongtien').html('Cộng tiền').prop('disabled', false);
                }

            });
        });
    </script>


    <div class="col-md-6">

        <div class="panel panel-primary" data-collapsed="0">

            <div class="panel-heading">
                <div class="panel-title">
                    <b>Trừ tiền thành viên</b>
                </div>
            </div>

            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered">

                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="color: red">Số tiền muốn trừ</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control daterange fnum" id="sotientru" value="0"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Ghi chú trừ tiền</label>
                        <div class="col-sm-5">
                            <textarea type="text" id="ghichu" class="form-control daterange" rows="4" placeholder="Ghi chú trừ tiền (Nếu có)"></textarea>
                        </div>
                    </div>

                
                    <div class="form-group">
                        <div class="col-sm-5 control-label">
                            <button type="submit" id="btnTrutien" class="btn btn-danger">Trừ tiền</button>
                        </div>
                    </div>
                </form>

            </div>

        </div>

    </div>

</div>

<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-primary">
            <div class="panel-heading">
                <div class="panel-title"><b>Lịch sử dòng tiền</b></div>

              
            </div>

            <table class="table table-bordered responsive">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Username</th>
                        <th>Tiền trước</th>
                        <th>Tổng tiền</th>
                        <th>Tiền sau</th>
                        <th>Thời gian</th>
                        <th>Nội dung</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $i = 0; foreach($TUANORI->get_list(" SELECT * FROM `biendongsodu` WHERE `username` = '".$row['username']."' ORDER BY id DESC LIMIT 20") as $row){ ?>
                    <tr>
                        <td><?=++$i;?></td>
                        <td><a target="_blank" style="color: #007BFF; font-weight: bold" href="/pages/admin/EditQuanlythanhvien.php?id=<?=$TUANORI->getUser($row['username'])['id'];?>"><?=$row['username'];?></a></td>
                        <td><b><?=number_format($row['truoc']);?>đ</b></td>
                        <td>
                            <?php if($row['sau'] > $row['truoc']) {
                                echo '<b style="color: green">+'.format_cash($row['tongtien']).'đ</b>';
                            }else {
                                echo '<b style="color: red">-'.format_cash($row['tongtien']).'đ</b>';
                            } ?>
                        </td>
                        <td><b><?=number_format($row['sau']);?>đ</b></td>
                        <td><b><?=$row['time'];?></b></td>
                        <td ><b><?=$row['note'];?></b></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

    </div>
 
</div>


<script type="text/javascript">
    $("#btnTrutien").on("click", function() {
        $('#btnTrutien').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled',
            true);
        $.ajax({
            url: "<?=BASE_URL('controller/admin/Users.php');?>",
            method: "POST",
            dataType: "JSON",
            data: {
                type: 'Trutien',
                id: <?=$_GET['id'];?>,
                sotien: $("#sotientru").val(),
                ghichu: $("#ghichu").val()
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
                $('#btnTrutien').html('Trừ tiền').prop('disabled', false);
            },
            error: function() {
                cuteToast({
                    type: "error",
                    message: 'Không thể xử lý',
                    timer: 5000
                });
                $('#btnTrutien').html('Trừ tiền').prop('disabled', false);
            }

        });
    });
</script>
<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
require_once("../../pages/admin/Footer.php");
?>