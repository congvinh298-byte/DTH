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
<script src="https://js.pusher.com/3.2/pusher.min.js"></script>
<script type="text/javascript">
    var pusher = new Pusher('10d5ea7e7b632db09c72', {
        encrypted: true
    });
    var channel = pusher.subscribe('<?=($getUser['username']) ?? '';?>');
    channel.bind('realtime', function (data) {
        console.log(data.message);
        if(data.message) {
            if(data.type == 'success') {
            }
            Swal.fire('Thông báo', data.message, data.type);
        }
    });
</script>

<!-- BEGIN: footer -->
<!-- Back to top button -->
<div id="thongbao"></div>
<button type="button" data-te-ripple-init data-te-ripple-color="light" class="!fixed bottom-5 right-5 hidden rounded-full bg-primary p-3 mb-[40px] text-xs font-medium uppercase leading-tight text-white shadow-md transition duration-150 ease-in-out hover:bg-red-700 hover:shadow-lg focus:bg-red-700 focus:shadow-lg focus:outline-none focus:ring-0 active:bg-red-800 active:shadow-lg" id="btn-back-to-top">
<svg aria-hidden="true" focusable="false" data-prefix="fas" class="h-4 w-4" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512">
    <path fill="currentColor" d="M34.9 289.5l-22.2-22.2c-9.4-9.4-9.4-24.6 0-33.9L207 39c9.4-9.4 24.6-9.4 33.9 0l194.3 194.3c9.4 9.4 9.4 24.6 0 33.9L413 289.4c-9.5 9.5-25 9.3-34.3-.4L264 168.6V456c0 13.3-10.7 24-24 24h-32c-13.3 0-24-10.7-24-24V168.6L69.2 289.1c-9.3 9.8-24.8 10-34.3.4z">
    </path>
</svg>
</button>
<script src="/build/assets/glightbox.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.6/clipboard.min.js"></script>
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
function copy() {
    showToast("Đã sao chép thành công", "success");
}

const mybutton = document.getElementById("btn-back-to-top");



const scrollFunction = () => {
    if (
        document.body.scrollTop > 20 ||
        document.documentElement.scrollTop > 20
    ) {
        mybutton.classList.remove("hidden");
    } else {
        mybutton.classList.add("hidden");
    }
};
const backToTop = () => {
    window.scrollTo({
        top: 0,
        behavior: "smooth"
    });
};

mybutton.addEventListener("click", backToTop);

window.addEventListener("scroll", scrollFunction);
</script>
<!-- BEGIN: Footer For Desktop and tab -->
<footer id="footer" class="mb-[60px] md:mb-0">
<div class="py-3" style="background: #1b1a1a">
    <div class="relative mx-auto mt-2 grid w-full max-w-6xl grid-cols-2 gap-4 px-4 font-semibold text-white md:mb-0 md:px-0">
        
        <div class="col-span-2 py-2 md:col-span-1">
            <div class="flex flex-col items-center">
                <a href="/">
                    <img src="<?=$TUANORI->site('logo');?>" alt="<?=$TUANORI->site('title');?>" class="mb-2 max-w-[170px]">
                </a>
                <span class="text-center"><?=$TUANORI->site('mota');?></span>
            </div>
        </div>
        
        <div class="col-span-2 py-2 md:col-span-1">
            <span class="flex flex-col items-center"><h5 class="mb-6 text-white">LIÊN HỆ HỖ TRỢ</h5>
                <span class="text-center">Hỗ trợ khách hàng từ khung giờ 11:30 đến 21:00.</span>
            </span>
        </div>
        
        <div class="col-span-2 mb-4 py-2">
            <div class="grid grid-cols-1 gap-6 text-center md:flex md:flex-nowrap md:justify-around">

                <a href="<?=$TUANORI->site('fbadmin');?>" target="_blank" class="btn btn-sm btn-outline-secondary !text-white"><i class="fa-brands fa-square-facebook"></i> Facebook</a>
                <a href="tel:<?=$TUANORI->site('zaloadmin');?>" class="btn btn-sm btn-outline-secondary !text-white"><i class="fa-solid fa-phone"></i> <?=$TUANORI->site('zaloadmin');?></a>
            </div>
        </div>


    </div>
</div>
<div class="site-footer bg-[#151212] px-6 py-3 text-slate-500 ltr:ml-[248px] rtl:mr-[248px] dark:bg-slate-800">
    <div class="flex justify-between font-medium text-white">
        <div>
            Phát triển bởi <a href="https://zalo.me/0812665001" target="_blank">PHẠM HOÀNG TUẤN</a>
        </div>
        <div class="hidden md:block">
            <a href="//www.dmca.com/Protection/Status.aspx?ID=ab25dfbc-d64c-43a4-9eb9-0603bce3e676" title="DMCA.com Protection Status" class="dmca-badge"> <img src ="https://images.dmca.com/Badges/dmca-badge-w150-5x1-06.png?ID=ab25dfbc-d64c-43a4-9eb9-0603bce3e676"  alt="DMCA.com Protection Status" /></a>  <script src="https://images.dmca.com/Badges/DMCABadgeHelper.min.js"> </script>
        </div>
    </div>
</div>
</footer>
<!-- END: Footer For Desktop and tab -->

<div class="custom-dropshadow footer-bg bothrefm-0 fixed bottom-0 left-0 z-[9999] flex w-full items-center justify-around bg-white bg-no-repeat px-4 py-[12px] backdrop-blur-[40px] backdrop-filter dark:bg-slate-700 md:hidden sm:mt-5">
<a href="<?=empty($_COOKIE['token']) ? '/dang-nhap': '/';?>">
    <div>
        <span class="relative mb-1 flex cursor-pointer flex-col items-center justify-center rounded-full text-[20px] text-slate-900 dark:text-white"></span>
        <span class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">
            <i class="fa-solid fa-money-bill"></i>:
            <span class="text-red-600"><?=number_format($getUser['money'] ?? 0) ?? '0';?> ₫</span>
        </span>
    </div>
</a>
<a href="<?=empty($_COOKIE['token']) ? '/dang-nhap': '/';?>" class="footer-bg relative z-[-1] -mt-[40px] flex h-[65px] w-[65px] items-center justify-center rounded-full bg-white bg-no-repeat backdrop-blur-[40px] backdrop-filter dark:bg-slate-700">
    <div class="hrefp-[0px] custom-dropshadow relative left-[0px] h-[50px] w-[50px] rounded-full">
        <img src="/images/logo_it.png" alt="TUANORI.VN" class="h-full w-full rounded-full border-2 border-slate-100">
    </div>
</a>
<?php if(isset($_COOKIE['token'])) { ?>
<a href="/dang-xuat">
    <div>
        <span class="relative mb-1 flex cursor-pointer flex-col items-center justify-center rounded-full text-[20px] text-slate-900 dark:text-white">
            <iconify-icon icon="heroicons-outline:logout"></iconify-icon>
        </span> 
        <span class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Đăng Xuất</span>
    </div>
</a>
<?php } else { ?>
<a href="/dang-nhap">
    <div>
        <span class="relative mb-1 flex cursor-pointer flex-col items-center justify-center rounded-full text-[20px] text-slate-900 dark:text-white">
            <iconify-icon icon="icon-park:user"></iconify-icon>
        </span> 
        <span class="block text-[11px] font-bold text-slate-600 dark:text-slate-300">Đăng Nhập</span>
    </div>
</a>
<?php } ?>
</div>
<!-- BEGIN: footer -->

</div>
</div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.11/clipboard.min.js"></script>

    <link rel="preload" as="style" href="/build/assets/chunk-1dd66bf7.css" />
    <link rel="modulepreload" href="/build/assets/app-5ec11d30.js" />
    <link rel="modulepreload" href="/build/assets/chunk-e47d8634.js" />
    <link rel="modulepreload" href="/build/assets/chunk-12ee37c2.js" />
    <link rel="modulepreload" href="/build/assets/main-5c6b3af9.js" />
    <link rel="modulepreload" href="/build/assets/functions-21ea85ed.js" />
    <link rel="stylesheet" href="/build/assets/chunk-1dd66bf7.css" />
    <script type="module" src="/build/assets/app-5ec11d30.js"></script>
    <script type="module" src="/build/assets/main-5c6b3af9.js"></script>
    <script type="module" src="/build/assets/functions-21ea85ed.js"></script>

    <script src="/assets/js/toastr.js"></script>



  
    <style>
        .contact-button {
            position: fixed;
            bottom: 90px;
            right: -10px;
            z-index: 9999;
        }
        
        .contact-button.zalo-button {
            /* left screen */
            
            right: 90px;
            left: 30px;
            bottom: 50px;
        }
        
        .contact-button a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 60%;
            text-decoration: none;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            animation: shake 2s infinite;
        }
        
        .contact-button a .icon {
            width: 50px;
            height: 60px;
            object-fit: cover;
        }
        
        @keyframes shake {
            0% {
                transform: translate(-50%, -50%) rotate(0deg);
            }
            10% {
                transform: translate(-50%, -50%) rotate(-10deg);
            }
            20% {
                transform: translate(-50%, -50%) rotate(10deg);
            }
            30% {
                transform: translate(-50%, -50%) rotate(-10deg);
            }
            40% {
                transform: translate(-50%, -50%) rotate(10deg);
            }
            50% {
                transform: translate(-50%, -50%) rotate(0deg);
            }
            100% {
                transform: translate(-50%, -50%) rotate(0deg);
            }
        }
    </style>

</body>

</html>
