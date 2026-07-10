<?php

define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Mã giảm giá";
require_once("../../pages/client/Head.php");
require_once("../../pages/client/Header.php");
?>
<div class="content-wrapper transition-all duration-150 ltr:ml-0 rtl:mr-0 xl:ltr:ml-[248px] xl:rtl:mr-[248px] margin-0" id="content_wrapper">
    <div class="page-content">
        <div class="container-fluid transition-all duration-150" id="page_layout">
            <main id="content_layout">
                <!-- Page Content -->
                <div class="mb-3">
                </div>

                <section id="app" class="space-y-6" data-v-app="">
                    <div class="card">
                        <header class="card-header noborder">
                            <h4 class="card-title">Mã giảm giá</h4>
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
                                                                    <th class="ant-table-cell" colstart="2" colend="2">Mã giảm giá</th>
                                                                    <th class="ant-table-cell" colstart="3" colend="3">Tổng giảm</th>
                                                                    <th class="ant-table-cell" colstart="4" colend="4">Thể loại </th>
                                                                    <th class="ant-table-cell" colstart="5" colend="5">Còn lại</th>
                                                                
                                                                </tr>
                                                            </thead>
                                                            <tbody class="ant-table-tbody">
                                                                <?php $i = 0; foreach($DMH->get_list(" SELECT * FROM `magiamgia` WHERE `hienthi` = 'SHOW' AND `conlai` > 0 ORDER BY id DESC") as $row){ ?>
                                                                    <tr class="ant-table-row ant-table-row-level-0">
                                                                        <td class="ant-table-cell"><?=++$i;?></td>
                                                                        <td class="ant-table-cell text-green-500"><?=$row['magiamgia'];?></td>
                                                                        <td class="ant-table-cell"><?=$row['giambaonhieu'];?>%</td>
                                                                        <td class="ant-table-cell" style="font-weight: bold"><?=magiamgia($row['theloai']);?></td>
                                                                        <td class="ant-table-cell text-red-500"><?=number_format($row['conlai']);?> lượt</td>

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

require_once("../../pages/client/Footer.php");
?>