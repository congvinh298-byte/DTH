<?php
  if (!defined('IN_SITE')) die('The Request Not Found');
?>
<head>
    <!-- Load OCD-ADHD Core Styles -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="font-inter bg-gray-50 text-gray-900" id="body_class">
    
    <!-- ADHD Top Ribbon -->
    <div class="bg-red-600 text-white font-black text-center py-2 px-4 text-sm md:text-base shadow-md z-50 relative uppercase tracking-wider">
        HOTLINE LẤP VÒ: 09xx.xxx.xxx - THỢ CÓ MẶT SAU 30 PHÚT
    </div>

    <div class="app-wrapper flex flex-col min-h-screen">
        
        <!-- Header / Navigation (OCD: Perfect alignment, sticky) -->
        <header class="sticky top-0 z-[40] w-full bg-white shadow-lg border-b-4 border-blue-600 transition-all duration-300">
            <div class="container mx-auto px-4 lg:px-8 max-w-7xl">
                <div class="flex items-center justify-between h-20">
                    
                    <!-- Logo -->
                    <a href="/" class="flex-shrink-0 flex items-center gap-3 transform hover:scale-105 transition-transform">
                        <img src="<?=$DMH->site('logo');?>" class="h-10 md:h-12 w-auto drop-shadow-sm" alt="Điện Máy Hiếu">
                        <span class="text-xl md:text-2xl font-black text-gray-900 uppercase tracking-tighter">Điện Máy <span class="text-blue-600">Hiếu</span></span>
                    </a>

                    <!-- Desktop Menu -->
                    <nav class="hidden md:flex space-x-8">
                        <a href="/" class="text-gray-800 hover:text-blue-600 font-bold uppercase tracking-wide transition-colors">Trang Chủ</a>
                        <a href="/dien-may" class="text-gray-800 hover:text-blue-600 font-bold uppercase tracking-wide transition-colors">Điện Máy</a>
                        <a href="/sua-chua" class="text-gray-800 hover:text-blue-600 font-bold uppercase tracking-wide transition-colors">Gọi Thợ</a>
                        <a href="/in-3d" class="text-gray-800 hover:text-blue-600 font-bold uppercase tracking-wide transition-colors text-adhd-alert">In 3D <i class="fa fa-cube"></i></a>
                        <a href="/lien-he" class="text-gray-800 hover:text-blue-600 font-bold uppercase tracking-wide transition-colors">Liên Hệ</a>
                    </nav>

                    <!-- CTA Button (ADHD: Bright, bold, popping) -->
                    <div class="hidden md:flex items-center space-x-4">
                        <a href="tel:09xxxxxxx" class="btn-cta-pulse">
                            <i class="fa fa-phone-alt"></i> Gọi Khẩn Cấp
                        </a>
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="md:hidden flex items-center">
                        <button id="mobile-menu-btn" class="text-gray-800 hover:text-blue-600 focus:outline-none p-2 bg-gray-100 rounded-lg">
                            <i class="fa fa-bars text-2xl"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100 shadow-inner absolute w-full left-0 z-50">
                <div class="px-4 pt-2 pb-6 space-y-2 shadow-xl">
                    <a href="/" class="block px-3 py-3 rounded-md text-base font-bold text-gray-900 hover:bg-gray-50 uppercase border-b border-gray-50">Trang Chủ</a>
                    <a href="/dien-may" class="block px-3 py-3 rounded-md text-base font-bold text-gray-900 hover:bg-gray-50 uppercase border-b border-gray-50">Điện Máy & Gia Dụng</a>
                    <a href="/sua-chua" class="block px-3 py-3 rounded-md text-base font-bold text-gray-900 hover:bg-gray-50 uppercase border-b border-gray-50">Dịch Vụ Sửa Chữa</a>
                    <a href="/in-3d" class="block px-3 py-3 rounded-md text-base font-bold text-orange-600 hover:bg-orange-50 uppercase border-b border-gray-50">Sản Phẩm In 3D</a>
                    <a href="tel:09xxxxxxx" class="mt-4 block w-full text-center bg-orange-500 hover:bg-orange-600 text-white px-4 py-4 rounded-lg font-black uppercase shadow-md transition-colors animate-pulse">
                        <i class="fa fa-phone-alt mr-2"></i> Gọi Thợ Khẩn Cấp
                    </a>
                </div>
            </div>
        </header>

        <script>
            document.getElementById('mobile-menu-btn').addEventListener('click', function() {
                var menu = document.getElementById('mobile-menu');
                menu.classList.toggle('hidden');
            });
        </script>