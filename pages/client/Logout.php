<?php
define("IN_SITE", true);
require_once("../../core/config.php");
require_once("../../core/function.php");

// thực hiện xóa bỏ token log thì đăng xuất
setcookie("token", "", time()-3600);
header('Location: /dang-nhap');
