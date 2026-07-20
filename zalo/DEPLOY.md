# TRIỂN KHAI ZALO MINI APP - ĐIỆN MÁY HIẾU

## Cấu trúc mới (đã chuẩn hóa theo Zalo Platforms)

```
zalo/
├── app-config.json              # Cấu hình chuẩn Zalo Mini App
├── index.html                   # Entry point dev (root)
├── vite.config.js               # Cấu hình Vite + zmp-vite-plugin
├── package.json                 # Dependencies & scripts
├── zmp-cli.json                 # Cấu hình ZMP CLI
├── src/
│   ├── index.html               # HTML template cho build
│   ├── app.jsx                  # Mount React app
│   ├── pages/
│   │   └── index.jsx            # Trang chính: mở webview
│   └── css/
│       └── app.scss             # Style
└── dist/                        # Output production build
```

## Chuẩn app-config.json theo nhà sản xuất

Xem tài liệu chính thức: https://docs.zaloplatforms.com/docs/MA/devtools/app-config

File `app-config.json` chỉ gồm:

- `app`: Cấu hình giao diện (title, headerColor, textColor, statusBar, ...)
- `listCSS`: Danh sách CSS cần load
- `listSyncJS`: Danh sách JS load đồng bộ
- `listAsyncJS`: Danh sách JS load bất đồng bộ

## Nguyên lý hoạt động

- Khi mở Mini App: hiển thị màn hình chào **Điện Máy Hiếu**
- Sau 300ms: tự động gọi `openWebview({ url: 'https://dienmayhieu.com/' })`
- Webview sẽ mở website chính trong Zalo
- Nếu webview không tự mở, khách bấm nút **"Mở website"**

## Build production

```bash
cd "C:\Users\pcpv\OneDrive\Desktop\DTH\zalo"
npm install      # chỉ cần lần đầu hoặc khi package.json thay đổi
npm run build    # build ra dist/ + copy app-config.json gốc
npm run zip      # tạo file zalo-production.zip
```

Hoặc thủ công:

```powershell
npm run build
Compress-Archive -Path "dist\*" -DestinationPath "C:\Users\pcpv\OneDrive\Desktop\DTH\zalo-production.zip" -Force
```

## File sẵn sàng upload

```
C:\Users\pcpv\OneDrive\Desktop\DTH\zalo-production.zip
```

## Upload lên Zalo Mini App Platform

1. Vào https://miniapp.zaloplatforms.com
2. Chọn app **Điện Máy Hiếu** (App ID: `1271764499975436667`)
3. **Quản lý phiên bản** → **Tạo phiên bản mới**
4. Upload file `zalo-production.zip`
5. Đợi Zalo xử lý → bấm **Preview** để test
6. Kiểm tra:
   - Header màu `#0f172a`
   - Tự động mở webview `https://dienmayhieu.com/`
   - Không còn cảnh báo `index.html` hay `robots.txt`
7. Bấm **Gửi duyệt** nếu OK

## Lưu ý quan trọng

- **KHÔNG** để file `robots.txt` trong ZIP — Zalo không hỗ trợ `.txt`
- **KHÔNG** dùng `index.html` tự do chứa iframe — phải qua `app-config.json` + React/Vite build
- **KHÔNG** để `manifest.json` tham chiếu file không tồn tại — đã xóa
- Nếu dùng `zmp-cli deploy`, cần Node.js v18 hoặc v20 (Node v26 trên máy anh hiện bị lỗi zmp-cli)

## Điều gì đã sửa so với bản cũ?

| Vấn đề cũ | Giải pháp |
|---|---|
| `index.html` will NOT be uploaded | Dùng template ZMP chuẩn, build bằng Vite |
| `robots.txt` unsupported extension | Xóa file `robots.txt` |
| `manifest.json` tham chiếu logo không tồn tại | Xóa `manifest.json` |
| App chạy lỗi Internal Server Error | Dùng `openWebview()` chuẩn thay vì iframe tự do |
