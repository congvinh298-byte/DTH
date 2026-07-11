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
            <p>Hotline: 0939.354.937</p>
            <p>Mua hàng và gọi thợ kỹ thuật, đặt in 3D</p>
        </div>
        <div>
            <h3>Thông tin pháp lý</h3>
            <p><a href="/quy-che">Quy chế hoạt động</a></p>
            <p><a href="/de-an">Đề án hoạt động</a></p>
            <p><a href="/bao-mat">Chính sách bảo mật</a></p>
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
