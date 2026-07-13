:TASTE-GATE 1.0
# HƯỚNG DẪN PULL CODE TỪ GITHUB LÊN HOST

## Repo
```
https://github.com/congvinh298-byte/DTH
Branch: main
```

## Bước 1: Trên host, vào thư mục web root

Ví dụ web root là `/home/username/public_html/`:
```bash
cd /home/username/public_html/
```

## Bước 2: Backup file .env hiện tại (quan trọng)

```bash
cp .env /home/username/.env_backup_dth
```

## Bước 3: Pull code mới

Nếu chưa có Git repo trên host:
```bash
git clone https://github.com/congvinh298-byte/DTH.git /home/username/public_html_new/
# Sau đó copy .env cũ vào public_html_new rồi đổi tên thư mục
```

Nếu đã có Git repo:
```bash
git fetch origin
git reset --hard origin/main
git clean -fd
```

⚠️ **Lưu ý:** `git reset --hard` sẽ xóa toàn bộ thay đổi local trên host. Nếu có file cần giữ, backup trước.

## Bước 4: Khôi phục .env

```bash
cp /home/username/.env_backup_dth .env
# Hoặc tạo .env mới từ .env.example
cp .env.example .env
# Rồi sửa .env bằng nano/vim
nano .env
```

## Bước 5: Cập nhật database

Chạy 1 trong 2 file trên host:
```bash
# Cách 1: setup_tho.php qua browser
https://dienmayhieu.com/setup_tho.php

# Cách 2: schema.php qua browser
https://dienmayhieu.com/schema.php

# Cách 3: import SQL trực tiếp (nếu có file .sql)
```

Sau khi chạy xong, **xóa ngay 2 file này khỏi host** để tránh chạy lại:
```bash
rm setup_tho.php schema.php
```

## Bước 6: Xóa file thừa trên host nếu còn sót

```bash
# Xóa zalo_mini_app nếu còn
rm -rf zalo_mini_app/

# Xóa file backup tạm
rm -f data.sql dth_master_update.zip
rm -f db_migrate.php sync_now.php header_ftp.php host_audit.php
```

## Bước 7: Cấu hình bảo mật .env

1. Đảm bảo `.env` nằm **ngoài web root** hoặc được chặn truy cập web.
2. Thêm `.htaccess`:
```apache
<Files .env>
    Require all denied
</Files>
```
3. Nginx:
```nginx
location ~ /\.env {
    deny all;
}
```

## Bước 8: Upload Zalo Mini App

1. Tạo ZIP chỉ chứa `/zalo/`:
```bash
cd zalo
zip -r ../zalo-deploy.zip .
cd ..
```

2. Upload `zalo-deploy.zip` lên https://miniapp.zaloplatforms.com
3. Kiểm tra preview, rồi gửi duyệt.

## Bước 9: Test toàn bộ

1. Trang chủ: `https://dienmayhieu.com/`
2. Tra cứu đơn: `https://dienmayhieu.com/tra-cuu-don.php`
3. Admin quản lý đặt lịch: `https://dienmayhieu.com/pages/admin/QuanLyDatLich.php`
4. Thợ dashboard: `https://dienmayhieu.com/tho-dashboard.php`
5. Zalo Mini App preview

## Khắc phục lỗi thường gặp

| Lỗi | Nguyên nhân | Cách fix |
|---|---|---|
| 500 Internal Server Error | `.env` sai / thiếu | Kiểm tra `.env` |
| Database error | Chưa chạy migrate | Chạy `setup_tho.php` hoặc `schema.php` |
| Mini App trắng trang | Website chặn iframe | Sửa header server |
| 404 trang mới | Rewrite chưa cấu hình | Kiểm tra `.htaccess` / nginx config |
