<?php

define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Quản lý tên miền";
require_once("../../pages/client/Head.php");
require_once("../../pages/client/Header.php");
CheckLogin();
?>
<?php
if(isset($_GET['id'])) {
    $row = $DMH->get_row(" SELECT * FROM `lichsumuamien` WHERE `id` = '".check_string($_GET['id'])."' AND `username` = '".$getUser['username']."' ");
    if(!$row)
    {
        msg_error("Dữ liệu này không hợp lệ", BASE_URL('Mua-mien'), 500);
    }
    $data = explode('.', $row['domain']);
    $row2 = $DMH->get_row(" SELECT * FROM `danhsachmien` WHERE `domain` = '".end($data)."' ");
}
else {
    msg_error("Liên kết của bạn thiếu Dữ Liệu", BASE_URL('Mua-mien'), 0);
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
                                    <h6>Gia hạn tên miền</h6>
                            </header>
                        
                            <div class="card-body px-6 pb-6">
                                <form class="space-y-6">
                                    <div class="grid grid-cols-6 gap-6">
                                        <div class="input-area">
                                            <label for="thang" class="form-label">Gia hạn thêm</label>
                                            <select class="form-control" id="thang" name="thang" required>
                                                <option value="">Chọn thời gian hoạt động</option>
                                                <?php for($i = 1; $i<=9; ++$i) {?>
                                                    <option value="<?=$i;?>"><?=$i;?> năm - <?=number_format($i*$row2['giahan']);?>đ</option>
                                                <?php } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="input-area">
                                        <button type="button" id = "XacNhan" class="btn btn-sm btn-primary w-full">Gia hạn</button>
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
                                            type: 'GiaHanDomain',
                                            id: '<?=$row['id'];?>',
                                            thang: $("#thang").val()
                                        },
                                        success: function(response) {
                                            $("#thongbao").html(response);
                                            $('#XacNhan').html(
                                                    'Gia hạn')
                                                .prop('disabled', false);
                                        }
                                    });
                                });
                            </script>
                           
                            <header class=" card-header noborder">
                                <h6>Thông tin chung <a href="//<?=$row['domain'];?>"><span class="text-danger-500"><?=$row['domain'];?></span></a></h6>
                            </header>
                            <div class="card-body px-6 pb-6">
                                <form class="space-y-3">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="input-area">
                                            <label for="username" class="form-label">Năm mua (Ban đầu)</label>

                                            <input type="text" class="form-control !pr-12" value="<?=$row['thoihan'];?> năm" disabled="">
                                        </div>
                                        <div class="input-area">
                                            <label for="username" class="form-label">Tổng Thanh Toán</label>
                                            <input type="text" class="form-control !pr-12" value="<?=number_format($row['tongtien']);?>đ" disabled="">
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        Trạng thái: <?=status($row['status']);?>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card">
                            <header class=" card-header noborder">
                                <h4 class="card-title">Thông Tin Tên Miền</h4>
                            </header>
                            <div class="card-body px-6 pb-6">
                                <form class="space-y-3">
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="input-area">
                                            <label for="username" class="form-label">Tên miền</label>
                                            <div class="relative">
                                                <input type="text" class="form-control !pr-12" value="<?=$row['domain'];?>" disabled="">
                                            </div>
                                        </div>
                                        <div class="input-area">
                                            <label for="password" class="form-label">Số ngày còn lại</label>
                                            <div class="relative">
                                                <input type="text" class="form-control !pr-12" value="<?=timeHave(strtotime($row['timedie']));?>" disabled="">
                                            </div>
                                        </div>
                                        <div class="input-area">
                                            <label for="password" class="form-label">Ngày đăng ký</label>
                                            <div class="relative">
                                                <input type="text" class="form-control !pr-12" value="<?=$row['timemua'];?>" disabled="">
                                            </div>
                                        </div>
                                        <div class="input-area">
                                            <label for="password" class="form-label">Ngày hết hạn</label>
                                            <div class="relative">
                                                <input type="text" class="form-control !pr-12" 
                                                value="<?php if(strtotime($row['timedie']) < 0) {
                                                        echo 'Chưa bắt đầu';
                                                    } else {
                                                        echo $row['timedie'];
                                                    }
                                                    ?>" 
                                                disabled="">
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>

                            <div class="card-body px-6 pb-12">
                                <form class="space-y-12">
                                    <div class="grid grid-cols-6 gap-12">
                                        <div class="input-area">
                                            <label for="username" class="form-label">Nameserver</label>
                                            <div class="relative">
                                                <textarea type="text" class="form-control !pr-12" rows="4" disabled=""><?=$row['ns'];?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div> <br/>
                            
                        </div>
                    </div>
                    <div id="app" class="card">
                        <header class="card-header noborder">
                            <h4 class="card-title">Lịch sử gia hạn</h4>
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
                                                                    <th class="ant-table-cell" colstart="1" colend="1">Tên miền</th>

                                                                    <th class="ant-table-cell" colstart="3" colend="3">Thời hạn</th>
                                                                    <th class="ant-table-cell" colstart="4" colend="4">Tổng gia hạn</th>
                                                                    <th class="ant-table-cell" colstart="5" colend="5">Gia hạn lúc</th>
                                                                    <th class="ant-table-cell" colstart="6" colend="6">Trạng thái</th>

                                                                </tr>
                                                            </thead>
                                                            <tbody class="ant-table-tbody">
                                                                <?php $i = 0; foreach($DMH->get_list(" SELECT * FROM `giahanmien` WHERE `username` = '".$getUser['username']."' AND `id_domain` = '".$row['id']."' ORDER BY id DESC") as $row){ ?>
                                                                    <tr class="ant-table-row ant-table-row-level-0">
                                                                        <td class="ant-table-cell"><?=++$i;?></td>
                                                                        <td class="ant-table-cell"><a style="color: green; font-weight: bold"><?=$row['tenmien'];?></a></td>
                                                                        <td class="ant-table-cell"><?=$row['thoigian'];?> năm</td>
                                                                        <td class="ant-table-cell"><?=number_format($row['tongtien']);?> đ</td>
                                                                        <td class="ant-table-cell"><?=$row['time'];?></td>
                                                                        <td class="ant-table-cell"><?=status($row['status']);?></td>

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
                        <a href="/Mua-mien" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Quay Lại</a>
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