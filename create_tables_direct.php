<?php
$host = 'localhost';
$db   = 'kwkrbcce_Goixelapvo';
$user = 'kwkrbcce_baocao';
$pass = 'SayTHC369@';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS marketplace_stores (
        id INT AUTO_INCREMENT PRIMARY KEY,
        phone VARCHAR(30) NOT NULL,
        tax_code VARCHAR(30) NOT NULL,
        owner_name VARCHAR(150) NULL,
        email VARCHAR(190) NULL,
        store_name VARCHAR(150) NOT NULL,
        address TEXT NULL,
        lat DECIMAL(10,7) NULL,
        lng DECIMAL(10,7) NULL,
        store_type VARCHAR(50) NULL,
        note TEXT NULL,
        login_key VARCHAR(128) NULL,
        approved_at DATETIME NULL,
        approved_by VARCHAR(150) NULL,
        status VARCHAR(30) NOT NULL DEFAULT 'pending',
        rating_score DECIMAL(3,1) NOT NULL DEFAULT 5.0,
        rating_count INT NOT NULL DEFAULT 0,
        report_token VARCHAR(64) NULL,
        last_login_at DATETIME NULL,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL DEFAULT NULL,
        INDEX idx_store_phone (phone),
        INDEX idx_store_tax (tax_code)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $pdo->exec("CREATE TABLE IF NOT EXISTS marketplace_products (
        id              INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
        seller_id       INT UNSIGNED    NULL COMMENT 'Người đăng bán (users.id)',
        store_id        INT             NULL,
        category_id     INT UNSIGNED    NULL COMMENT 'Danh mục (categories.id)',
        type            ENUM('sim','dien_may','dien_thoai','phu_kien','dich_vu_lao_dong','van_tai','bao_hiem','do_cu','viec_lam','other', 'Trà sữa', 'Soda', 'Sinh tố', 'Sinh tố Mix', 'Nước ép', 'Nước ép Mix', 'Đá xay', 'Trà trái cây', 'Coffee', 'Đồ uống khác', 'Các món khác', 'Best Seller') NOT NULL DEFAULT 'other',
        name            VARCHAR(255)    NOT NULL,
        slug            VARCHAR(255)    NULL UNIQUE,
        description     TEXT            NULL,
        price           DECIMAL(15,2)   NOT NULL DEFAULT 0.00 COMMENT 'Giá bán',
        sale_price      DECIMAL(15,2)   NULL COMMENT 'Giá khuyến mãi (nếu có)',
        stock           INT             NOT NULL DEFAULT 0 COMMENT 'Tồn kho (-1 = không giới hạn)',
        sold_count      INT             NOT NULL DEFAULT 0 COMMENT 'Đã bán',
        images          JSON            NULL COMMENT 'Mảng URL hình ảnh',
        attributes      JSON            NULL COMMENT 'Thuộc tính mở rộng (màu sắc, dung lượng, kỹ năng...)',
        location        VARCHAR(255)    NULL COMMENT 'Địa điểm (cho dịch vụ lao động)',
        latitude        DECIMAL(10,8)   NULL COMMENT 'Tọa độ (cho tìm kiếm gần đây)',
        longitude       DECIMAL(11,8)   NULL,
        status          ENUM('draft','active','sold','hidden','disabled') NOT NULL DEFAULT 'draft',
        is_featured     TINYINT(1)      NOT NULL DEFAULT 0 COMMENT 'Sản phẩm nổi bật',
        view_count      INT UNSIGNED    NOT NULL DEFAULT 0,
        created_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at      DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        deleted_at      DATETIME        NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Tables created successfully.\n";
} catch (PDOException $e) {
    echo "Creation failed: " . $e->getMessage() . "\n";
}

$storeId = 3; // Cafe Goc Pho
$stmt = $pdo->prepare("SELECT id FROM marketplace_stores WHERE id = 3");
$stmt->execute();
if (!$stmt->fetch()) {
    $pdo->exec("INSERT INTO marketplace_stores (id, phone, tax_code, store_name, status, created_at) VALUES (3, '0939627909', '1402220000', 'Cafe Góc Phố', 'active', NOW())");
    echo "Inserted Cafe Goc Pho store.\n";
}

