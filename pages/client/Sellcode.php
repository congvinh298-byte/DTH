
<?php
/*MÃ NGUỒN NÀY ĐƯỢC PHÁT TRIỂN BỞI TUANORI - ZALO: 0812665001*/
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Đăng bán code";
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
                    <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                        <div class="card">
                            <div class="card-body flex flex-col p-6">
                                <header class="-mx-6 mb-5 flex items-center border-b border-slate-100 px-6 pb-5 dark:border-slate-700">
                                    <div class="flex-1">
                                        <div class="card-title text-slate-900 dark:text-white">Thông Tin Sản Phẩm</div>
                                        </div>
                                    </header>
                                <div class="card-text h-full space-y-4">
                                <?php if($getUser['verify'] != 1)  { ?>
                                    <div class="alert alert-danger" role="alert">Vui lòng <a href="/Verify" style="color: blue">xác minh</a> tài khoản để đăng bán code.</div>
                                <?php } ?> 
                                    <form method="POST" class="space-y-3">
                                    <div class="input-area">
                                        <label for="name" class="form-label">Tên mã nguồn</label>
                                        <input type="text" class="form-control  py-2" id="name"  placeholder="Nhập tên khái quát cho mã nguồn" required="">
                                    </div>
                                    <div class="input-area">
                                        <label for="mota" class="form-label">Mô tả mã nguồn</label>
                                        <input type="text" class="form-control  py-2" id="mota" placeholder="Nhập mô tả cho mã nguồn" required="">
                                    </div>
                                    <div class="input-area">
                                        <label for="mota" class="form-label">Chủ đề mã nguồn</label>
                                        <select class="form-control" id="chude" required>
                                            <option value="">Chọn chủ đề</option>
                                            <?php foreach($TUANORI->get_list(" SELECT * FROM `danhmucmuacode` WHERE `status` = 'SHOW' ") as $row){ ?>
                                                <option value="<?=$row['id'];?>"><?=$row['title'];?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                    <div class="input-area">
                                        <label for="link" class="form-label">Link download</label>
                                        <input type="text" class="form-control  py-2" id="link" placeholder="Link driver google (Chế độ công khai)" required="">
                                    </div>
                                    <div class="input-area">
                                        <label for="sotien" class="form-label">Số tiền </label>
                                        <input type="number" class="form-control  py-2" id="sotien" placeholder="Số tiền" required="">
                                        <i>Bạn sẽ chỉ nhận được <?=(100-$TUANORI->site('ptpartner'));?>% số tiền khi có khách mua</i>
                                    </div>
                                    <div class="input-area">
                                        <label for="img_c" class="form-label">Link ảnh đại diện</label>
                                        <input type="text" class="form-control  py-2" id="img_c" placeholder="Link ảnh đại diện" required="">
                                    </div>
                                    <div class="input-area">
                                        <label for="img" class="form-label">List ảnh mô tả code</label>
                                        <textarea type="text" class="form-control  py-2" id="img" placeholder="Mỗi link ảnh trên 1 dòng" rows="4" required=""></textarea>
                                    </div>
                                    <div class="input-area">
                                        <label for="nd" class="form-label">Cách cài đặt mã nguồn</label>
                                        <textarea type="text" class="form-control  py-2" id="nd" placeholder="Cách cài đặt mã nguồn" rows="4" required=""></textarea>
                                    </div>
                                    <div class="input-area">
                                        <label for="" class="form-label">Số Zalo (Liên hệ nếu cần)</label>
                                        <input type="text" class="form-control  py-2" value="<?=$getUser['zalo'];?>" disabled>
                                        <i>Cập nhật lại số zalo trong phần "<a href="/Profile" style="font-weight:bold;">Thông tin</a>"</i>
                                    </div>
                                    <div class="checkbox-area">
                                        <label class="inline-flex cursor-pointer items-center" for="check">
                                            <input type="checkbox" class="hidden" name="check" id="check">
                                            <span class="relative inline-flex h-4 w-4 flex-none rounded border border-slate-100 bg-slate-100 transition-all duration-150 ltr:mr-3 rtl:ml-3 dark:border-slate-800 dark:bg-slate-900">
                                            <img src="/images/svg/ck-white.svg" alt="" class="m-auto block h-[10px] w-[10px] opacity-0"></span>
                                            <span class="text-sm leading-6 text-slate-500 dark:text-slate-400">Xác nhận rằng thông tin trên đã tuân thủ Nội Quy</span>
                                        </label>
                                    </div>
                                    <div class="input-area">
                                        <button type="button" id = "XacNhan" class="btn btn-primary w-full"><i class="fas fa-share"></i> Xác nhận đăng bán</button>
                                    </div>
                                </form>

                                </div>
                            </div>
                        </div>
                        <script type="text/javascript">
                        $("#XacNhan").on("click", function() {

                            $('#XacNhan').html('Đang xử lý...').prop('disabled',
                                true);
                                var isChecked = $("#check").prop("checked") ? 1 : 0;
                            $.ajax({
                                url: "<?=BASE_URL('controller/partner/Sellcode.php');?>",
                                method: "POST",
                                data: {
                                    name: $("#name").val(),
                                    mota: $("#mota").val(),
                                    sotien: $("#sotien").val(),
                                    img: $("#img").val(),
                                    img_c: $("#img_c").val(),
                                    link: $("#link").val(),
                                    chude: $("#chude").val(),
                                    nd: $("#nd").val(),
                                    check: isChecked

                                },
                                success: function(response) {
                                    $("#thongbao").html(response);
                                    $('#XacNhan').html(
                                            '<i class="fas fa-share"></i> Xác nhận đăng bán')
                                        .prop('disabled', false);
                                }
                            });
                        });
                        </script>
                        <div class="card">
                            <header class="card-header noborder">
                                <h4 class="card-title">Lưu ý khi đăng</h4>
                            </header>
                            <div class="card-body px-6 pb-6">
                                <div class="card border border-red-400 p-3">
                                    <div class="card-body">
                                        <p class="text-red-500 text-xl">1. Đăng bán phải đảm bảo các yếu tố</p>
                                        <p>1.1 Ảnh mô tả code phải giống với code sau khi được cài đặt xong</p>
                                        <p>1.2 Số tiền bán phải phù hợp với mã nguồn</p>
                                        <p>1.3 Mã nguồn không được trùng với sản phẩm đã có trước đó</p>
                                        <p>1.4 Link download code phải là driver google (để ở chế độ công khai)</p>
                                        <p>1.5 Tên, mô tả phải phù hợp với mã nguồn đang bán</p>
                                        <p>1.6 Hướng dẫn cài đặt rõ ràng, có thể là link video hd cài đặt</p>

                                        <p class="text-red-500 text-xl">2. Sau khi đăng bán</p>
                                        <p>2.1 Chúng tôi có thể thay đổi lại tên, mô tả, giá bán nếu cảm thấy chưa phù hợp</p>
                                        <p>2.2 Bạn sẽ bị <b>chấm dứt hoạt động</b> nếu đăng mã nguồn không đúng hoặc vi phạm pháp luật</p>
                                        <p>2.3 Nếu được phê duyệt, mã nguồn của bạn sẽ được lên sàn</p>
                                        <p>2.4 Bạn sẽ nhận được <?=(100-$TUANORI->site('ptpartner'));?>% số tiền khi có khách hàng thanh toán</p>
                                        <p>2.5 Mã nguồn của bạn sẽ được gỡ xuống nếu phát hiện khiếu nại đúng</p>
                                        <p class="text-red-500 text-xl">Sau khi <b style="color: green">xác minh tài khoản</b> xong, bạn sẽ có trang quản lý riêng cho cộng tác viên bán mã nguồn. </p>

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