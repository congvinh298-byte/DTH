# TRẠNG THÁI DỌN DẸP DTH - 13/07/2026

## ✅ ĐÃ XÓA THÀNH CÔNG

| File/Thư mục | Lý do |
|---|---|
| `replace_tuanori.js` | Script tạm, không liên quan |
| `test.php` (root) | File test |
| `controller/client/test.php` | File test |
| `old_footer.php` | Footer cũ, trống |
| `host_audit.php` | Audit host, không liên quan ứng dụng |
| `zalo_verifierKFcR1wpKB0Pbq9y_bAG-96-hgJY3tNDaDJKp.html` | File verify tạm |
| `db_migrate.php` | Migrate đã chạy xong |
| `sync_now.php` | Nguy hiểm — pull code từ GitHub public |
| `header_ftp.php` | Header cũ, hỏng encoding |
| `data.sql` | Dump database, rủi ro bảo mật |
| `dth_master_update.zip` | File zip backup cũ |

## ⚠️ CHƯA XÓA ĐƯỢC

| File/Thư mục | Lý do chưa xóa |
|---|---|
| `zalo_mini_app/` | Bị process `esbuild` / `rollup` khóa. Đã kill esbuild nhưng rollup.win32-x64-msvc.node vẫn bị giữ. Tạm dừng OneDrive cũng không xóa được. |

## 🔧 Cách xóa `zalo_mini_app/` thủ công

1. **Đóng tất cả ứng dụng liên quan:** VS Code, Cursor, terminal, trình duyệt.
2. **Restart máy** (cách chắc chắn nhất).
3. Sau khi restart, xóa thư mục:
   ```powershell
   Remove-Item -Path 'C:\Users\pcpv\OneDrive\Desktop\DTH\zalo_mini_app' -Recurse -Force
   ```
   Hoặc nhấn Shift + Delete trong File Explorer.

## 📊 Tổng dung lượng sau dọn dẹp
- **151.5 MB** (trước đó khoảng 170+ MB)
- Giảm chủ yếu nhờ xóa `data.sql`, `dth_master_update.zip`, và các file dư thừa.
- Sau khi xóa `zalo_mini_app/` sẽ giảm thêm ~12 MB.

## 🗂️ Folder `/zalo/` đã xây dựng đủ mạnh
- `index.html` — nhúng web chuẩn production (loader, offline check, postMessage).
- `app-config.json` — JSON valid, UTF-8 no BOM.
- `manifest.json` — PWA manifest.
- `robots.txt` — chặn bot.
- `www/index.html` — backup entry point.
- `README.md` + `DEPLOY.md` — tài liệu triển khai.
- `package.json` + `vite.config.js` — để VS Code extension nhận diện project.
- `src/App.jsx`, `src/main.jsx`, `src/pages/index.jsx`, `src/index.html` — cấu trúc dev/VS Code.
- `.vscode/settings.json` — config VS Code extension.
- `.cursor/rules/dmctn-taste-gate.mdc` — rule Cursor.
