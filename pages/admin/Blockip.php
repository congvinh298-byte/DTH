<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'BLOCK IP';
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
CheckAdmin();
?>
<?php
if(isset($_GET['id']) && $getUser['level'] == 'admin')
{
    $user2 = $TUANORI->get_row(" SELECT * FROM `blockip` WHERE `id` = '".$_GET['id']."'");
    if(!$user2)
    {
        echo msg_admin("error", "Tài khoản này không tồn tại trong hệ thống", BASE_URL('Admin/Blockip'), 1000);
    }
    else
    {
        $dele = $TUANORI->remove("blockip", " `id` = '".$_GET['id']."' ");
        if($dele)
        {
            echo msg_admin("success","Ân xá thành công cho thiết bị này", BASE_URL('Admin/Blockip'), 1500);
        }
        else
        {
            echo msg_admin("error", "Ân xá thất bại. Lỗi hệ thống", BASE_URL('Admin/Blockip'), 1000);
        }
    }
}
?>
<h2>BLOCK IP</h2>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <b>BLOCK IP THÀNH VIÊN</b>
                </div>
            </div>
            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Tên username</label>
                        <div class="col-sm-5">
                            <input type="text" id="username" class="form-control daterange" placeholder="Tên username cần khóa"/>
                            <i style="color: green">Hệ thống sẽ khóa tất cả các username có chung IP với username này.</i>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-sm-5 control-label">
                            <button type="submit" id="btnCheck" class="btn btn-danger"><i class="fa fa-lock"></i> Khóa</button>
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
                url: "<?=BASE_URL('controller/admin/BlockIP.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    username: $("#username").val()
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
                    $('#btnCheck').html('<i class="fa fa-lock"></i> Khóa').prop('disabled', false);
                },
                error: function() {
                    cuteToast({
                        type: "error",
                        message: 'Không thể xử lý',
                        timer: 5000
                    });
                    $('#btnCheck').html('<i class="fa fa-lock"></i> Khóa').prop('disabled', false);
                }

            });
        });
    </script>

</div>

<br />

<h2>Danh Sách username bị BLOCK</h2>
<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>STT</th>
            <th>USERNAME</th>
            <th>IP</th>
            <th>THỜI GIAN KHÓA</th>
            <th>THAO TÁC</th>
		</tr>
	</thead>
	<?php $i = 1;  foreach($TUANORI->get_list(" SELECT * FROM `blockip` ORDER BY id DESC") as $row){ ?>
        <tr>
            <td><?=$i++;?></td>
            <td>
                <?php foreach($TUANORI->get_list(" SELECT * FROM `users` WHERE `ip` = '".$row['ip']."' ORDER BY id DESC") as $row2){ 
                    echo '<a href="/pages/admin/EditQuanlythanhvien.php?id='.$TUANORI->getUser($row2['username'])['id'].'">'.$row2['username'].'</a>, ';
                }
                ?>
            </td>
            <td><b style="color: green"><?=$row['ip'];?></b></td>
            <td><b><?=$row['time'];?></b></td>
            <td>
                <a href="<?=BASE_URL('Admin/Blockip?id='.$row['id'].'');?>" class="btn btn-danger btn-sm btn-icon icon-left">
					<i class="entypo-cancel"></i>
					Ân Xá
				</a>
            </td>
        </tr>
    <?php } ?>
	</tbody>
	
</table>

<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
require_once("../../pages/admin/Footer.php");
?>

