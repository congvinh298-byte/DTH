
<?php

define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");
$title = "Xác minh tài khoản";
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
                                        <div class="card-title text-slate-900 dark:text-white">Xác minh tài khoản</div>
                                        </div>
                                    </header>
                                <div class="card-text h-full space-y-4">
                                <?php if($getUser['verify'] == 1)  { ?>
                                    <div class="alert alert-success" role="alert">Tài khoản của bạn đã được xác minh.</div>
                                <?php } else { ?> 
                                <form method="POST" class="space-y-3">
                                    <div class="input-area">
                                        <label for="name" class="form-label">Mặt trước CCCD</label>
                                        <input type="file" class="form-control  py-2" id="mattruoc" required="" accept="image/*">
                                    </div>
                                    <div class="input-area">
                                        <label for="mota" class="form-label">Mặt sau CCCD</label>
                                        <input type="file" class="form-control  py-2" id="matsau" required="" accept="image/*">
                                    </div>
                                    <div class="input-area">
                                        <label for="mota" class="form-label">Ảnh chân dung cầm giấy tờ</label>
                                        <input type="file" class="form-control  py-2" id="chandung" required="" accept="image/*">
                                    </div>
                                    
                                    <div class="checkbox-area">
                                        <label class="inline-flex cursor-pointer items-center" for="check">
                                            <input type="checkbox" class="hidden" name="check" id="check">
                                            <span class="relative inline-flex h-4 w-4 flex-none rounded border border-slate-100 bg-slate-100 transition-all duration-150 ltr:mr-3 rtl:ml-3 dark:border-slate-800 dark:bg-slate-900">
                                            <img src="/images/svg/ck-white.svg" alt="" class="m-auto block h-[10px] w-[10px] opacity-0"></span>
                                            <span class="text-sm leading-6 text-slate-500 dark:text-slate-400">Xác nhận đã gửi đúng thông tin.</span>
                                        </label>
                                    </div>
                                    <div class="input-area">
                                        <button type="button" id = "XacNhan" class="btn btn-primary w-full"><i class="fas fa-share"></i> Tải lên giấy tờ</button>
                                    </div>
                                </form>
                                <?php } ?>

                                </div>
                            </div>
                        </div>
                        <script type="text/javascript">
                            $("#XacNhan").on("click", function() {
                                $('#XacNhan').html('Đang xử lý...').prop('disabled', true);

                                var isChecked = $("#check").prop("checked") ? 1 : 0;

                                // Tạo đối tượng FormData để chứa dữ liệu tệp và giá trị của checkbox
                                var formData = new FormData();
                                formData.append('check', isChecked);
                                formData.append('file1', $('#mattruoc')[0].files[0]); // Tệp mặt trước CCCD
                                formData.append('file2', $('#matsau')[0].files[0]); // Tệp mặt sau CCCD
                                formData.append('file3', $('#chandung')[0].files[0]); // Tệp ảnh chân dung cầm giấy tờ
                                $.ajax({
                                    url: "<?=BASE_URL('controller/client/UploadHoSo.php');?>",
                                    method: "POST",
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    success: function(response) {
                                        $("#thongbao").html(response);
                                        $('#XacNhan').html('<i class="fas fa-share"></i> Tải lên giấy tờ').prop('disabled', false);
                                    }
                                });
                            });
                        </script>
                        <div class="card">
                            <header class="card-header noborder">
                                <h4 class="card-title">Lưu ý</h4>
                            </header>
                            <div class="card-body px-6 pb-6">
                                <div class="card border border-red-400 p-3">
                                    <div class="card-body">
                                        <?php if($getUser['total_money'] > 0) { ?>
                                            <p style="color: red">+ Để tránh SPAM, bạn vui lòng nạp ít nhất 1 đồng, để có thể gửi.</p>
                                        <?php } ?>
                                        <p>1. Chúng tôi không giới hạn độ tuổi (Chỉ cần có CCCD là được).</p>
                                        <p style="color: red">2. Chú ý mục "Ảnh Chân Dung Cầm Giấy Tờ".</p>
                                        <p>2.1. Chụp rõ mặt.</p>
                                        <p>2.1. Cầm thêm tờ giấy có ghi: Tên website, và ngày/tháng/năm.</p>
                                        <p><center><img src="/images/verify.png"></center></p>
                                        <p>VD Tên website: <b style="color: red"><?=strtoupper($_SERVER['SERVER_NAME']);?></b></p>
                                        <p>VD Ngày/tháng/năm: <b style="color: red"><?=date('d/m/Y');?></b></p>
                                        <p>3. Tất cả ảnh bạn gửi cho chúng tôi phải rõ nét, CCCD không bị mất góc.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr class="mb-2 mt-2 h-[10px]">
                    <div class="card">
                        <header class="card-header noborder">
                            <h4 class="card-title">Hồ sơ đã gửi của bạn</h4>
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
                                                                    <th class="ant-table-cell" colstart="2" colend="2">Username</th>
                                                                    <th class="ant-table-cell" colstart="3" colend="3">Thời gian nộp</th>
                                                                    <th class="ant-table-cell" colstart="4" colend="4">Ghi chú</th>
                                                                    <th class="ant-table-cell" colstart="5" colend="5">Trạng thái</th>
                                                                
                                                                </tr>
                                                            </thead>
                                                            <tbody class="ant-table-tbody">
                                                                <?php $i = 0; foreach($DMH->get_list(" SELECT * FROM `upload_hoso` WHERE `username` = '".$getUser['username']."' ORDER BY id DESC LIMIT 10") as $row){ ?>
                                                                    <tr class="ant-table-row ant-table-row-level-0">
                                                                        <td class="ant-table-cell"><?=++$i;?></td>
                                                                        <td class="ant-table-cell" style="color: green; font-weight: bold"><?=$row['username'];?></td>
                                                                        <td class="ant-table-cell"><?=$row['thoigian'];?></td>
                                                                        <td class="ant-table-cell"><?=inkq($row['note'], 'Chưa có');?></td>

                                                                        <td class="ant-table-cell"><?=hoso($row['status']);?></td>

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