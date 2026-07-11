<?php
  if (!defined('IN_SITE')) die('The Request Not Found');
?>
<head>
    <!-- Load OCD-ADHD Core Styles -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body class="font-inter bg-gray-50 text-gray-900" id="body_class">
    
    <!-- Top Ribbon (Navy Blue) -->
    <div class="bg-[#0A192F] text-white font-black text-center py-2 px-4 text-sm md:text-base shadow-md z-50 relative uppercase tracking-wider">
        HOTLINE LẤP VÒ: 0939.354.937 - THỢ CÓ MẶT SAU 30 PHÚT
    </div>

    <div class="app-wrapper flex flex-col min-h-screen">
        
        <!-- Header / Navigation -->
        <header class="sticky top-0 z-[40] w-full bg-white shadow-lg border-b-4 border-black transition-all duration-300">
            <div class="container mx-auto px-4 lg:px-8 max-w-7xl">
                <div class="flex items-center justify-between h-20">
                    
                    <!-- Logo -->
                    <a href="/" class="flex-shrink-0 flex items-center gap-3 transform hover:scale-105 transition-transform">
                        <img src="/logo.png" class="h-10 md:h-12 w-auto drop-shadow-sm" alt="Điện Máy Hiếu">
                        <span class="text-xl md:text-2xl font-black text-black uppercase tracking-tighter">Điện Máy <span class="text-[#0A192F]">Hiếu</span></span>
                    </a>

                    <!-- Desktop Menu -->
                    <nav class="hidden md:flex space-x-8">
                        <a href="/" class="text-black hover:text-[#0A192F] font-bold uppercase tracking-wide transition-colors">Trang Chủ</a>
                        <a href="/dien-may" class="text-black hover:text-[#0A192F] font-bold uppercase tracking-wide transition-colors">Điện Máy</a>
                        <a href="/goi-tho.php" class="text-black hover:text-[#0A192F] font-bold uppercase tracking-wide transition-colors">Gọi Thợ</a>
                        <a href="/in-3d.php" class="text-black hover:text-[#0A192F] font-bold uppercase tracking-wide transition-colors">In 3D <i class="fa fa-cube"></i></a>
                        <a href="/lien-he" class="text-black hover:text-[#0A192F] font-bold uppercase tracking-wide transition-colors">Liên Hệ</a>
                    </nav>

                    <!-- CTA Buttons (Navy Blue / Black) -->
                    <div class="hidden md:flex items-center space-x-3">
                        <a href="/in-3d.php" class="border-2 border-black hover:bg-black hover:text-white text-black font-black px-5 py-2.5 rounded-xl uppercase tracking-wide transition-all flex items-center gap-2">
                            <i class="fa fa-cube"></i> Đặt In 3D
                        </a>
                        <a href="/goi-tho.php" class="bg-[#0A192F] hover:bg-black text-white font-black px-5 py-2.5 rounded-xl uppercase tracking-wide shadow-md hover:-translate-y-0.5 transition-all flex items-center gap-2">
                            <i class="fa fa-tools animate-pulse"></i> Gọi Thợ Ngay
                        </a>
                    </div>

                    <!-- Mobile Menu Button -->
                    <div class="md:hidden flex items-center">
                        <button id="mobile-menu-btn" class="text-black hover:text-[#0A192F] focus:outline-none p-2 bg-gray-100 rounded-lg">
                            <i class="fa fa-bars text-2xl"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-gray-100 shadow-inner absolute w-full left-0 z-50">
                <div class="px-4 pt-2 pb-6 space-y-2 shadow-xl">
                    <a href="/" class="block px-3 py-3 rounded-md text-base font-bold text-black hover:bg-gray-50 uppercase border-b border-gray-50">Trang Chủ</a>
                    <a href="/dien-may" class="block px-3 py-3 rounded-md text-base font-bold text-black hover:bg-gray-50 uppercase border-b border-gray-50">Điện Máy & Gia Dụng</a>
                    <a href="/goi-tho.php" class="block px-3 py-3 rounded-md text-base font-bold text-black hover:bg-gray-50 uppercase border-b border-gray-50">Dịch Vụ Sửa Chữa</a>
                    <a href="/in-3d.php" class="block px-3 py-3 rounded-md text-base font-bold text-black hover:bg-gray-50 uppercase border-b border-gray-50">Sản Phẩm In 3D</a>
                    
                    <a href="/goi-tho.php" class="mt-4 block w-full text-center bg-[#0A192F] hover:bg-black text-white px-4 py-4 rounded-lg font-black uppercase shadow-md transition-colors animate-pulse">
                        <i class="fa fa-tools mr-2"></i> Gọi Thợ Khẩn Cấp
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