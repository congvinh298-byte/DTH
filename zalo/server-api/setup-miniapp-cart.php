<?php
header('Content-Type: text/plain; charset=utf-8');
define("IN_SITE", true);
require_once(__DIR__."/../core/config.php");
require_once(__DIR__."/../core/function.php");

if (!isset($DMH)) {
    $DMH = new DMH();
}

$sql = "CREATE TABLE IF NOT EXISTS `miniapp_carts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `device_id` varchar(255) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `device_id` (`device_id`),
  KEY `product_id` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

if ($DMH->query($sql)) {
    echo "OK: miniapp_carts created/updated\n";
} else {
    echo "ERROR creating miniapp_carts\n";
}

// Add source/device_id columns to store_orders if not exists (safe to run multiple times)
$cols = $DMH->get_list("SHOW COLUMNS FROM `store_orders`");
$colNames = [];
if (is_array($cols)) {
    foreach ($cols as $c) {
        $colNames[] = $c['Field'];
    }
}

if (!in_array('source', $colNames)) {
    $DMH->query("ALTER TABLE `store_orders` ADD COLUMN `source` varchar(50) DEFAULT 'website' AFTER `vat_requested`");
    echo "OK: added source column\n";
}

if (!in_array('device_id', $colNames)) {
    $DMH->query("ALTER TABLE `store_orders` ADD COLUMN `device_id` varchar(255) DEFAULT NULL AFTER `source`");
    echo "OK: added device_id column\n";
}

echo "Done\n";
?>
