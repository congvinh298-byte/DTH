# ⚠️ CẢNH BÁO BẢO MẬT NGHIÊM TRỌNG

## Phát hiện
File `.env` trong thư mục gốc dự án đang chứa các thông tin nhạy cảm ở dạng plaintext:
- Database username/password
- SePay API key
- ZMP Token (Zalo Mini App JWT)
- Admin password hash
- BCT report credentials

## Rủi ro
Nếu file này bị lộ (upload nhầm lên Git, backup bị tải về, hoặc server bị xâm nhập),
kẻ tấn công có thể:
- Truy cập toàn bộ database
- Giả mạo admin/Zalo Mini App
- Rút tiền/quỹ qua SePay
- Chiếm đoạt tài khoản BCT

## Hành động cần thực hiện NGAY

1. **Di chuyển .env ra ngoài web root**
   - Không để `.env` trong `public_html/`, `www/`, hoặc thư mục có thể truy cập qua HTTP.
   - Ví dụ đặt tại: `/home/username/config/dmh.env`
   - Sửa `core/config.php` để load từ đường dẫn mới.

2. **Thay đổi tất cả credentials đã lộ**
   - Đổi password database
   - Revoke/regenerate SePay API key
   - Revoke/regenerate ZMP token trên Zalo Mini App Platform
   - Đổi admin password

3. **Thêm .env vào .gitignore**
   ```
   .env
   *.env
   ```

4. **Chặn truy cập .env từ web server**
   - Apache `.htaccess`:
     ```apache
     <Files .env>
     Require all denied
     </Files>
     ```
   - Nginx:
     ```nginx
     location ~ /\.env {
       deny all;
     }
     ```

5. **Xóa file .env hiện tại khỏi source code sau khi đã di chuyển lên server**
   - Chỉ giữ lại `.env.example` (đã được làm mẫu an toàn).

## Lưu ý
Em (Thiên) đã tạo sẵn file `.env.example` để anh Vinh dùng làm mẫu khi triển khai lên server.
File `.env` thật không nên nằm trong workspace này.
