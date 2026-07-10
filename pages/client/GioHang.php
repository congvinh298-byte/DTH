<?php

define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
if(isset($_POST['id']) && isset($_COOKIE['token'])) {
    $id = check_string($_POST['id']);
    if($id == -1) {
        $DMH->remove("giohang", " `username` = '".$getUser['username']."' ");
        echo json_encode(['status' => 'success', 'msg' => 'Đã xóa hết giỏ hàng']);
        die;
    } else {
        $DMH->remove("giohang", " `id` = '".$id."' AND `username` = '".$getUser['username']."' ");
        echo json_encode(['status' => 'success', 'msg' => 'Đã xóa khỏi giỏ hàng']);
        die;
    }
}
$title = "Danh sách giỏ hàng";
require_once("../../pages/client/Head.php");
require_once("../../pages/client/Header.php");
$sotien = 0;
CheckLogin();
?>
<div class="content-wrapper transition-all duration-150 ltr:ml-0 rtl:mr-0 xl:ltr:ml-[248px] xl:rtl:mr-[248px] margin-0" id="content_wrapper">
    <div class="page-content">
        <div class="container-fluid transition-all duration-150" id="page_layout">
            <main id="content_layout">
                <!-- Page Content -->
                <div class="mb-3">
                </div>

                <section id="app" class="space-y-6" data-v-app="">
                 
                <style>
                    table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    th {
                        background-color: #f2f2f2;
                    }
                    tfoot {
                        font-weight: bold;
                    }
                </style>
                    <div class="card">
                        <header class="card-header noborder">
                            <h4 class="card-title">Danh sách giỏ hàng</h4>
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
                                                                    <col style="width: 13%;"></col>

                                                                    <col style="width: 13%;"></col>
                                                                    <col style="width: 20%;"></col>
                                                            </colgroup>
                                                            <thead class="ant-table-thead">
                                                                <tr>
                                                                    <th class="ant-table-cell ant-table-column-has-sorters" tabindex="0" colstart="0" colend="0">
                                                                        <div class="ant-table-column-sorters"><span class="ant-table-column-title">ID</span><span class="ant-table-column-sorter ant-table-column-sorter-full"><span class="ant-table-column-sorter-inner"><span role="presentation" aria-label="caret-up" class="anticon anticon-caret-up ant-table-column-sorter-up"><svg focusable="false" class="" data-icon="caret-up" width="1em" height="1em" fill="currentColor" aria-hidden="true" viewBox="0 0 1024 1024"><path d="M858.9 689L530.5 308.2c-9.4-10.9-27.5-10.9-37 0L165.1 689c-12.2 14.2-1.2 35 18.5 35h656.8c19.7 0 30.7-20.8 18.5-35z"></path></svg></span><span role="presentation" aria-label="caret-down" class="anticon anticon-caret-down ant-table-column-sorter-down"><svg focusable="false" class="" data-icon="caret-down" width="1em" height="1em" fill="currentColor" aria-hidden="true" viewBox="0 0 1024 1024"><path d="M840.4 300H183.6c-19.7 0-30.7 20.8-18.5 35l328.4 380.8c9.4 10.9 27.5 10.9 37 0L858.9 335c12.2-14.2 1.2-35-18.5-35z"></path></svg></span></span>
                                                                            </span>
                                                                        </div>
                                                                        
                                                                    </th>
                                                                    <th class="ant-table-cell" colstart="2" colend="2">Mã code</th>
                                                                    <th class="ant-table-cell" colstart="3" colend="3">Số tiền</th>
                                                                    <th class="ant-table-cell" colstart="4" colend="4">Ảnh sản phẩm</th>
                                                                    <th class="ant-table-cell" colstart="5" colend="5">Thời gian</th>
                                                                    <th class="ant-table-cell" colstart="6" colend="6">Thao tác</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody class="ant-table-tbody">
                                                                <?php $i = 0; foreach($DMH->get_list(" SELECT * FROM `giohang` WHERE `username` = '".$getUser['username']."' ORDER BY id DESC") as $row){
                                                                        if($DMH->site('sukien') == 'ON' && $DMH->site('ptgiamgia') > 0) {
                                                                            $tienid= $row['sotien'] - ($row['sotien']*$DMH->site('ptgiamgia')/100);
                                                                        } else {
                                                                            $tienid = $row['sotien'];                                                                            
                                                                        }
                                                                        $sotien +=$tienid;
                                                                    ?>
                                                                    <tr class="ant-table-row ant-table-row-level-0">
                                                                        <td class="ant-table-cell"><?=++$i;?></td>
                                                                        <td class="ant-table-cell">
                                                                            <a target="_bank" href="/mua-code/<?=$row['id_code'];?>">
                                                                                <span class="badge bg-warning rounded-lg" style="background-color: #FFC436"><?=$row['id_code'];?></span>
                                                                            </a>
                                                                        </td>
                                                                        <?php $check2 = $DMH->get_row(" SELECT * FROM `danhsachmuacode` WHERE `id` = '".$row['id_code']."' "); ?>
                                                                        <td class="ant-table-cell"><?=sotienmua($tienid);?></td>
                                                                        <td class="ant-table-cell"><a href="<?=$check2['img']?>" class="glightbox">
                                                                            <img style="height: 100px; width: 500px" src="/images/svg/spinner.svg" data-src="<?=$check2['img']?>" class="lazyload" alt="<?=$check2['title'];?>" />
                                                                        </td>
                                                                        <td class="ant-table-cell" ><?=$row['thoigian'];?></td>
                                                                        <!-- <td class="ant-table-cell"><a onclick="remove(<?=$row['id'];?>)"><i class="fa-regular fa-trash-can"></i></a></td> -->
                                                                        <td> <a onclick="remove(<?=$row['id'];?>)">
                                                                                <span class="badge bg-warning rounded-lg" style="background-color: #FF0000"><i class="fa-regular fa-trash-can"></i></span>
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
                                                            <tfoot>
                                                                <tr>
                                                                    <td colspan="3" class="right-cart">
                                                                        <h6>Tổng Tiền: <?=sotienmua($sotien);?></h6>
                                                                    </td>
                                                                    
                                                                </tr>
                                                            </tfoot>
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
                    <ul class="checkout">
                        <div class="tab mb-3">
                            <button onclick="remove('-1')"class="tablinks btn btn-danger"><i class="fa fa-trash"></i> Xóa hết giỏ hàng</button>
                            <a href="/mua-source-code"><button class="tablinks btn btn-warning"><i class="fa fa-shopping-cart"></i> Mua thêm hàng</button></a>
                            <a href="/Thanhtoan"><button class="tablinks btn btn-success"><i class="fa fa-shopping-cart"></i> Thanh toán</button></a>
                        </div>
                    </ul>
                </section>
            </main>
        </div>
    </div>
</div>
</div>

<script>
    function remove(id)   {
        if(confirm("Bạn có chắc chắn muốn xóa"))
        $.ajax({
            url: "/GioHang",
            method: "POST",
            dataType: "JSON",
            data: {
                'id'    : id
            },
            success: function(respone) {
                showToast(respone.msg, respone.status)
                if(respone.status == 'success')
                    setTimeout(function() {
                        window.location = "";
                    }, 1000);
            }
            
        });
    }
</script>
<?php

require_once("../../pages/client/Footer.php");
?>