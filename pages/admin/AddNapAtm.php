<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'ADD NẠP ATM';
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
CheckAdmin();
?>
<h2>NẠP TIỀN ATM <a href="/Admin/LichsunaptienATM" class="btn btn-info"><i class="fa fa-backward"></i> Quay Lại</a></h2>
<div class="row">
    <div class="col-md-12">

        <div class="panel panel-primary" data-collapsed="0">

            <div class="panel-heading">
                <div class="panel-title">
                    <b>Thêm thông tin nạp tiền ATM</b>
                </div>

            </div>

            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">ID nạp tiền</label>
                        <div class="col-sm-5">
                            <input type="text" id="id_user" class="form-control daterange"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Mã giao dịch</label>
                        <div class="col-sm-5">
                            <input type="text" id="magd" class="form-control daterange"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Name ATM</label>
                        <div class="col-sm-5">
                            <select id="nameatm" class="selectboxit">
                                <option value="MBBANK">MBBANK</option>
                                <option value="MOMO">MOMO</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Số tiền</label>
                        <div class="col-sm-5">
                            <input type="text" id="sotien" class="form-control daterange fnum" />
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
                url: "<?=BASE_URL('controller/admin/AddNapAtm.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    id_user: $("#id_user").val(),
                    nameatm: $("#nameatm").val(),
                    magd: $("#magd").val(),
                    sotien: $("#sotien").val()
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
<h2>Thông tin nạp tiền</h2>
<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>STT</th>
            <th>Username</th>
            <th>ND Nạp tiền</th>
            <th>Mã giao dịch</th>
            <th>Ngân Hàng</th>
            <th>Số tiền</th>
            <th>Thời gian</th>
            <th>Status</th>
		</tr>
	</thead>
	<tbody>
    <?php $i = 1;  foreach($TUANORI->get_list(" SELECT * FROM `napatm` ORDER BY id DESC LIMIT 10") as $row){ ?>
        <tr>
            <td><?=$i++;?></td>
            <td><a href="/pages/admin/EditQuanlythanhvien.php?id=<?=$TUANORI->getUser($row['username'])['id'];?>" target="_blank" style="color: #0099CC; font-weight: bold;"><?=$row['username'];?></a></td>
            <td style="font-weight: bold;"><?=$row['ndnaptien'];?></td>
            <td style="font-weight: bold; color: black"><?=$row['magd'];?></td>
            <td><?=$row['hinhthuc'];?></td>
            <td><b style="color: green"><?=format_cash($row['sotien']);?>đ</b></td>
            <td><?=$row['thoigian'];?></td>
            <td><span class="badge badge-success">Thành công</span></td>
        </tr>
    <?php } ?>
	</tbody>
	
</table>

<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
require_once("../../pages/admin/Footer.php");
?>

