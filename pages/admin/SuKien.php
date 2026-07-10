<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'DANH MỤC BÁN CODE';
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
CheckAdmin();
?>
<?php
if(isset($_POST['Kichhoat']) && $getUser['level'] == 'admin')
{
    foreach ($_POST as $key => $value)
    {
        if($key == 'date') {
            if($value != '') {
                $value = time() + $value * 86400;
                $key = 'timeskoff';
            }
        }
        $UPDATTE = $TUANORI->update("options", array(
            'value' => $value
        ), " `key` = '$key' ");
    }
    if($UPDATTE)
    {
        echo msg_admin("success", 'Lưu thành công', '', 2000);
    }
    else
    {
        echo msg_admin("error", 'Đã xảy ra lỗi', '', 2000);
    }
}
?>
<h2>Quản lý sự kiện</h2>
<div class="row">
    <div class="col-md-12">

        <div class="panel panel-primary" data-collapsed="0">

            <div class="panel-heading">
                <div class="panel-title">
                    <b>Tạo sự kiện</b>
                </div>

            </div>

            <div class="panel-body">
            <form role="form" method="POST" class="validate">
                <div class="form-group">
                    <label class="control-label">Tên sự kiện</label>

                    <input type="text" class="form-control" name="namesukien" value="<?=$TUANORI->site('namesukien');?>" placeholder="Tên sự kiện" />
                </div>

                <div class="form-group">
                    <label class="control-label">Nội dung sự kiện</label>
                    <textarea class="form-control ckeditor" name="noidungsukien"><?=$TUANORI->site('noidungsukien');?></textarea>
                </div>

                <div class="form-group">
                    <label class="control-label" style="color: green">% giảm giá code</label>
                    <input type="text" class="form-control" value="<?=$TUANORI->site('ptgiamgia');?>" name="ptgiamgia" placeholder="% giảm giá code" />
                </div>
                <div class="form-group">
                    <label class="control-label" style="color: green">% giảm giá tạo websitie</label>
                    <input type="text" class="form-control" value="<?=$TUANORI->site('ptgiamgiaweb');?>" name="ptgiamgiaweb" placeholder="% giảm giá web" />
                </div>

                <div class="form-group">
                    <label class="control-label">Tặng % nạp tiền</label>
                    <input type="text" class="form-control" name="khuyenmai" value="<?=$TUANORI->site('khuyenmai');?>" placeholder="Tặng % nạp tiền" />
                </div>
                <div class="form-group">
                    <label class="control-label">Số ngày hoạt động</label>
                    <input type="text" class="form-control" name="date" placeholder="Số ngày hoạt động" />
                </div>
                <div class="form-group">
                    <label class="control-label">ON/OFF sự kiện</label>
                    <select name="sukien" class="selectboxit">
                        <option value="ON" <?=($TUANORI->site('sukien') == 'ON') ? 'selected' : ''?>>Hiển thị</option>
                        <option value="OFF" <?=($TUANORI->site('sukien') == 'OFF') ? 'selected' : ''?>>Ẩn đi</option>
                    </select>
                </div>
                <?php if($TUANORI->site('sukien') == 'ON') { ?>
                <div class="form-group">
                    <label class="control-label">Ngày kết thúc sự kiện</label>
                    <input type="text" class="form-control" value="<?=gettime2($TUANORI->site('timeskoff'));?>" placeholder="Số ngày hoạt động" />
                </div>
                <?php } ?>


                <div class="form-group">
                    <button type="submit" name="Kichhoat" class="btn btn-success">Lưu thông tin</button>
                </div>

            </form>
            </div>
        </div>
    </div>

</div>

<br />

<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
require_once("../../pages/admin/Footer.php");
?>





