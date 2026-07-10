<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'TRANG QUẢN TRỊ HỆ THỐNG';
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
CheckAdmin();
?>
<?php
if(isset($_GET['xoa']) && $getUser['level'] == 'admin') {
    $user2 = $TUANORI->get_row(" SELECT * FROM `danhsachtaoweb` WHERE `id` = '".$_GET['xoa']."'");
    if(!$user2) {
        echo msg_admin("error", "Mẫu web này không tồn tại", BASE_URL('Admin/ThemMauWeb'), 1000); die;
    } else {
        $dele = $TUANORI->remove("danhsachtaoweb", " `id` = '".$_GET['xoa']."' ");
        if($dele) {
            echo msg_admin("success", "Đã xóa mẫu web thành công", BASE_URL('Admin/ThemMauWeb'), 1000);
        } else {
            echo msg_admin("error", "Xóa thất bại. Lỗi hệ thống", BASE_URL('Admin/ThemMauWeb'), 1000);
        }
    }
}
?>
<h2>Đăng bán mẫu web</h2>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <b>Thêm danh mục bán mẫu web</b>
                </div>
            </div>
            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Danh mục</label>
                        
                        <div class="col-sm-5">
                            <select id="danhmuc" class="selectboxit">
                                <?php foreach($TUANORI->get_list(" SELECT * FROM `danhmuctaoweb` ORDER BY id DESC") as $row){ ?>
                                    <option value="<?=$row['id'];?>"><?=$row['title'];?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Tên Mã Web</label>
                        <div class="col-sm-5">
                            <input type="text" id="name" class="form-control daterange"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Mô Tả Mẫu Web</label>
                        <div class="col-sm-5">
                            <input type="text" id="mota" class="form-control daterange"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="color: green">Giá Bán Mẫu Web</label>
                        <div class="col-sm-5">
                            <input type="text" id="money" class="form-control daterange fnum" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Hình Ảnh (Thumbnail)</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control daterange" id="img" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">List ảnh mô tả (Mỗi ảnh cách 1 dòng)</label>
                        <div class="col-sm-5">
                            <textarea type="text" class="form-control daterange" rows="4" placeholder="Link ảnh mô tả" id="listimg"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Link Demo</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control daterange" id="demo"/>
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
                url: "<?=BASE_URL('controller/admin/DanhMucWeb.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    type: 'AddMauWeb',
                    type2: 'AddThemMauWeb',
                    danhmuc: $("#danhmuc").val(),
                    name: $("#name").val(),
                    mota: $("#mota").val(),
                    money: $("#money").val(),
                    img: $("#img").val(),
                    listimg: $("#listimg").val(),
                    demo: $("#demo").val()
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

<h2>Các mẫu website</h2>
<table class="table table-bordered responsive">
	<thead>
		<tr>
            <th>STT</th>
            <th width="5%">Danh Mục</th>
            <th width="25%">Tên</th>
            <th width="25%">Mô Tả</th>
            <th>Giá Bán</th>
            <th>Thumbnail</th>
            <th>STATUS</th>
            <th>Thao tác</th>
		</tr>
	</thead>
	<tbody>
        <?php $i = 0; foreach($TUANORI->get_list(" SELECT * FROM `danhsachtaoweb` ORDER BY id DESC") as $row){ ?>
        <tr>
            <td><?=++$i;?></td>
            <td><a target="_blank" href="/pages/admin/EditDanhmuctaoweb.php?id=<?=$row['id_danhmuc'];?>"><span class="btn btn-info" style="padding: 4px 8px;"><?=$row['id_danhmuc'];?></span></a></td>
            <td><b style="font-size: 15px"><?=$row['title'];?></b></td>
            <td><b style="font-size: 13px"><?=$row['mota'];?></b></td>
            <td><b style="color: green"><?=giaban(format_cash($row['money']));?></b></td>
            <td> <img class="rounded" src="<?=$row['img'];?>" style="width: 300px; height: 100px"> </td>
            <td><?=danhmuc($row['hienthi']);?><br/><?=taoweb_on($row['status']);?></td>
            <td>
                <a href="<?=BASE_URL('pages/admin/EditThemMauWeb.php?id='.$row['id'].'');?>" class="btn btn-default btn-sm btn-icon icon-left">
					<i class="entypo-pencil"></i>
					Edit
				</a>
				<a href="<?=BASE_URL('Admin/ThemMauWeb?xoa='.$row['id'].'');?>" class="btn btn-danger btn-sm btn-icon icon-left">
					<i class="entypo-cancel"></i>
					Delete
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

