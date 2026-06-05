<?php
$dsn = "mysql:host=localhost;dbname=kwkrbcce_Goixelapvo;charset=utf8mb4";
$pdo = new PDO($dsn, 'kwkrbcce_baocao', 'SayTHC369@');
$stmt = $pdo->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
print_r($tables);
