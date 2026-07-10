<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Nạp tiền qua ví THESIEURE";
require_once("../../pages/client/Head.php");
require_once("../../pages/client/Header.php");
CheckLogin();
?>
<div class="content-wrapper transition-all duration-150 ltr:ml-0 rtl:mr-0 xl:ltr:ml-[248px] xl:rtl:mr-[248px]" id="content_wrapper">
    <div class="page-content">
        <div class="container-fluid transition-all duration-150" id="page_layout">
            <main id="content_layout">
                <!-- Page Content -->
                <div class="mb-3">
                </div>

                <section class="space-y-6">
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div class="card">
                            <div class="card-body flex flex-col p-6">
                                <header class="-mx-6 mb-5 flex items-center border-b border-slate-100 px-6 pb-5 dark:border-slate-700">
                                    <div class="flex-1">
                                        <div class="card-title text-slate-900 dark:text-white">Nạp Tiền Bằng Thesieure</div>
                                    </div>
                                </header>
                                <div class="card-text h-full space-y-4">
                                    <div class="flex justify-center mt-5 mb-3">
                                        <img src="/images/logo_thesieure.png" style="width: 200px">
                                    </div>
                                    <!-- <div class="border border-primary p-3 bg-white rounded-lg mb-3">
                                        <p style="text-align:center;"><span style="font-size:18px;"><strong><span style="font-family:Calibri, sans-serif;">
                                            <span style="font-family:Arial, sans-serif;"><span style="color:#000000;">Tài khoản:  <b style="color: red">hacknro</b></span>
                                            </span></span></strong></span>
                                        </p> 
                                        <p style="text-align:center;"><span style="font-size:18px;"><strong><span style="font-family:Calibri, sans-serif;">
                                            <span style="font-family:Arial, sans-serif;"><span style="color:#000000;">Nội dung: <b style="color: red">naptien 12</b></span>
                                            </span></span></strong></span>
                                        </p>
                                    </div> -->
                                    <div>
                                        <form>
                                            <div class="mb-3">
                                                <label for="sotien" class="form-label">Nhập số tiền nạp: (VNĐ)</label>
                                                <input type="text" class="form-control fnum" id="sotien" value="10.000" required>
                                                <i>Số tiền tối thiểu nạp là 10.000đ</i>
                                            </div>
                                            <div class="mb-3 text-center">
                                                <button class="btn btn-primary" type="submit" id="Napvi"><i class="fas fa-share"></i> Xác nhận</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <div class="card">
                            <header class="card-header noborder">
                                <h4 class="card-title">Lưu ý nạp tiền</h4>
                            </header>
                            <div class="card-body px-6 pb-6">

                                <div class="card border border-red-400 p-3">
                                    <div class="card-body">
                                        <p class="text-red-500 text-xl">1. Số tiền nạp tối thiểu là 10.000VNĐ</p>
                                        <p class="text-red-500 text-xl">2. Sau khi nạp, vui lòng chờ 1-2p hệ thống sẽ tự động cộng tiền</p>
                                        <p class="text-red-500 text-xl">3. Nạp tiền sẽ được cộng ngay lập tức.</p>
                                        <?php if($TUANORI->site('sukien') == 'ON' && $TUANORI->site('khuyenmai') > 0) { ?>
                                        <p class="text-xl">
                                            <i style="color: green" class="fa-solid fa-crown"></i> <b style="color: green">Hệ thống đang khuyến mãi thêm <b style="color: red"><?=$TUANORI->site('khuyenmai');?>%</b> giá trị nạp tiền qua ATM/ MOMO/ THESIEURE.</b>
                                        </p>
                                        <br/>
                                        <?php } ?>
                                        <p class="text-xl" style="font-weight: bold;"><i class="fa fa-arrow-circle-right"></i> Chờ quá lâu chưa được cộng, vui lòng liên hệ Admin.</p>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <script type="text/javascript">
                        $("#Napvi").on("click", function() {

                            $('#Napvi').html('Đang xử lý...').prop('disabled',
                                true);
                            $.ajax({
                                url: "<?=BASE_URL('controller/client/NapVi.php');?>",
                                method: "POST",
                                data: {
                                    sotien: $("#sotien").val()
                                },
                                success: function(response) {
                                    $("#thongbao").html(response);
                                    $('#Napvi').html(
                                            '<i class="fas fa-share"></i> Xác nhận')
                                        .prop('disabled', false);
                                }
                            });
                        });
                        </script>
                    <div id="app" class="card" data-v-app="">
                        <header class="card-header noborder">
                            <h4 class="card-title">Lịch sử nạp THESIEURE</h4>
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
                                                            <colgroup></colgroup>
                                                            <thead class="ant-table-thead">
                                                                <tr>
                                                                    <th class="ant-table-cell" colstart="0" colend="0">ID</th>
                                                                    <th class="ant-table-cell" colstart="1" colend="1">Mã hóa đơn</th>
                                                                    <th class="ant-table-cell" colstart="2" colend="2">Số tiền</th>
                                                                    <th class="ant-table-cell" colstart="2" colend="2">Thực nhận</th>
                                                                    <th class="ant-table-cell" colstart="3" colend="3">Trạng thái</th>
                                                                    <th class="ant-table-cell ant-table-column-has-sorters" tabindex="0" colstart="4" colend="4">
                                                                        <div class="ant-table-column-sorters"><span class="ant-table-column-title">Thời Gian</span><span class="ant-table-column-sorter ant-table-column-sorter-full"><span class="ant-table-column-sorter-inner"><span role="presentation" aria-label="caret-up" class="anticon anticon-caret-up ant-table-column-sorter-up"><svg focusable="false" class="" data-icon="caret-up" width="1em" height="1em" fill="currentColor" aria-hidden="true" viewBox="0 0 1024 1024"><path d="M858.9 689L530.5 308.2c-9.4-10.9-27.5-10.9-37 0L165.1 689c-12.2 14.2-1.2 35 18.5 35h656.8c19.7 0 30.7-20.8 18.5-35z"></path></svg></span><span role="presentation" aria-label="caret-down" class="anticon anticon-caret-down ant-table-column-sorter-down"><svg focusable="false" class="" data-icon="caret-down" width="1em" height="1em" fill="currentColor" aria-hidden="true" viewBox="0 0 1024 1024"><path d="M840.4 300H183.6c-19.7 0-30.7 20.8-18.5 35l328.4 380.8c9.4 10.9 27.5 10.9 37 0L858.9 335c12.2-14.2 1.2-35-18.5-35z"></path></svg></span></span>
                                                                            </span>
                                                                        </div>
                                                                    </th>
                                                                    <th class="ant-table-cell" colstart="5" colend="5">Thao tác</th>

                                                                </tr>
                                                            </thead>
                                                            <tbody class="ant-table-tbody">
                                                                <?php $i = 0; foreach($TUANORI->get_list(" SELECT * FROM `hoadon_vi` WHERE `username` = '".$getUser['username']."' ORDER BY id DESC LIMIT 30") as $row){ ?>
                                                                    <tr class="ant-table-row ant-table-row-level-0">
                                                                        <td class="ant-table-cell"><?=++$i;?></td>
                                                                        <td class="ant-table-cell"><b><?=$row['magd'];?></b></td>
                                                                        <td class="ant-table-cell"><?=number_format($row['sotien']);?>₫</td>
                                                                        <td class="ant-table-cell text-green-500">+ <?=number_format($row['thucnhan']);?>₫</td>

                                                                        <td class="ant-table-cell"><?=stnapvi($row['status']);?></td>
                                                                        <td class="ant-table-cell"><?=$row['thoigian'];?></td>
                                                                        <td class="ant-table-cell">
                                                                            <a href="/Nap/Vi/<?=$row['magd'];?>">
                                                                                <button class="btn btn-outline-primary btn-sm">
                                                                                    <div class="flex justify-between font-bold">
                                                                                        <span>Quản Lý</span>
                                                                                    </div>
                                                                                </button>
                                                                            </a>
                                                                        </td>

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