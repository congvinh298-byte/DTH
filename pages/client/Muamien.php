<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Đăng Ký Tên Miền";
require_once("../../pages/client/Head.php");
require_once("../../pages/client/Header.php");
if(isset($_GET['name']) && isset($_GET['duoi'])) {
    $name = check_string($_GET['name']);
    $duoi = check_string($_GET['duoi']);
} else {
    $name = $duoi = '';
}
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
                                        <div class="card-title text-slate-900 dark:text-white">Đăng ký tên miền</div>
                                    </div>
                                </header>
                                <div class="card-text h-full space-y-4">
                                  <form method="POST" class="space-y-3">
                                      <div class="input-area">
                                          <label for="ten" class="form-label">Nhập tên miền</label>
                                          <input type="text" class="form-control  py-2" id="ten" placeholder="VD: tuanori" value="<?=$name;?>" required="">
                                      </div>
                                      <div class="input-area">
                                          <label for="duoi" class="form-label">Chọn Đuôi Miền</label>
                                          <select class="form-control" id="duoi" required>
                                            <option value="0">Chọn loại miền</option>
                                              <?php foreach($TUANORI->get_list(" SELECT * FROM `danhsachmien`") as $row){ ?>
                                                <option value="<?=$row['id'];?>" <?=($duoi == $row['domain']) ? 'selected': '';?> >.<?=$row['domain'];?> - (<?=format_cash($row['money']);?>đ)</option>
                                              <?php } ?>
                                          </select>
                                      </div>
                                      <div class="input-area">
                                          <label for="  " class="form-label">Chọn năm mua</label>
                                          <select class="form-control" id="thanhtoan" required>
                                              <option value="0">Chọn hạn sử dụng:</option>
                                              <option value="1">1 năm</option>
                                              <option value="2">2 năm</option>
                                              <option value="3">3 năm</option>
                                              <option value="4">4 năm</option>
                                              <option value="5">5 năm</option>
                                              <option value="6">6 năm</option>
                                              <option value="7">7 năm</option>
                                              <option value="8">8 năm</option>
                                              <option value="9">9 năm</option>
                                          </select>
                                      </div>
                                      <div class="input-area">
                                          <label for="ns" class="form-label">Nameserver</label>
                                          <textarea type="text" class="form-control  py-2" id="ns" placeholder="Mỗi nameserver trên 1 dòng" rows="4" required=""></textarea>
                                      </div>
                                      <center><h6>Số tiền thanh toán: <b id="sotien">0</b> đ</h6></center>

                                      <div class="input-area">
                                          <button type="button" id="Muamien" class="btn btn-sm btn-primary w-full"><i class="fas fa-shopping-cart me-2"></i> Thanh toán</button>
                                      </div>
                                  </form>
                                </div>
                            </div>
                        </div>

                            <script type="text/javascript">
                                $("#Muamien").on("click", function() {
                                    $('#Muamien').html('Đang xử lý...').prop('disabled',
                                        true);
                                    $.ajax({
                                        url: "<?=BASE_URL("controller/client/Muahang.php");?>",
                                        method: "POST",
                                        data: {
                                            type: 'Muamien',
                                            ten: $("#ten").val(),
                                            duoi: $("#duoi").val(),
                                            nam: $("#thanhtoan").val(),
                                            ns: $("#ns").val()
                                        },
                                        success: function(response) {
                                            $("#thongbao").html(response);
                                            $('#Muamien').html(
                                                    '<i class="fas fa-shopping-cart me-2"></i> Thanh toán')
                                                .prop('disabled', false);
                                        }
                                    });
                                });
                              function updateSotien() {
                                  var ketqua = 'Đang tính thực nhận';
                                  $("#sotien").html(ketqua); // hoặc $("#sotien").text(ketqua);
                                  
                                  $.ajax({
                                      url: "<?=BASE_URL("controller/client/ViewGia.php");?>",
                                      method: "GET",
                                      data: {
                                          duoi: $("#duoi").val(),
                                          thanhtoan: $("#thanhtoan").val()
                                      },
                                      success: function(response) {
                                          var ketqua = response;
                                          $("#sotien").html(ketqua);
                                      }
                                  });
                              }

                              $('#duoi, #thanhtoan').change(function() {
                                  updateSotien();
                              });
                          </script>
                        <div class="card">
                            <header class="card-header noborder">
                                <h4 class="card-title">Lưu ý khi mua miền</h4>
                            </header>
                            <div class="card-body px-6 pb-6">

                                <div class="card border border-red-400 p-3">
                                    <div class="card-body">
                                        <p class="text-red-500" style="font-size:20px">1. Bạn không thể thay đổi thông tin sau khi mua</p>
                                        <p class="text-red-500" style="font-size:20px">2. Tên miền sẽ được hoàn tiền nếu trong quá trình đăng ký gặp lỗi</p>
                                        <p class="text-red-500" style="font-size:20px">3. Mỗi nameserver được nhập trên 1 dòng, khuyến khích dùng nameserver của <b style="color: black">cloudflare</b></p>
                                        <p class="text-red-500" style="font-size:20px">4. Bạn có thể gia hạn tên miền sau khi hết hạn</p>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                    <?php if(isset($_COOKIE['token'])) { ?>
                    <div id="app" class="card">
                        <header class="card-header noborder">
                            <h4 class="card-title">Lịch sử mua tên miền</h4>
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
                                                                    <th class="ant-table-cell" colstart="2" colend="2">Tên miền</th>
                                                                    <th class="ant-table-cell" colstart="3" colend="3">Thời hạn</th>
                                                                    <th class="ant-table-cell" colstart="4" colend="4">Ngày mua </th>
                                                                    <th class="ant-table-cell" colstart="5" colend="5">Ngày hết</th>
                                                                    <th class="ant-table-cell" colstart="7" colend="7">Trạng thái</th>
                                                                    <th class="ant-table-cell" colstart="8" colend="8">Thao tác</th>

                                                                </tr>
                                                            </thead>
                                                            <tbody class="ant-table-tbody">
                                                                <?php $i = 0; foreach($TUANORI->get_list(" SELECT * FROM `lichsumuamien` WHERE `username` = '".$getUser['username']."' ORDER BY id DESC") as $row){ ?>
                                                                    <tr class="ant-table-row ant-table-row-level-0">
                                                                        <td class="ant-table-cell"><?=++$i;?></td>
                                                                        <td class="ant-table-cell"><a target="_blank" style="color: green" href="//<?=$row['domain'];?>"><b><?=$row['domain'];?></b></a></td>
                                                                        <td class="ant-table-cell"><b style="color: red"><?=$row['thoihan'];?></b> năm</td>
                                                                        <td class="ant-table-cell"><?=$row['timemua'];?></td>
                                                                        <td class="ant-table-cell">
                                                                            <?php if(strtotime($row['timedie']) < 0) {
                                                                                echo '<b style="color: red">Chưa bắt đầu</b>';
                                                                            } else {
                                                                                echo $row['timedie'];
                                                                            }
                                                                            ?>
                                                                        </td>
                                                                        <td class="ant-table-cell"><?=status($row['status']);?></td>
                                                                        <td class="ant-table-cell">
                                                                            <a href="/QuanLy/TenMien/<?=$row['id'];?>">
                                                                                <button class="btn btn-outline-primary btn-sm">
                                                                                    <div class="flex justify-between font-bold">
                                                                                        <span><i class="fa-solid fa-pen-to-square"></i> Edit</span>
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
                    <?php } ?>
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
