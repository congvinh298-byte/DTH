<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Lịch Sử Tạo Website";
require_once("../../pages/client/Head.php");
require_once("../../pages/client/Header.php");
CheckLogin();
?>
<?php
$check = '';
if(isset($_GET['z'])) {
    $z = check_string($_GET['z']);
    switch($z)  {
        case 1:
            $check = 'AND `buoc` IN (1,2,3)';
            break;
        case 2:
            $check = 'AND `buoc` IN (4,5)';
            break;
        case 3:
            $check = 'AND `buoc` = 5';
            break;
        case 4:
            $check = 'AND `buoc` = 6';
            break;
    }
} ?>
<div class="content-wrapper transition-all duration-150 ltr:ml-0 rtl:mr-0 xl:ltr:ml-[248px] xl:rtl:mr-[248px] margin-0" id="content_wrapper">
    <div class="page-content">
        <div class="container-fluid transition-all duration-150" id="page_layout">
            <main id="content_layout">
                <!-- Page Content -->
                <div class="mb-3">
                </div>

                <section id="app" class="space-y-6" data-v-app="">
                    <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4">
                    <div class="relative flex items-center rounded-[6px] bg-cover bg-center bg-no-repeat px-5 py-8" style="background-image: url(&quot;/images/widget-bg-4.png&quot;);">
                            <div class="flex-1">
                                <div class="max-w-[180px]">
                                    <h4 class="mb-2 text-2xl font-medium text-[#27374D]"><span class="block text-sm">Chưa hoạt động,</span>
                                    <span class="block"><?=number_format($TUANORI->num_rows(" SELECT * FROM `lichsutaoweb` WHERE`username` = '".$getUser['username']."' AND `buoc` IN (1,2,3) "));?></span></h4>
                                </div>
                            </div>
                            <div class="flex-none"><a href="/History-tao-web?z=1" class="btn-light btn-sm btn bg-white">Kiểm tra</a>
                            </div>
                        </div>
                        <div class="relative flex items-center rounded-[6px] bg-cover bg-center bg-no-repeat px-5 py-8" style="background-image: url(&quot;/images/widget-bg-1.png&quot;);">
                            <div class="flex-1">
                                <div class="max-w-[180px]">
                                    <h4 class="mb-2 text-2xl font-medium text-[#27374D]"><span class="block text-sm">Đang hoạt động,</span>
                                    <span class="block"><?=number_format($TUANORI->num_rows(" SELECT * FROM `lichsutaoweb` WHERE`username` = '".$getUser['username']."' AND `buoc` IN (4,5)"));?></span></h4>
                                </div>
                            </div>
                            <div class="flex-none"><a href="/History-tao-web?z=2" class="btn-light btn-sm btn bg-white">Kiểm tra</a>
                            </div>
                        </div>
                        <div class="relative flex items-center rounded-[6px] bg-cover bg-center bg-no-repeat px-5 py-8" style="background-image: url(&quot;/images/widget-bg-2.png&quot;);">
                            <div class="flex-1">
                                <div class="max-w-[180px]">
                                    <h4 class="mb-2 text-2xl font-medium text-white"><span class="block text-sm"> Đã hết hạn </span>
                                    <span class="block"><?=number_format($TUANORI->num_rows(" SELECT * FROM `lichsutaoweb` WHERE`username` = '".$getUser['username']."' AND `buoc` = '6'"));?> </span></h4>
                                </div>
                            </div>
                            <div class="flex-none"><a href="/History-tao-web?z=3" class="btn-light btn-sm btn bg-white">Kiểm tra</a>
                            </div>
                        </div>
                        <div class="relative flex items-center rounded-[6px] bg-cover bg-center bg-no-repeat px-5 py-8" style="background-image: url(&quot;/images/widget-bg-3.png&quot;);">
                            <div class="flex-1">
                                <div class="max-w-[180px]">
                                    <h4 class="mb-2 text-2xl font-medium text-white"><span class="block text-sm"> Cần gia hạn, </span>
                                    <span class="block"><?=number_format($TUANORI->num_rows(" SELECT * FROM `lichsutaoweb` WHERE`username` = '".$getUser['username']."' AND `buoc` = '5'"));?></span></h4>
                                </div>
                            </div>
                            <div class="flex-none"><a href="/History-tao-web?z=4" class="btn-light btn-sm btn bg-white">Kiểm tra</a>
                            </div>
                        </div>
                       
                        
                    </div>
                    <hr class="mb-2 mt-2 h-[10px]">
                    <div class="card">
                        <header class="card-header noborder">
                            <h4 class="card-title">Lịch sử Tạo Website</h4>
                        </header>
                        <div class="card-body px-6 pb-6">
                            <div class="overflow-auto">
                                <div class="mb-5 flex flex-col gap-5 md:flex-row md:items-center">
                                    
                                </div>
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
                                                                    <th class="ant-table-cell" colstart="2" colend="2">Mẫu mã</th>
                                                                    <th class="ant-table-cell" colstart="3" colend="3">Tổng tiền</th>
                                                                    <th class="ant-table-cell" colstart="4" colend="4">Tên miền </th>
                                                                    <th class="ant-table-cell" colstart="5" colend="5">Trạng thái</th>
                                                                    <th class="ant-table-cell" colstart="6" colend="6">Mã giảm giá</th>
                                                                    <th class="ant-table-cell" colstart="7" colend="7">Còn lại</th>
                                                                    <th class="ant-table-cell" colstart="8" colend="8">Thao tác</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="ant-table-tbody">
                                                                <?php $i = 0; foreach($TUANORI->get_list(" SELECT * FROM `lichsutaoweb` WHERE `username` = '".$getUser['username']."' $check ORDER BY id DESC") as $row){ ?>
                                                                    <tr class="ant-table-row ant-table-row-level-0">
                                                                        <td class="ant-table-cell"><?=++$i;?></td>
                                                                        <td class="ant-table-cell"><a target="_bank" href="/mua-code/<?=$row['id_code'];?>" style="color: red"><?=$row['id_code'];?></a></td>
                                                                        <td class="ant-table-cell" style="color: green"><?=sotienmua($row['tongtien']);?></td>
                                                                        <td class="ant-table-cell"><b><?=$row['tenmien'];?></b></td>
                                                                        <td class="ant-table-cell"><?=statustaoweb($row['buoc']);?></td>
                                                                        <td class="ant-table-cell"><b><?=inkq($row['magiamgia'], 'Không có');?></b></td>
                                                                        <td class="ant-table-cell"><b>
                                                                            <?php if(in_array($row['buoc'] , [1,2,3])) {
                                                                                echo 'Chưa bắt đầu';
                                                                            } else {
                                                                                echo timeHave($row['ngayhethan']);
                                                                            } ?>
                                                                        </b></td>

                                                                        <td class="ant-table-cell">
                                                                            <a href="/QuanLy/TrangWeb/<?=$row['id'];?>">
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