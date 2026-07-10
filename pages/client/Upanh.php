<?php

define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Up ảnh lấy link";
require_once("../../pages/client/Head.php");
require_once("../../pages/client/Header.php");
$arr = [];
?>
<?php if(isset($_POST['Upload'])) {
    $images_report    = $_FILES['images_report']['name'];
    for ($i = 0; $i < count($images_report); $i++) {
        $check_images_report = strtolower(pathinfo(basename($images_report[$i]), PATHINFO_EXTENSION));
        if ($check_images_report != "jpg" && $check_images_report != "png" && $check_images_report != "jpeg" && $check_images_report != "gif") {
        } else {
            $resultCreate       = upload_imgur($_FILES['images_report']['tmp_name'][$i]);
            $resultCreateDecode = json_decode($resultCreate, true);
            array_push($arr, $resultCreateDecode['data']['link']);
        }
    }
} ?>
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
                                        <div class="card-title text-slate-900 dark:text-white">Up ảnh lấy link</div>
                                    </div>
                                </header>
                                <div class="card-text h-full space-y-4">
                                  <form method="POST" class="space-y-3" enctype="multipart/form-data">
                                      <div class="input-area">
                                          <label for="ten" class="form-label">Chọn ảnh để chuyển đổi</label>
                                          <input type="file" class="form-control  py-2" id="images_report" name="images_report[]" required="" multiple accept="image/*">
                                      </div>
                                      <div class="input-area">
                                            <button type="submit" name ="Upload" class="btn btn-sm btn-primary w-full">Tải lên</button>
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
                                <h4 class="card-title">Hình ảnh</h4>
                            </header>
                            <div class="card-body px-6 pb-6">
                                <div class="card border border-red-400 p-3">
                                    <div class="card-body">
                                        <?php foreach($arr as $rs) { ?>
                                            <p class="text-red-500"><?=$rs;?> </p>
                                        <?php } ?>

                                    </div>
                                </div>
                                <!-- <form action="https://shopnickv3.baocms.net/account/profile/update-password" method="POST" class="space-y-3">
                                    <input type="hidden" name="_token" value="XVlySc4KMk22GPX3nKRoqjqRopvLnwv13uicJNyW" autocomplete="off">
                                    <div class="input-area">
                                        <label for="old_password" class="form-label">Mật Khẩu Cũ</label>
                                        <input type="password" class="form-control  py-2" id="old_password" name="old_password" placeholder="Nhập mật khẩu cũ" required>
                                    </div>
                                    <div class="input-area">
                                        <label for="new_password" class="form-label">Mật Khẩu Mới</label>
                                        <input type="password" class="form-control  py-2" id="new_password" name="new_password" placeholder="Nhập mật khẩu mới" required>
                                    </div>
                                    <div class="input-area">
                                        <label for="confirm_password" class="form-label">Xác Nhận Mật Khẩu</label>
                                        <input type="password" class="form-control  py-2" id="confirm_password" name="confirm_password" placeholder="Nhập lại mật khẩu mới" required>
                                    </div>
                                    <div class="input-area">
                                        <button type="submit" class="btn btn-sm btn-primary w-full">Đổi Mật Khẩu</button>
                                    </div>
                                </form> -->
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
