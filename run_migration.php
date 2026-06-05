<?php
$host = 'localhost';
$db   = 'kwkrbcce_Goixelapvo';
$user = 'kwkrbcce_baocao';
$pass = 'SayTHC369@';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$pdo = new PDO($dsn, $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$sql = file_get_contents(__DIR__ . '/database_migration.sql');

// execute multiple statements
try {
    $pdo->exec($sql);
    echo "Migration completed successfully.";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage();
}
