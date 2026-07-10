<?php

define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Nạp tiền qua PAYPAL";
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
                                        <div class="card-title text-slate-900 dark:text-white">Nạp Tiền Bằng PayPal</div>
                                    </div>
                                </header>
                                <div class="card-text h-full space-y-4">
                                    <div class="flex justify-center mt-5 mb-3">
                                        <img src="/images/svg/paypal.svg" style="width: 200px">
                                    </div>
                                    <div>
                                        <form id="form">
                                            <div class="mb-3">
                                                <label for="amount" class="form-label" data-key="dp-nhap-so-tien">Nhập Số Tiền: (USD)</label>
                                                <input type="number" class="form-control" id="amount" name="amount" value="1" required>
                                            </div>
                                            <div id="paypal-button-container"></div>
                                        </form>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                        <script src="https://www.paypal.com/sdk/js?client-id=<?=$DMH->site('clientId');?>&currency=USD"></script>
                        <script>
                        (function($) {
                            paypal.Buttons({

                                // Sets up the transaction when a payment button is clicked
                                createOrder: function(data, actions) {
                                    return actions.order.create({
                                        purchase_units: [{
                                            amount: {
                                                value: $('#amount')
                                                .val() // Can reference variables or functions. Example: `value: document.getElementById('...').value`
                                            }
                                        }]
                                    });
                                },

                                // Finalize the transaction after payer approval
                                onApprove: function(data, actions) {
                                    return actions.order.capture().then(function(orderData) {
                                        $.ajax({
                                            url: '<?=BASE_URL('assets/ajaxs/PayPal.php');?>',
                                            method: 'POST',
                                            data: {
                                                type: 'PayPal',
                                                order: orderData
                                            },
                                            success: function(response) {
                                                $("#thongbao").html(response);
                                            }
                                        })
                                    });
                                }
                            }).render('#paypal-button-container');
                        })(jQuery)
                        </script>


                        <div class="card">
                            <header class="card-header noborder">
                                <h4 class="card-title">Lưu ý nạp tiền</h4>
                            </header>
                            <div class="card-body px-6 pb-6">
                                <div class="card border border-red-400 p-3">
                                    <div class="card-body">
                                        <p class="text-red-500 text-xl">1. Giá quy đổi 1$ = <?=number_format($DMH->site('sotien_paypal'));?>VNĐ</p>
                                        <p class="text-red-500 text-xl">2. Min nạp là 1$ nạp dưới 1$ nếu xảy ra lỗi không hỗ trợ</p>
                                        <p class="text-red-500 text-xl">3. Nạp tiền sẽ được cộng ngay lập tức.</p>
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
                            <h4 class="card-title">Lịch sử nạp PayPal</h4>
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
                                                                    <th class="ant-table-cell" colstart="1" colend="1">Đô nạp</th>
                                                                    <th class="ant-table-cell" colstart="2" colend="2">Thực nhận</th>
                                                                    <th class="ant-table-cell" colstart="3" colend="3">Thời gian</th>

                                                                </tr>
                                                            </thead>
                                                            <tbody class="ant-table-tbody">
                                                                <?php $i = 0; foreach($DMH->get_list(" SELECT * FROM `nappaypal` WHERE `username` = '".$getUser['username']."' ORDER BY id DESC LIMIT 30") as $row){ ?>
                                                                    <tr class="ant-table-row ant-table-row-level-0">
                                                                        <td class="ant-table-cell"><?=++$i;?></td>
                                                                        <td class="ant-table-cell"><b style="color: red">+ <?=$row['donap'];?>$</b></td>
                                                                        <td class="ant-table-cell text-green-500">+ <?=number_format($row['thucnhan']);?> VNĐ</td>
                                                                        <td class="ant-table-cell"><?=$row['create_date'];?></td>
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