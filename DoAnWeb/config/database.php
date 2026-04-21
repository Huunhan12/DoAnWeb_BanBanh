<?php
/**
 * Kết nối Cơ sở dữ liệu MySQL
 * Sử dụng tài khoản mặc định của VertrigoServ: root / vertrigo
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'vertrigo');
define('DB_NAME', 'banh_ngot_db');

// Tạo kết nối MySQLi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Kiểm tra kết nối
if ($conn->connect_error) {
    die("Lỗi kết nối CSDL: " . $conn->connect_error);
}

// Thiết lập charset UTF-8
$conn->set_charset("utf8mb4");
