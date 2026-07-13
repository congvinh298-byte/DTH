:TASTE-GATE 1.0
# Zalo Mini App - Điện Máy Hiếu

## Nguyên tắc: SSOT (Single Source of Truth)

Mini App này chủ yếu là một "cửa sổ" nhúng trực tiếp website chính `https://dienmayhieu.com/`.

Khi website cập nhật, Mini App sẽ tự động cập nhật theo. **Không cần build lại mỗi lần website thay đổi**.

## 2 chế độ hoạt động

### 1. Production (deploy lên Zalo)
- Entry point: `/zalo/index.html` (file nằm ở root thư mục `zalo/`)
- File này nhúng iframe web trực tiếp.
- `app-config.json` chỉ khai báo `index.html` (root).

### 2. Dev / VS Code Extension
- Cấu trúc `/zalo/src/` chứa React/Vite tối thiểu để VS Code extension Zalo Mini App nhận diện project.
- `src/index.html` là template dev.
- Chạy `npm install` rồi `npm run dev` để preview trong VS Code.

## Cấu trúc thư mục

```
zalo/
├── app-config.json              # Cấu hình Zalo Mini App
├── index.html                   # ⭐ ENTRY POINT PRODUCTION
├── manifest.json                # PWA manifest
├── package.json                 # Để VS Code extension nhận diện
├── vite.config.js               # Cấu hình Vite
├── robots.txt                   # Chặn bot
├── DEPLOY.md                    # Hướng dẫn deploy
├── README.md                    # File này
├── src/                         # Dành cho dev/VS Code
│   ├── index.html
│   ├── main.jsx
│   ├── App.jsx
│   └── pages/
│       └── index.jsx
└── www/
    └── index.html               # Backup entry point
```

## Triển khai Production

Xem `DEPLOY.md`.

## Lưu ý quan trọng

- **KHÔNG xóa `/zalo/index.html` root** — đây là entry point thực tế khi upload lên Zalo.
- `/zalo/src/` chỉ để VS Code/dev mode nhận diện, không phải entry point production.
- Khi upload ZIP lên Zalo, đảm bảo `app-config.json` + root `index.html` nằm ở root ZIP.
