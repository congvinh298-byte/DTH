:TASTE-GATE 1.0
# TRIỂN KHAI ZALO MINI APP - ĐIỆN MÁY HIẾU

## 2 chế độ

| Chế độ | Entry point | Mục đích |
|---|---|---|
| **Production** | `/zalo/index.html` root | Upload lên Zalo Mini App Platform |
| **Dev/VS Code** | `/zalo/src/index.html` | Preview trong VS Code extension |

## Bước 1: Tạo ZIP deploy Production

```powershell
# Trong PowerShell:
Compress-Archive -Path 'C:\Users\pcpv\OneDrive\Desktop\DTH\zalo\index.html', 'C:\Users\pcpv\OneDrive\Desktop\DTH\zalo\app-config.json', 'C:\Users\pcpv\OneDrive\Desktop\DTH\zalo\manifest.json', 'C:\Users\pcpv\OneDrive\Desktop\DTH\zalo\robots.txt' -DestinationPath 'C:\Users\pcpv\OneDrive\Desktop\DTH\zalo-production.zip' -Force
```

Hoặc nếu muốn deploy kèm cả cấu trúc src (không bắt buộc):
```powershell
Compress-Archive -Path 'C:\Users\pcpv\OneDrive\Desktop\DTH\zalo\*' -DestinationPath 'C:\Users\pcpv\OneDrive\Desktop\DTH\zalo-deploy.zip' -Force
```

**Yêu cầu ZIP:**
- `app-config.json` phải nằm ở **root ZIP**
- `index.html` (root) phải nằm ở **root ZIP**

## Bước 2: Upload lên Zalo Mini App Platform

1. Vào https://miniapp.zaloplatforms.com
2. Chọn app **Điện Máy Hiếu** (App ID: `1271764499975436667`)
3. **Quản lý phiên bản** → **Tạo phiên bản mới**
4. Upload ZIP
5. Đợi Zalo xử lý → bấm **Preview**
6. Kiểm tra:
   - Header màu `#0f172a`
   - Nội dung hiển thị website `https://dienmayhieu.com/`
   - Không còn lỗi 404/nginx
7. Bấm **Gửi duyệt**

## Chế độ Dev trong VS Code

```bash
cd C:\Users\pcpv\OneDrive\Desktop\DTH\zalo
npm install
npm run dev
```

VS Code extension Zalo Mini App sẽ nhận diện được project nhờ `package.json` + `vite.config.js` + `src/App.jsx`.

## Lưu ý

- `src/index.html` chỉ dùng cho dev, **KHÔNG phải** entry point production.
- `app-config.json` chỉ khai báo `index.html` (root) để production chạy đúng.
- KHÔNG upload `zalo_mini_app/` (thư mục cũ đã lỗi thời).
- Website gốc phải bật HTTPS.
