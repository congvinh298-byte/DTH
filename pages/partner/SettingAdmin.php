<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$tieude = 'CÀI ĐẶT WEBSITE';
require_once("../../pages/admin/Head.php");
require_once("../../pages/admin/Header.php");
CheckVeri();
?>
<?php
if(isset($_POST['SaveALL']) && $getUser['level'] == 'admin') {
    foreach ($_POST as $key => $value)
    {
        $UPDATTE = $TUANORI->update("options", array(
            'value' => $value
        ), " `key` = '$key' ");
    }
    if($UPDATTE) {
        echo msg_admin("success",'Lưu thành công', '', 1000);
    }
    else
    {
        echo msg_admin("error","Đã xảy ra lỗi", '', 500);
    }
}
?>
<h2>CÀI ĐẶT CHUNG</h2>
<div class="row">
    <div class="col-md-12">

        <div class="panel panel-primary" data-collapsed="0">

            <div class="panel-heading">
                <div class="panel-title">
                    <b>Cài đặt trang web</b>
                </div>

            </div>

            <div class="panel-body">
                <form role="form" method="POST" class="validate">
                    <div class="form-group">
                        <label class="control-label" style="color: black">Tiêu đề website</label>

                        <input type="text" class="form-control" name="title" value="<?=$TUANORI->site('title');?>" placeholder="Tiêu đề website" />
                    </div>
                    <div class="form-group">
                        <label class="control-label" style="color: green">Mô tả website</label>
                        <input type="text" class="form-control" value="<?=$TUANORI->site('mota');?>" name="mota" placeholder="Mô tả website" />
                    </div>

                    <div class="form-group">
                        <label class="control-label" style="color: green">Từ khóa mô tả website (SEO)</label>
                        <input type="text" class="form-control" value="<?=$TUANORI->site('tukhoa');?>" name="tukhoa" placeholder="Từ khóa mô tả website (SEO)" />
                    </div>

                    <div class="form-group">
                        <label class="control-label" style="color: black">Logo Website</label>
                        <input type="text" class="form-control" name="logo" value="<?=$TUANORI->site('logo');?>" placeholder="Logo Website" />
                    </div>
                    <div class="form-group">
                        <label class="control-label" style="color: black">Ảnh Bìa Websie</label>
                        <input type="text" class="form-control" name="/anhbia" placeholder="Ảnh Bìa Websie" value="<?=$TUANORI->site('anhbia');?>" />
                    </div>
                    <div class="form-group">
                        <label class="control-label" style="color: black">API KEY DRIVE</label>
                        <input type="text" class="form-control" name="keyDrive" value="<?=$TUANORI->site('keyDrive');?>" placeholder="/API KEY DRIVE" />
                    </div>
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label class="control-label" style="color: black">Hình Ảnh Banner 1</label>
                            <input class="form-control" type="link" name="banner1" value="<?=$TUANORI->site('banner1');?>" placeholder="Hình Ảnh Banner 1">
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="control-label" style="color: black">Hình Ảnh Banner 2</label>
                            <input class="form-control" type="link" name="banner2" value="<?=$TUANORI->site('banner2');?>" placeholder="Hình Ảnh Banner 2">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label class="control-label" style="color: black">Hình Ảnh Danh Mục 1</label>
                            <input class="form-control" type="link" name="danhmuc1" value="<?=$TUANORI->site('danhmuc1');?>" placeholder="Hình Ảnh Danh Mục 1">
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="control-label" style="color: black">Hình Ảnh Danh Mục 2</label>
                            <input class="form-control" type="link" name="danhmuc2" value="<?=$TUANORI->site('danhmuc2');?>" placeholder="Hình Ảnh Danh Mục 2">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label class="control-label" style="color: black">Hình Ảnh Danh Mục 3</label>
                            <input class="form-control" type="link" name="danhmuc3" value="<?=$TUANORI->site('danhmuc3');?>" placeholder="Hình Ảnh Danh Mục 3">
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="control-label" style="color: black">Hình Ảnh Danh Mục 4</label>
                            <input class="form-control" type="link" name="danhmuc4" value="<?=$TUANORI->site('danhmuc4');?>" placeholder="Hình Ảnh Danh Mục 4">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6 form-group">
                            <label class="control-label" style="color: black">Hình Ảnh Danh Mục 5</label>
                            <input class="form-control" type="link" name="danhmuc5" value="<?=$TUANORI->site('danhmuc5');?>" placeholder="Hình Ảnh Danh Mục 5">
                        </div>
                        <div class="col-sm-6 form-group">
                            <label class="control-label" style="color: black">Hình Ảnh Danh Mục 6</label>
                            <input class="form-control" type="link" name="danhmuc6" value="<?=$TUANORI->site('danhmuc6');?>" placeholder="Hình Ảnh Danh Mục 6">
                        </div>
                    </div>
                    <div class="row">
                    <div class="col-sm-6 form-group">
                        <label class="control-label" style="color: black">Facebook Admin</label>
                        <input class="form-control" type="text" name="fbadmin" value="<?=$TUANORI->site('fbadmin');?>" placeholder="Facebook Admin">
                    </div>
                    <div class="col-sm-6 form-group">
                        <label class="control-label" style="color: black">Zalo Admin</label>
                        <input class="form-control" type="text" name="zaloadmin" value="<?=$TUANORI->site('zaloadmin');?>" placeholder="Zalo Admin">
                    </div>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-sm-4 form-group">
                        <label class="control-label" style="color: black">PARTNER ID THESIEURE</label>
                        <input class="form-control" type="text" name="partner_id" value="<?=$TUANORI->site('partner_id');?>" placeholder="PARTNER ID THESIEURE">
                    </div>
                    <div class="col-sm-4 form-group">
                        <label class="control-label" style="color: black">PARTNER KEY THESIEURE</label>
                        <input class="form-control" type="text" name="partner_key" value="<?=$TUANORI->site('partner_key');?>" placeholder="PARTNER KEY THESIEURE">
                    </div>
                    <div class="col-sm-4 form-group">
                        <label class="control-label" style="color: black">CHIẾT KHẤU</label>
                        <input class="form-control" type="number" name="ckcard" value="<?=$TUANORI->site('ckcard');?>" placeholder="CHIẾT KHẤU">
                    </div>
                </div>
                <div class="col-sm-4 form-group">
                    <i style="color: black">Link callback của bạn là: <b>https://<?=$_SERVER['SERVER_NAME'];?>/assets/ajaxs/Callback.php</b></i>
                </div>
                <hr/>
                <div class="row">
                    <div class="col-sm-6 form-group">
                        <label class="control-label" style="color: black">Số điện thoại MOMO</label>
                        <input class="form-control" type="text" name="sdt_momo" value="<?=$TUANORI->site('sdt_momo');?>" placeholder="Số điện thoại MOMO">
                    </div>
                    <div class="col-sm-6 form-group">
                        <label class="control-label" style="color: black">TOKEN MOMO (APIGIARE.COM)</label>
                        <input class="form-control" type="text" name="token_momo" value="<?=$TUANORI->site('token_momo');?>" placeholder="TOKEN MOMO (APIGIARE.COM)">
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-3 form-group">
                        <label class="control-label" style="color: black">TOKEN BANK</label>
                        <input class="form-control" type="text" name="token_bank" value="<?=$TUANORI->site('token_bank');?>" placeholder="TOKEN BANK">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label class="control-label" style="color: black">STK BANK</label>
                        <input class="form-control" type="text" name="stk_bank" value="<?=$TUANORI->site('stk_bank');?>" placeholder="STK BANK">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label class="control-label" style="color: black">TÊN ĐĂNG NHẬP BANK</label>
                        <input class="form-control" type="text" name="user_bank" value="<?=$TUANORI->site('user_bank');?>" placeholder="TÊN ĐĂNG NHẬP BANK">
                    </div>
                    <div class="col-sm-3 form-group">
                        <label class="control-label" style="color: black">MẬT KHẨU BANK</label>
                        <input class="form-control" type="password" name="mk_bank" value="<?=$TUANORI->site('mk_bank');?>" placeholder="MẬT KHẨU BANK">
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-4 form-group">
                        <label class="control-label" style="color: black">LOẠI NGÂN HÀNG (<b><?=$TUANORI->site('loaibank');?></b>)</label>
                        <select name="loaibank" class="form-control">
                            <option value="<?=$TUANORI->site('loaibank');?>"><?=$TUANORI->site('loaibank');?></option>
                            <option value="VIETCOMBANK">VIETCOMBANK</option>
                            <option value="MBBANK">MBBANK</option>
                        </select>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label class="control-label" style="color: black">TÊN CHỦ THẺ (MOMO VÀ <?=$TUANORI->site('loaibank');?>)</label>
                        <input class="form-control" type="text" name="chuthe" value="<?=$TUANORI->site('chuthe');?>" placeholder="TÊN CHỦ THẺ">
                    </div>
                    <div class="col-sm-4 form-group">
                        <label class="control-label" style="color: black">NỘI DUNG CHUYỂN TIỀN</label>
                        <input class="form-control" type="text" name="nd_bank" value="<?=$TUANORI->site('nd_bank');?>" placeholder="NỘI DUNG CHUYỂN TIỀN">
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-4 form-group">
                        <label class="control-label" style="color: black">STATUS NẠP AUTO <b>THESIEURE</b></label>
                        <select name="status_tsr" class="form-control">
                            <option value="<?=$TUANORI->site('status_tsr');?>"><?=$TUANORI->site('status_tsr');?></option>
                            <option value="ON">ON</option>
                            <option value="OFF">OFF</option>
                        </select>
                    </div>
                    <div class="col-sm-4 form-group">
                        <label class="control-label" style="color: black">TÀI KHOẢN TSR</label>
                        <input class="form-control" type="text" name="tk_tsr" value="<?=$TUANORI->site('tk_tsr');?>" placeholder="TÀI KHOẢN TSR">
                    </div>
                    <div class="col-sm-4 form-group">
                        <label class="control-label" style="color: black">MẬT KHẨU TSR</label>
                        <input class="form-control" type="password" name="mk_tsr" value="<?=$TUANORI->site('mk_tsr');?>" placeholder="MẬT KHẨU TSR">
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-6 form-group">
                        <label class="control-label" style="color: black">EMAIL</label>
                        <input class="form-control" type="email" name="email" value="<?=$TUANORI->site('email');?>" placeholder="EMAIL">
                    </div>
                    <div class="col-sm-6 form-group">
                        <label class="control-label" style="color: black">PASS ỨNG DỤNG EMAIL</label>
                        <input class="form-control" type="password" name="pass_email" value="<?=$TUANORI->site('pass_email');?>" placeholder="PASS ỨNG DỤNG EMAIL">
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-6 form-group">
                        <label class="control-label" style="color: black">APP ID FB</label>
                        <input class="form-control" type="text" name="/app_id" value="<?=$TUANORI->site('app_id');?>" placeholder="/APP ID FB">
                        <i>Để trống 1 trong 2. Chức năng Login bằng FaceBook sẽ bị tắt</i>
                    </div>
                    <div class="col-sm-6 form-group">
                        <label class="control-label" style="color: black">APP KEY FB</label>
                        <input class="form-control" type="text" name="/app_key" value="<?=$TUANORI->site('app_key');?>" placeholder="/APP KEY FB">
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-6 form-group">
                        <label class="control-label" style="color: black">CLIENT ID</label>
                        <input class="form-control" type="text" name="client_id" value="<?=$TUANORI->site('client_id');?>" placeholder="CLIENT ID">
                        <i>Để trống 1 trong 2. Chức năng Login bằng Google sẽ bị tắt</i>
                    </div>
                    <div class="col-sm-6 form-group">
                        <label class="control-label" style="color: black">CLIENT KEY</label>
                        <input class="form-control" type="text" name="client_key" value="<?=$TUANORI->site('client_key');?>" placeholder="CLIENT KEY">
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12 form-group">
                        <label class="control-label" style="color: black">COOKIE THESIEURE</label>
                        <textarea class="form-control" type="text" name="cookie_thesieure" rows="4" ><?=$TUANORI->site('cookie_thesieure');?></textarea>
                        <i>Để trống 1 trong 2. Chức năng Login bằng Google sẽ bị tắt</i>
                    </div>
                </div>
                <div class="form-group">
                    <label>Thông báo website</label>
                    <!-- <textarea class="form-control ckeditor" name="thongbao"><?=$TUANORI->site('thongbao');?></textarea> -->
                    <textarea class="form-control" name="thongbao" data-uk-markdownarea><?=$TUANORI->site('thongbao');?></textarea>
                </div>
                <div class="form-group">
                    <button type="submit" name="SaveALL" class="btn btn-success">Lưu thông tin</button>
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


