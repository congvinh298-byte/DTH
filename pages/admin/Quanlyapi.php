<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'QUẢN LÝ API';
CheckAdmin();
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
?>
<?php
if(isset($_GET['dele']) || isset($_GET['on']) || isset($_GET['off']))
{
    if($getUser['level'] == 'admin')
    {
        $user2 = $DMH->get_row(" SELECT * FROM `key_apis` WHERE `id` = '".$_GET['id']."'");
        if(!$user2)
        {
            echo msg_admin("error","Token API này không chính xác", BASE_URL('Admin/Quanlyapi'), 1000);
        } else {
            if(isset($_GET['dele'])) {
                $dele = $DMH->remove("key_apis", " `id` = '".$_GET['id']."' ");
                if($dele) {
                    echo msg_admin("success","Đã xóa API thành công", BASE_URL('Admin/Quanlyapi'), 1000);
                } else {
                    echo msg_admin("error","Tắt kích hoạt thất bại", BASE_URL('Admin/Quanlyapi'), 1000);
                }
            }
            if(isset($_GET['on']) || isset($_GET['off'])) {
                if(isset($_GET['on'])) {
                    $giatri = 'ON';
                } else {
                    $giatri = 'OFF';
                }
                $update = $DMH->update("key_apis", array(
                    'whois'         => $giatri,
                    'list_code'     => $giatri,
                    'buy_code'      => $giatri,
                    'buy_domain'    => $giatri
                ), " `id` = '".$_GET['id']."' ");
                if($update) {
                    echo msg_admin("success","Thao tác $giatri ALL thành công", BASE_URL('Admin/Quanlyapi'), 1000);
                } else {
                    echo msg_admin("error","Thao tác $giatri ALL thất bại", BASE_URL('Admin/Quanlyapi'), 1000);
                }
            }
        }
    }
}
?>
<h2>Kích hoạt API</h2>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <b>Kích Hoạt Mã API</b>
                </div>
            </div>
            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Mã token API</label>
                        <div class="col-sm-5">
                            <input type="text" id="token" placeholder="Mã token API" class="form-control daterange"/>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-sm-5 control-label">
                            <button type="submit" id="btnCheck" class="btn btn-success"><i class="fa fa-unlock-alt"></i> Kích hoạt</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $("#btnCheck").on("click", function() {
            $('#btnCheck').html('<i class="fa fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled',
                true);
            $.ajax({
                url: "<?=BASE_URL('controller/admin/KichHoatAPI.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    token: $("#token").val()
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
                    $('#btnCheck').html('<i class="fa fa-unlock-alt"></i> Kích hoạt').prop('disabled', false);
                },
                error: function() {
                    cuteToast({
                        type: "error",
                        message: 'Không thể xử lý',
                        timer: 5000
                    });
                    $('#btnCheck').html('<i class="fa fa-unlock-alt"></i> Kích hoạt').prop('disabled', false);
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
	<?php $i = 1;  foreach($DMH->get_list(" SELECT * FROM `key_apis` ORDER BY id DESC LIMIT 200") as $row){ ?>
        <tr>
            <td><?=$i++;?></td>
            <td><a href="/pages/admin/EditQuanlythanhvien.php?id=<?=$DMH->getUser($row['username'])['id'];?>" target="_blank" style="color: #0099CC; font-weight: bold;"><?=$row['username'];?></a></td>
            <td><?=on_off($row['whois']);?></td>
            <td><?=on_off($row['list_code']);?></td>
            <td><?=on_off($row['buy_code']);?></td>
            <td><?=on_off($row['buy_domain']);?></td>                                  
            <td>
                <a href="<?=BASE_URL('pages/admin/EditApi.php?id='.$row['id']);?>" class="btn btn-default btn-sm btn-icon icon-left">
					<i class="entypo-pencil"></i>
					Edit
				</a>
                <a href="<?=BASE_URL('Admin/Quanlyapi?dele=true&id='.$row['id'].'');?>" class="btn btn-danger btn-sm btn-icon icon-left">
					<i class="entypo-cancel"></i>
					Delete
				</a>
                <a href="<?=BASE_URL('Admin/Quanlyapi?on=true&id='.$row['id'].'');?>" class="btn btn-success btn-sm btn-icon icon-left">
					<i class="entypo-check"></i>
					ON ALL
				</a>
                <a href="<?=BASE_URL('Admin/Quanlyapi?off=true&id='.$row['id'].'');?>" class="btn btn-default btn-sm btn-icon icon-left">
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

