# HƯỚNG DẪN TRIỂN KHAI ZALO MINI APP ĐIỆN MÁY HIẾU

## Nguyên tắc
Mini App là "cửa sổ" nhúng website chính (`https://dienmayhieu.com/`).
Khi website thay đổi, Mini App tự động cập nhật theo — không cần build lại.

## Chuẩn bị file ZIP

```powershell
# Trong PowerShell, chạy lệnh này để tạo ZIP đúng:
Compress-Archive -Path 'C:\Users\pcpv\OneDrive\Desktop\DTH\zalo\*' -DestinationPath 'C:\Users\pcpv\OneDrive\Desktop\DTH\zalo-miniapp-deploy.zip' -Force
```

**Yêu cầu ZIP:**
- `app-config.json` phải nằm ở **root ZIP**
- `index.html` phải nằm ở **root ZIP**
- Không có thư mục con `zalo/` bên trong ZIP

## Các bước upload

1. Truy cập https://miniapp.zaloplatforms.com
2. Chọn app **Điện Máy Hiếu** (appId: `1271764499975436667`)
3. Vào **Quản lý phiên bản** → **Tạo phiên bản mới**
4. Upload file `zalo-miniapp-deploy.zip`
5. Chờ Zalo xử lý, sau đó bấm **Preview**
6. Kiểm tra:
   - Header màu `#0f172a`
   - Nội dung hiển thị website `dienmayhieu.com`
   - Không còn lỗi 404 / nginx
7. Bấm **Gửi duyệt**

## Nếu vẫn lỗi

| Lỗi | Nguyên nhân | Cách fix |
|-----|-------------|----------|
| 404 nginx | Upload nhầm thư mục / thiếu index.html root | Upload lại ZIP đúng cấu trúc |
| Trắng trang | Website gốc chặn iframe (X-Frame-Options) | Kiểm tra server headers |
| Không load GPS | Thiếu allow geolocation | Đã có `allow="geolocation; microphone; camera"` |
| Logo/app info sai | app-config.json chưa cập nhật | Sửa lại appId/title/headerColor |

## Lưu ý
- Không upload thư mục `zalo_mini_app/` (đã đánh dấu DEPRECATED).
- Không upload toàn bộ source code DTH vào ZIP.
- Đảm bảo website gốc đã bật HTTPS.
