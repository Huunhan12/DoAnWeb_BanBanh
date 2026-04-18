<?php
/**
 * ĐĂNG XUẤT
 */
require_once 'includes/functions.php';
session_destroy();
session_start();
datThongBao('info', '<i class="bi bi-box-arrow-left me-1"></i> Đã đăng xuất thành công!');
header("Location: index.php");
exit();
