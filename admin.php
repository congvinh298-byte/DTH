<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");

// Only Admin can access
if(!isset($_COOKIE['token'])) {
    header("Location: /admin-login.php");
    exit;
}

if($getUser['level'] != 'admin') {
    die("<div style='background:#f43f5e; color:white; padding:30px; text-align:center; font-family:sans-serif; margin:50px auto; max-width:500px; border-radius:12px; box-shadow:0 10px 25px rgba(0,0,0,0.2);'>
        <h2 style='margin-top:0;'>LỖI KẾT NỐI / PHÂN QUYỀN</h2>
        <p style='font-size:16px; line-height:1.5;'>Tài khoản của bạn (Level: <strong>" . htmlspecialchars($getUser['level']) . "</strong>) không có quyền truy cập vào Khu Vực Quản Trị!</p>
        <p style='color:#fecdd3; font-size:14px; margin-bottom:25px;'>Bạn cần đăng xuất khỏi tài khoản hiện tại và đăng nhập bằng tài khoản Giám Đốc (Admin).</p>
        <a href='/pages/client/Logout.php' style='display:inline-block; background:#fff; color:#f43f5e; font-weight:bold; padding:12px 24px; border-radius:8px; text-decoration:none; margin-right:10px;'>Đăng Xuất Ngay</a>
        <a href='/' style='color:#fff; text-decoration:underline;'>Về Trang Chủ</a>
    </div>");
}

$title = "Văn Phòng Giám Đốc | Điện Máy Hiếu";
require_once(__DIR__."/pages/client/Head.php");
?>
<style>
    /* Admin Dashboard Custom Styles - ADHD & OCD */
    :root {
        --admin-bg: #0f172a;
        --admin-panel: #1e293b;
        --admin-border: #334155;
        --admin-text: #f8fafc;
        --admin-muted: #94a3b8;
        --c-cyan: #06b6d4;
        --c-yellow: #eab308;
        --c-rose: #f43f5e;
        --c-green: #10b981;
        --c-purple: #a855f7;
    }
    body {
        margin: 0;
        background-color: var(--admin-bg);
        color: var(--admin-text);
        font-family: 'Inter', sans-serif;
        overflow-x: hidden;
    }
    .admin-layout {
        display: flex;
        height: 100vh;
    }
    /* Left Sidebar */
    .sidebar {
        width: 320px;
        background: var(--admin-panel);
        border-right: 2px solid var(--admin-border);
        display: flex;
        flex-direction: column;
        padding: 24px;
        flex-shrink: 0;
    }
    .sidebar-header {
        text-align: center;
        padding-bottom: 24px;
        border-bottom: 2px dashed var(--admin-border);
        margin-bottom: 24px;
    }
    .menu-item {
        display: flex;
        align-items: center;
        padding: 16px 20px;
        margin-bottom: 12px;
        border-radius: 12px;
        cursor: pointer;
        font-weight: 800;
        font-size: 16px;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: var(--admin-muted);
        transition: all 0.2s;
        border: 2px solid transparent;
    }
    .menu-item i {
        font-size: 24px;
        margin-right: 16px;
        width: 30px;
        text-align: center;
    }
    .menu-item:hover {
        background: rgba(255,255,255,0.05);
        color: #fff;
    }
    .menu-item.active {
        background: var(--c-cyan);
        color: #0f172a;
        border-color: #fff;
        box-shadow: 4px 4px 0px rgba(0,0,0,0.5);
        transform: translate(-2px, -2px);
    }
    
    /* Right Content */
    .content-area {
        flex-grow: 1;
        padding: 40px;
        overflow-y: auto;
        position: relative;
    }
    .tab-content {
        display: none;
        animation: fadeIn 0.3s ease;
    }
    .tab-content.active {
        display: block;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* UI Elements */
    .card {
        background: var(--admin-panel);
        border: 2px solid var(--admin-border);
        border-radius: 20px;
        padding: 30px;
        box-shadow: 8px 8px 0px rgba(0,0,0,0.3);
        margin-bottom: 30px;
    }
    .card-title {
        font-size: 24px;
        font-weight: 900;
        color: var(--c-yellow);
        text-transform: uppercase;
        margin-bottom: 24px;
        border-bottom: 2px solid var(--admin-border);
        padding-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .input-group {
        margin-bottom: 24px;
    }
    .input-group label {
        display: block;
        font-size: 14px;
        font-weight: 800;
        text-transform: uppercase;
        color: var(--admin-muted);
        margin-bottom: 8px;
    }
    .input-group input, .input-group textarea {
        width: 100%;
        background: rgba(0,0,0,0.2);
        border: 2px solid var(--admin-border);
        color: #fff;
        padding: 16px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 700;
        outline: none;
        transition: border 0.2s;
    }
    .input-group input:focus, .input-group textarea:focus {
        border-color: var(--c-cyan);
    }
    .btn-action {
        background: var(--c-cyan);
        color: #0f172a;
        border: none;
        padding: 16px 32px;
        border-radius: 12px;
        font-size: 18px;
        font-weight: 900;
        text-transform: uppercase;
        cursor: pointer;
        transition: all 0.1s;
        box-shadow: 4px 4px 0px rgba(0,0,0,0.5);
    }
    .btn-action:active {
        transform: translate(2px, 2px);
        box-shadow: 2px 2px 0px rgba(0,0,0,0.5);
    }
    .qr-result {
        background: #fff;
        padding: 20px;
        border-radius: 16px;
        display: inline-block;
        margin-top: 20px;
        border: 4px solid var(--c-green);
        box-shadow: 8px 8px 0px rgba(0,0,0,0.3);
    }
</style>

<div class="admin-layout">
    
    <!-- LEFT SIDEBAR -->
    <div class="sidebar">
        <div class="sidebar-header">
            <div style="font-size: 50px; margin-bottom: 10px;">👑</div>
            <h1 style="margin: 0; font-size: 22px; font-weight: 900; color: #fff; text-transform: uppercase; letter-spacing: 1px;">Văn Phòng Giám Đốc</h1>
            <p style="margin: 5px 0 0; color: var(--c-green); font-weight: bold;">[ Admin Mode ]</p>
            <a href="/pages/client/Logout.php" style="display: inline-block; margin-top: 15px; color: var(--c-rose); text-decoration: none; font-size: 14px; font-weight: bold; background: rgba(244, 63, 94, 0.1); padding: 8px 16px; border-radius: 8px; border: 1px solid var(--c-rose);"><i class="fa-solid fa-power-off"></i> Đăng Xuất An Toàn</a>
        </div>
        
        <div class="menu-item active" onclick="switchTab('tab-tichdiem', this)">
            <i class="fa-solid fa-gem" style="color: var(--c-purple);"></i> Tích Điểm Khách
        </div>
        <div class="menu-item" onclick="switchTab('tab-store', this)">
            <i class="fa-solid fa-box-open" style="color: var(--c-cyan);"></i> Quản lý Cửa Hàng
        </div>
        <div class="menu-item" onclick="switchTab('tab-orders', this)">
            <i class="fa-solid fa-cart-shopping" style="color: var(--c-yellow);"></i> Quản lý Đơn Mua Hàng
        </div>
        <div class="menu-item" onclick="switchTab('tab-technicians', this)">
            <i class="fa-solid fa-user-gear" style="color: #fb923c;"></i> Quản lý Thợ
        </div>
        <div class="menu-item" onclick="switchTab('tab-bookings', this)">
            <i class="fa-solid fa-calendar-check" style="color: #38bdf8;"></i> Đơn Gọi Thợ
        </div>
        <div class="menu-item" onclick="switchTab('tab-qr-khuyenmai', this)">
            <i class="fa-solid fa-ticket" style="color: var(--c-rose);"></i> QR Khuyến Mãi
        </div>
        <div class="menu-item" onclick="switchTab('tab-qr-baohanh', this)">
            <i class="fa-solid fa-shield-halved" style="color: var(--c-green);"></i> QR Bảo Hành
        </div>
        <div class="menu-item" onclick="switchTab('tab-bct', this)">
            <i class="fa-solid fa-file-contract" style="color: #60a5fa;"></i> Hồ Sơ BCT
        </div>
        <div class="menu-item" onclick="switchTab('tab-thongke', this)">
            <i class="fa-solid fa-chart-line" style="color: #cbd5e1;"></i> Thống Kê
        </div>
    </div>
    
    <!-- RIGHT CONTENT -->
    <div class="content-area">
        
        <!-- TAB 1: TÍCH ĐIỂM -->
        <div id="tab-tichdiem" class="tab-content active">
            <div class="card">
                <div class="card-title"><i class="fa-solid fa-gem"></i> Hệ Thống Tích Điểm Khách Hàng</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                    <div>
                        <div class="input-group">
                            <label>Tra cứu theo Số Điện Thoại</label>
                            <div style="display: flex; gap: 10px;">
                                <input type="tel" id="td_phone" placeholder="Nhập SĐT khách hàng...">
                                <button class="btn-action" onclick="findCustomer()" style="background: var(--c-purple); color: #fff;"><i class="fa-solid fa-magnifying-glass"></i></button>
                            </div>
                        </div>
                        
                        <div id="customer_info_panel" style="display: none; background: rgba(16, 185, 129, 0.1); border: 2px dashed var(--c-green); padding: 20px; border-radius: 12px; margin-bottom: 24px;">
                            <h4 style="margin: 0 0 10px; color: var(--c-green); font-size: 18px; text-transform: uppercase;">Thông tin Khách Hàng</h4>
                            <p style="margin: 5px 0; font-size: 16px;">👤 Tên: <strong id="ci_name" style="color: #fff;"></strong></p>
                            <p style="margin: 5px 0; font-size: 16px;">🏠 Đ/C: <strong id="ci_address" style="color: #fff;"></strong></p>
                            <p style="margin: 5px 0; font-size: 16px;">💎 Điểm hiện tại: <strong id="ci_points" style="color: var(--c-yellow); font-size: 24px;"></strong></p>
                        </div>
                        
                        <div id="customer_error_panel" style="display: none; background: rgba(244, 63, 94, 0.1); border: 2px dashed var(--c-rose); padding: 20px; border-radius: 12px; margin-bottom: 24px; color: var(--c-rose); font-weight: bold;">
                            Không tìm thấy khách hàng! Yêu cầu khách hàng truy cập website và đăng nhập bằng SĐT này để hệ thống tạo hồ sơ.
                        </div>
                    </div>
                    
                    <div id="add_points_panel" style="opacity: 0.3; pointer-events: none;">
                        <h3 style="color: var(--c-cyan); margin-top: 0;">Nạp Điểm Mua Hàng</h3>
                        <div class="input-group">
                            <label>Số tiền hóa đơn (VNĐ)</label>
                            <input type="number" id="td_amount" placeholder="VD: 500000" oninput="calcPoints()">
                        </div>
                        <div class="input-group">
                            <label>Số điểm quy đổi (100.000đ = 1 điểm)</label>
                            <input type="number" id="td_points" placeholder="0" readonly style="background: rgba(0,0,0,0.4); color: var(--c-yellow); font-size: 24px;">
                        </div>
                        <button class="btn-action" id="btnAddPoints" onclick="addPoints()" style="width: 100%;"><i class="fa-solid fa-plus"></i> Cộng Điểm Ngay</button>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- TAB: QUẢN LÝ CỬA HÀNG -->
        <div id="tab-store" class="tab-content">
            <div class="card">
                <div class="card-title"><i class="fa-solid fa-box-open"></i> Quản Lý Cửa Hàng & In 3D</div>
                
                <div style="display: flex; gap: 20px; margin-bottom: 24px;">
                    <button class="btn-action" onclick="$('#addProductModal').show()" style="font-size: 14px; padding: 10px 20px; display: flex; align-items: center; gap: 8px;"><i class="fa-solid fa-plus"></i> Thêm Sản Phẩm Mới</button>
                    <button class="btn-action" onclick="loadProducts('dienmay')" style="background: rgba(255,255,255,0.1); color: #fff; font-size: 14px; padding: 10px 20px;"><i class="fa-solid fa-tv"></i> Điện Máy</button>
                    <button class="btn-action" onclick="loadProducts('3d')" style="background: rgba(255,255,255,0.1); color: #fff; font-size: 14px; padding: 10px 20px;"><i class="fa-solid fa-cube"></i> Mô hình 3D</button>
                </div>
                
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; color: #fff; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--admin-border); color: var(--c-cyan);">
                                <th style="padding: 12px; width: 60px;">ID</th>
                                <th style="padding: 12px; width: 80px;">Ảnh</th>
                                <th style="padding: 12px;">Tên Sản Phẩm</th>
                                <th style="padding: 12px; width: 150px;">Phân loại</th>
                                <th style="padding: 12px; width: 150px;">Giá bán</th>
                                <th style="padding: 12px; width: 100px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="storeTableBody">
                            <tr><td colspan="6" style="padding: 20px; text-align: center; color: var(--admin-muted);">Đang tải dữ liệu...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- TAB: QUẢN LÝ ĐƠN HÀNG -->
        <div id="tab-orders" class="tab-content">
            <div class="card">
                <div class="card-title"><i class="fa-solid fa-cart-shopping"></i> Quản Lý Đơn Đặt Hàng</div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; color: #fff; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--admin-border); color: var(--c-yellow);">
                                <th style="padding: 12px; width: 80px;">Mã Đơn</th>
                                <th style="padding: 12px;">Khách Hàng</th>
                                <th style="padding: 12px;">Tổng Tiền</th>
                                <th style="padding: 12px; width: 150px;">Trạng Thái</th>
                                <th style="padding: 12px; width: 180px;">Thời Gian</th>
                                <th style="padding: 12px; width: 120px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="ordersTableBody">
                            <tr><td colspan="6" style="padding: 20px; text-align: center; color: var(--admin-muted);">Đang tải dữ liệu...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB: QUẢN LÝ THỢ -->
        <div id="tab-technicians" class="tab-content">
            <div class="card">
                <div class="card-title" style="display: flex; justify-content: space-between; align-items: center;">
                    <div><i class="fa-solid fa-user-gear"></i> Quản Lý Thợ Kỹ Thuật</div>
                    <button onclick="$('#addTechModal').css('display', 'flex')" class="btn" style="background: var(--c-green); color: white; padding: 8px 15px;"><i class="fa-solid fa-plus"></i> Thêm Thợ</button>
                </div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; color: #fff; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--admin-border); color: #fb923c;">
                                <th style="padding: 12px; width: 60px;">ID</th>
                                <th style="padding: 12px;">Tên Thợ</th>
                                <th style="padding: 12px;">Tài khoản</th>
                                <th style="padding: 12px;">SĐT / Vùng</th>
                                <th style="padding: 12px;">Dư nợ (Phí)</th>
                                <th style="padding: 12px; width: 120px;">Trạng thái</th>
                                <th style="padding: 12px; width: 180px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="techTableBody">
                            <tr><td colspan="7" style="padding: 20px; text-align: center; color: var(--admin-muted);">Đang tải dữ liệu...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- TAB: QUẢN LÝ ĐƠN GỌI THỢ -->
        <div id="tab-bookings" class="tab-content">
            <div class="card">
                <div class="card-title"><i class="fa-solid fa-calendar-check"></i> Đơn Gọi Thợ Kỹ Thuật</div>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse; color: #fff; text-align: left;">
                        <thead>
                            <tr style="border-bottom: 2px solid var(--admin-border); color: #38bdf8;">
                                <th style="padding: 12px; width: 60px;">Mã</th>
                                <th style="padding: 12px;">Khách Hàng</th>
                                <th style="padding: 12px;">Dịch Vụ</th>
                                <th style="padding: 12px; width: 150px;">Trạng Thái</th>
                                <th style="padding: 12px;">Thợ Nhận</th>
                                <th style="padding: 12px; width: 120px;">Hành động</th>
                            </tr>
                        </thead>
                        <tbody id="bookingsTableBody">
                            <tr><td colspan="6" style="padding: 20px; text-align: center; color: var(--admin-muted);">Đang tải dữ liệu...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <!-- Modal Thêm Sản Phẩm -->
        <div id="addProductModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 100; align-items: center; justify-content: center;">
            <div class="card" style="width: 100%; max-width: 600px; margin: 0;">
                <div class="card-title" style="justify-content: space-between;">
                    <span><i class="fa-solid fa-plus"></i> Thêm Sản Phẩm Mới</span>
                    <i class="fa-solid fa-xmark" style="cursor: pointer; color: var(--c-rose);" onclick="$('#addProductModal').hide()"></i>
                </div>
                <form id="formAddProduct" onsubmit="submitProduct(event)">
                    <div class="input-group">
                        <label>Phân loại</label>
                        <select id="p_type" required style="width: 100%; background: rgba(0,0,0,0.2); border: 2px solid var(--admin-border); color: #fff; padding: 16px; border-radius: 12px; font-size: 16px; font-weight: 700; outline: none;">
                            <option value="dienmay">Sản phẩm Điện Máy</option>
                            <option value="3d">Sản phẩm In 3D</option>
                        </select>
                    </div>
                    <div class="input-group">
                        <label>Tên Sản Phẩm</label>
                        <input type="text" id="p_name" required placeholder="VD: Tủ lạnh Samsung Inverter 208 Lít...">
                    </div>
                    <div class="input-group">
                        <label>Giá Bán (VNĐ)</label>
                        <input type="number" id="p_price" required placeholder="VD: 5500000">
                    </div>
                    <div class="input-group">
                        <label>Đường dẫn hình ảnh (URL)</label>
                        <input type="text" id="p_image" placeholder="https://...">
                    </div>
                    <div class="input-group">
                        <label>Mô tả chi tiết</label>
                        <textarea id="p_description" rows="3" placeholder="Đặc điểm nổi bật..."></textarea>
                    </div>
                    <button type="submit" class="btn-action" style="width: 100%;"><i class="fa-solid fa-save"></i> LƯU SẢN PHẨM</button>
                </form>
            </div>
        </div>
        
        <!-- Modal Xem Chi Tiết Đơn Hàng -->
        <div id="viewOrderModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 100; align-items: center; justify-content: center;">
            <div class="card" style="width: 100%; max-width: 700px; margin: 0; max-height: 90vh; overflow-y: auto;">
                <div class="card-title" style="justify-content: space-between;">
                    <span><i class="fa-solid fa-receipt"></i> Chi Tiết Đơn Hàng #<span id="v_order_id"></span></span>
                    <i class="fa-solid fa-xmark" style="cursor: pointer; color: var(--c-rose);" onclick="$('#viewOrderModal').hide()"></i>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px; font-size: 14px;">
                    <div>
                        <p style="color: var(--admin-muted); margin: 0 0 5px;">Tên Khách Hàng</p>
                        <p style="font-weight: bold; font-size: 16px; margin: 0 0 15px;" id="v_customer_name"></p>
                        
                        <p style="color: var(--admin-muted); margin: 0 0 5px;">Số Điện Thoại</p>
                        <p style="font-weight: bold; font-size: 16px; margin: 0 0 15px;" id="v_phone"></p>
                    </div>
                    <div>
                        <p style="color: var(--admin-muted); margin: 0 0 5px;">Trạng Thái</p>
                        <p style="margin: 0 0 15px;">
                            <select id="v_status" onchange="updateOrderStatus()" style="background: rgba(0,0,0,0.2); border: 2px solid var(--c-yellow); color: var(--c-yellow); padding: 8px; border-radius: 8px; font-weight: bold; outline: none;">
                                <option value="pending" style="color:#000;">Chờ xử lý</option>
                                <option value="shipping" style="color:#000;">Đang giao hàng</option>
                                <option value="completed" style="color:#000;">Đã hoàn thành</option>
                                <option value="cancelled" style="color:#000;">Đã hủy</option>
                            </select>
                        </p>
                        
                        <p style="color: var(--admin-muted); margin: 0 0 5px;">Ghi chú của khách</p>
                        <p style="font-style: italic; margin: 0 0 15px; color: var(--c-cyan);" id="v_note"></p>
                    </div>
                    <div style="grid-column: span 2;">
                        <p style="color: var(--admin-muted); margin: 0 0 5px;">Địa chỉ giao hàng</p>
                        <p style="font-weight: bold; font-size: 16px; margin: 0 0 15px;" id="v_address"></p>
                    </div>
                </div>
                
                <div style="border-top: 2px dashed var(--admin-border); padding-top: 20px;">
                    <h4 style="margin: 0 0 15px; color: var(--c-cyan);">Danh sách sản phẩm</h4>
                    <div id="v_items_list" style="max-height: 250px; overflow-y: auto;">
                        <!-- JS renders items here -->
                    </div>
                    <div style="text-align: right; margin-top: 15px; font-size: 20px; color: var(--c-yellow); font-weight: 900;">
                        TỔNG CỘNG: <span id="v_total">0</span>đ
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB 2: QR KHUYẾN MÃI -->
        <div id="tab-qr-khuyenmai" class="tab-content">
            <div class="card">
                <div class="card-title"><i class="fa-solid fa-ticket"></i> Máy Tạo QR Code Khuyến Mãi</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                    <div>
                        <div class="input-group">
                            <label>Nội dung / Link Khuyến Mãi</label>
                            <textarea id="qr_km_content" rows="4" placeholder="Nhập đường link hoặc mã giảm giá..."></textarea>
                        </div>
                        <button class="btn-action" onclick="generateQR('km')" style="background: var(--c-yellow);"><i class="fa-solid fa-qrcode"></i> Tạo QR Khuyến Mãi</button>
                    </div>
                    <div style="text-align: center;">
                        <h3 style="color: var(--admin-muted); margin-top: 0;">Kết quả QR Code</h3>
                        <div id="qr_km_result" style="min-height: 200px; display: flex; align-items: center; justify-content: center; border: 2px dashed var(--admin-border); border-radius: 12px;">
                            <span style="color: var(--admin-muted);">Chưa có dữ liệu</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- TAB 3: QR BẢO HÀNH -->
        <div id="tab-qr-baohanh" class="tab-content">
            <div class="card">
                <div class="card-title"><i class="fa-solid fa-shield-halved"></i> Máy Tạo QR Code Bảo Hành</div>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 40px;">
                    <div>
                        <div class="input-group">
                            <label>Tên / Model Sản Phẩm</label>
                            <input type="text" id="qr_bh_product" placeholder="VD: Máy giặt Toshiba X...">
                        </div>
                        <div class="input-group">
                            <label>Số điện thoại khách (ID Bảo hành)</label>
                            <input type="text" id="qr_bh_phone" placeholder="09xxxx...">
                        </div>
                        <div class="input-group">
                            <label>Thời hạn bảo hành</label>
                            <input type="text" id="qr_bh_time" placeholder="VD: 12 Tháng">
                        </div>
                        <button class="btn-action" onclick="generateQR('bh')" style="background: var(--c-green); color: #fff;"><i class="fa-solid fa-qrcode"></i> Tạo Tem QR Bảo Hành</button>
                    </div>
                    <div style="text-align: center;">
                        <h3 style="color: var(--admin-muted); margin-top: 0;">Kết quả QR Code</h3>
                        <div id="qr_bh_result" style="min-height: 200px; display: flex; align-items: center; justify-content: center; border: 2px dashed var(--admin-border); border-radius: 12px;">
                            <span style="color: var(--admin-muted);">Chưa có dữ liệu</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- TAB 4: THỐNG KÊ -->
        <!-- TAB BCT -->
        <div id="tab-bct" class="tab-content">
            <div class="card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <div>
                        <div class="card-title" style="margin-bottom: 5px;"><i class="fa-solid fa-file-contract"></i> Hồ Sơ Đăng Ký Bộ Công Thương</div>
                        <p style="color: #94a3b8; margin: 0;">Tải về các file chính sách định dạng Word (.doc) để in ra, đóng dấu và nộp cho BCT.</p>
                    </div>
                    <a href="/controller/admin/ExportAllPolicies.php" target="_blank" class="btn" style="background: var(--c-purple); color: white; padding: 12px 24px; text-decoration: none; display: inline-flex; align-items: center; gap: 10px; font-weight: bold; border-radius: 8px;"><i class="fa-solid fa-print"></i> In Toàn Bộ (7 File)</a>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th style="width: 50px; text-align: center;">STT</th>
                            <th>Tên tài liệu</th>
                            <th style="width: 150px; text-align: right;">Tệp đính kèm</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align: center; font-weight: bold;">1</td>
                            <td>Chính sách bảo mật <span style="color: var(--c-rose);">(*)</span></td>
                            <td style="text-align: right;"><a href="/controller/admin/ExportPolicy.php?id=1" target="_blank" class="btn" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; padding: 6px 12px; text-decoration: none; display: inline-block;"><i class="fa-solid fa-download"></i> Download</a></td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: bold;">2</td>
                            <td>Phương thức tiếp nhận và giải quyết phản ánh, yêu cầu, khiếu nại <span style="color: var(--c-rose);">(*)</span></td>
                            <td style="text-align: right;"><a href="/controller/admin/ExportPolicy.php?id=2" target="_blank" class="btn" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; padding: 6px 12px; text-decoration: none; display: inline-block;"><i class="fa-solid fa-download"></i> Download</a></td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: bold;">3</td>
                            <td>Chính sách giá <span style="color: var(--c-rose);">(*)</span></td>
                            <td style="text-align: right;"><a href="/controller/admin/ExportPolicy.php?id=3" target="_blank" class="btn" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; padding: 6px 12px; text-decoration: none; display: inline-block;"><i class="fa-solid fa-download"></i> Download</a></td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: bold;">4</td>
                            <td>Chính sách về thanh toán <span style="color: var(--c-rose);">(*)</span></td>
                            <td style="text-align: right;"><a href="/controller/admin/ExportPolicy.php?id=4" target="_blank" class="btn" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; padding: 6px 12px; text-decoration: none; display: inline-block;"><i class="fa-solid fa-download"></i> Download</a></td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: bold;">5</td>
                            <td>Các điều kiện hoặc hạn chế trong việc cung cấp hàng hóa hoặc dịch vụ trên nền tảng <span style="color: var(--c-rose);">(*)</span></td>
                            <td style="text-align: right;"><a href="/controller/admin/ExportPolicy.php?id=5" target="_blank" class="btn" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; padding: 6px 12px; text-decoration: none; display: inline-block;"><i class="fa-solid fa-download"></i> Download</a></td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: bold;">6</td>
                            <td>Chính sách giao hàng, đổi trả và hoàn tiền (áp dụng cho hàng hóa) hoặc phương thức cung cấp dịch vụ, chính sách chấm dứt dịch vụ và hoàn tiền (áp dụng cho dịch vụ) <span style="color: var(--c-rose);">(*)</span></td>
                            <td style="text-align: right;"><a href="/controller/admin/ExportPolicy.php?id=6" target="_blank" class="btn" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; padding: 6px 12px; text-decoration: none; display: inline-block;"><i class="fa-solid fa-download"></i> Download</a></td>
                        </tr>
                        <tr>
                            <td style="text-align: center; font-weight: bold;">7</td>
                            <td>Hình thức hỗ trợ trực tuyến</td>
                            <td style="text-align: right;"><a href="/controller/admin/ExportPolicy.php?id=7" target="_blank" class="btn" style="background: rgba(255,255,255,0.1); border: 1px solid rgba(255,255,255,0.2); color: white; padding: 6px 12px; text-decoration: none; display: inline-block;"><i class="fa-solid fa-download"></i> Download</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div id="tab-thongke" class="tab-content">
            <div class="card">
                <div class="card-title"><i class="fa-solid fa-chart-line"></i> Bảng Thống Kê Nhanh</div>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                    <?php
                        $total_users = $DMH->get_row("SELECT COUNT(*) as c FROM `users` WHERE `level` = 'user'")['c'];
                        $total_points = $DMH->get_row("SELECT SUM(points) as s FROM `users` WHERE `level` = 'user'")['s'];
                        $total_tho = $DMH->get_row("SELECT COUNT(*) as c FROM `users` WHERE `level` = 'tho'")['c'];
                    ?>
                    <div style="background: rgba(6, 182, 212, 0.1); border: 2px solid var(--c-cyan); padding: 30px; border-radius: 16px; text-align: center;">
                        <h2 style="margin: 0; font-size: 48px; color: var(--c-cyan);"><?= number_format($total_users) ?></h2>
                        <p style="margin: 10px 0 0; color: #fff; font-weight: bold; text-transform: uppercase;">Khách Hàng</p>
                    </div>
                    <div style="background: rgba(234, 179, 8, 0.1); border: 2px solid var(--c-yellow); padding: 30px; border-radius: 16px; text-align: center;">
                        <h2 style="margin: 0; font-size: 48px; color: var(--c-yellow);"><?= number_format((int)$total_points) ?></h2>
                        <p style="margin: 10px 0 0; color: #fff; font-weight: bold; text-transform: uppercase;">Tổng Điểm Tích Lũy</p>
                    </div>
                    <div style="background: rgba(244, 63, 94, 0.1); border: 2px solid var(--c-rose); padding: 30px; border-radius: 16px; text-align: center;">
                        <h2 style="margin: 0; font-size: 48px; color: var(--c-rose);"><?= number_format($total_tho) ?></h2>
                        <p style="margin: 10px 0 0; color: #fff; font-weight: bold; text-transform: uppercase;">Thợ Đối Tác</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Thêm Thợ -->
        <div id="addTechModal" style="display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.8); z-index: 100; align-items: center; justify-content: center;">
            <form onsubmit="submitAddTech(event)" style="background: #1e293b; padding: 25px; border-radius: 12px; width: 90%; max-width: 450px; border: 1px solid var(--admin-border);">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                    <h3 style="margin: 0; color: #fb923c;">Thêm Thợ Mới</h3>
                    <i class="fa-solid fa-xmark" style="cursor: pointer; font-size: 20px;" onclick="$('#addTechModal').hide()"></i>
                </div>
                
                <div class="field" style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom: 5px;">Tên hiển thị (Tên Thợ)</label>
                    <input type="text" id="tech_name" required style="width: 100%; padding: 10px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 6px;">
                </div>
                
                <div class="field" style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom: 5px;">Tên đăng nhập (Tài khoản)</label>
                    <input type="text" id="tech_username" required style="width: 100%; padding: 10px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 6px;">
                </div>

                <div class="field" style="margin-bottom: 15px;">
                    <label style="display:block; margin-bottom: 5px;">Mật khẩu</label>
                    <input type="text" id="tech_password" required style="width: 100%; padding: 10px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 6px;">
                </div>
                
                <div class="field" style="margin-bottom: 20px;">
                    <label style="display:block; margin-bottom: 5px;">Số điện thoại</label>
                    <input type="text" id="tech_phone" required style="width: 100%; padding: 10px; background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.1); color: #fff; border-radius: 6px;">
                </div>
                
                <button type="submit" class="btn" style="width: 100%; background: #fb923c; color: white; padding: 12px; font-weight: bold; font-size: 16px;">Tạo Tài Khoản</button>
            </form>
        </div>

    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
function switchTab(tabId, element) {
    $('.menu-item').removeClass('active');
    $(element).addClass('active');
    
    $('.tab-content').removeClass('active');
    $('#' + tabId).addClass('active');
}

let currentCustomerPhone = '';

function findCustomer() {
    let phone = $('#td_phone').val().trim();
    if(!phone) { alert("Nhập số điện thoại!"); return; }
    
    $.ajax({
        url: "/controller/client/AdminAction.php",
        method: "POST",
        data: { action: 'find_customer', phone: phone },
        success: function(r) {
            let res = typeof r === 'string' ? JSON.parse(r) : r;
            if(res.status == 'success') {
                $('#customer_error_panel').hide();
                $('#customer_info_panel').show();
                $('#ci_name').text(res.data.name || 'Chưa cập nhật tên');
                $('#ci_address').text(res.data.address || 'Chưa cập nhật địa chỉ');
                $('#ci_points').text(res.data.points);
                
                $('#add_points_panel').css({'opacity': 1, 'pointer-events': 'auto'});
                currentCustomerPhone = phone;
            } else {
                $('#customer_info_panel').hide();
                $('#customer_error_panel').show();
                $('#add_points_panel').css({'opacity': 0.3, 'pointer-events': 'none'});
                currentCustomerPhone = '';
            }
        }
    });
}

function calcPoints() {
    let amount = parseInt($('#td_amount').val()) || 0;
    let points = Math.floor(amount / 100000); // 100k = 1 point
    $('#td_points').val(points);
}

function addPoints() {
    let points = parseInt($('#td_points').val()) || 0;
    if(points <= 0) { alert("Số điểm phải lớn hơn 0!"); return; }
    if(!currentCustomerPhone) return;
    
    $('#btnAddPoints').html('<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...').prop('disabled', true);
    
    $.ajax({
        url: "/controller/client/AdminAction.php",
        method: "POST",
        data: { action: 'add_points', phone: currentCustomerPhone, points: points },
        success: function(r) {
            let res = typeof r === 'string' ? JSON.parse(r) : r;
            if(res.status == 'success') {
                alert("Cộng thành công " + points + " điểm!");
                $('#td_amount').val('');
                $('#td_points').val('');
                findCustomer(); // reload points
            } else {
                alert(res.msg);
            }
            $('#btnAddPoints').html('<i class="fa-solid fa-plus"></i> Cộng Điểm Ngay').prop('disabled', false);
        }
    });
}

function generateQR(type) {
    let dataStr = '';
    
    if (type === 'km') {
        let content = $('#qr_km_content').val().trim();
        if(!content) { alert("Nhập nội dung!"); return; }
        dataStr = encodeURIComponent(content);
        let qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${dataStr}&bgcolor=ffffff&color=000000`;
        $('#qr_km_result').html(`
            <div class="qr-result">
                <img src="${qrUrl}" width="200" height="200" style="display:block; margin:0 auto;">
                <p style="text-align:center; font-weight:bold; color: #0f172a; margin: 10px 0 0;">Mã Khuyến Mãi</p>
            </div>
        `);
    } else if (type === 'bh') {
        let product = $('#qr_bh_product').val().trim();
        let phone = $('#qr_bh_phone').val().trim();
        let time = $('#qr_bh_time').val().trim();
        if(!product || !phone) { alert("Nhập đủ thông tin sản phẩm và SĐT!"); return; }
        
        let text = `BẢO HÀNH ĐIỆN MÁY HIẾU\nSP: ${product}\nSĐT/ID: ${phone}\nThời hạn: ${time}\nWeb: dienmayhieu.com`;
        dataStr = encodeURIComponent(text);
        let qrUrl = `https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=${dataStr}&bgcolor=ffffff&color=0f172a`;
        $('#qr_bh_result').html(`
            <div class="qr-result">
                <img src="${qrUrl}" width="200" height="200" style="display:block; margin:0 auto;">
                <p style="text-align:center; font-weight:bold; color: #0f172a; margin: 10px 0 0;">Tem Bảo Hành Điện Tử</p>
            </div>
        `);
    }
}

// =================== LOGIC CỬA HÀNG & ĐƠN HÀNG ===================
function loadProducts(type = 'all') {
    $('#storeTableBody').html('<tr><td colspan="6" style="padding: 20px; text-align: center;">Đang tải dữ liệu...</td></tr>');
    $.ajax({
        url: "/controller/client/AdminStoreAction.php",
        method: "POST",
        data: { action: 'list_products', type: type },
        success: function(r) {
            try {
                let res = typeof r === 'string' ? JSON.parse(r) : r;
                if(res.status == 'success') {
                    let html = '';
                    if(res.data.length === 0) {
                        html = '<tr><td colspan="6" style="padding: 20px; text-align: center;">Chưa có sản phẩm nào</td></tr>';
                    } else {
                        res.data.forEach(p => {
                            let typeBadge = p.type === 'dienmay' ? '<span style="background:var(--c-cyan);color:#000;padding:4px 8px;border-radius:4px;font-size:12px;font-weight:bold;">Điện Máy</span>' : '<span style="background:var(--c-purple);color:#fff;padding:4px 8px;border-radius:4px;font-size:12px;font-weight:bold;">In 3D</span>';
                            let priceFormat = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(p.price);
                            html += `
                            <tr style="border-bottom: 1px solid var(--admin-border);">
                                <td style="padding: 12px;">#${p.id}</td>
                                <td style="padding: 12px;"><img src="${p.image || '/public/assets/logo.png'}" style="width:50px; height:50px; object-fit:cover; border-radius:8px;"></td>
                                <td style="padding: 12px; font-weight:bold;">${p.name}</td>
                                <td style="padding: 12px;">${typeBadge}</td>
                                <td style="padding: 12px; color:var(--c-yellow); font-weight:bold;">${priceFormat}</td>
                                <td style="padding: 12px;">
                                    <button onclick="deleteProduct(${p.id})" style="background:none; border:none; color:var(--c-rose); cursor:pointer; font-size:18px;"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>`;
                        });
                    }
                    $('#storeTableBody').html(html);
                }
            } catch(e) { console.error(e); }
        }
    });
}

function submitProduct(e) {
    e.preventDefault();
    let data = {
        action: 'add_product',
        type: $('#p_type').val(),
        name: $('#p_name').val(),
        price: $('#p_price').val(),
        image: $('#p_image').val(),
        description: $('#p_description').val()
    };
    
    $.ajax({
        url: "/controller/client/AdminStoreAction.php",
        method: "POST",
        data: data,
        success: function(r) {
            let res = typeof r === 'string' ? JSON.parse(r) : r;
            if(res.status == 'success') {
                alert("Đã thêm sản phẩm thành công!");
                $('#addProductModal').css('display', 'none');
                document.getElementById('formAddProduct').reset();
                loadProducts('all');
            } else {
                alert(res.msg || "Lỗi khi thêm sản phẩm");
            }
        }
    });
}

function deleteProduct(id) {
    if(confirm("Xóa sản phẩm này?")) {
        $.ajax({
            url: "/controller/client/AdminStoreAction.php",
            method: "POST",
            data: { action: 'delete_product', id: id },
            success: function(r) {
                let res = typeof r === 'string' ? JSON.parse(r) : r;
                if(res.status == 'success') loadProducts('all');
            }
        });
    }
}

// --- QUẢN LÝ ĐƠN HÀNG ---
function loadOrders() {
    $('#ordersTableBody').html('<tr><td colspan="6" style="padding: 20px; text-align: center;">Đang tải dữ liệu...</td></tr>');
    $.ajax({
        url: "/controller/client/AdminStoreAction.php",
        method: "POST",
        data: { action: 'list_orders' },
        success: function(r) {
            try {
                let res = typeof r === 'string' ? JSON.parse(r) : r;
                if(res.status == 'success') {
                    let html = '';
                    if(res.data.length === 0) {
                        html = '<tr><td colspan="6" style="padding: 20px; text-align: center;">Chưa có đơn hàng nào</td></tr>';
                    } else {
                        res.data.forEach(o => {
                            let statusColor = o.status == 'pending' ? 'var(--c-rose)' : (o.status == 'shipping' ? 'var(--c-cyan)' : (o.status == 'completed' ? 'var(--c-green)' : 'gray'));
                            let statusText = o.status == 'pending' ? 'Chờ xử lý' : (o.status == 'shipping' ? 'Đang giao' : (o.status == 'completed' ? 'Hoàn thành' : 'Đã hủy'));
                            let priceFormat = new Intl.NumberFormat('vi-VN', { style: 'currency', currency: 'VND' }).format(o.total_amount);
                            
                            html += `
                            <tr style="border-bottom: 1px solid var(--admin-border);">
                                <td style="padding: 12px; font-weight:900; color:var(--c-yellow);">#${o.id}</td>
                                <td style="padding: 12px;"><b>${o.customer_name}</b><br><small style="color:var(--admin-muted)">${o.phone}</small></td>
                                <td style="padding: 12px; font-weight:bold; color:var(--c-green);">${priceFormat}</td>
                                <td style="padding: 12px;">
                                    <span style="background:rgba(255,255,255,0.1); color:${statusColor}; border:1px solid ${statusColor}; padding:4px 8px; border-radius:4px; font-size:12px; font-weight:bold;">${statusText}</span>
                                </td>
                                <td style="padding: 12px; font-size:12px; color:var(--admin-muted);">${o.created_at}</td>
                                <td style="padding: 12px;">
                                    <button onclick="viewOrder(${o.id})" style="background:var(--c-cyan); border:none; color:#000; font-weight:bold; padding:6px 12px; border-radius:6px; cursor:pointer;"><i class="fa-solid fa-eye"></i> Xem</button>
                                </td>
                            </tr>`;
                        });
                    }
                    $('#ordersTableBody').html(html);
                }
            } catch(e) { console.error(e); }
        }
    });
}

function viewOrder(id) {
    $.ajax({
        url: "/controller/client/AdminStoreAction.php",
        method: "POST",
        data: { action: 'get_order', id: id },
        success: function(r) {
            try {
                let res = typeof r === 'string' ? JSON.parse(r) : r;
                if(res.status == 'success') {
                    let o = res.data.order;
                    $('#v_order_id').text(o.id);
                    $('#v_customer_name').text(o.customer_name);
                    $('#v_phone').text(o.phone);
                    $('#v_address').text(o.address);
                    $('#v_note').text(o.note || 'Không có ghi chú');
                    $('#v_status').val(o.status);
                    
                    let statusColor = o.status == 'pending' ? 'var(--c-rose)' : (o.status == 'shipping' ? 'var(--c-cyan)' : (o.status == 'completed' ? 'var(--c-green)' : 'gray'));
                    $('#v_status').css('borderColor', statusColor).css('color', statusColor);
                    
                    $('#v_total').text(new Intl.NumberFormat('vi-VN').format(o.total_amount));
                    
                    let html = '';
                    res.data.items.forEach(item => {
                        let price = new Intl.NumberFormat('vi-VN').format(item.price);
                        html += `
                        <div style="display:flex; justify-content:space-between; padding:10px 0; border-bottom:1px solid rgba(255,255,255,0.05);">
                            <div>
                                <span style="font-weight:bold;">${item.name}</span><br>
                                <small style="color:var(--admin-muted)">SL: ${item.quantity} x ${price}đ</small>
                            </div>
                            <div style="font-weight:bold; color:var(--c-cyan);">
                                ${new Intl.NumberFormat('vi-VN').format(item.price * item.quantity)}đ
                            </div>
                        </div>`;
                    });
                    $('#v_items_list').html(html);
                    
                    // Attach order id for status update
                    $('#v_status').attr('data-oid', o.id);
                    
                    $('#viewOrderModal').css('display', 'flex');
                }
            } catch(e) { console.error(e); }
        }
    });
}

function updateOrderStatus() {
    let id = $('#v_status').attr('data-oid');
    let status = $('#v_status').val();
    
    $.ajax({
        url: "/controller/client/AdminStoreAction.php",
        method: "POST",
        data: { action: 'update_order_status', id: id, status: status },
        success: function(r) {
            let res = typeof r === 'string' ? JSON.parse(r) : r;
            if(res.status == 'success') {
                let statusColor = status == 'pending' ? 'var(--c-rose)' : (status == 'shipping' ? 'var(--c-cyan)' : (status == 'completed' ? 'var(--c-green)' : 'gray'));
                $('#v_status').css('borderColor', statusColor).css('color', statusColor);
                loadOrders();
            }
        }
    });
}

// --- QUẢN LÝ THỢ & ĐƠN GỌI THỢ ---
function loadTechnicians() {
    $('#techTableBody').html('<tr><td colspan="7" style="padding: 20px; text-align: center;">Đang tải dữ liệu...</td></tr>');
    $.ajax({
        url: "/controller/client/AdminStoreAction.php",
        method: "POST",
        data: { action: 'list_techs' },
        success: function(r) {
            try {
                let res = typeof r === 'string' ? JSON.parse(r) : r;
                if(res.status == 'success') {
                    let html = '';
                    if(res.data.length === 0) {
                        html = '<tr><td colspan="7" style="padding: 20px; text-align: center;">Chưa có thợ nào</td></tr>';
                    } else {
                        res.data.forEach(t => {
                            let banBadge = t.banned == 'ON' ? '<span style="color:var(--c-green); font-weight:bold;">Đang làm việc</span>' : '<span style="color:var(--c-rose); font-weight:bold;">Bị khóa</span>';
                            let money = parseInt(t.money);
                            let debtStr = money < 0 ? `<span style="color:var(--c-rose); font-weight:bold;">Nợ ${new Intl.NumberFormat('vi-VN').format(Math.abs(money))}đ</span> <button onclick="clearDebt(${t.id})" style="background:var(--c-green); border:none; color:#fff; padding:2px 6px; border-radius:4px; font-size:11px; cursor:pointer; margin-left:5px;">Xóa nợ</button>` : `<span style="color:var(--c-green);">${new Intl.NumberFormat('vi-VN').format(money)}đ</span>`;

                            html += `
                            <tr style="border-bottom: 1px solid var(--admin-border);">
                                <td style="padding: 12px; color:#fb923c; font-weight:bold;">#${t.id}</td>
                                <td style="padding: 12px; font-weight:bold;">${t.name}</td>
                                <td style="padding: 12px; color:var(--admin-muted);">${t.username}</td>
                                <td style="padding: 12px;">${t.phone || 'Chưa cập nhật'}</td>
                                <td style="padding: 12px;">${debtStr}</td>
                                <td style="padding: 12px;">${banBadge}</td>
                                <td style="padding: 12px; display: flex; gap: 5px;">
                                    <button onclick="toggleBanTech(${t.id}, '${t.banned}')" style="background:${t.banned == 'ON' ? 'var(--c-rose)' : 'var(--c-green)'}; border:none; color:#fff; font-weight:bold; padding:6px 12px; border-radius:6px; cursor:pointer;">
                                        ${t.banned == 'ON' ? 'Khóa' : 'Mở'}
                                    </button>
                                    <button onclick="deleteTech(${t.id})" style="background:#dc2626; border:none; color:#fff; font-weight:bold; padding:6px 12px; border-radius:6px; cursor:pointer;">
                                        Xóa
                                    </button>
                                </td>
                            </tr>`;
                        });
                    }
                    $('#techTableBody').html(html);
                }
            } catch(e) {}
        }
    });
}

function submitAddTech(e) {
    e.preventDefault();
    $.ajax({
        url: "/controller/client/AdminStoreAction.php",
        method: "POST",
        data: {
            action: 'add_tech',
            name: $('#tech_name').val(),
            username: $('#tech_username').val(),
            password: $('#tech_password').val(),
            phone: $('#tech_phone').val()
        },
        success: function(r) {
            try {
                let res = typeof r === 'string' ? JSON.parse(r) : r;
                if(res.status == 'success') {
                    showToast('Đã thêm thợ thành công', 'success');
                    $('#addTechModal').hide();
                    $('form')[1].reset(); // Reset form in modal 2
                    loadTechnicians();
                } else {
                    Swal.fire('Lỗi', res.msg, 'error');
                }
            } catch(e) {}
        }
    });
}

function deleteTech(id) {
    if(confirm("Xóa vĩnh viễn tài khoản thợ này? Hành động không thể hoàn tác!")) {
        $.ajax({
            url: "/controller/client/AdminStoreAction.php",
            method: "POST",
            data: { action: 'delete_tech', id: id },
            success: function(r) { loadTechnicians(); showToast('Đã xóa thợ', 'success'); }
        });
    }
}

function clearDebt(id) {
    if(confirm("Xác nhận thợ đã thanh toán tiền và xóa nợ?")) {
        $.ajax({
            url: "/controller/client/AdminStoreAction.php",
            method: "POST",
            data: { action: 'clear_debt', id: id },
            success: function(r) { loadTechnicians(); showToast('Đã xóa nợ thành công', 'success'); }
        });
    }
}

function toggleBanTech(id, currentStatus) {
    let newStatus = currentStatus == 'ON' ? 'OFF' : 'ON';
    if(confirm(newStatus == 'OFF' ? "Khóa tài khoản thợ này?" : "Mở khóa tài khoản thợ này?")) {
        $.ajax({
            url: "/controller/client/AdminStoreAction.php",
            method: "POST",
            data: { action: 'ban_tech', id: id, banned: newStatus },
            success: function(r) { loadTechnicians(); }
        });
    }
}

function loadBookings() {
    $('#bookingsTableBody').html('<tr><td colspan="6" style="padding: 20px; text-align: center;">Đang tải dữ liệu...</td></tr>');
    $.ajax({
        url: "/controller/client/AdminStoreAction.php",
        method: "POST",
        data: { action: 'list_bookings' },
        success: function(r) {
            try {
                let res = typeof r === 'string' ? JSON.parse(r) : r;
                if(res.status == 'success') {
                    let html = '';
                    if(res.data.length === 0) {
                        html = '<tr><td colspan="6" style="padding: 20px; text-align: center;">Chưa có đơn gọi thợ nào</td></tr>';
                    } else {
                        res.data.forEach(b => {
                            let statusColor = b.trangthai == 'CHO_XU_LY' ? 'var(--c-rose)' : (b.trangthai == 'DA_NHAN' ? 'var(--c-yellow)' : (b.trangthai == 'HOAN_THANH' ? 'var(--c-green)' : 'gray'));
                            let statusText = b.trangthai == 'CHO_XU_LY' ? 'Chờ thợ nhận' : (b.trangthai == 'DA_NHAN' ? 'Thợ đang đến' : (b.trangthai == 'HOAN_THANH' ? 'Hoàn thành' : 'Đã hủy'));
                            
                            html += `
                            <tr style="border-bottom: 1px solid var(--admin-border);">
                                <td style="padding: 12px; color:#38bdf8; font-weight:bold;">#${b.id}</td>
                                <td style="padding: 12px;"><b>${b.ten}</b><br><small style="color:var(--admin-muted)">${b.sdt}</small></td>
                                <td style="padding: 12px;">${b.dichvu}</td>
                                <td style="padding: 12px;">
                                    <span style="background:rgba(255,255,255,0.1); color:${statusColor}; border:1px solid ${statusColor}; padding:4px 8px; border-radius:4px; font-size:12px; font-weight:bold;">${statusText}</span>
                                </td>
                                <td style="padding: 12px; font-weight:bold; color:var(--admin-muted);">${b.tho_name || 'Đang trống'}</td>
                                <td style="padding: 12px;">
                                    <button onclick="deleteBooking(${b.id})" style="background:none; border:none; color:var(--c-rose); font-size:18px; cursor:pointer;"><i class="fa-solid fa-trash"></i></button>
                                </td>
                            </tr>`;
                        });
                    }
                    $('#bookingsTableBody').html(html);
                }
            } catch(e) {}
        }
    });
}

function deleteBooking(id) {
    if(confirm("Xóa vĩnh viễn đơn gọi thợ này?")) {
        $.ajax({
            url: "/controller/client/AdminStoreAction.php",
            method: "POST",
            data: { action: 'delete_booking', id: id },
            success: function(r) { loadBookings(); }
        });
    }
}

// Tự động load dữ liệu khi vào tab
$('.menu-item').click(function() {
    if($(this).text().includes('Quản lý Cửa Hàng')) {
        loadProducts('all');
    }
    if($(this).text().includes('Quản lý Đơn Mua Hàng')) {
        loadOrders();
    }
    if($(this).text().includes('Quản lý Thợ')) {
        loadTechnicians();
    }
    if($(this).text().includes('Đơn Gọi Thợ')) {
        loadBookings();
    }
});

</script>
</body>
</html>
