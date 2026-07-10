<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'EDIT THÊM MÃ NGUỒN';
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
CheckAdmin();
?>
<?php
if(isset($_GET['id']) && $getUser['level'] == 'admin')
{
    $id = check_string($_GET['id']);
    $row = $TUANORI->get_row(" SELECT * FROM `danhsachmuacode` WHERE `id` = '".$_GET['id']."'");
    if(!$row) {
        echo msg_admin("error","Mã nguồn không tồn tại",BASE_URL('Admin/ThemMaNguon'), 1000); die;
    }
} else {
    echo msg_admin("error","Đường link không hợp lệ",BASE_URL('Admin/ThemMaNguon'), 1000); die;
}
?>
<h2>Edit mã nguồn <a href="/Admin/ThemMaNguon" class="btn btn-info"><i class="fa fa-backward"></i> Quay Lại</a></h2>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <b>Edit Mã Nguồn</b>
                </div>
            </div>
            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Danh mục</label>
                        
                        <div class="col-sm-5">
                            <select id="danhmuc" class="selectboxit">
                                <?php foreach($TUANORI->get_list(" SELECT * FROM `danhmucmuacode` ORDER BY id DESC") as $ok){ ?>
                                    <option value="<?=$ok['id'];?>" <?=($ok['id'] == $row['id_danhmuc']) ? 'selected': ''; ?>><?=$ok['title'];?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Tên Mã Web</label>
                        <div class="col-sm-5">
                            <input type="text" id="name" value="<?=$row['title'];?>" class="form-control daterange"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Mô Tả Mẫu Web</label>
                        <div class="col-sm-5">
                            <input type="text" id="mota" value="<?=$row['mota'];?>" class="form-control daterange"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Link DownLoad Mã Nguồn</label>
                        <div class="col-sm-5">
                            <input type="text" id="download" value="<?=$row['download'];?>" class="form-control daterange"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Link 1s</label>
                        <div class="col-sm-5">
                            <input type="text" id="download_link1s" value="<?=$row['download_link1s'];?>" class="form-control daterange"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="color: green">Giá Bán Mẫu Web</label>
                        <div class="col-sm-5">
                            <input type="text" id="money" value="<?=format_cash($row['money']);?>" class="form-control daterange fnum" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Hình Ảnh (Thumbnail)</label>
                        <div class="col-sm-5">
                            <input type="text" value="<?=$row['img'];?>" class="form-control daterange" id="img" />
                            <img src="<?=$row['img'];?>" class="form-control daterange" style="width: 100%; height: 200px">

                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">List ảnh mô tả (Mỗi ảnh cách 1 dòng)</label>
                        <div class="col-sm-5">
                            <textarea type="text" class="form-control daterange" rows="4" placeholder="Link ảnh mô tả" id="listimg"><?=$row['listimg'];?></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Link Demo</label>
                        <div class="col-sm-5">
                            <input type="text" value="<?=$row['demo'];?>" class="form-control daterange" id="demo"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Trạng thái</label>
                        <div class="col-sm-5">
                            <select id="hienthi" class="selectboxit">
                                <option value="SHOW" <?=($row['hienthi'] == 'SHOW') ? 'selected': ''?>>Hiển thị</option>
                                <option value="OFF" <?=($row['hienthi'] == 'OFF') ? 'selected': ''?>>Ẩn đi</option>
                            </select>
                        </div>
                    </div>
            
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Status mua</label>
                        <div class="col-sm-5">
                            <select id="statusmua" class="selectboxit">
                                <option value="ON" <?=($row['statusmua'] == 'ON') ? 'selected': ''?>>ON</option>
                                <option value="OFF" <?=($row['statusmua'] == 'OFF') ? 'selected': ''?>>OFF</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Người đăng bán</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control daterange" id="partner" value="<?=$row['partner'];?>"/>
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
                url: "<?=BASE_URL('controller/admin/DanhMucCode.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    id: "<?=$row['id'];?>",
                    type: 'ThemCode',
                    type2: 'EditThemCode',
                    danhmuc: $("#danhmuc").val(),
                    name: $("#name").val(),
                    mota: $("#mota").val(),
                    download: $("#download").val(),
                    money: $("#money").val(),
                    img: $("#img").val(),
                    listimg: $("#listimg").val(),
                    download_link1s: $("#download_link1s").val(),
                    demo: $("#demo").val(),
                    hienthi: $("#hienthi").val(),
                    statusmua: $("#statusmua").val(),
                    partner: $("#partner").val(),
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

<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
require_once("../../pages/admin/Footer.php");
?>

