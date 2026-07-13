<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'EDIT API';
CheckAdmin();
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
?>
<?php
if(isset($_GET['id']) && $getUser['level'] == 'admin') {
    $row = $DMH->get_row(" SELECT * FROM `key_apis` WHERE `id` = '".$_GET['id']."'");
    if(!$row) {
        echo msg_admin("error","Danh mục không tồn tại",BASE_URL('Admin/Quanlyapi'), 1000); die;
    }
} else {
    echo msg_admin("error","Đường link không hợp lệ",BASE_URL('Admin/Quanlyapi'), 1000); die;
}
?>
<h2>Edit API <a href="/Admin/Quanlyapi" class="btn btn-info"><i class="fa fa-backward"></i> Quay Lại</a></h2>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <b>Edit API</b>
                </div>
            </div>
            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered">

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Username</label>
                        <div class="col-sm-5">
                            <input type="text" value="<?=$row['username'];?>" class="form-control daterange" disabled/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Mã API</label>
                        <div class="col-sm-5">
                            <input type="text" value="<?=$DMH->getUser($row['username'])['token_api'];?>" class="form-control daterange" disabled/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">WHOIS</label>
                        <div class="col-sm-5">
                            <select id="whois" class="selectboxit">
                                <option value="ON" <?=($row['whois'] == 'ON') ? 'selected': ''?>>ON</option>
                                <option value="OFF" <?=($row['whois'] == 'OFF') ? 'selected': ''?>>OFF</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">LIST CODE</label>
                        <div class="col-sm-5">
                            <select id="list_code" class="selectboxit">
                                <option value="ON" <?=($row['list_code'] == 'ON') ? 'selected': ''?>>ON</option>
                                <option value="OFF" <?=($row['list_code'] == 'OFF') ? 'selected': ''?>>OFF</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">MUA CODE</label>
                        <div class="col-sm-5">
                            <select id="buy_code" class="selectboxit">
                                <option value="ON" <?=($row['buy_code'] == 'ON') ? 'selected': ''?>>ON</option>
                                <option value="OFF" <?=($row['buy_code'] == 'OFF') ? 'selected': ''?>>OFF</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">MUA MIỀN</label>
                        <div class="col-sm-5">
                            <select id="buy_domain" class="selectboxit">
                                <option value="ON" <?=($row['buy_domain'] == 'ON') ? 'selected': ''?>>ON</option>
                                <option value="OFF" <?=($row['buy_domain'] == 'OFF') ? 'selected': ''?>>OFF</option>
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
                url: "<?=BASE_URL('controller/admin/EditApi.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    id: '<?=$_GET['id'];?>',
                    whois: $("#whois").val(),
                    list_code: $("#list_code").val(),
                    buy_code: $("#buy_code").val(),
                    buy_domain: $("#buy_domain").val()
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

<br />
<h2>Danh Sách API</h2>
<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>STT</th>
            <th width="10%">Username</th>
            <th>WHOIS</th>
            <th>List Code</th>
            <th>Mua Code</th>
            <th>Mua Miền</th>
            <th>Thao tác</th>
		</tr>
	</thead>
	<?php $i = 1;  foreach($DMH->get_list(" SELECT * FROM `key_apis` WHERE `id` = '".$_GET['id']."' ") as $row){ ?>
        <tr>
            <td><?=$i++;?></td>
            <td><a href="/pages/admin/EditQuanlythanhvien.php?id=<?=$DMH->getUser($row['username'])['id'];?>" target="_blank" style="color: #0099CC; font-weight: bold;"><?=$row['username'];?></a></td>
            <td><?=on_off($row['whois']);?></td>
            <td><?=on_off($row['list_code']);?></td>
            <td><?=on_off($row['buy_code']);?></td>
            <td><?=on_off($row['buy_domain']);?></td>                                  
            <td>
                <a href="<?=BASE_URL('pages/admin/Quanlyapi.php?dele=true&id='.$row['id'].'');?>" class="btn btn-danger btn-sm btn-icon icon-left">
					<i class="entypo-cancel"></i>
					Delete
				</a>
                <a href="<?=BASE_URL('pages/admin/Quanlyapi.php?on=true&id='.$row['id'].'');?>" class="btn btn-success btn-sm btn-icon icon-left">
					<i class="entypo-check"></i>
					ON ALL
				</a>
                <a href="<?=BASE_URL('pages/admin/Quanlyapi.php?off=true&id='.$row['id'].'');?>" class="btn btn-default btn-sm btn-icon icon-left">
					<i class="entypo-block"></i>
					OFF ALL
				</a>
            </td>
        </tr>
    <?php } ?>
	</tbody>
	
</table>
<?php

require_once("../../pages/admin/Footer.php");
?>

