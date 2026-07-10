<?php

define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$id = check_string($_GET['id']);
$tt = $DMH->get_row(" SELECT * FROM `danhsachtaoweb` WHERE `id` = '$id' AND `hienthi` = 'SHOW'");
$title = '['.strtoupper($_SERVER['SERVER_NAME']).'] '.$tt['title'];
$mota = $tt['mota'];
$anhbia = $tt['img'];
require_once("../../pages/client/Head.php");
require_once("../../pages/client/Header.php");
if(!$tt)
{
	msg_error('Link bạn truy cập không đúng hoặc đã bị xóa', BASE_URL(''), 3000);
}
if($DMH->site('sukien') == 'ON') {
    $tongtien = $tt['money'] - ($tt['money'] * $DMH->site('ptgiamgia') / 100);
}
else {
    $tongtien = $tt['money'];
}
?>
<div class="content-wrapper transition-all duration-150 ltr:ml-0 rtl:mr-0 xl:ltr:ml-[248px] xl:rtl:mr-[248px]" id="content_wrapper">
    <div class="page-content">
        <div class="container-fluid transition-all duration-150" id="page_layout">
            <main id="content_layout">
                <!-- Page Content -->
                <div class="mb-3">
                </div>

                <script>
                    const ITEM_DATA = {
                        "code": "80244179"
                    };
                </script>
                <section class="space-y-6">
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            <div class="card">
                                <div class="card-body grid grid-cols-1 gap-x-6 p-6 md:grid-cols-6">
                                    <div class="ant-ribbon-wrapper css-eq3tly">
                                        <div class="mb-5 md:mb-0" >
                                            <img src="<?=$tt['img']?>" class="h-[200px] w-full cursor-pointer rounded-lg md:h-[300px]" alt="<?=$tt['title']?>">
                                        </div>
                                        <div class="ant-ribbon ant-ribbon-placement-end ant-ribbon-color-black css-eq3tly"><span class="ant-ribbon-text">Mã: <?=$tt['id'];?></span>
                                            <div class="ant-ribbon-corner"></div>
                                        </div>
                                        <?php if($DMH->site('sukien') == 'ON' && $DMH->site('ptgiamgiaweb') > 0) { ?>
                                            <div class="ant-ribbon ant-ribbon-placement-start ant-ribbon-color-red css-eq3tly"><span class="ant-ribbon-text">-<?=$DMH->site('ptgiamgiaweb');?>%</span>
                                                <div class="ant-ribbon-corner"></div>
                                            </div>
                                        <?php } ?>
                                    </div>
                              </div>
                              <ul class="nav nav-pills flex items-center flex-wrap list-none pl-0 mb-6 space-x-4 justify-center" id="pills-tabHorizontal" role="tablist">
                                  <li class="nav-item text-center" role="presentation">
                                      <a href="#pills-infomation" class="nav-link block font-medium font-Inter text-sm leading-tight capitalize rounded-md px-6 py-3 focus:outline-none focus:ring-0 active dark:bg-slate-900 dark:text-slate-300">
                                        <i class="fas fa-eye me-2"></i>Xem Demo</a>
                                  </li> 
                                  <li class="nav-item text-center" role="presentation">
                                      <a href="https://youtu.be/m0YDSfyZMDI?si=J9oFtOfUI06he-Dy" target="_blank" class="nav-link block font-medium font-Inter text-sm leading-tight capitalize rounded-md px-6 py-3 focus:outline-none focus:ring-0 active dark:bg-slate-900 dark:text-slate-300" >
                                        <i class="fas fa-question me-2"></i>Cách tạo website</a>
                                  </li>
                              </ul>
                          </div>

                          <div class="card">
                              <header class="card-header noborder">
                                  <h4 class="card-title">Thông tin khởi tạo <span class="text-red-500">[Mã mẫu #<?=$tt['id'];?>]</span></h4>
                              </header>
                              <div class="card-body px-6 pb-6">
                                  <form method="POST" class="space-y-3">
                                      <div class="input-area">
                                          <label for="taikhoan" class="form-label">Tài khoản Admin</label>
                                          <input type="text" class="form-control  py-2" id="taikhoan" placeholder="Dùng để quản trị website" required="">
                                      </div>
                                      <div class="input-area">
                                          <label for="matkhau" class="form-label">Mật khẩu Admin</label>
                                          <input type="password" class="form-control  py-2" id="matkhau" placeholder="Dùng để quản trị website" required="">
                                      </div>
                                      <div class="input-area">
                                          <label for="tenmien" class="form-label">Tên miền</label>
                                          <input type="text" class="form-control  py-2" id="tenmien"  placeholder="Nhập tên miền website" required="">
                                      </div>
                                      <div class="input-area">
                                          <label for="magiamgia" class="form-label">Mã giảm giá</label>
                                          <input type="text" class="form-control  py-2" id="magiamgia"  placeholder="Bỏ qua nếu không có" required="">
                                      </div>
                                      <div class="text-center">
                                        <h2 class="text-sm text-primary">Giá: 
                                            <?php if($DMH->site('sukien') == 'ON' && $DMH->site('ptgiamgiaweb') > 0 && $tt['money'] > 0) { ?> 
                                                <del><b style="color: red"><?=sotienmua($tt['money']);?></b></del> - <b style="color: green"><?=sotienmua($tt['money'] - $tt['money']*$DMH->site('ptgiamgiaweb')/100);?></b>
                                            <?php } else { ?>
                                                <b style="color: green"><?=sotienmua($tt['money']);?></b>
                                            <?php } ?>
                                        </h2>
                                      </div>
                                      <div class="input-area">
                                          <button type="button" id = "Taoweb" class="btn btn-primary w-full"><i class="fas fa-shopping-cart me-2"></i> Tạo ngay</button>
                                      </div>
                                  </form>
                              </div>
                          </div>
                      </div>
                      <script type="text/javascript">
                          $("#Taoweb").on("click", function() {

                              $('#Taoweb').html(' Đang xử lý...').prop('disabled',
                                  true);
                              $.ajax({
                                  url: "<?=BASE_URL('controller/client/Muahang.php');?>",
                                  method: "POST",
                                  data: {
                                      type: 'Taoweb',
                                      id:	'<?=$id;?>',
                                      taikhoan: $("#taikhoan").val(),
                                      matkhau: $("#matkhau").val(),
                                      tenmien: $("#tenmien").val(),
                                      magiamgia: $("#magiamgia").val()
                                  },
                                  success: function(response) {
                                      $("#thongbao").html(response);
                                      $('#Taoweb').html(
                                              '<i class="fas fa-shopping-cart me-2"></i> Tạo Ngay')
                                          .prop('disabled', false);
                                  }
                              });
                          });
                        </script>
                    <div>
                        <div class="card">
                            <div class="card-body flex flex-col p-6">
                                <header class="-mx-6 mb-5 flex items-center border-b border-slate-100 px-6 pb-5 dark:border-slate-700">
                                    <div class="flex-1">
                                        <div class="card-title text-slate-900 dark:text-white">Thông tin chi tiết:</div>
                                    </div>
                                </header>
                                <div class="card-text h-full">
                                    <div>
                                        <div class="text-center">
                                            <ul class="nav nav-pills flex items-center flex-wrap list-none pl-0 mb-6 space-x-4 justify-center" id="pills-tabHorizontal" role="tablist">
                                                <li class="nav-item text-center" role="presentation">
                                                    <a href="<?=$DMH->site('fbadmin');?>" target="_blank" class="nav-link block font-medium font-Inter text-sm leading-tight capitalize rounded-md px-6 py-3 focus:outline-none focus:ring-0 active dark:bg-slate-900 dark:text-slate-300">
                                                        <i class="fa-brands fa-facebook"></i> FB ADMIN </a>
                                                </li> 
                                                <li class="nav-item text-center" role="presentation">
                                                    <a href="https://zalo.me/<?=$DMH->site('zaloadmin');?>" target="_blank"  class="nav-link block font-medium font-Inter text-sm leading-tight capitalize rounded-md px-6 py-3 focus:outline-none focus:ring-0 active dark:bg-slate-900 dark:text-slate-300">
                                                        <i class="fab fa-facebook-messenger"></i> Zalo ADMIN</a>
                                                </li> 
                                            </ul>
                                        </div>
                                        <div class="tab-content" id="pills-tabContentHorizontal">
                                            <div class="tab-pane fade show active" id="pills-infomation" role="tabpanel" aria-labelledby="pills-home-tabHorizontal">
                                                <div class="space-y-5">
                                                    <div class="card border border-red-400 p-3">
                                                        <div class="card-body">
                                                            <p style="font-size: 15px" class="py-1">+ CAM KẾT WEBSITE NHƯ ẢNH 100%</p>
                                                            <p style="font-size: 15px" class="py-1">+ HỖ TRỢ CÀI ĐẶT WEBSITE TỪ A-Z</p>
                                                            <p style="font-size: 15px" class="py-1">+ HỖ TRỢ TÍCH HỢP NẠP TIỀN TỰ ĐỘNG (NẾU CÓ)</p>
                                                        </div>
                                                    </div>
                                                    <div class="card border border-red-400 p-3">
                                                        <div class="card-body">
                                                            <p style="font-size:20px; color: black">Tên: <?=$tt['title'];?></p>
                                                        </div>
                                                    </div>
                                                    <div class="card border border-red-400 p-3">
                                                        <div class="card-body">
                                                            <p style="font-size:15px; color: black">Mô tả: <?=$tt['mota'];?></p>
                                                        </div>
                                                    </div>

                                                    <div class="grid grid-cols-1 gap-3">
                                                      <?php foreach(explode("\n", $tt['listimg']) as $ok) { ?>
                                                        <div class="gallery cursor-pointer">
                                                            <a href="<?=$ok;?>" class="glightbox" >
                                                                <img class="w-full h-full rounded-sm lazyload" src="/images/svg/spinner.svg" data-src="<?=$ok;?>" alt="<?=$tt['title'];?>">
                                                            </a>
                                                            
                                                        </div>
                                                        
                                                        <?php } ?>
                                                        
                                                    </div>
                                                   
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="fullpage" onclick="this.style.display='none';"></div>
                </section>
            </main>
        </div>
    </div>
</div>
</div>
<?php

require_once("../../pages/client/Footer.php");
?>

