<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Quản Lý Tạo Website";
require_once("../../pages/client/Head.php");
require_once("../../pages/client/Header.php");
CheckLogin();
$rowV2 = $TUANORI->get_row(" SELECT * FROM `domainclf` WHERE `accountid` IS NOT NULL AND `status` = 'ON' ");
?>
<?php
if(isset($_GET['id'])) {
    $id = check_string($_GET['id']);
    $row = $TUANORI->get_row(" SELECT * FROM `lichsutaoweb` WHERE `id` = '".check_string($_GET['id'])."' AND `username` = '".$getUser['username']."' ");
    if(!$row) {
        msg_error("Dữ liệu tạo trang web này không hợp lệ", BASE_URL('History-tao-web'), 500);
    }
    if(($row['ngayhethan'] +  24 * 60 * 60 * 7) < time() && $row['buoc'] == 6) {
        msg_error("Website này đã hết hạn. Không thể truy cập", BASE_URL('History-tao-web'), 5000);
    }
    $gh = $row['moneygiahan']; // tiền gia hạn mỗi 3 tháng
} else {
    msg_error("Liên kết của bạn thiếu Dữ Liệu", BASE_URL('History-tao-web'), 0);
}
?>
<div class="content-wrapper transition-all duration-150 ltr:ml-0 rtl:mr-0 xl:ltr:ml-[248px] xl:rtl:mr-[248px] margin-0" id="content_wrapper">
    <div class="page-content">
        <div class="container-fluid transition-all duration-150" id="page_layout">
            <main id="content_layout">
                <!-- Page Content -->
                <div class="mb-3">
                </div>

                <section class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="card">
                            <header class=" card-header noborder">
                                    <?php if(in_array($row['buoc'], [4,5])) { ?>
                                        <h6>Gia hạn website</h6>
                                    <?php } else {  ?>
                                        <h6>Tiến hành khởi tạo</h6>
                                    <?php } ?>
                            </header>
                            
                            <?php // xử lý bước 1
                            if(in_array($row['buoc'], [1,4,5])) { ?>
                                <div class="card-body px-6 pb-6">
                                    <form class="space-y-6">
                                        <div class="grid grid-cols-6 gap-6">
                                            <div class="input-area">
                                                <label for="thang" class="form-label">Thời gian hoạt động</label>
                                                <select class="form-control" id="thang" name="thang" required>
                                                    <option value="">Chọn thời gian hoạt động</option>
                                                    <option value="3">3 tháng - <?=sotienmua($gh);?></option>
                                                    <option value="6">6 tháng - <?=sotienmua($gh*2);?></option>
                                                    <option value="12">1 năm - <?=sotienmua($gh*4);?></option>
                                                    <option value="24">2 năm - <?=sotienmua($gh*8);?></option>
                                                    <option value="36">3 năm - <?=sotienmua($gh*12);?></option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="input-area">
                                            <button type="button" id = "XacNhan" class="btn btn-sm btn-primary w-full">Xác nhận thông tin</button>
                                        </div>
                                    </form>
                                </div>
                                <script type="text/javascript">
                                    $("#XacNhan").on("click", function() {

                                        $('#XacNhan').html('Đang xử lý...').prop('disabled',
                                            true);
                                        $.ajax({
                                            url: "<?=BASE_URL('controller/client/Muahang.php');?>",
                                            method: "POST",
                                            data: {
                                                type: 'XulyTaoWeb',
                                                id: '<?=$row['id'];?>',
                                                thang: $("#thang").val()
                                            },
                                            success: function(response) {
                                                $("#thongbao").html(response);
                                                $('#XacNhan').html(
                                                        'Xác nhận thông tin')
                                                    .prop('disabled', false);
                                            }
                                        });
                                    });
                                </script>
                            <?php }  // xử lý bước 2
                            else if($row['buoc'] == 2) { ?>

                                <div class="card-body px-6 pb-6">
                                    <form class="space-y-6">
                                        <div class="grid grid-cols-6 gap-6">
                                            <div class="input-area">
                                                <label for="tenmien" class="form-label">Tên miền</label>
                                                <input type="text" class="form-control !pr-12" id="tenmien" value="<?=$row['tenmien'];?>">
                                                <i>Bạn có thể đổi lại tên miền khác hoặc giữ nguyên</i>
                                            </div>
                                            <div class="input-area">
                                                <label for="password" class="form-label">Nameserver Mới 1:</label>
                                                <div class="relative">
                                                <b class="form-control !pr-12" id="ns1"><?=$rowV2['ns1'];?></b>
                                                <button class="absolute right-0 top-1/2 -translate-y-1/2 w-9 h-full border-l border-l-slate-200 dark:border-l-slate-700 flex items-center justify-center copy" data-clipboard-target="#ns1"  type="button">
                                                    <i class="far fa-copy"></i>
                                                </button>
                                                </div>
                                            </div>
                                            <div class="input-area">
                                                <label for="password" class="form-label">Nameserver Mới 2:</label>
                                                <div class="relative">
                                                <b class="form-control !pr-12" id="ns2"><?=$rowV2['ns2'];?></b>
                                                <button class="copy absolute right-0 top-1/2 -translate-y-1/2 w-9 h-full border-l border-l-slate-200 dark:border-l-slate-700 flex items-center justify-center"  data-clipboard-target="#ns2" type="button">
                                                    <i class="far fa-copy"></i>
                                                </button>
                                                </div>
                                            </div>
                                            <div class="card border border-red-400 p-3">
                                                <div class="card-body">
                                                    <i><b>1</b>. Quý khách vui lòng trỏ <span style="color: red">nameserver hiện tại</span> của tên miền về <span style="color: red">nameserver mới</span> của chúng tôi</i> <br/>
                                                    <i><b>2</b>. Sau khi đã trỏ hoàn tất, vui lòng chờ đợi để hệ thống cập nhật và sau đó bấm vào <span style="color: red">"Xác nhận thông tin"</span></i><br/>
                                                    <i><span style="color: red"><b>Lưu ý:</b></span> Nếu bạn đăng ký tên miền <a href="/Mua-mien" target="_blank" style="color: green; font-weight: bold;">tại đây</a>. Hệ thống sẽ tự động hoàn thành bước này cho bạn.</span></i>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="input-area">
                                            <button type="button" id = "XacNhan" class="btn btn-sm btn-primary w-full">Xác nhận thông tin</button>
                                        </div>
                                    </form>
                                </div>
                                <script type="text/javascript">
                                    $("#XacNhan").on("click", function() {

                                        $('#XacNhan').html('Đang xử lý...').prop('disabled',
                                            true);
                                        $.ajax({
                                            url: "<?=BASE_URL('controller/client/Muahang.php');?>",
                                            method: "POST",
                                            data: {
                                                type: 'XulyTaoWeb',
                                                id: '<?=$row['id'];?>',
                                                tenmien: $("#tenmien").val()
                                            },
                                            success: function(response) {
                                                $("#thongbao").html(response);
                                                $('#XacNhan').html(
                                                        'Xác nhận thông tin')
                                                    .prop('disabled', false);
                                            }
                                        });
                                    });
                                </script>
                            <?php } else { ?>
                                <div class="card-body px-6 pb-6">
                                    <div class="col-span-2">
                                        <div class="border border-red-500 rounded-lg p-1 font-bold text-green-500"><center>Bạn đã hoàn thành xong các bước</center></span></div>
                                    </div>
                                </div>
                                
                            <?php } ?>
                            <header class=" card-header noborder">
                                <h6>Thông tin chung <a href="//<?=$row['tenmien'];?>"><span class="text-danger-500"><?=$row['tenmien'];?></span></a></h6>
                            </header>
                            <div class="card-body px-6 pb-6">
                                <form class="space-y-3">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="input-area">
                                            <label for="username" class="form-label">Số tháng mua (Ban đầu)</label>

                                            <input type="text" class="form-control !pr-12" value="<?=inkq($row['thangmua'], 'Chưa chọn');?>" disabled="">
                                        </div>
                                        <div class="input-area">
                                            <label for="username" class="form-label">Tổng Thanh Toán (Ban đầu)</label>
                                            <input type="text" class="form-control !pr-12" value="<?=number_format($row['tongtien']);?>đ" disabled="">
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        Trạng thái: <?=statustaoweb($row['buoc']);?>
                                    </div>
                                </form>
                                
                            </div>
                        </div>
                        <div class="card">
                            <header class=" card-header noborder">
                                <h4 class="card-title">Thông Tin Khởi Tạo</h4>
                            </header>
                            <div class="card-body px-6 pb-6">
                                <form class="space-y-3">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="input-area">
                                            <label for="username" class="form-label">Tài Khoản</label>
                                            <div class="relative">
                                                <input type="text" class="form-control !pr-12" value="<?=$row['taikhoan'];?>" disabled="">
                                            </div>
                                        </div>
                                        <div class="input-area">
                                            <label for="password" class="form-label">Mật Khẩu</label>
                                            <div class="relative">
                                                <input type="text" class="form-control !pr-12" value="<?=$row['matkhau'];?>" disabled="">
                                            </div>
                                        </div>
                                        <div class="input-area">
                                            <label for="username" class="form-label">Ngày khởi tạo</label>
                                            <div class="relative">
                                                <input type="text" class="form-control !pr-12" value="<?=gettime2($row['ngaytao']);?>" disabled="">
                                            </div>
                                        </div>
                                        
                                        
                                        <div class="input-area">
                                            <label for="username" class="form-label">Ngày hết hạn</label>
                                            <div class="relative">
                                            <?php if($row['ngayhethan']) {
                                                    $timecc =  gettime2($row['ngayhethan']);
                                                } else {
                                                    $timecc =  'Chưa bắt đầu';
                                                } ?>
                                                <input type="text" class="form-control !pr-12" value="<?=$timecc;?>" disabled="">
                                            </div>
                                        </div>
                                    </div>
                                    <?php if(isset($row['linklogin']) && isset($row['tkhs']) && isset($row['mkhs'])) { ?>
                                     <div class="input-area">
                                        <label class="form-label">Link login hosting</label>
                                        <input type="text" class="form-control" value="<?=$row['linklogin'];?>" disabled>
                                    </div>
                                    <div class="input-area">
                                        <label class="form-label">Tài khoản hosting</label>
                                        <input type="text" class="form-control" value="<?=$row['tkhs'];?>" disabled>
                                    </div>
                                     <div class="input-area">
                                        <label  class="form-label">Mật khẩu hosting</label>
                                        <input type="text" class="form-control" value="<?=$row['mkhs'];?>" disabled>
                                    </div>
                                    <div class="input-area">
                                        <label for="order_note" class="form-label">Ghi Chú từ ADMIN</label>
                                        <textarea name="order_note" class="form-control !pr-12" rows="5" disabled=""><?=$row['note'];?></textarea>
                                    </div>
                                    <?php } ?>
                                </form>
                                <br/>
                                <div class="card border border-red-400 p-3">
                                    <div class="card-body">
                                        <i><span style="color: red"><b>Trình tự website</b></i> <br/>
                                        <?php $i =0; $data = ['Chọn thời gian hoạt động', 'Trỏ tên miền về hệ thống', 'Chờ ADMIN xử lý đơn hàng', 'Wesbite đang hoạt động', 'Wesbite sắp hoặc đang hết hạn', 'Wesbite chấm dứt hoạt động']; foreach($data as $kq)  { ?>
                                            <i <?php echo ($row['buoc'] == $i+1) ? 'class="text-green-500"': '';?> ><b><?=++$i;?></b>. <?=$kq;?>.</i><?php echo ($row['buoc'] == $i) ? '<i class="fas fa-arrow-left"></i>': '';?>  <br/>
                                        <?php } ?>
                                        <i>Note: Quý khách có thể gia hạn bất cứ lúc nào (Ngoại trừ đã <span style="color: red">hết hạn</span> quá 4 ngày)</i>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <header class="card-header noborder">
                            <h4 class="card-title">Lịch sử gia hạn website</h4>
                        </header>
                        <div class="card-body px-6 pb-6">
                            <div class="overflow-auto">
                               
                                <div class="ant-table-wrapper font-medium whitespace-nowrap css-eq3tly">
                                    <div class="ant-spin-nested-loading css-eq3tly">
                                        
                                        <div class="ant-spin-container">
                                            
                                            <div class="ant-table ant-table-small ant-table-empty">
                                                
                                                <div class="ant-table-container">
                                                    <div class="ant-table-content">
                                                        <table style="table-layout: auto;">
                                                            <colgroup>
                                                                <col>
                                                                    <col style="width: 13%;">
                                                            </colgroup>
                                                            <thead class="ant-table-thead">
                                                                <tr>
                                                                    <th class="ant-table-cell ant-table-column-has-sorters" tabindex="0" colstart="0" colend="0">
                                                                        <div class="ant-table-column-sorters"><span class="ant-table-column-title">ID</span><span class="ant-table-column-sorter ant-table-column-sorter-full"><span class="ant-table-column-sorter-inner"><span role="presentation" aria-label="caret-up" class="anticon anticon-caret-up ant-table-column-sorter-up"><svg focusable="false" class="" data-icon="caret-up" width="1em" height="1em" fill="currentColor" aria-hidden="true" viewBox="0 0 1024 1024"><path d="M858.9 689L530.5 308.2c-9.4-10.9-27.5-10.9-37 0L165.1 689c-12.2 14.2-1.2 35 18.5 35h656.8c19.7 0 30.7-20.8 18.5-35z"></path></svg></span><span role="presentation" aria-label="caret-down" class="anticon anticon-caret-down ant-table-column-sorter-down"><svg focusable="false" class="" data-icon="caret-down" width="1em" height="1em" fill="currentColor" aria-hidden="true" viewBox="0 0 1024 1024"><path d="M840.4 300H183.6c-19.7 0-30.7 20.8-18.5 35l328.4 380.8c9.4 10.9 27.5 10.9 37 0L858.9 335c12.2-14.2 1.2-35-18.5-35z"></path></svg></span></span>
                                                                            </span>
                                                                        </div>
                                                                        
                                                                    </th>
                                                                    <th class="ant-table-cell" colstart="2" colend="2">Tên miền</th>
                                                                    <th class="ant-table-cell" colstart="3" colend="3">Tổng tiền</th>
                                                                    <th class="ant-table-cell" colstart="4" colend="4">Thời gian </th>
                                                                    <th class="ant-table-cell" colstart="5" colend="5">Trạng thái</th>
                                                                
                                                                </tr>
                                                            </thead>
                                                            <tbody class="ant-table-tbody">
                                                                <?php $i = 0; foreach($TUANORI->get_list(" SELECT * FROM `lichsugiahan` WHERE `id_web` = '$id' ORDER BY id DESC LIMIT 30") as $row){ ?>
                                                                    <tr class="ant-table-row ant-table-row-level-0">
                                                                        <td class="ant-table-cell"><?=++$i;?></td>
                                                                        <td class="ant-table-cell"><a href="//<?=$row['tenmien'];?>" target="_blank" style="color: green"><?=$row['tenmien'];?></a></td>
                                                                        <td class="ant-table-cell"><?=number_format($row['tongtien']);?> ₫</td>
                                                                        <td class="ant-table-cell"><b style="color: red"> + <?=$row['thoigian'];?> tháng</b></td>
                                                                        <td class="ant-table-cell"><?=sttgiahan($row['status']);?></td>
                                                                    </tr>
                                                                <?php } if($i == 0) { ?>
                                                                    <tr class="ant-table-placeholder">
                                                                        <td colspan="11" class="ant-table-cell">
                                                                    
                                                                            <?=nodata();?>
                                                                    
                                                                        </td>
                                                                    </tr> 
                                                                <?php } ?>

                                                                    
                                                                
                                                            </tbody>
                                                            
                                                        </table>
                                                    </div>
                                                </div>
                                                
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>  
                    </div>

                    <div class="text-center">
                        <a href="/History-tao-web" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Quay Lại</a>
                    </div>
                </section>
            </main>
        </div>
    </div>
</div>
</div>
<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
require_once("../../pages/client/Footer.php");
?>