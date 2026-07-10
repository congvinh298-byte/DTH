<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'THÊM MÃ NGUỒN MỚI';
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
CheckAdmin();
?>
<?php
if(isset($_GET['id']) && $getUser['level'] == 'admin')
{
    $row = $DMH->get_row(" SELECT * FROM `danhsachtaoweb` WHERE `id` = '".$_GET['id']."'");
    if(!$row)
    {
        echo msg_admin("error" ,"mẫu web không tồn tạii",BASE_URL('Admin/ThemMauWeb'), 1000); die;
    }
} else {
    echo msg_admin("error" ,"Đường link không hợp lệ",BASE_URL('Admin/ThemMauWeb'), 1000); die;
}
?>
<h2>Edit mẫu website <a href="/Admin/ThemMauWeb" class="btn btn-info"><i class="fa fa-backward"></i> Quay Lại</a></h2>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <b>Edit Mẫu Website</b>
                </div>
            </div>
            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered">
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Danh mục</label>
                        
                        <div class="col-sm-5">
                            <select id="danhmuc" class="selectboxit">
                                <?php foreach($DMH->get_list(" SELECT * FROM `danhmuctaoweb` ORDER BY id DESC") as $row2){ ?>
                                    <option value="<?=$row2['id'];?>" <?=($row2['id'] == $row['id_danhmuc']) ? 'selected' : ''?>><?=$row2['title'];?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label">Tên Mã Web</label>
                        <div class="col-sm-5">
                            <input type="text" id="name"  value="<?=$row['title'];?>"  class="form-control daterange"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Mô Tả Mẫu Web</label>
                        <div class="col-sm-5">
                            <input type="text" id="mota" value="<?=$row['mota'];?>" class="form-control daterange"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="color: green">Giá Bán</label>
                        <div class="col-sm-5">
                            <input type="text" id="money" value="<?=format_cash($row['money']);?>"  class="form-control daterange fnum" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Hình Ảnh (Thumbnail)</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control daterange" id="img" value="<?=$row['img'];?>" />
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
                            <input type="text" class="form-control daterange" value="<?=$row['demo'];?>" id="demo"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label">Trạng thái</label>
                        <div class="col-sm-5">
                            <select class="form-control" id="status">
                                <option value="ON" <?=($row['status'] == 'ON') ? 'selected' : '' ;?>>Hoạt Động</option>
                                <option value="OFF" <?=($row['status'] == 'OFF') ? 'selected' : '' ;?>>Tạm ngưng</option>
                            </select>
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
                url: "<?=BASE_URL('controller/admin/DanhMucWeb.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    id: "<?=$row['id'];?>",
                    type: 'AddMauWeb',
                    type2: 'EditThemMauWeb',
                    danhmuc: $("#danhmuc").val(),
                    name: $("#name").val(),
                    mota: $("#mota").val(),
                    money: $("#money").val(),
                    img: $("#img").val(),
                    listimg: $("#listimg").val(),
                    demo: $("#demo").val(),
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

</div>

<br />


<?php

require_once("../../pages/admin/Footer.php");
?>

