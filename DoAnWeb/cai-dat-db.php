<?php
/**
 * SCRIPT CÀI ĐẶT CƠ SỞ DỮ LIỆU
 * Chạy file này 1 lần để tạo database và dữ liệu mẫu
 * Truy cập: http://localhost/DoAnWeb/cai-dat-db.php
 */

$host = 'localhost';
$user = 'root';
$pass = 'vertrigo';

echo "<h1>Cai dat Database - Lu Cake</h1>";
echo "<hr>";

// Kết nối MySQL (chưa chọn database)
$conn = new mysqli($host, $user, $pass);
if ($conn->connect_error) {
    die("<p style='color:red;'>Loi ket noi MySQL: " . $conn->connect_error . "</p>");
}
echo "<p style='color:green;'>Ket noi MySQL thanh cong!</p>";

$conn->set_charset("utf8mb4");

// Tạo database
$conn->query("CREATE DATABASE IF NOT EXISTS banh_ngot_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
$conn->select_db("banh_ngot_db");
echo "<p style='color:green;'>Database 'banh_ngot_db' da tao!</p>";

// Xóa bảng cũ (theo thứ tự đúng)
$conn->query("DROP TABLE IF EXISTS cai_dat");
$conn->query("DROP TABLE IF EXISTS chi_tiet_don_hang");
$conn->query("DROP TABLE IF EXISTS don_hang");
$conn->query("DROP TABLE IF EXISTS san_pham");
$conn->query("DROP TABLE IF EXISTS danh_muc");
$conn->query("DROP TABLE IF EXISTS nguoi_dung");


// Tạo bảng
$tables = array(
    "CREATE TABLE danh_muc (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ten_danh_muc VARCHAR(255) NOT NULL,
        mo_ta TEXT,
        hinh_anh VARCHAR(255),
        ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    "CREATE TABLE san_pham (
        id INT AUTO_INCREMENT PRIMARY KEY,
        danh_muc_id INT NOT NULL,
        ten_san_pham VARCHAR(255) NOT NULL,
        mo_ta TEXT,
        gia DECIMAL(12,0) NOT NULL,
        hinh_anh VARCHAR(255),
        noi_bat TINYINT(1) DEFAULT 0,
        moi TINYINT(1) DEFAULT 0,
        ton_kho INT DEFAULT 0,
        ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (danh_muc_id) REFERENCES danh_muc(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    "CREATE TABLE nguoi_dung (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ho_ten VARCHAR(255) NOT NULL,
        email VARCHAR(191) UNIQUE NOT NULL,
        dien_thoai VARCHAR(20),
        mat_khau VARCHAR(255) NOT NULL,
        dia_chi TEXT,
        vai_tro ENUM('user','admin') DEFAULT 'user',
        ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    "CREATE TABLE don_hang (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nguoi_dung_id INT,
        ho_ten VARCHAR(255) NOT NULL,
        email VARCHAR(255),
        dien_thoai VARCHAR(20) NOT NULL,
        dia_chi TEXT NOT NULL,
        tong_tien DECIMAL(12,0) NOT NULL,
        trang_thai ENUM('cho_xac_nhan','da_xac_nhan','dang_giao','hoan_thanh','da_huy') DEFAULT 'cho_xac_nhan',
        ghi_chu TEXT,
        ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (nguoi_dung_id) REFERENCES nguoi_dung(id) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    
    "CREATE TABLE chi_tiet_don_hang (
        id INT AUTO_INCREMENT PRIMARY KEY,
        don_hang_id INT NOT NULL,
        san_pham_id INT NOT NULL,
        so_luong INT NOT NULL,
        gia DECIMAL(12,0) NOT NULL,
        FOREIGN KEY (don_hang_id) REFERENCES don_hang(id) ON DELETE CASCADE,
        FOREIGN KEY (san_pham_id) REFERENCES san_pham(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    

);

$tenBang = array('danh_muc', 'san_pham', 'nguoi_dung', 'don_hang', 'chi_tiet_don_hang');

foreach ($tables as $i => $sql) {
    if ($conn->query($sql)) {
        echo "<p style='color:green;'>Tao bang '" . $tenBang[$i] . "' thanh cong!</p>";
    } else {
        echo "<p style='color:red;'>Loi tao bang '" . $tenBang[$i] . "': " . $conn->error . "</p>";
    }
}

// Thêm dữ liệu mẫu
echo "<hr><h2>Them du lieu mau...</h2>";



// Tạo mật khẩu hash đúng
$matKhauAdmin = password_hash('admin123', PASSWORD_DEFAULT);
$matKhauUser = password_hash('user123', PASSWORD_DEFAULT);

// Tài khoản Admin
$stmt = $conn->prepare("INSERT INTO nguoi_dung (ho_ten, email, dien_thoai, mat_khau, dia_chi, vai_tro) VALUES (?, ?, ?, ?, ?, 'admin')");
$hoTen = 'Quan tri vien';
$email = 'admin@lucake.vn';
$dt = '0382150012';
$dc = 'Long Xuyên, An Giang';
$stmt->bind_param("sssss", $hoTen, $email, $dt, $matKhauAdmin, $dc);
$stmt->execute();
echo "<p style='color:green;'>Tai khoan Admin: admin@lucake.vn / admin123</p>";

// Tài khoản User  
$stmt2 = $conn->prepare("INSERT INTO nguoi_dung (ho_ten, email, dien_thoai, mat_khau, dia_chi, vai_tro) VALUES (?, ?, ?, ?, ?, 'user')");
$hoTen2 = 'Nguyen Van A';
$email2 = 'user@lucake.vn';
$dt2 = '0912345678';
$dc2 = 'Long Xuyên, An Giang';
$stmt2->bind_param("sssss", $hoTen2, $email2, $dt2, $matKhauUser, $dc2);
$stmt2->execute();
echo "<p style='color:green;'>Tai khoan User: user@lucake.vn / user123</p>";

// Danh mục
$conn->query("INSERT INTO danh_muc (ten_danh_muc, mo_ta, hinh_anh) VALUES
('Bánh Kem Sinh Nhật', 'Các loại bánh kem trang trí đẹp mắt dành cho ngày sinh nhật', NULL),
('Bánh Mì & Bánh Mặn', 'Bánh mì tươi nướng hàng ngày và các loại bánh mặn', NULL),
('Bánh Ngọt Pháp', 'Croissant, Macaron, Éclair và các loại bánh Pháp tinh tế', NULL),
('Bánh Cupcake', 'Cupcake nhỏ xinh với nhiều hương vị đa dạng', NULL),
('Bánh Cookie & Quy', 'Cookie giòn tan, bánh quy bơ thơm ngon', NULL)");
echo "<p style='color:green;'>Đã thêm 5 danh mục!</p>";

// Sản phẩm
$conn->query("INSERT INTO san_pham (danh_muc_id, ten_san_pham, mo_ta, gia, hinh_anh, noi_bat, moi, ton_kho) VALUES
(1, 'Bánh Kem Socola Đen', 'Bánh kem socola đen cao cấp 3 tầng, trang trí hoa kem tinh tế. Phù hợp cho tiệc sinh nhật sang trọng.', 450000, 'Bánh Kem Socola Đen.jpg', 1, 1, 20),
(1, 'Bánh Kem Dâu Tây', 'Bánh kem vị dâu tây tươi mát, phủ kem whipping và dâu tươi. Nhẹ nhàng, thanh mát.', 380000, 'Bánh Kem Dâu Tây.jpg', 1, 0, 15),
(1, 'Bánh Kem Trà Xanh', 'Bánh kem matcha Nhật Bản, vị trà xanh đậm đà hòa quyện kem béo ngậy.', 420000, 'Bánh Kem Trà Xanh.jpg', 0, 1, 10),
(1, 'Bánh Kem Vanilla Classic', 'Bánh kem vanilla truyền thống, hương thơm ngọt ngào, trang trí hoa hồng kem bơ.', 350000, 'Bánh Kem Vanilla Classic.jpg', 1, 0, 25),
(2, 'Bánh Mì Baguette', 'Bánh mì Pháp giòn rụm, ruột mềm xốp, nướng tươi mỗi ngày.', 35000, 'Bánh Mì Baguette.png', 0, 1, 50),
(2, 'Bánh Croissant Bơ', 'Croissant bơ thơm lừng, nhiều lớp mỏng giòn tan trong miệng.', 45000, 'Bánh Croissant Bơ.jpg', 1, 1, 40),
(3, 'Macaron Hộp 12 Viên', 'Hộp 12 viên macaron đủ màu sắc và hương vị: dâu, socola, matcha, vanilla, chanh dây, việt quất.', 280000, 'Macaron Hộp 12 Viên.jpg', 1, 0, 30),
(3, 'Éclair Socola', 'Bánh su kem Pháp phủ socola đen, nhân kem custard béo ngậy.', 65000, 'Éclair Socola.jpg', 0, 1, 35),
(4, 'Cupcake Red Velvet', 'Cupcake red velvet mềm mịn, phủ kem cheese thơm ngon. Bán lẻ hoặc set 6 viên.', 55000, 'Cupcake Red Velve.jpg', 1, 1, 45),
(4, 'Cupcake Hoa Hồng', 'Cupcake trang trí hoa hồng bằng kem bơ, nhiều màu sắc lựa chọn.', 60000, 'Cupcake Hoa Hồng.jpg', 0, 0, 30),
(5, 'Cookie Socola Chip', 'Cookie giòn bên ngoài, mềm bên trong, đầy ắp socola chip. Túi 10 cái.', 120000, 'Cookie Socola Chip.jpg', 1, 1, 50),
(5, 'Bánh Quy Bơ Hình Thú', 'Bánh quy bơ giòn tan hình các con thú dễ thương, phù hợp cho trẻ em. Hộp 20 cái.', 95000, 'Bánh Quy Bơ Hình Thú.jpg', 0, 0, 40)");
echo "<p style='color:green;'>Đã thêm 12 sản phẩm!</p>";

echo "<hr>";
echo "<h2 style='color:green;'>Cai dat hoan tat!</h2>";
echo "<p><strong>Thong tin dang nhap:</strong></p>";
echo "<ul>";
echo "<li><strong>Admin:</strong> admin@lucake.vn / admin123</li>";
echo "<li><strong>User:</strong> user@lucake.vn / user123</li>";
echo "</ul>";
echo "<p><a href='index.php' style='font-size:1.2rem;'>Truy cap Website</a></p>";
echo "<p><a href='admin/dang-nhap.php' style='font-size:1.2rem;'>Truy cap Trang Quan Tri</a></p>";
echo "<p style='color:orange;'>Hay xoa file nay sau khi cai dat xong!</p>";

$conn->close();
?>
