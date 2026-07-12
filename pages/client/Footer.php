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

<!-- BEGIN: Footer -->
<footer><div class="wrap">
    <div class="footer-grid">
        <div>
            <h3>CÔNG TY TNHH MTV ĐIỆN TỬ HIẾU</h3>
            <p>MST: 1402228630</p>
            <p>Địa chỉ: 166, Ấp Bình Thạnh 1, Xã Lấp Vò, Tỉnh Đồng Tháp</p>
            <p>Website: dienmayhieu.com</p>
            <p>Khu vực phục vụ: bán kính 15 km tính từ Chợ Lấp Vò, Đồng Tháp</p>
        </div>
        <div>
            <h3>Liên hệ</h3>
            <p>Hotline: 0979.553.289</p>
            <p>Email: congvinh298@gmail.com</p>
            <p>Mua hàng và gọi thợ kỹ thuật, đặt in 3D</p>
        </div>
        <div>
            <h3>Thông tin pháp lý</h3>
            <p><a href="/#pol-1">Chính sách bảo mật</a></p>
            <p><a href="/#pol-2">Giải quyết khiếu nại</a></p>
            <p><a href="/#pol-3">Chính sách giá</a></p>
            <p><a href="/#pol-4">Chính sách thanh toán</a></p>
            <p><a href="/#pol-5">Điều kiện & hạn chế</a></p>
            <p><a href="/#pol-6">Giao hàng & Đổi trả</a></p>
            <p><a href="/#pol-7">Hỗ trợ trực tuyến</a></p>
        </div>
        <div>
            <h3>Truy cập nhanh</h3>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data=https://dienmayhieu.com" alt="QR truy cập" style="max-width: 120px; border-radius: 8px; background: white; padding: 5px; margin-top: 5px;">
        </div>
    </div>
    <div class="footer-bottom">
        <span>© <?=date('Y');?> Điện Máy Hiếu</span>
        <span>Website đang chờ duyệt</span>
    </div>
</div></footer>
<!-- END: Footer -->
    
<!-- CHATBOT ANH THIÊN OPENCLAW -->
<style>
:root {
    --chat-cyan: #06b6d4;
    --chat-purple: #a855f7;
}
#openclaw-chatbot { position: fixed; bottom: 30px; right: 30px; z-index: 9999; }
#openclaw-btn { width: 60px; height: 60px; border-radius: 50%; background: linear-gradient(135deg, var(--chat-cyan), var(--chat-purple)); color: white; display: flex; align-items: center; justify-content: center; font-size: 28px; cursor: pointer; box-shadow: 0 5px 15px rgba(0,0,0,0.4); transition: transform 0.3s; animation: pulse 2s infinite; }
#openclaw-btn:hover { transform: scale(1.1); }
@keyframes pulse { 0% { box-shadow: 0 0 0 0 rgba(6, 182, 212, 0.7); } 70% { box-shadow: 0 0 0 15px rgba(6, 182, 212, 0); } 100% { box-shadow: 0 0 0 0 rgba(6, 182, 212, 0); } }
#openclaw-window { display: none; width: 350px; height: 500px; background: #1e293b; border-radius: 16px; border: 2px solid var(--chat-cyan); box-shadow: 0 10px 25px rgba(0,0,0,0.5); flex-direction: column; overflow: hidden; position: absolute; bottom: 80px; right: 0; }
#openclaw-header { background: var(--chat-cyan); color: #0f172a; padding: 15px; display: flex; justify-content: space-between; align-items: center; font-weight: 900; }
#openclaw-messages { flex-grow: 1; padding: 15px; overflow-y: auto; display: flex; flex-direction: column; gap: 10px; background: #0f172a; }
.msg { max-width: 85%; padding: 10px 14px; border-radius: 12px; font-size: 14px; line-height: 1.4; }
.msg-bot { background: #334155; color: #fff; align-self: flex-start; border-bottom-left-radius: 0; }
.msg-user { background: var(--chat-purple); color: #fff; align-self: flex-end; border-bottom-right-radius: 0; }
#openclaw-input-area { display: flex; padding: 10px; background: #1e293b; border-top: 1px solid #334155; }
#openclaw-input { flex-grow: 1; background: #0f172a; border: 1px solid #334155; color: white; padding: 10px; border-radius: 8px; outline: none; }
#openclaw-send { background: var(--chat-cyan); color: #000; border: none; padding: 10px 15px; margin-left: 10px; border-radius: 8px; cursor: pointer; font-weight: bold; }
</style>

<div id="openclaw-chatbot" style="display: flex; gap: 15px; align-items: flex-end;">
    <!-- Zalo Button -->
    <a href="https://zalo.me/0979553289" target="_blank" id="zalo-btn" style="width: 60px; height: 60px; border-radius: 50%; background: #0068ff; color: white; display: flex; align-items: center; justify-content: center; font-size: 32px; box-shadow: 0 5px 15px rgba(0,0,0,0.4); transition: transform 0.3s; text-decoration: none;">
        <i class="fa-solid fa-comment-dots"></i>
    </a>
    
    <div id="openclaw-window" style="right: 0; bottom: 80px;">
        <div id="openclaw-header">
            <span><i class="fa-solid fa-robot"></i> Dân chơi</span>
            <i class="fa-solid fa-xmark" style="cursor: pointer; font-size: 18px;" onclick="$('#openclaw-window').hide()"></i>
        </div>
        <div id="openclaw-messages">
            <div class="msg msg-bot">Xin chào! Tui là <b>Dân chơi</b>, trợ lý AI của cửa hàng. Tui có thể giúp gì cho bạn về Mua sắm Điện Máy, Gọi Thợ hay In 3D?</div>
        </div>
        <form id="openclaw-input-area" onsubmit="sendOpenclawMsg(event)">
            <input type="text" id="openclaw-input" placeholder="Nhập câu hỏi..." autocomplete="off">
            <button type="submit" id="openclaw-send"><i class="fa-solid fa-paper-plane"></i></button>
        </form>
    </div>
    
    <!-- Chatbot Button -->
    <div id="openclaw-btn" onclick="$('#openclaw-window').toggle()">
        <i class="fa-solid fa-headset"></i>
    </div>
</div>

<style>
#zalo-btn:hover { transform: scale(1.1); }
</style>

<script>
function sendOpenclawMsg(e) {
    e.preventDefault();
    let text = $('#openclaw-input').val().trim();
    if(!text) return;
    
    // Add user message
    $('#openclaw-messages').append(`<div class="msg msg-user">${text}</div>`);
    $('#openclaw-input').val('');
    scrollToBottom();
    
    // Add typing indicator
    let typingId = 'typing-' + Date.now();
    $('#openclaw-messages').append(`<div class="msg msg-bot" id="${typingId}">Đang gõ... <i class="fa-solid fa-ellipsis fa-fade"></i></div>`);
    scrollToBottom();
    
    $.ajax({
        url: "/controller/client/Chatbot.php",
        method: "POST",
        data: { message: text },
        success: function(r) {
            $('#' + typingId).remove();
            try {
                let res = typeof r === 'string' ? JSON.parse(r) : r;
                $('#openclaw-messages').append(`<div class="msg msg-bot">${res.reply}</div>`);
            } catch(e) {
                $('#openclaw-messages').append(`<div class="msg msg-bot">Xin lỗi, Anh thiên đang bận bảo trì server rùi!</div>`);
            }
            scrollToBottom();
        },
        error: function() {
            $('#' + typingId).remove();
            $('#openclaw-messages').append(`<div class="msg msg-bot">Lỗi kết nối!</div>`);
        }
    });
}
function scrollToBottom() {
    let box = document.getElementById('openclaw-messages');
    box.scrollTop = box.scrollHeight;
}
</script>


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
    </script>
</body>
</html>
