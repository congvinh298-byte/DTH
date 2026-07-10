<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'RÚT TIỀN';
require_once("../../pages/partner/Head.php");
require_once("../../pages/partner/Header.php");
CheckVeri();
?>
<?php
if(isset($_GET['xoa']) && $getUser['level'] == 'admin')
{
    $user2 = $TUANORI->get_row(" SELECT * FROM `magiamgia` WHERE `id` = '".$_GET['xoa']."'");
    if(!$user2) {
        echo msg_admin("error", "Mã giảm giá này không tồn tại", BASE_URL('Admin/Magiamgia'), 1000);
    } else {
        $dele = $TUANORI->remove("magiamgia", " `id` = '".$_GET['xoa']."' ");
        if($dele) {
            echo msg_admin("success","Đã xóa mã giảm giá thành công", BASE_URL('Admin/Magiamgia'), 1000);
        } else {
            echo msg_admin("error", "Xóa thất bại. Lỗi hệ thống", BASE_URL('Admin/Magiamgia'), 1000);
        }
    }
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
                                    <option value="<?=$ok;?>"><?=$ok;?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Số tài khoản</label>
                        <div class="col-sm-5">
                            <input type="text" id="stk" class="form-control daterange"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Tên chủ thẻ</label>
                        <div class="col-sm-5">
                            <input type="text" id="name" class="form-control daterange"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Số tiền muốn rút</label>
                        <div class="col-sm-5">
                            <input type="text" id="sotien" class="form-control daterange fnum"/>
                            <i>Số tiền rút tối thiểu là <b style="color: green">10.000đ</b>. Phí rút tiền là <b style="color: green">5.000đ</b></i>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-5 control-label">
                            <button type="submit" id="btnRuttien" class="btn btn-success"><i class="fa fa-share"></i> Rút tiền</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $("#btnRuttien").on("click", function() {
            $('#btnRuttien').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled',
                true);
            $.ajax({
                url: "<?=BASE_URL('controller/partner/Ruttien.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    atm: $("#atm").val(),
                    stk: $("#stk").val(),
                    name: $("#name").val(),
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
                    $('#btnRuttien').html('<i class="fa fa-share"></i> Rút tiền').prop('disabled', false);
                },
                error: function() {
                    cuteToast({
                        type: "error",
                        message: 'Không thể xử lý',
                        timer: 5000
                    });
                    $('#btnRuttien').html('<i class="fa fa-share"></i> Rút tiền').prop('disabled', false);
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
            <th>Ghi chú</th>
            <th>Trạng thái</th>
		</tr>
	</thead>
	<?php $i = 1;  foreach($TUANORI->get_list(" SELECT * FROM `partner_ruttien` WHERE `username` = '".$getUser['username']."' ORDER BY id DESC") as $row){ ?>
        <tr>
            <td><?=$i++;?></td>
            <td><b style="color: green"><?=$row['atm'];?></b></td>
            <td><b><?=$row['stk'];?></b></td>
            <td><b><?=$row['name'];?></b></td>
            <td><b><?=format_cash($row['sotien']);?>đ</b></td>
            <td><b><?=$row['note'];?></b></td>
            <td><b><?=status_partner($row['status']);?></b></td>
        </tr>
    <?php } ?>
	</tbody>
	
</table>

<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
require_once("../../pages/partner/Footer.php");
?>

