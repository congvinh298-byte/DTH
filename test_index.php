<?php
opcache_reset();
header("X-LiteSpeed-Purge: *");
echo "OPCACHE_CLEARED_SUCCESSFULLY_123";
?>
