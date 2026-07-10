<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'THÊM MÃ NGUỒN MỚI';
require_once("../../pages/partner/Head.php");
require_once("../../pages/partner/Header.php");
CheckVeri();
?>
<?php
if(isset($_GET['xoa']) && $getUser['level'] == 'admin')
{
    $user2 = $DMH->get_row(" SELECT * FROM `danhsachmuacode` WHERE `id` = '".$_GET['xoa']."'");
    if(!$user2)
    {
        echo msg_admin("error","Mã nguồn này không tồn tại", BASE_URL('Admin/ThemMaNguon'), 1000); die;
    }
    else
    {
        $dele = $DMH->remove("danhsachmuacode", " `id` = '".$_GET['xoa']."' ");
        if($dele)
        {
            echo msg_admin("success","Đã xóa mã nguồn thành công", BASE_URL('Admin/ThemMaNguon'), 1000);
        }
        else
        {
            echo msg_admin("error","Xóa thất bại. Lỗi hệ thống", BASE_URL('Admin/ThemMaNguon'), 1000);
        }
    }
}
?>
<h2>Đăng bán mã nguồn</h2>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-primary" data-collapsed="0">
            <div class="panel-heading">
                <div class="panel-title">
                    <b>Thêm bán mã nguồn</b>
                </div>
            </div>
            <div class="panel-body">
                <form role="form" class="form-horizontal form-groups-bordered">
            

                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="color: black">Tên Mã Nguồn</label>
                        <div class="col-sm-5">
                            <input type="text" id="name" class="form-control daterange"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="color: black">Mô Tả Mã Nguồn</label>
                        <div class="col-sm-5">
                            <input type="text" id="mota" class="form-control daterange"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="color: black">Chủ Đề Mã Nguồn</label>
                        
                        <div class="col-sm-5">
                            <select id="chude" class="selectboxit">
                                <?php foreach($DMH->get_list(" SELECT * FROM `danhmucmuacode` WHERE `status` = 'SHOW' ") as $row){ ?>
                                    <option value="<?=$row['id'];?>"><?=$row['title'];?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="color: black">Link Download</label>
                        <div class="col-sm-5">
                            <input type="text" id="link" class="form-control daterange"/>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="color: green">Số Tiền</label>
                        <div class="col-sm-5">
                            <input type="text" id="sotien" class="form-control daterange fnum" />
                            <i>Bạn sẽ chỉ nhận được <?=(100-$DMH->site('ptpartner'));?>% số tiền khi có khách mua</i>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="color: black">Link Ảnh Đại Diện</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control daterange" id="img_c" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="color: black">List Ảnh Mô Tả Code (Mỗi ảnh cách 1 dòng)</label>
                        <div class="col-sm-5">
                            <textarea type="text" class="form-control daterange" rows="4" placeholder="List Ảnh Mô Tả Code (Mỗi ảnh cách 1 dòng)" id="img"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="color: black">Cách cài đặt mã nguồn</label>
                        <div class="col-sm-5">
                            <textarea type="text" class="form-control daterange" rows="4" placeholder="Cách cài đặt mã nguồn" id="nd"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3 control-label" style="color: black">Số zalo (Liên hệ nếu cần)</label>
                        <div class="col-sm-5">
                            <input type="text" class="form-control daterange" value="<?=$getUser['zalo'];?>" disabled/>
                            <i>Cập nhật lại số zalo trong phần "<a href="/Profile" style="font-weight:bold;">Thông tin</a>"</i>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-5 control-label">
                            <button type="submit" id="btnAdd" class="btn btn-success"><i class="fa fa-share"></i> Xác nhận đăng bán</button>
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
                url: "<?=BASE_URL('controller/partner/Sellcode2.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    name: $("#name").val(),
                    mota: $("#mota").val(),
                    sotien: $("#sotien").val(),
                    img: $("#img").val(),
                    img_c: $("#img_c").val(),
                    link: $("#link").val(),
                    chude: $("#chude").val(),
                    nd: $("#nd").val()
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
                    $('#btnAdd').html('<i class="fa fa-share"></i> Xác nhận đăng bán').prop('disabled', false);
                },
                error: function() {
                    cuteToast({
                        type: "error",
                        message: 'Không thể xử lý',
                        timer: 5000
                    });
                    $('#btnAdd').html('<i class="fa fa-share"></i> Xác nhận đăng bán').prop('disabled', false);
                }

            });
        });
    </script>

</div>

<br />


<?php

require_once("../../pages/admin/Footer.php");
?>

