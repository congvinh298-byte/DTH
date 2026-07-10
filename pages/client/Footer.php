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

<!-- BEGIN: Footer -->
<footer class="bg-gray-900 text-white pt-16 pb-8 border-t-[8px] border-orange-500 mt-20 relative overflow-hidden">
    <!-- Background Decor -->
    <div class="absolute top-0 right-0 w-64 h-64 bg-blue-600 rounded-full blur-[100px] opacity-20 -mr-20 -mt-20"></div>
    <div class="absolute bottom-0 left-0 w-96 h-96 bg-orange-600 rounded-full blur-[150px] opacity-20 -ml-20 -mb-20"></div>

    <div class="container mx-auto px-4 max-w-7xl relative z-10">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-12 mb-12">
            
            <!-- Company Info (OCD: Aligned perfectly) -->
            <div class="col-span-1 md:col-span-5 flex flex-col items-center md:items-start text-center md:text-left space-y-6">
                <a href="/">
                    <img src="<?=$DMH->site('logo');?>" alt="Điện Máy Hiếu" class="max-w-[200px] drop-shadow-xl filter brightness-0 invert">
                </a>
                <p class="text-gray-400 font-medium leading-relaxed max-w-md">
                    Hệ thống bán lẻ và sửa chữa thiết bị Điện Máy, Điện Lạnh, Điện Tử uy tín số 1 tại khu vực miền Tây. Uy Tín - Tận Tâm - Chuyên Nghiệp.
                </p>
            </div>
            
            <!-- Contact (ADHD: Big typography) -->
            <div class="col-span-1 md:col-span-4 space-y-6">
                <h3 class="text-2xl font-black uppercase text-orange-400 border-b-2 border-gray-800 pb-3 inline-block">Liên Hệ Khẩn Cấp</h3>
                <ul class="space-y-4 font-bold text-gray-300">
                    <li class="flex items-start gap-4">
                        <i class="fa fa-map-marker-alt text-orange-500 text-xl mt-1"></i>
                        <span>Trụ sở chính:<br/><span class="text-white text-lg">Lấp Vò, Đồng Tháp</span></span>
                    </li>
                    <li class="flex items-start gap-4">
                        <i class="fa fa-phone-alt text-orange-500 text-xl mt-1 animate-pulse"></i>
                        <span>Hotline Hỗ Trợ 24/7:<br/><a href="tel:09xxxxxxx" class="text-orange-400 text-2xl font-black hover:text-white transition-colors">09xx.xxx.xxx</a></span>
                    </li>
                    <li class="flex items-start gap-4">
                        <i class="fa fa-clock text-orange-500 text-xl mt-1"></i>
                        <span>Giờ phục vụ:<br/><span class="text-white">6:00 - 22:00 (Kể cả Chủ Nhật)</span></span>
                    </li>
                </ul>
            </div>
            
            <!-- Social / Quick Links -->
            <div class="col-span-1 md:col-span-3 space-y-6">
                <h3 class="text-2xl font-black uppercase text-blue-400 border-b-2 border-gray-800 pb-3 inline-block">Kết Nối</h3>
                <div class="flex gap-4">
                    <a href="<?=$DMH->site('fbadmin');?>" target="_blank" class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center text-white text-xl hover:bg-blue-600 transform hover:scale-110 transition-all shadow-lg">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="tel:<?=$DMH->site('zaloadmin');?>" class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center text-white text-xl hover:bg-blue-500 transform hover:scale-110 transition-all shadow-lg">
                        <i class="fa-solid fa-comment-dots"></i>
                    </a>
                    <a href="tel:09xxxxxxx" class="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center text-white text-xl hover:bg-orange-500 transform hover:scale-110 transition-all shadow-lg">
                        <i class="fa-solid fa-phone"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row items-center justify-between text-gray-500 font-medium">
            <p>&copy; 2026 Điện Máy Hiếu. All rights reserved.</p>
            <p class="mt-2 md:mt-0">Thiết kế chuẩn mực OCD-ADHD.</p>
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
