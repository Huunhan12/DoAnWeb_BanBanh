<?php
require_once '../includes/functions.php';
session_destroy();
session_start();
datThongBao('info', 'Đã đăng xuất khỏi trang quản trị.');
header("Location: dang-nhap.php");
exit();
