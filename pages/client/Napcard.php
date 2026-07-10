<?php

define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Nạp tiền qua thẻ cào";
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
                                        <div class="card-title text-slate-900 dark:text-white">Nạp Tiền Bằng Thẻ Cào</div>
                                    </div>
                                </header>
                                <div class="card-text h-full space-y-4">
                                    <div>
                                        <form method="POST" class="space-y-3">
                                            <div class="input-area">
                                                <label for="old_password" class="form-label">Loại Thẻ</label>
                                                <select class="form-control" id="loaithe" name="telco" required>
                                                    <option value="">Chọn loại thẻ</option>
                                                    <option value="VIETTEL">Viettel - Phí <?=$ck;?>%</option>
                                                    <option value="VINAPHONE">Vinaphone - Phí <?=$ck;?>%</option>
                                                    <option value="MOBIFONE">Mobifone - Phí <?=$ck;?>%</option>
                                                    <option value="VNMOBI">Vietnammobi - Phí <?=$ck;?>%</option>
                                                    <option value="ZING">Zing Card - Phí <?=$ck;?>%</option>
                                                </select>
                                            </div>
                                            <div class="input-area">
                                                <label for="new_password" class="form-label">Mệnh Giá</label>
                                                <select class="form-control" id="menhgia" onchange="totalPrice()" name="menhgia" required>
                                                    <option value="">Chọn mệnh giá</option>
                                                    <option value="10000">10.000 đ - Nhận <?=number_format(10000 - 10000*$ck/100);?>đ</option>
                                                    <option value="20000">20.000 đ - Nhận <?=number_format(30000 - 30000*$ck/100);?>đ</option>
                                                    <option value="30000">30.000 đ - Nhận <?=number_format(100000 - 100000*$ck/100);?>đ</option>
                                                    <option value="50000">50.000 đ - Nhận <?=number_format(50000 - 50000*$ck/100);?>đ</option>
                                                    <option value="100000">100.000 đ - Nhận <?=number_format(100000 - 100000*$ck/100);?>đ</option>
                                                    <option value="200000">200.000 đ - Nhận <?=number_format(200000 - 200000*$ck/100);?>đ</option>
                                                    <option value="300000">300.000 đ - Nhận <?=number_format(300000 - 300000*$ck/100);?>đ</option>
                                                    <option value="500000">500.000 đ - Nhận <?=number_format(500000 - 500000*$ck/100);?>đ</option>
                                                    <option value="1000000">1.000.000 đ - Nhận <?=number_format(1000000 - 1000000*$ck/100);?>đ</option>
                                                </select>
                                            </div>
                                            <div class="input-area">
                                                <label for="mathe" class="form-label">Mã Thẻ</label>
                                                <input type="text" class="form-control  py-2" id="mathe" placeholder="Mã số thẻ" required="">
                                            </div>
                                            <div class="input-area">
                                                <label for="serial" class="form-label">Số Serial</label>
                                                <input type="text" class="form-control  py-2" id="serial" placeholder="Mã số serial" required="">
                                            </div>
                                            <div class="input-area">
                                                <button type="button" id= "Napthe" class="btn btn-sm btn-primary w-full">Gửi thẻ</button>
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
                                        <p class="text-red-500 text-xl">1. Sai mệnh giá mất thẻ, khiếu nại vô ích </p>
                                        <p class="text-red-500 text-xl">2. Tự động cộng tiền nếu thẻ đúng</p>
                                        <p class="text-xl" style="font-weight: bold;">3. Chờ quá lâu chưa được cộng, vui lòng liên hệ Admin.</p>

                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <div id="app" class="card" data-v-app="">
                        <header class="card-header noborder">
                            <h4 class="card-title">Lịch sử nạp thẻ</h4>
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
                                                                    <th class="ant-table-cell ant-table-column-has-sorters" tabindex="0" colstart="0" colend="0">
                                                                        
                                                                        <div class="ant-table-column-sorters"><span class="ant-table-column-title">ID</span><span class="ant-table-column-sorter ant-table-column-sorter-full"><span class="ant-table-column-sorter-inner"><span role="presentation" aria-label="caret-up" class="anticon anticon-caret-up ant-table-column-sorter-up"><svg focusable="false" class="" data-icon="caret-up" width="1em" height="1em" fill="currentColor" aria-hidden="true" viewBox="0 0 1024 1024"><path d="M858.9 689L530.5 308.2c-9.4-10.9-27.5-10.9-37 0L165.1 689c-12.2 14.2-1.2 35 18.5 35h656.8c19.7 0 30.7-20.8 18.5-35z"></path></svg></span><span role="presentation" aria-label="caret-down" class="anticon anticon-caret-down ant-table-column-sorter-down"><svg focusable="false" class="" data-icon="caret-down" width="1em" height="1em" fill="currentColor" aria-hidden="true" viewBox="0 0 1024 1024"><path d="M840.4 300H183.6c-19.7 0-30.7 20.8-18.5 35l328.4 380.8c9.4 10.9 27.5 10.9 37 0L858.9 335c12.2-14.2 1.2-35-18.5-35z"></path></svg></span></span>
                                                                            </span>
                                                                        </div>
                                                                    </th>
                                                                    <th class="ant-table-cell" colstart="2" colend="2">Số Serial</th>
                                                                    <th class="ant-table-cell" colstart="3" colend="3">Mã Thẻ</th>
                                                                    <th class="ant-table-cell" colstart="4" colend="4">Thực Nhận </th>
                                                                    <th class="ant-table-cell" colstart="5" colend="5">Mệnh Giá</th>
                                                                    <th class="ant-table-cell" colstart="7" colend="7">Trạng Thái</th>
                                                                    <th class="ant-table-cell ant-table-column-has-sorters" tabindex="0" colstart="9" colend="9">
                                                                        <div class="ant-table-column-sorters"><span class="ant-table-column-title">Thời Gian Nạp</span>
                                                                        </div>
                                                                    </th>
                                                                    <th class="ant-table-cell ant-table-column-has-sorters" tabindex="0" colstart="10" colend="10">
                                                                        <div class="ant-table-column-sorters"><span class="ant-table-column-title">Cập Nhật</span></div>
                                                                    </th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="ant-table-tbody">
                                                                <?php $i = 0; foreach($DMH->get_list(" SELECT * FROM `napcard` WHERE `username` = '".$getUser['username']."' ORDER BY id DESC LIMIT 30") as $row){ ?>
                                                                    <tr class="ant-table-row ant-table-row-level-0">
                                                                        <td class="ant-table-cell"><?=++$i;?></td>
                                                                        <td class="ant-table-cell"><?=$row['seri'];?></td>
                                                                        <td class="ant-table-cell"><?=$row['pin'];?></td>
                                                                        <td class="ant-table-cell"><span class="text-<?=(($row['status'] == 'thatbai') ? 'danger': 'green');?>-500"><?=number_format($row['thucnhan']);?>₫</span></td>
                                                                        <td class="ant-table-cell"><?=number_format($row['menhgia']);?>₫</td>
                                                                        <td class="ant-table-cell"><?=stnapthe($row['status']);?></td>
                                                                        <td class="ant-table-cell"><?=$row['thoigian'];?></td>
                                                                        <td class="ant-table-cell"><?=$row['uptime'];?></td>
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
<script type="text/javascript">
$("#Napthe").on("click", function() {

    $('#Napthe').html('Đang xử lý...').prop('disabled',
        true);
    $.ajax({
        url: "<?=BASE_URL('controller/client/Napthe.php');?>",
        method: "POST",
        data: {
            type: 'Napthe',
            loaithe: $("#loaithe").val(),
            menhgia: $("#menhgia").val(),
            mathe: $("#mathe").val(),
            seri: $("#serial").val()
        },
        success: function(response) {
            $("#thongbao").html(response);
            $('#Napthe').html(
                    'Gửi thẻ')
                .prop('disabled', false);
        }
    });
});
</script>
<?php

require_once("../../pages/client/Footer.php");
?>