<?php
  if (!defined('IN_SITE')) die('The Request Not Found');
?>
<body class="font-inter dashcode-app" id="body_class">
<!-- <script type='text/javascript'>
//<![CDATA[
shortcut={all_shortcuts:{},add:function(a,b,c){var d={type:"keydown",propagate:!1,disable_in_input:!1,target:document,keycode:!1};if(c)for(var e in d)"undefined"==typeof c[e]&&(c[e]=d[e]);else c=d;d=c.target,"string"==typeof c.target&&(d=document.getElementById(c.target)),a=a.toLowerCase(),e=function(d){d=d||window.event;if(c.disable_in_input){var e;d.target?e=d.target:d.srcElement&&(e=d.srcElement),3==e.nodeType&&(e=e.parentNode);if("INPUT"==e.tagName||"TEXTAREA"==e.tagName)return}d.keyCode?code=d.keyCode:d.which&&(code=d.which),e=String.fromCharCode(code).toLowerCase(),188==code&&(e=","),190==code&&(e=".");var f=a.split("+"),g=0,h={"`":"~",1:"!",2:"@",3:"#",4:"$",5:"%",6:"^",7:"&",8:"*",9:"(",0:")","-":"_","=":"+",";":":","'":'"',",":"<",".":">","/":"?","\\":"|"},i={esc:27,escape:27,tab:9,space:32,"return":13,enter:13,backspace:8,scrolllock:145,scroll_lock:145,scroll:145,capslock:20,caps_lock:20,caps:20,numlock:144,num_lock:144,num:144,pause:19,"break":19,insert:45,home:36,"delete":46,end:35,pageup:33,page_up:33,pu:33,pagedown:34,page_down:34,pd:34,left:37,up:38,right:39,down:40,f1:112,f2:113,f3:114,f4:115,f5:116,f6:117,f7:118,f8:119,f9:120,f10:121,f11:122,f12:123},j=!1,l=!1,m=!1,n=!1,o=!1,p=!1,q=!1,r=!1;d.ctrlKey&&(n=!0),d.shiftKey&&(l=!0),d.altKey&&(p=!0),d.metaKey&&(r=!0);for(var s=0;k=f[s],s<f.length;s++)"ctrl"==k||"control"==k?(g++,m=!0):"shift"==k?(g++,j=!0):"alt"==k?(g++,o=!0):"meta"==k?(g++,q=!0):1<k.length?i[k]==code&&g++:c.keycode?c.keycode==code&&g++:e==k?g++:h[e]&&d.shiftKey&&(e=h[e],e==k&&g++);if(g==f.length&&n==m&&l==j&&p==o&&r==q&&(b(d),!c.propagate))return d.cancelBubble=!0,d.returnValue=!1,d.stopPropagation&&(d.stopPropagation(),d.preventDefault()),!1},this.all_shortcuts[a]={callback:e,target:d,event:c.type},d.addEventListener?d.addEventListener(c.type,e,!1):d.attachEvent?d.attachEvent("on"+c.type,e):d["on"+c.type]=e},remove:function(a){var a=a.toLowerCase(),b=this.all_shortcuts[a];delete this.all_shortcuts[a];if(b){var a=b.event,c=b.target,b=b.callback;c.detachEvent?c.detachEvent("on"+a,b):c.removeEventListener?c.removeEventListener(a,b,!1):c["on"+a]=!1}}},shortcut.add("Ctrl+U",function(){top.location.href="/"}),shortcut.add("F12",function(){top.location.href="/"}),shortcut.add("Ctrl+Shift+I",function(){top.location.href="/"}),shortcut.add("Ctrl+S",function(){top.location.href="/"}),shortcut.add("Ctrl+Shift+C",function(){top.location.href="/"});
//]]>
</script> -->
    <div class="app-wrapper">

        <!-- BEGIN: Sidebar Navigation -->
        <!-- BEGIN: Sidebar -->
        <div class="sidebar-wrapper group hidden w-0 xl:block xl:w-[248px]">
            <div id="bodyOverlay" class="fixed top-0 z-10 hidden h-screen w-screen bg-slate-900 bg-opacity-50 backdrop-blur-sm">
            </div>
            <div class="logo-segment">

                <!-- Application Logo -->
                <a class="flex items-center" href="/">
                    <img src="<?=$TUANORI->site('logo');?>" class="black_logo w-[130px] md:w-[150px] h-[40px] " alt="<?=$TUANORI->site('title');?>">
                    <img src="<?=$TUANORI->site('logo');?>" class="white_logo w-[130px] md:w-[150px] h-[40px]" alt="Sh<?=$TUANORI->site('title');?>">

                </a>

                <!-- Sidebar Type Button -->
                <div id="sidebar_type" class="cursor-pointer text-lg text-slate-900 dark:text-white">
                    <iconify-icon class="sidebarDotIcon extend-icon text-slate-900 dark:text-slate-200" icon="fa-regular:dot-circle"></iconify-icon>
                    <iconify-icon class="sidebarDotIcon collapsed-icon text-slate-900 dark:text-slate-200" icon="material-symbols:circle-outline"></iconify-icon>
                </div>
                <button class="sidebarCloseIcon inline-block text-2xl md:hidden">
                    <iconify-icon class="text-slate-900 dark:text-slate-200" icon="clarity:window-close-line"></iconify-icon>
                </button>
            </div>
            <div id="nav_shadow" class="nav_shadow nav-shadow pointer-events-none absolute top-[80px] z-[1] h-[60px] w-full opacity-0 transition-all duration-200"></div>
            <div class="sidebar-menus z-50 h-[calc(100%-80px)] bg-white px-4 py-2 dark:bg-slate-800" id="sidebar_menus">
                <ul class="sidebar-menu">
                    <li class="sidebar-menu-title">MENU</li>
                    <li>
                        <a href="/" class="navItem <?=active('/')?>">
                            <span class="flex items-center">
                                <iconify-icon class="nav-icon" icon="material-symbols:dashboard-outline"></iconify-icon>
                                <span>Trang Chủ</span>
                            </span>
                        </a>
                    </li>
                    <li>
                        <a href="/Ma-giam-gia" class="navItem <?=active('/Ma-giam-gia')?>">
                            <span class="flex items-center">
                                <iconify-icon  class="nav-icon" icon="grommet-icons:money"></iconify-icon>
                                <span> Xem giảm giá</span>
                            </span>
                        </a>
                    </li>
                 
                    <li class="">
                        <a href="javascript:void(0)" class="navItem">
                            <span class="flex items-center">
                                <iconify-icon  class=" nav-icon" icon="ic:twotone-dashboard"></iconify-icon>
                                <span>Lịch Sử</span>
                            </span>
                            <iconify-icon class="icon-arrow" icon="heroicons-outline:chevron-right"></iconify-icon>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="/Profile/Giaodich" class="<?=active('/Profile/Giaodich')?>">1. Biến động số dư</a>
                            </li>
                            <li class="">
                                <a href="/History-tao-web" class="<?=active('/History-tao-web')?>">2. Lịch sử tạo website</a>
                            </li>
                            <li>
                                <a href="/History-mua-code" class="<?=active('/History-mua-code')?>">3. Lịch sử mua code</a>
                            </li>
                            <?php if(isset($_COOKIE['token']) && $getUser['verify']) { ?>
                            <li>
                                <a href="/Profile/NapThe" class="<?=active('/Profile/NapThe')?>">4. Lịch sử nạp thẻ</a>
                            </li>
                            <?php } ?>
                            <li>
                                <a href="/Partner/HistoryCode" class="">5. Lịch sử bán code</a>
                            </li>
                        </ul>
                    </li>
                    <li class="">
                        <a href="javascript:void(0)" class="navItem">
                            <span class="flex items-center">
                                <iconify-icon class=" nav-icon" icon="gg:credit-card"></iconify-icon>
                                <span>Nạp tiền</span>
                            </span>
                            <iconify-icon class="icon-arrow" icon="heroicons-outline:chevron-right"></iconify-icon>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="/Nap-Tien" class="<?=active('/Nap-Tien')?>">1. Ngân Hàng</a>
                            </li>
                            <li>
                                <a href="/Nap/Card" class="<?=active('/Nap/Card')?>">2. Nạp Card</a>
                            </li>
                            <li>
                                <a href="/Nap/Vi" class="<?=active('/Nap/Vi')?>">3. Ví Thesieure</a>
                            </li>
                            <li>
                                <a href="/Nap/Paypal" class="<?=active('/Nap/Paypal')?>">4. Paypal</a>
                            </li>
                        </ul>
                    </li>
                    <li class="">
                        <a href="javascript:void(0)" class="navItem">
                            <span class="flex items-center">
                                <iconify-icon class=" nav-icon" icon="ant-design:product-filled"></iconify-icon>
                                <span>Danh Mục Khác</span>
                            </span>
                            <iconify-icon class="icon-arrow" icon="heroicons-outline:chevron-right"></iconify-icon>
                        </a>
                        <ul class="sidebar-submenu">
                            <li>
                                <a href="/Profile" class="<?=active('/Profile')?>">1. Thông tin của bạn</a>
                            </li>
                            <li>
                                <a href="/Docs_api" class="<?=active('/Docs_api')?>">2. Kết nối API</a>
                            </li>
                            <li>
                                <a href="/WhoisDomain" class="<?=active('/WhoisDomain')?>">3. Whois tên miền</a>
                            </li>
                            <li>
                                <a href="/upanh" class="<?=active('/upanh')?>">4. UpLoad ảnh lấy link</a>
                            </li>
                            <li>
                                <a href="/Sellcode" class="<?=active('/Sellcode')?>">5. Đăng bán code</a>
                            </li>
                            <li>
                                <a href="/Chuyen-tien" class="<?=active('/Chuyen-tien')?>">6. Chuyển tiền</a>
                            </li>
                        </ul>
                    </li>
                    <?php if(empty($_COOKIE['token'])) { ?>
                    <li>
                        <a href="/dang-nhap" class="navItem <?=active('/dang-nhap')?>">
                            <span class="flex items-center">
                                <iconify-icon class="nav-icon" icon="solar:user-linear"></iconify-icon>
                                <span> Đăng nhập</span>
                            </span>
                        </a>
                    </li>
                    <?php } if(isset($_COOKIE['token'])) { ?>
                    <?php if($getUser['level'] == 'admin') { ?>
                    <li>
                        <a href="/Admin" class="navItem ">
                            <span class="flex items-center">
                                <iconify-icon class=" nav-icon" icon="carbon:share-knowledge" class="text-base leading-[1]"></iconify-icon>
                                <span>Quản trị viên</span>
                            </span>
                        </a>
                    </li>
                    <?php } else if($getUser['verify'] == '1') { ?>
                    <li>
                        <a href="/Partner" class="navItem ">
                            <span class="flex items-center">
                                <iconify-icon class=" nav-icon" icon="carbon:share-knowledge" class="text-base leading-[1]"></iconify-icon>
                                <span>Cộng tác viên</span>
                            </span>
                        </a>
                    </li>
                    <?php } } ?>
                    


                </ul>
            </div>
        </div>
        <!-- End: Sidebar -->
        <!-- End: Sidebar -->


        <div class="flex min-h-screen flex-col justify-between">
            <div>
                <!-- BEGIN: header -->
                <div class="sticky top-0 z-[9]" id="app_header">
                    <div class="app-header z-[999] bg-white shadow-sm dark:bg-slate-800 dark:shadow-slate-700">
                        <div class="flex h-full items-center justify-between">
                            <div class="vertical-box flex items-center space-x-4 rtl:space-x-reverse md:space-x-4">
                                <div class="inline-block xl:hidden">
                                    <a class="flex items-center" href="/">
                                        <img src="<?=$TUANORI->site('logo');?>" class="black_logo w-[130px] md:w-[150px] h-[40px] " alt="Shop Acc Liên Quân Tiktoker Ly Chuột Bạch">
                                        <img src="<?=$TUANORI->site('logo');?>" class="white_logo w-[130px] md:w-[150px] h-[40px]" alt="Shop Acc Liên Quân Tiktoker Ly Chuột Bạch">

                                    </a>
                                </div>
                                <button class="smallDeviceMenuController open-sdiebar-controller hidden md:inline-block xl:hidden">
                                    <iconify-icon class="relative top-[2px] bg-transparent text-xl leading-none text-slate-900 dark:text-white" icon="heroicons-outline:menu-alt-3"></iconify-icon>
                                </button>
                                <button class="sidebarOpenButton !ml-0 text-xl text-slate-900 dark:text-white">
                                    <iconify-icon icon="ph:arrow-right-bold"></iconify-icon>
                                </button>

                            </div>
                            <!-- end vertcial -->

                            <div class="horizental-box items-center space-x-4 rtl:space-x-reverse">
                                <a class="flex items-center" href="/">
                                    <img src="<?=$TUANORI->site('logo');?>" class="black_logo w-[130px] md:w-[150px] h-[40px] " alt="Shop Acc Liên Quân Tiktoker Ly Chuột Bạch">
                                    <img src="<?=$TUANORI->site('logo');?>" class="white_logo w-[130px] md:w-[150px] h-[40px]" alt="Shop Acc Liên Quân Tiktoker Ly Chuột Bạch">

                                </a>
                                <button class="smallDeviceMenuController open-sdiebar-controller hidden md:inline-block xl:hidden">
                                    <iconify-icon class="relative top-[2px] bg-transparent text-xl leading-none text-slate-900 dark:text-white" icon="heroicons-outline:menu-alt-3"></iconify-icon>
                                </button>


                            </div>
                            <!-- end horizontal -->

                            <!-- start horizontal nav -->
                            <div class="main-menu">
                                <ul class="whitespace-nowrap">
                                    <li class="menu-item-has-children !hidden">
                                        <a href="home.html" class="transition-all hover:scale-[105%]">
                                            <div class="flex flex-1 items-center space-x-[6px] rtl:space-x-reverse">
                                                <span class="icon-box">
                                                    <iconify-icon icon="icon-park:dashboard-car"></iconify-icon>
                                                </span>
                                                <div class="text-box"> Mua Tài Khoản
                                                </div>
                                            </div>
                                        </a>
                                    </li>

                                    <li class="menu-item-has-children">
                                        <a href="/" class="transition-all hover:scale-[105%]">
                                            <div class="flex flex-1 items-center space-x-[6px] rtl:space-x-reverse">
                                                <span class="icon-box"><iconify-icon icon="ic:twotone-dashboard"></iconify-icon></span>
                                                <div class="text-box"> Trang Chủ
                                                </div>
                                            </div>
                                        </a>
                                    </li>


                                    <li class="menu-item-has-children">
                                        <a href="/Ma-giam-gia" class="transition-all hover:scale-[105%]">
                                            <div class="flex flex-1 items-center space-x-[6px] rtl:space-x-reverse">
                                                <span class="icon-box"><iconify-icon icon="grommet-icons:money"></iconify-icon></span>
                                                <div class="text-box"> Xem giảm giá
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <li class="menu-item-has-children">
                                        <a href="javascript:void()" class="transition-all hover:scale-[105%]">
                                            <div class="flex flex-1 items-center space-x-[6px] rtl:space-x-reverse">
                                                <span class="icon-box"><iconify-icon icon="ic:twotone-dashboard"></iconify-icon></span>
                                                <div class="text-box"> Lịch Sử
                                                </div>
                                            </div>
                                            <div class="relative top-1 flex-none text-sm leading-[1] ltr:ml-3 rtl:mr-3">
                                                <iconify-icon icon="heroicons-outline:chevron-down"></iconify-icon>
                                            </div>
                                        </a>
                                        <ul class="sub-menu">
                                            <li>
                                                <a href="/Profile/Giaodich" class="transition-all hover:scale-[105%]">
                                                    <div class="flex items-start space-x-2 rtl:space-x-reverse">
                                                        <span class="leading-[1]">1. Biến động số dư</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/History-tao-web" class="transition-all hover:scale-[105%]">
                                                    <div class="flex items-start space-x-2 rtl:space-x-reverse">
                                                        <span class="leading-[2]">2. Lịch sử tạo website</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/History-mua-code" class="transition-all hover:scale-[105%]">
                                                    <div class="flex items-start space-x-2 rtl:space-x-reverse">
                                                        <span class="leading-[3]">3. Lịch sử mua code</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/Profile/NapThe" class="transition-all hover:scale-[105%]">
                                                    <div class="flex items-start space-x-2 rtl:space-x-reverse">
                                                        <span class="leading-[4]">4. Lịch sử nạp thẻ</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <?php if(isset($_COOKIE['token']) && $getUser['verify']) { ?>
                                            <li>
                                                <a href="/Partner/HistoryBuyCode" class="transition-all hover:scale-[105%]">
                                                    <div class="flex items-start space-x-2 rtl:space-x-reverse">
                                                        <span class="leading-[5]">5. Lịch sử bán code</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <?php } ?>
                                        </ul>
                                    </li>
                                    <li class="menu-item-has-children">
                                        <a href="javascript:void()" class="transition-all hover:scale-[105%]">
                                            <div class="flex flex-1 items-center space-x-[6px] rtl:space-x-reverse">
                                                <span class="icon-box">
                                                <iconify-icon icon="gg:credit-card"></iconify-icon>
                                            </span>
                                                <div class="text-box"> Nạp Tiền
                                                </div>
                                            </div>
                                            <div class="relative top-1 flex-none text-sm leading-[1] ltr:ml-3 rtl:mr-3">
                                                <iconify-icon icon="heroicons-outline:chevron-down"></iconify-icon>
                                            </div>
                                        </a>

                                        <ul class="sub-menu">

                                            <li>
                                                <a href="/Nap-Tien" class="transition-all hover:scale-[105%]">
                                                    <div class="flex items-start space-x-2 rtl:space-x-reverse">
                                                        <iconify-icon icon="clarity:bank-line" class="text-base leading-[1]"></iconify-icon>
                                                        <span class="leading-[1]">Ngân hàng</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/Nap/Card" class="transition-all hover:scale-[105%]">
                                                    <div class="flex items-start space-x-2 rtl:space-x-reverse">
                                                        <iconify-icon icon="arcticons:card-emulator-pro" class="text-base leading-[4]"></iconify-icon>
                                                        <span class="leading-[2]">Nạp Card</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/Nap/Vi" class="transition-all hover:scale-[105%]">
                                                    <div class="flex items-start space-x-2 rtl:space-x-reverse">
                                                        <iconify-icon icon="arcticons:card-emulator-pro" class="text-base leading-[3]"></iconify-icon>
                                                        <span class="leading-[3]">Ví Thesieure</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/Nap/Paypal" class="transition-all hover:scale-[105%]">
                                                    <div class="flex items-start space-x-2 rtl:space-x-reverse">
                                                        <iconify-icon icon="arcticons:paypal" class="text-base leading-[4]"></iconify-icon>
                                                        <span class="leading-[4]">Paypal</span>
                                                    </div>
                                                </a>
                                            </li>
                                            
                                        </ul>
                                    </li>

                                    <li class="menu-item-has-children">
                                        <a href="javascript:void()" class="transition-all hover:scale-[105%]">
                                            <div class="flex flex-1 items-center space-x-[6px] rtl:space-x-reverse">
                                                <span class="icon-box">
                                                <iconify-icon icon="ant-design:product-filled"></iconify-icon>
                                            </span>
                                                <div class="text-box"> Danh mục khác
                                                </div>
                                            </div>
                                            <div class="relative top-1 flex-none text-sm leading-[1] ltr:ml-3 rtl:mr-3">
                                                <iconify-icon icon="heroicons-outline:chevron-down"></iconify-icon>
                                            </div>
                                        </a>

                                        <ul class="sub-menu">
                                            <li>
                                                <a href="/Docs_api" class="transition-all hover:scale-[105%]">
                                                    <div class="flex items-start space-x-2 rtl:space-x-reverse">
                                                        <span class="leading-[1]"><i class="fa-sharp fa-solid fa-code"></i> Kết nối API</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/upanh" class="transition-all hover:scale-[105%]">
                                                    <div class="flex items-start space-x-2 rtl:space-x-reverse">
                                                        <span class="leading-[2]"><i class="fa fa-file"></i> Upload ảnh lấy link</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/WhoisDomain" class="transition-all hover:scale-[105%]">
                                                    <div class="flex items-start space-x-2 rtl:space-x-reverse">
                                                        <span class="leading-[3]"><i class="fa fa-globe"></i> Whois tên miền</span>
                                                    </div>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/Sellcode" class="transition-all hover:scale-[105%]">
                                                    <div class="flex items-start space-x-2 rtl:space-x-reverse">
                                                        <span class="leading-[3]"><i class="fa-sharp fa-solid fa-code"></i> Đăng bán code</span>
                                                    </div>
                                                </a>
                                            </li>
                                        </ul>
                                    </li>
                                    <?php if(empty($_COOKIE['token'])) { ?>
                                    <li class="menu-item-has-children">
                                        <a href="/dang-nhap" class="transition-all hover:scale-[105%]">
                                            <div class="flex flex-1 items-center space-x-[6px] rtl:space-x-reverse">
                                                <span class="icon-box"><iconify-icon icon="solar:user-linear"></iconify-icon></span>
                                                <div class="text-box"> Đăng nhập
                                                </div>
                                            </div>
                                        </a>
                                    </li>
                                    <?php } ?>

                                    <?php if(isset($_COOKIE['token'])) { ?>
                                        <?php if($getUser['level'] == 'admin') { ?>
                                        <li class="menu-item-has-children">
                                            <a href="/Admin" class="transition-all hover:scale-[105%]">
                                                <div class="flex flex-1 items-center space-x-[6px] rtl:space-x-reverse">
                                                    <span class="icon-box"><iconify-icon icon="carbon:share-knowledge" class="text-base leading-[1]"></iconify-icon></span>
                                                    <div class="text-box"> Quản trị viên
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <?php } else if($getUser['verify'] == 1) { ?>
                                        <li class="menu-item-has-children">
                                            <a href="/Partner" class="transition-all hover:scale-[105%]">
                                                <div class="flex flex-1 items-center space-x-[6px] rtl:space-x-reverse">
                                                    <span class="icon-box"><iconify-icon icon="carbon:share-knowledge" class="text-base leading-[1]"></iconify-icon></span>
                                                    <div class="text-box"> Cộng tác viên
                                                    </div>
                                                </div>
                                            </a>
                                        </li>
                                        <?php } ?>
                                    <?php } ?>
                                    


                                </ul>
                            </div>
                            <!-- end top menu -->
                            <!-- end horizontal nav -->

                            <div class="nav-tools leading-0 flex items-center space-x-3 rtl:space-x-reverse lg:space-x-5">
                                <div class="leading-0 relative">

                                    <div class="gtranslate_wrapper"></div>
                                </div>
                            <div>
                                <a href="/GioHang"><button class="lg:h-[32px] lg:w-[32px] lg:bg-slate-100 lg:dark:bg-slate-900 dark:text-white text-slate-900 cursor-pointer rounded-full text-[20px] flex flex-col items-center justify-center">
                                        <div id="cart-icon">
                                            <i class="fas fa-shopping-cart"></i>
                                            <span id="cart-count">
                                                <?php if(isset($_COOKIE['token'])) {
                                                    echo format_cash($TUANORI->num_rows(" SELECT * FROM `giohang` WHERE `username` = '".$getUser['username']."' "));
                                                    } else {
                                                        echo 0;
                                                    }
                                                ?>
                                            </span>
                                        </div>
                                </button></a>
                            </div>
                                <div class="leading-0 hidden w-full md:block">
                                    <button class="inline-flex items-center rounded-lg text-center text-sm font-medium text-slate-800 focus:outline-none focus:ring-0 dark:text-white" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <div class="h-7 w-7 flex-1 rounded-full ltr:mr-[10px] rtl:ml-[10px] lg:h-8 lg:w-8">
                                            <img class="block h-full w-full rounded-full object-cover" src="/images/logo_it.png" alt="user" />
                                        </div>
                                        <div class="ltr:text-left rtl:text-right">
                                            <span class="hidden flex-none items-center overflow-hidden text-ellipsis whitespace-nowrap text-sm font-bold text-slate-600 dark:text-white lg:flex"><?php echo (empty($getUser['username']) ? 'Khách' : $getUser['username']); ?></span>
                                            <small class="text-danger-600 block text-[15px] font-bold"><?php echo (empty($getUser['username']) ? '0' : number_format($getUser['money'])); ?> ₫</small>
                                        </div>
                                        <svg class="ml-[10px] inline-block h-[16px] w-[16px] text-base rtl:mr-[10px] dark:text-white lg:inline-block" aria-hidden="true" fill="none" stroke="currentColor" viewbox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                    <!-- Dropdown menu -->
                                    <div class="dropdown-menu !top-[23px] z-10 hidden w-44 divide-y divide-slate-100 overflow-hidden rounded-md border bg-white shadow dark:border-slate-700 dark:bg-slate-800">
                                        <?php if(empty($getUser['username'])) { ?>
                                        <ul class="py-1 text-sm text-slate-800 dark:text-slate-200" :class="listView ? 'z-20 opacity-100 top-[61px]' : 'opacity-0 -z-20 top-5'" x-show="listView" @click.away="listView = false">
                                            <li>
                                                <a href="<?=BASE_URL('dang-nhap');?>" class="font-inter flex items-center px-4 py-2 text-sm font-normal text-slate-600 hover:bg-slate-100 dark:text-white dark:hover:bg-slate-600 dark:hover:text-white" class="active">
                                                    <iconify-icon class="text-textColor mr-2 text-lg dark:text-white" icon="material-symbols:login"></iconify-icon>
                                                    <span class="dropdown-option">Đăng Nhập</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?=BASE_URL('dang-ky');?>" class="font-inter flex items-center px-4 py-2 text-sm font-normal text-slate-600 hover:bg-slate-100 dark:text-white dark:hover:bg-slate-600 dark:hover:text-white" class="">
                                                    <iconify-icon class="text-textColor mr-2 text-lg dark:text-white" icon="solar:user-linear"></iconify-icon>
                                                    <span class="dropdown-option">Tạo Tài Khoản</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/quen-mat-khau" class="font-inter flex items-center px-4 py-2 text-sm font-normal text-slate-600 hover:bg-slate-100 dark:text-white dark:hover:bg-slate-600 dark:hover:text-white" class="">
                                                    <iconify-icon class="text-textColor mr-2 text-lg dark:text-white" icon="solar:password-outline"></iconify-icon>
                                                    <span class="dropdown-option">Quên Mật Khẩu?</span>
                                                </a>
                                            </li>
                                        </ul>
                                        <?php } else { ?>
                                        <ul class="py-1 text-sm text-slate-800 dark:text-slate-200" :class="listView ? 'z-20 opacity-100 top-[61px]' : 'opacity-0 -z-20 top-5'" x-show="listView" @click.away="listView = false">
                                            <li>
                                                <a href="/Profile" class="font-inter flex items-center px-4 py-2 text-sm font-normal text-slate-600 hover:bg-slate-100 dark:text-white dark:hover:bg-slate-600 dark:hover:text-white">
                                                    <iconify-icon class="text-textColor mr-2 text-lg dark:text-white" icon="carbon:user-avatar">
                                                    </iconify-icon>
                                                    <span class="dropdown-option">Thông Tin</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/Nap-Tien" class="font-inter flex items-center px-4 py-2 text-sm font-normal text-slate-600 hover:bg-slate-100 dark:text-white dark:hover:bg-slate-600 dark:hover:text-white">
                                                    <iconify-icon class="text-textColor mr-2 text-lg dark:text-white" icon="carbon:money"></iconify-icon>
                                                    <span class="dropdown-option">Nạp Tiền</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/Chuyen-tien" class="font-inter flex items-center px-4 py-2 text-sm font-normal text-slate-600 hover:bg-slate-100 dark:text-white dark:hover:bg-slate-600 dark:hover:text-white">
                                                    <iconify-icon class="text-textColor mr-2 text-lg dark:text-white" icon="mdi:exchange">
                                                    </iconify-icon>
                                                    <span class="dropdown-option">Chuyển tiền</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/Sellcode" class="font-inter flex items-center px-4 py-2 text-sm font-normal text-slate-600 hover:bg-slate-100 dark:text-white dark:hover:bg-slate-600 dark:hover:text-white">
                                                    <iconify-icon class="text-textColor mr-2 text-lg dark:text-white" icon="fluent:money-20-regular">
                                                    </iconify-icon>
                                                    <span class="dropdown-option">Đăng bán code</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a href="/dang-xuat" class="font-inter flex items-center px-4 py-2 text-sm font-normal text-slate-600 hover:bg-slate-100 dark:text-white dark:hover:bg-slate-600 dark:hover:text-white">
                                                    <iconify-icon iconify-icon class="text-textColor mr-2 text-lg dark:text-white" icon="carbon:logout">
                                                        </iconify-icon>
                                                    <span class="dropdown-option">Đăng Xuất</span>
                                                </a>
                                            </li>
                                           
                                        </ul>
                                        <?php } ?>
                                    </div>

                                    

                                </div>
                                <button class="smallDeviceMenuController leading-0 block md:hidden">
                                    <iconify-icon class="cursor-pointer text-2xl text-slate-900 dark:text-white" icon="heroicons-outline:menu-alt-3"></iconify-icon>
                                </button>
                                <!-- end mobile menu -->
                            </div>
                            <!-- end nav tools -->
                        </div>
                    </div>
                </div>

                <!-- BEGIN: Search Modal -->
                <div class="modal fade backdrop-brightness-10 fixed inset-0 left-0 top-0 hidden h-full w-full overflow-y-auto overflow-x-hidden bg-slate-900/40 outline-none backdrop-blur-sm backdrop-filter" id="searchModal" tabindex="-1" aria-labelledby="searchModalLabel" aria-hidden="true">
                    <div class="modal-dialog pointer-events-none relative top-1/4 w-auto">
                        <div class="modal-content pointer-events-auto relative flex w-full flex-col rounded-md border-none bg-white bg-clip-padding text-current shadow-lg outline-none dark:bg-slate-900">
                            <form>
                                <div class="relative">
                                    <button class="absolute left-0 top-1/2 flex h-full w-9 -translate-y-1/2 items-center justify-center text-xl dark:text-slate-300">
                                        <iconify-icon icon="heroicons-solid:search"></iconify-icon>
                                    </button>
                                    <input type="text" class="form-control !py-[14px] !pl-10" placeholder="Search" autofocus>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>