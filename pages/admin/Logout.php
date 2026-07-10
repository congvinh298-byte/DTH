<?php
session_start();
unset($_SESSION['loginadmin']);
header("Location: /Login-Admin");
?>