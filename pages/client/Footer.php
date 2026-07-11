<?php
  if (!defined('IN_SITE')) die('The Request Not Found');
?>
<script>
    function updateTextView(_obj){
        var num = getNumber(_obj.val());
        if(num==0){
            _obj.val('');
        }else{
            _obj.val(num.toLocaleString());
        }
    }
    function getNumber(_str){
        var arr = _str.split('');
        var out = new Array();
        for(var cnt=0;cnt<arr.length;cnt++){
            if(isNaN(arr[cnt])==false){
                out.push(arr[cnt]);
            }
        }
        return Number(out.join(''));
    }
    $(document).ready(function(){
        $('.fnum').on('keyup',function(){
            updateTextView($(this));
        });
    });
</script>

<div id="thongbao"></div>
<button type="button" data-te-ripple-init data-te-ripple-color="light" class="!fixed bottom-5 right-5 hidden rounded-full bg-blue-600 p-3 text-xs font-medium uppercase leading-tight text-white shadow-md transition duration-150 ease-in-out hover:bg-blue-700 hover:shadow-lg focus:bg-blue-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-blue-800 active:shadow-lg z-[9999]" id="btn-back-to-top">
<svg aria-hidden="true" focusable="false" data-prefix="fas" class="h-4 w-4" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
    <path fill="currentColor" d="M34.9 289.5l-22.2-22.2c-9.4-9.4-9.4-24.6 0-33.9L207 39c9.4-9.4 24.6-9.4 33.9 0l194.3 194.3c9.4 9.4 9.4 24.6 0 33.9L413 289.4c-9.5 9.5-25 9.3-34.3-.4L264 168.6V456c0 13.3-10.7 24-24 24h-32c-13.3 0-24-10.7-24-24V168.6L69.2 289.1c-9.3 9.8-24.8 10-34.3.4z">
    </path>
</svg>
</button>

<!-- BEGIN: Footer (Chuẩn Pháp Lý - Bộ Công Thương) -->
<footer class="bg-gray-900 text-gray-300 pt-16 pb-8 border-t-[8px] border-blue-600">
    <div class="container mx-auto px-4 max-w-7xl">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-12">
            
            <!-- Cột 1: Thông Tin Công Ty Pháp Lý (Thực tế từ Giấy ĐKDN) -->
            <div class="space-y-4">
                <a href="/"><img src="/logo.png" alt="Công ty TNHH MTV Điện Tử Hiếu" class="h-16 rounded-xl bg-white p-1 mb-4 hover:opacity-90 transition-opacity"></a>
                <h3 class="text-lg font-black text-white uppercase tracking-tight leading-tight">CÔNG TY TNHH MTV ĐIỆN TỬ HIẾU</h3>
                <div class="text-xs text-gray-400 space-y-2 leading-relaxed">
                    <p class="flex gap-2"><i class="fa fa-id-badge text-blue-400 w-4 text-center mt-0.5 flex-shrink-0"></i><span><strong class="text-gray-300">Mã số DN:</strong> 1402228630</span></p>
                    <p class="flex gap-2"><i class="fa fa-calendar-check text-blue-400 w-4 text-center mt-0.5 flex-shrink-0"></i><span><strong class="text-gray-300">Đăng ký lần đầu:</strong> 06/04/2026</span></p>
                    <p class="flex gap-2"><i class="fa fa-university text-blue-400 w-4 text-center mt-0.5 flex-shrink-0"></i><span><strong class="text-gray-300">Nơi cấp:</strong> Sở KH&ĐT Tỉnh Đồng Tháp</span></p>
                    <p class="flex gap-2"><i class="fa fa-user-tie text-orange-400 w-4 text-center mt-0.5 flex-shrink-0"></i><span><strong class="text-gray-300">Giám đốc:</strong> TRẦN CÔNG VINH</span></p>
                    <p class="flex gap-2"><i class="fa fa-map-marker-alt text-red-400 w-4 text-center mt-0.5 flex-shrink-0"></i><span><strong class="text-gray-300">Trụ sở:</strong> Số 166 Ấp Bình Thạnh 1, Xã Lấp Vò, Tỉnh Đồng Tháp</span></p>
                    <p class="flex gap-2"><i class="fa fa-phone text-green-400 w-4 text-center mt-0.5 flex-shrink-0"></i><span><a href="tel:0939354937" class="text-green-400 font-black hover:text-green-300">0939.354.937</a></span></p>
                    <p class="flex gap-2"><i class="fa fa-piggy-bank text-yellow-400 w-4 text-center mt-0.5 flex-shrink-0"></i><span><strong class="text-gray-300">Vốn điều lệ:</strong> 30.000.000 VNĐ</span></p>
                </div>
            </div>
            
            <!-- Cột 2: Sản Phẩm & Dịch Vụ -->
            <div>
                <h3 class="text-lg font-black uppercase text-white mb-6 border-b-2 border-orange-500 pb-2 inline-block">Sản Phẩm & Dịch Vụ</h3>
                <ul class="space-y-3 font-bold text-sm">
                    <li><a href="/dien-may" class="hover:text-blue-400 transition-colors flex items-center"><i class="fa fa-angle-right mr-2 text-orange-500"></i> Phân phối Điện Máy Chính Hãng</a></li>
                    <li><a href="/sua-chua" class="hover:text-blue-400 transition-colors flex items-center"><i class="fa fa-angle-right mr-2 text-orange-500"></i> Sửa chữa Tủ Lạnh, Máy Lạnh</a></li>
                    <li><a href="/sua-chua" class="hover:text-blue-400 transition-colors flex items-center"><i class="fa fa-angle-right mr-2 text-orange-500"></i> Dịch vụ Vệ sinh Thiết Bị</a></li>
                    <li><a href="/in-3d" class="hover:text-blue-400 transition-colors flex items-center"><i class="fa fa-angle-right mr-2 text-orange-500"></i> Dịch vụ In 3D Độc Quyền</a></li>
                </ul>
            </div>
            
            <!-- Cột 3: Pháp Lý & Chính Sách -->
            <div>
                <h3 class="text-lg font-black uppercase text-white mb-6 border-b-2 border-orange-500 pb-2 inline-block">Chính Sách & Quy Chế</h3>
                <ul class="space-y-3 font-bold text-sm">
                    <li><a href="#" class="hover:text-blue-400 transition-colors flex items-center"><i class="fa fa-shield-alt mr-2 text-orange-500"></i> Chính sách bảo mật thông tin</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition-colors flex items-center"><i class="fa fa-file-contract mr-2 text-orange-500"></i> Điều khoản sử dụng dịch vụ</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition-colors flex items-center"><i class="fa fa-gavel mr-2 text-orange-500"></i> Quy chế hoạt động Website</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition-colors flex items-center"><i class="fa fa-undo-alt mr-2 text-orange-500"></i> Chính sách bảo hành & đổi trả</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition-colors flex items-center"><i class="fa fa-truck mr-2 text-orange-500"></i> Quy trình giao nhận & thanh toán</a></li>
                    <li><a href="#" class="hover:text-blue-400 transition-colors flex items-center"><i class="fa fa-book mr-2 text-orange-500"></i> Đề án kinh doanh</a></li>
                </ul>
            </div>

            <!-- Cột 4: Liên Hệ & Chứng Nhận -->
            <div>
                <h3 class="text-lg font-black uppercase text-white mb-6 border-b-2 border-orange-500 pb-2 inline-block">Tổng Đài Hỗ Trợ</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-4 bg-gray-800 p-3 rounded-xl border border-gray-700">
                        <div class="w-10 h-10 bg-orange-500 rounded-full flex items-center justify-center text-white font-bold animate-pulse shadow-[0_0_15px_rgba(255,94,0,0.5)]">
                            <i class="fa fa-phone-alt"></i>
                        </div>
                        <div>
                            <div class="text-xs uppercase font-bold text-gray-400">Hotline Kỹ Thuật (6:00 - 22:00)</div>
                            <a href="tel:0939354937" class="text-lg font-black text-white hover:text-orange-400">0939.354.937</a>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-4 bg-gray-800 p-3 rounded-xl border border-gray-700">
                        <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">
                            <i class="fa fa-comment-dots"></i>
                        </div>
                        <div>
                            <div class="text-xs uppercase font-bold text-gray-400">Zalo CSKH & Đặt Hàng</div>
                            <a href="https://zalo.me/<?=$DMH->site('zaloadmin');?>" target="_blank" class="text-lg font-black text-white hover:text-blue-400"><?=$DMH->site('zaloadmin');?></a>
                        </div>
                    </div>
                    
                    <!-- BCT Mockup -->
                    <div class="pt-4 flex gap-4 items-center">
                        <a href="http://online.gov.vn" target="_blank" title="Đã thông báo Bộ Công Thương">
                            <img src="https://i.imgur.com/B9B1z2L.png" alt="Đã thông báo Bộ Công Thương" class="h-10 opacity-80 hover:opacity-100 transition-opacity drop-shadow-md">
                        </a>
                        <img src="https://images.dmca.com/Badges/dmca-badge-w150-5x1-07.png?ID=dummy" alt="DMCA.com Protection Status" class="h-6 opacity-80 hover:opacity-100 transition-opacity">
                    </div>
                </div>
            </div>

        </div>

        <div class="border-t border-gray-800 pt-6 flex flex-col md:flex-row items-center justify-between text-xs font-medium text-gray-500">
            <p>&copy; <?=date('Y');?> Bản quyền thuộc về <strong>CÔNG TY TNHH MTV ĐIỆN TỬ HIẾU</strong>. Bảo lưu mọi quyền lợi.</p>
            <p class="mt-2 md:mt-0">Thiết kế & Vận hành bởi <span class="text-blue-500 font-bold">Hệ Sinh Thái DTH</span>.</p>
        </div>
    </div>
</footer>
<!-- END: Footer -->

    </div> <!-- End .app-wrapper from Header -->
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.min.js"></script>
    <script src="/assets/js/toastr.js"></script>

    <script>
    function showToast(message, type) {
        toastr.options = {
            positionClass: 'toast-top-right',
            progressBar: true 
        };
        if (type === 'success') {
            toastr.success(message);
        } else if (type === 'warning') {
            toastr.warning(message);
        } else if (type === 'error') {
            toastr.error(message);
        }
    }
    
    new ClipboardJS('.copy');
    
    const mybutton = document.getElementById("btn-back-to-top");
    const scrollFunction = () => {
        if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
            mybutton.classList.remove("hidden");
        } else {
            mybutton.classList.add("hidden");
        }
    };
    const backToTop = () => {
        window.scrollTo({ top: 0, behavior: "smooth" });
    };
    
    mybutton.addEventListener("click", backToTop);
    window.addEventListener("scroll", scrollFunction);
    </script>
</body>
</html>
