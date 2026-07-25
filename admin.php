<?php
define("IN_SITE", true);
require_once(__DIR__."/core/config.php");
require_once(__DIR__."/core/function.php");
CheckAdmin();

header("Location: /admin/index.php");
exit;
?>
