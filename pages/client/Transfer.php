
<?php

define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Chuyển tiền";
require_once("../../pages/client/Head.php");
require_once("../../pages/client/Header.php");
CheckLogin();
?>

<div class="content-wrapper transition-all duration-150 ltr:ml-0 rtl:mr-0 xl:ltr:ml-[248px] xl:rtl:mr-[248px]" id="content_wrapper">
    <div class="page-content">
        <div class="container-fluid transition-all duration-150" id="page_layout">
            <main id="content_layout">
                <!-- Page Content -->
                <div class="mb-3"></div>
                <section class="space-y-6">
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-6">
                     
                        <div class="card">
                            <header class="card-header noborder">
                                <h4 class="card-title">Chuyển tiền</h4>
                            </header>
                            <div class="card-body px-6 pb-6">
                                <form method="POST" class="space-y-3">
                                    <div class="input-area">
                                        <label for="user" class="form-label">Username</label>
                                        <input type="text" class="form-control  py-2" id="username" placeholder="Username" required="">
                                    </div>
                                    <div class="input-area">
                                        <label for="sotien" class="form-label">Số tiền chuyển</label>
                                        <input type="number" class="form-control py-2" id="sotien" placeholder="Số tiền chuyển" value="10000" required="">
                                        <i>Chuyển tối thiểu là <b style="color: green">10.000đ</b> và tối đa là <b style="color: green">100.000.000đ</b></i>
                                    </div>
                                    <div class="input-area">
                                        <button type="button" id="Xacnhan" class="btn btn-sm btn-primary w-full"><i class="fa-solid fa-money-bill-transfer"></i> Xác nhận</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <script type="text/javascript">
                        $("#Xacnhan").on("click", function() {

                            $('#Xacnhan').html('Đang xử lý...').prop('disabled',
                                true);
                            $.ajax({
                                url: "<?=BASE_URL('controller/client/Chuyentien.php');?>",
                                method: "POST",
                                data: {
                                    username: $("#username").val(),
                                    sotien: $("#sotien").val()
                                },
                                success: function(response) {
                                    $("#thongbao").html(response);
                                    $('#Xacnhan').html(
                                            '<i class="fa-solid fa-money-bill-transfer"></i> Xác nhận')
                                        .prop('disabled', false);
                                }
                            });
                        });
                        </script>
                    <div id="app" class="card" data-v-app="">
                        <header class="card-header noborder">
                            <h4 class="card-title">Lịch sử chuyển/ nhận tiền</h4>
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
                                                                    <th class="ant-table-cell" colstart="1" colend="1">Người chuyển</th>
                                                                    <th class="ant-table-cell" colstart="2" colend="2">Người nhận</th>
                                                                    <th class="ant-table-cell" colstart="3" colend="3">Số tiền</th>
                                                                    <th class="ant-table-cell" colstart="4" colend="4">Thời gian</th>
                                                                    <th class="ant-table-cell" colstart="5" colend="5">Ip thực hiện</th>

                                                                </tr>
                                                            </thead>
                                                            <tbody class="ant-table-tbody">
                                                                <?php $i = 0; foreach($DMH->get_list(" SELECT * FROM `chuyentien` WHERE `userchuyen` = '".$getUser['username']."' or `usernhan` = '".$getUser['username']."' ORDER BY id DESC") as $row){ ?>
                                                                    <tr class="ant-table-row ant-table-row-level-0">
                                                                        <td class="ant-table-cell"><?=++$i;?></td>
                                                                        <td class="ant-table-cell"><?=$row['userchuyen'];?></td>
                                                                        <td class="ant-table-cell"><?=$row['usernhan'];?></td>
                                                                        <?php if($row['usernhan'] == $getUser['username']) { ?>
                                                                            <td class="ant-table-cell text-green-500">+ <?=number_format($row['sotien']);?> ₫</td>
                                                                        <?php } else { ?>
                                                                            <td class="ant-table-cell text-red-500">- <?=number_format($row['sotien']);?> ₫</td>
                                                                        <?php } ?>
                                                                        <td class="ant-table-cell"><?=gettime2($row['time']);?></td>
                                                                        <td class="ant-table-cell text-green-500"><?=$row['ip'];?></td>
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