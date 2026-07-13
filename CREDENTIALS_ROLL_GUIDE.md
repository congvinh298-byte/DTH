# HƯỚNG DẪN ĐỔI TOÀN BỘ CREDENTIALS SAU KHI BỊ LỘ

## ⚠️ Tình huống
File `.env` trong workspace đã chứa plaintext credentials. Dù anh chưa đẩy lên Git,
nhưng vì file đã tồn tại trong máy tính, cẩn tắc vô áy náy — anh nên đổi toàn bộ.

---

## 1. DATABASE (MySQL)

### Bước 1.1: Đổi password user MySQL
Đăng nhập phpMyAdmin hoặc SSH vào MySQL:
```sql
ALTER USER 'kwkrbcce_dientuhieu'@'localhost' IDENTIFIED BY 'MatKhauMoiManh@2026';
ALTER USER 'kwkrbcce_baocao'@'localhost' IDENTIFIED BY 'MatKhauMoiBaoCao@2026';
FLUSH PRIVILEGES;
```

### Bước 1.2: Cập nhật `.env`
```env
DB_PASS=MatKhauMoiManh@2026
DB_PASS_BAOCAO=MatKhauMoiBaoCao@2026
```

### Bước 1.3: Test kết nối
Mở website, đăng nhập thử xem có lỗi CSDL không.

---

## 2. SEPAY API KEY

### Bước 2.1: Revoke key cũ
- Vào https://my.sepay.vn hoặc dashboard tương ứng.
- Tìm API Key đang dùng → chọn **Revoke / Xóa**.

### Bước 2.2: Tạo key mới
- Tạo API Key mới, chỉ cấp quyền tối thiểu cần thiết.

### Bước 2.3: Cập nhật `.env`
```env
SEPAY_API_KEY=KeyMoiDayDuKiTuSoVaChu
```

---

## 3. ZALO MINI APP TOKEN (ZMP_TOKEN)

### Bước 3.1: Lấy token mới
- Vào https://miniapp.zaloplatforms.com
- Chọn app **Điện Máy Hiếu**.
- Vào phần cấu hình / token → **Regenerate / Tạo lại token**.

### Bước 3.2: Cập nhật `.env`
```env
ZMP_TOKEN=eyJ0eXAiOiJKV1Qi...TokenMoi
```

---

## 4. ADMIN PASSWORD

### Bước 4.1: Tạo hash mới
Dùng PHP trên server hoặc tool online an toàn:
```php
<?php echo password_hash('MatKhauAdminMoi', PASSWORD_BCRYPT); ?>
```

### Bước 4.2: Cập nhật `.env`
```env
ADMIN_PASS=MatKhauAdminMoi
ADMIN_PASS_HASH=$2y$10$...HashMoi
```

### Bước 4.3: Đổi luôn trên DB
Nếu hệ thống lưu admin password ở bảng `users` hoặc `options`, cập nhật luôn.

---

## 5. BỘ CÔNG THƯƠNG REPORT PASSWORD

Nếu tài khoản `qltmdt@moit.gov.vn` là tài khoản thật:
- Vào https://qltmdt.moit.gov.vn đổi mật khẩu.
- Tạo lại hash bằng PHP `password_hash()`.
- Cập nhật `.env`.

Nếu đây chỉ là tài khoản kỹ thuật tự tạo → vẫn nên đổi để đảm bảo.

---

## 6. CRON SECRET
```env
CRON_SECRET=ChuoiNgauNhienDai32KyTroTroLen@2026
```

---

## 7. DI CHUYỂN .ENV RA NGOÀI WEB ROOT

### Bước 7.1: Trên server
Giả sử web root là `/home/username/public_html/`, di chuyển `.env` lên trên:
```bash
mv /home/username/public_html/.env /home/username/config/dmh.env
chmod 600 /home/username/config/dmh.env
```

### Bước 7.2: Sửa `core/config.php`
Tìm đoạn:
```php
if (file_exists(__DIR__.'/../.env')) {
    $lines = file(__DIR__.'/../.env', ...
```
Sửa thành đường dẫn mới:
```php
$envPath = '/home/username/config/dmh.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
```

### Bước 7.3: Chặn truy cập .env từ web
**Apache `.htaccess`:**
```apache
<Files .env>
    Require all denied
</Files>
```

**Nginx:**
```nginx
location ~ /\.env {
    deny all;
}
```

---

## 8. SAU KHI ĐỔI XONG

1. Xóa file `.env` cũ trong workspace/máy tính.
2. Xóa file `.env` trong web root nếu còn sót.
3. Kiểm tra log server xem có request lạ truy cập `.env` không.
4. Test toàn bộ chức năng: đăng nhập, đặt lịch, thanh toán, Mini App.

---

## 📝 Mẫu `.env` mới an toàn

```env
DB_HOST=localhost
DB_NAME=kwkrbcce_dienmayhieulapvo
DB_USER=kwkrbcce_dientuhieu
DB_PASS=MatKhauManh@2026
DB_USER_BAOCAO=kwkrbcce_baocao
DB_PASS_BAOCAO=MatKhauBaoCao@2026
DB_CHARSET=utf8mb4

SEPAY_API_KEY=KeyMoiCuaSepay

ADMIN_PASS=MatKhauAdminMoi
ADMIN_PASS_HASH=$2y$10$...HashMoi
ADMIN_TELEGRAM_ID=648065292
INITIAL_WORKER_TELEGRAM_ID=8729878070
APP_DEBUG=false
CRON_SECRET=ChuoiNgauNhienDaiHon32KyTu

BCT_REPORT_USER=qltmdt@moit.gov.vn
BCT_REPORT_PASS_HASH=$2y$10$...HashMoi
BCT_REPORT_API_KEY_HASH=$2y$10$...HashMoi
BCT_COMPANY_NAME=CONG TY TNHH MTV DIEN TU HIEU
BCT_TAX_CODE=1402228630
BCT_WEBSITE=https://dienmayhieu.com
BCT_REPORT_MAX_DAYS=370
BCT_REPORT_SESSION_TTL=3600
BCT_INPUT_PDF_MAX_MB=20

COMPANY_NAME=CONG TY TNHH MTV DIEN TU HIEU
COMPANY_TAX_CODE=1402228630
COMPANY_ADDRESS=166, Ap Binh Thanh 1, Xa Lap Vo, Tinh Dong Thap
COMPANY_PHONE=0979.553.289
COMPANY_EMAIL=
COMPANY_WEBSITE=https://dienmayhieu.com

VNB_ACC=115003056025
VNB_BIN=ICB
VNB_HOLDER=CONG TY TNHH MTV DIEN TU HIEU
APP_URL=https://dienmayhieu.com
BANK_PAYMENT_URL=
```

---

## ❌ KHÔNG BAO GIỜ
- KHÔNG commit `.env` lên Git.
- KHÔNG để `.env` trong web root.
- KHÔNG gửi `.env` qua Telegram/Zalo/email.
- KHÔNG dùng lại password cũ.
