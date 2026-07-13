<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'RÚT TIỀN';
CheckAdmin();
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
?>
<?php
if(isset($_GET['id']) && $getUser['level'] == 'admin') {
    $row = $DMH->get_row(" SELECT * FROM `partner_ruttien` WHERE `id` = '".$_GET['id']."'");
    if(!$row) {
        echo msg_admin("error", "Đơn rút tiền không tồn tại", BASE_URL('Admin/Par_ruttien'), 1000); die;
    }
} else {
    echo msg_admin("error", "Đường link không hợp lệ", BASE_URL('Admin/Par_ruttien'), 1000); die;
}
?>
<h2>Rút tiền</h2>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <b>Rút tiền</b>
                </div>
            </div>
            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Ngân hàng</label>
                        <div class="col-sm-5">
                            <select id="atm" class="selectboxit">
                                <option value="">Chọn ngân hàng</option>
                                <?php foreach(list_bank() as $ok) { ?>
                                    <option value="<?=$ok;?>" <?=($ok == $row['atm']) ? 'selected': ''?>><?=$ok;?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Số tài khoản</label>
                        <div class="col-sm-5">
                            <input type="text" id="stk" value="<?=$row['stk'];?>" class="form-control daterange"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Tên chủ thẻ</label>
                        <div class="col-sm-5">
                            <input type="text" id="name" value="<?=$row['name'];?>" class="form-control daterange"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="color: green">Số tiền muốn rút</label>
                        <div class="col-sm-5">
                            <input type="text" id="sotien" value="<?=format_cash($row['sotien']);?>" class="form-control daterange fnum"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Ghi chú</label>
                        <div class="col-sm-5">
                            <textarea type="text" id="note" rows="5" class="form-control daterange"><?=$row['note'];?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">MÃ QR CODE</label>
                        <div class="col-sm-5">
                            <?php if($row['atm'] == 'MOMO') { ?>
                                <img src="https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl=2|99|<?=$row['stk'];?>|||0|0|<?=$row['sotien'];?>|DMH THANH TOAN|transfer_myqr" width="400px">
                            <?php } else { ?>
                                <img src="https://api.vietqr.io/<?=$row['atm'];?>/<?=$row['stk'];?>/<?=$row['sotien'];?>/DMH THANH TOAN/vietqr_net_2.jpg?accountName=<?=$row['name'];?>" width="400px">
                            <?php } ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Trạng thái</label>
                        <div class="col-sm-5">
                            <select id="status" class="selectboxit">
                                <option value="xuly" <?=($row['status'] == 'xuly') ? 'selected': '';?>>Đang xử lý</option>
                                <option value="thatbai" <?=($row['status'] == 'thatbai') ? 'selected': '';?>>Đã hủy</option>
                                <option value="thanhcong" <?=($row['status'] == 'thanhcong') ? 'selected': '';?>>Đã hoàn thành</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-5 control-label">
                            <button type="submit" id="btnUpdate" class="btn btn-success"><i class="fa fa-share"></i> Lưu thông tin</button>
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
                url: "<?=BASE_URL('controller/admin/Par_Ruttien.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    id: '<?=$_GET['id'];?>',
                    atm: $("#atm").val(),
                    stk: $("#stk").val(),
                    name: $("#name").val(),
                    sotien: $("#sotien").val(),
                    note: $("#note").val(),
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
                    $('#btnUpdate').html('<i class="fa fa-share"></i> Lưu thông tin').prop('disabled', false);
                },
                error: function() {
                    cuteToast({
                        type: "error",
                        message: 'Không thể xử lý',
                        timer: 5000
                    });
                    $('#btnUpdate').html('<i class="fa fa-share"></i> Lưu thông tin').prop('disabled', false);
                }

            });
        });
    </script>

</div>

<br />

<h2>Lịch sử rút tiền</h2>
<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>STT</th>
            <th>Ngân Hàng</th>
            <th>Số tài khoản</th>
            <th>Tên chủ thẻ</th>
            <th>Số tiền rút</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
		</tr>
	</thead>
	<?php $i = 1;  foreach($DMH->get_list(" SELECT * FROM `partner_ruttien` WHERE `username` = '".$getUser['username']."' ORDER BY id DESC") as $row){ ?>
        <tr>
            <td><?=$i++;?></td>
            <td><b style="color: green"><?=$row['atm'];?></b></td>
            <td><b><?=$row['stk'];?></b></td>
            <td><b><?=$row['name'];?></b></td>
            <td><b style="color: green"><?=format_cash($row['sotien']);?>đ</b></td>
            <td><b><?=status_partner($row['status']);?></b></td>
            <td>
                <a href="<?=BASE_URL('Admin/EditPar_ruttien?id='.$row['id']);?>" class="btn btn-default btn-sm btn-icon icon-left">
					<i class="entypo-pencil"></i>
					Edit
				</a>
            </td>
        </tr>
    <?php } ?>
	</tbody>
	
</table>

<?php

require_once("../../pages/admin/Footer.php");
?>

