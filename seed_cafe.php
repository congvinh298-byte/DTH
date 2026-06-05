<?php

$host = 'localhost';
$db   = 'kwkrbcce_Goixelapvo';
$user = 'kwkrbcce_baocao';
$pass = 'SayTHC369@';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    throw new \PDOException($e->getMessage(), (int)$e->getCode());
}

$storeId = 3; // Cafe Goc Pho

$menu = [
    // Trà sữa
    ['name' => 'Trà sữa Phúc Long', 'price' => 25000, 'category' => 'Trà sữa'],
    ['name' => 'Trà sữa Matcha', 'price' => 20000, 'category' => 'Trà sữa'],
    ['name' => 'Trà sữa Socola', 'price' => 20000, 'category' => 'Trà sữa'],
    ['name' => 'Latte sữa tươi', 'price' => 25000, 'category' => 'Trà sữa'],
    ['name' => 'Trà sữa rau má', 'price' => 20000, 'category' => 'Trà sữa'],
    // Soda
    ['name' => 'Soda dâu tằm', 'price' => 30000, 'category' => 'Soda'],
    ['name' => 'Soda atiso đỏ', 'price' => 30000, 'category' => 'Soda'],
    ['name' => 'Soda bạc hà', 'price' => 25000, 'category' => 'Soda'],
    ['name' => 'Soda dâu', 'price' => 25000, 'category' => 'Soda'],
    // Sinh tố
    ['name' => 'Sinh tố Bơ', 'price' => 25000, 'category' => 'Sinh tố'],
    ['name' => 'Sinh tố Dâu', 'price' => 25000, 'category' => 'Sinh tố'],
    ['name' => 'Sinh tố Mãng cầu', 'price' => 20000, 'category' => 'Sinh tố'],
    ['name' => 'Sinh tố Đu đủ', 'price' => 20000, 'category' => 'Sinh tố'],
    ['name' => 'Sinh tố Xoài', 'price' => 20000, 'category' => 'Sinh tố'],
    ['name' => 'Sinh tố Cam', 'price' => 20000, 'category' => 'Sinh tố'],
    // Sinh tố Mix
    ['name' => 'Sinh tố Đu đủ + Chuối', 'price' => 25000, 'category' => 'Sinh tố Mix'],
    ['name' => 'Sinh tố Bơ + Chuối', 'price' => 30000, 'category' => 'Sinh tố Mix'],
    ['name' => 'Sinh tố Dâu + Cam', 'price' => 30000, 'category' => 'Sinh tố Mix'],
    // Nước ép
    ['name' => 'Nước ép Táo', 'price' => 25000, 'category' => 'Nước ép'],
    ['name' => 'Nước ép Cà rốt', 'price' => 20000, 'category' => 'Nước ép'],
    ['name' => 'Nước ép Khóm', 'price' => 20000, 'category' => 'Nước ép'],
    ['name' => 'Nước ép Cam', 'price' => 15000, 'category' => 'Nước ép'],
    ['name' => 'Nước ép Dưa hấu', 'price' => 15000, 'category' => 'Nước ép'],
    ['name' => 'Cam sữa', 'price' => 20000, 'category' => 'Nước ép'],
    ['name' => 'Cà rốt sữa', 'price' => 25000, 'category' => 'Nước ép'],
    // Nước ép Mix
    ['name' => 'Nước ép Cam + Cà rốt', 'price' => 25000, 'category' => 'Nước ép Mix'],
    ['name' => 'Nước ép Cam + Khóm', 'price' => 20000, 'category' => 'Nước ép Mix'],
    ['name' => 'Nước ép Cam + Táo', 'price' => 30000, 'category' => 'Nước ép Mix'],
    ['name' => 'Nước ép Khóm + Cà rốt', 'price' => 30000, 'category' => 'Nước ép Mix'],
    ['name' => 'Nước ép Khóm + Dưa hấu', 'price' => 25000, 'category' => 'Nước ép Mix'],
    // Đá xay
    ['name' => 'Matcha đá xay', 'price' => 25000, 'category' => 'Đá xay'],
    ['name' => 'Socola đá xay', 'price' => 25000, 'category' => 'Đá xay'],
    // Trà trái cây
    ['name' => 'Trà vải', 'price' => 15000, 'category' => 'Trà trái cây'],
    ['name' => 'Trà đào', 'price' => 15000, 'category' => 'Trà trái cây'],
    ['name' => 'Trà ổi hồng', 'price' => 15000, 'category' => 'Trà trái cây'],
    ['name' => 'Trà dâu', 'price' => 15000, 'category' => 'Trà trái cây'],
    ['name' => 'Trà bí đao', 'price' => 15000, 'category' => 'Trà trái cây'],
    // Coffee
    ['name' => 'Cà phê (nóng/đá)', 'price' => 15000, 'category' => 'Coffee'],
    ['name' => 'Cà phê sữa (nóng/đá)', 'price' => 18000, 'category' => 'Coffee'],
    ['name' => 'Bạc xỉu (nóng/đá)', 'price' => 27000, 'category' => 'Coffee'],
    ['name' => 'Trà lipton (nóng/đá)', 'price' => 13000, 'category' => 'Coffee'],
    ['name' => 'Trà gừng nóng', 'price' => 10000, 'category' => 'Coffee'],
    ['name' => 'Sting', 'price' => 10000, 'category' => 'Đồ uống khác'],
    ['name' => 'Bò cụng', 'price' => 17000, 'category' => 'Đồ uống khác'],
    ['name' => 'Trà xanh không độ', 'price' => 15000, 'category' => 'Đồ uống khác'],
    ['name' => 'Nước suối', 'price' => 8000, 'category' => 'Đồ uống khác'],
    // Các món khác
    ['name' => 'Cacao sữa', 'price' => 20000, 'category' => 'Các món khác'],
    ['name' => 'Milo dầm', 'price' => 30000, 'category' => 'Các món khác'],
    ['name' => 'Sâm lạnh', 'price' => 10000, 'category' => 'Các món khác'],
    ['name' => 'Rau má', 'price' => 10000, 'category' => 'Các món khác'],
    ['name' => 'Rau má sữa', 'price' => 15000, 'category' => 'Các món khác'],
    ['name' => 'Rau má đậu xanh', 'price' => 20000, 'category' => 'Các món khác'],
    // Best Seller
    ['name' => 'Trà Atiso Đỏ', 'price' => 25000, 'category' => 'Best Seller'],
    ['name' => 'Atiso đỏ đá xay', 'price' => 30000, 'category' => 'Best Seller'],
    ['name' => 'Dâu tằm đác ghim', 'price' => 25000, 'category' => 'Best Seller'],
];

$added = 0;
foreach ($menu as $item) {
    // Check if exists
    $stmt = $pdo->prepare("SELECT id FROM marketplace_products WHERE store_id = ? AND name = ? LIMIT 1");
    $stmt->execute([$storeId, $item['name']]);
    if (!$stmt->fetch()) {
        $insert = $pdo->prepare("INSERT INTO marketplace_products (store_id, name, price, sale_price, stock, type, status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 'active', NOW(), NOW())");
        $insert->execute([
            $storeId,
            $item['name'],
            $item['price'],
            $item['price'],
            100, // stock
            $item['category']
        ]);
        $added++;
    }
}

echo "Thanh cong! Da them $added mon vao cua hang Cafe Goc Pho (ID = $storeId).\n";
