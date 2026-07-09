<?php
$host = 'localhost';
$db   = 'kwkrbcce_Goixelapvo';
$user = 'kwkrbcce_baocao';
$pass = 'SayTHC369@';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        role VARCHAR(30) NOT NULL DEFAULT 'buyer',
        fullname VARCHAR(150) NOT NULL,
        phone VARCHAR(30) NOT NULL,
        login_key VARCHAR(128) NULL,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        member_rank VARCHAR(30) NULL,
        total_spent INT NOT NULL DEFAULT 0,
        loyalty_points INT NOT NULL DEFAULT 0,
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
        updated_at DATETIME NULL DEFAULT NULL,
        INDEX idx_users_phone (phone)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    echo "Users table created successfully.\n";
} catch (PDOException $e) {
    echo "Creation failed: " . $e->getMessage() . "\n";
}
