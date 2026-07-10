<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Đơn hàng mua code #".$_GET['magd'];
require_once("../../pages/client/Head.php");
require_once("../../pages/client/Header.php");
CheckLogin();
if(isset($_GET['magd']))
{
    $magd = check_string($_GET['magd']);
    $row2 = $TUANORI->get_row(" SELECT * FROM `lichsumuacode` WHERE `magd` = '".check_string($_GET['magd'])."' AND `username` = '".$getUser['username']."' ");
    if(!$row2)
    {
        msg_error("Dữ liệu đơn hàng này không hợp lệ", BASE_URL('History-mua-code'), 500);
    }
}
else
{
    msg_error("Liên kết của bạn thiếu Dữ Liệu", BASE_URL('History-mua-code'), 0);
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
                    <div class="grid grid-cols-1 md:grid-cols-1 gap-6">
                        <div class="card">
                            <header class="card-header noborder">
                                <h4 class="card-title">Đơn hàng <b style="color: red">#<?=$magd;?></b></h4>
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
                                                                        <th class="ant-table-cell" colstart="2" colend="2">Mã Code</th>
                                                                        <th class="ant-table-cell" colstart="3" colend="3">Tổng tiền</th>
                                                                        <th class="ant-table-cell" colstart="4" colend="4">Thời gian </th>
                                                                        <th class="ant-table-cell" colstart="5" colend="5">Thao tác</th>
                                                                        <th class="ant-table-cell" colstart="7" colend="7">Mã giảm giá</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody class="ant-table-tbody">
                                                                    <?php $i = 0; foreach($TUANORI->get_list(" SELECT * FROM `lichsumuacode2` WHERE `username` = '".$getUser['username']."' AND `magd` = '".check_string($_GET['magd'])."' ORDER BY id DESC") as $row){ ?>
                                                                        <?php $code = $TUANORI->get_row(" SELECT * FROM `danhsachmuacode` WHERE `id` = '".$row['id_code']."' AND `hienthi` = 'SHOW'"); ?>
                                                                        <tr class="ant-table-row ant-table-row-level-0">
                                                                            <td class="ant-table-cell"><?=++$i;?></td>
                                                                            <td class="ant-table-cell"><a target="_bank" href="/mua-code/<?=$row['id_code'];?>">
                                                                                <span class="badge bg-warning rounded-lg" style="background-color: #FFC436"><?=$row['id_code'];?></span>
                                                                                </a>
                                                                            </td>
                                                                            <td class="ant-table-cell"><?=sotienmua($row['tongtien']);?></td>
                                                                            <td class="ant-table-cell"><?=$row['thoigian'];?></td>
                                                                            <?php $code = $TUANORI->get_row(" SELECT * FROM `danhsachmuacode` WHERE `id` = '".$row['id_code']."' AND `hienthi` = 'SHOW'"); ?>
                                                                            <td class="ant-table-cell">
                                                                                <a target="_blank" href="<?=$code['download'];?>">
                                                                                    <span class="badge bg-warning rounded-lg" style="background-color: #33FF33"><i class="fa-solid fa-download"></i> Tải xuống</span></a>
                                                                              
                                                                            </td>
                                                                            <td class="ant-table-cell"><?=inkq($row['magiamgia'], 'Không có');?></td>

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


                    </div>
                    <script type="text/javascript">
                        function getStatusInvoice() {
                            $.ajax({
                                url: "<?=BASE_URL('controller/status/NapVi.php');?>",
                                type: "GET",
                                dataType: "JSON",
                                data: {
                                    magd: "<?=$magd;?>"
                                },
                                success: function(result) {
                                    if (result.status == 1) {
                                        setTimeout("location.href = '/Nap-Vi';", 3000);
                                    }
                                    $('#status_vi').html(result.msg);
                                }
                            });
                        }
                        setInterval(function() {
                            $('#status_vi').load(getStatusInvoice());
                        }, 3000);
                        $("#HuyNap").on("click", function() {

                            $('#HuyNap').html('Đang xử lý...').prop('disabled',
                                true);
                            $.ajax({
                                url: "<?=BASE_URL('controller/client/HuyNap.php');?>",
                                method: "POST",
                                data: {
                                    magd: '<?=$magd;?>'
                                },
                                success: function(response) {
                                    $("#thongbao").html(response);
                                    $('#HuyNap').html(
                                            '<i class="fa-sharp fa-solid fa-square-xmark"></i> Hủy Nạp')
                                        .prop('disabled', false);
                                }
                            });
                            });
                    </script>
                    <div class="text-center">
                        <a href="/History-mua-code" class="btn btn-primary"><i class="fas fa-arrow-left"></i> Quay Lại</a>
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