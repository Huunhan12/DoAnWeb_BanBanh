<?php
/**
 * SCRIPT CÀI ĐẶT CƠ SỞ DỮ LIỆU
 * Chạy file này 1 lần để tạo database và dữ liệu mẫu
 * Truy cập: http://localhost/DoAnWeb/cai-dat-db.php
 */

$host = 'localhost';
$user = 'root';
$pass = 'vertrigo';

echo "<h1>Cai dat Database - Sweet Cake Shop</h1>";
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
$conn->query("DROP TABLE IF EXISTS chi_tiet_don_hang");
$conn->query("DROP TABLE IF EXISTS don_hang");
$conn->query("DROP TABLE IF EXISTS san_pham");
$conn->query("DROP TABLE IF EXISTS danh_muc");
$conn->query("DROP TABLE IF EXISTS nguoi_dung");
$conn->query("DROP TABLE IF EXISTS cai_dat");

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
    
    "CREATE TABLE cai_dat (
        id INT AUTO_INCREMENT PRIMARY KEY,
        ten_website VARCHAR(255),
        mo_ta TEXT,
        dien_thoai VARCHAR(20),
        email VARCHAR(255),
        dia_chi TEXT
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
);

$tenBang = array('danh_muc', 'san_pham', 'nguoi_dung', 'don_hang', 'chi_tiet_don_hang', 'cai_dat');

foreach ($tables as $i => $sql) {
    if ($conn->query($sql)) {
        echo "<p style='color:green;'>Tao bang '" . $tenBang[$i] . "' thanh cong!</p>";
    } else {
        echo "<p style='color:red;'>Loi tao bang '" . $tenBang[$i] . "': " . $conn->error . "</p>";
    }
}

// Thêm dữ liệu mẫu
echo "<hr><h2>Them du lieu mau...</h2>";

// Cài đặt website
$conn->query("INSERT INTO cai_dat (ten_website, mo_ta, dien_thoai, email, dia_chi) VALUES
('Sweet Cake Shop', 'Tiem banh ngot cao cap', '0901234567', 'contact@sweetcake.vn', '123 Nguyen Hue, Quan 1, TP. Ho Chi Minh')");

// Tạo mật khẩu hash đúng
$matKhauAdmin = password_hash('admin123', PASSWORD_DEFAULT);
$matKhauUser = password_hash('user123', PASSWORD_DEFAULT);

// Tài khoản Admin
$stmt = $conn->prepare("INSERT INTO nguoi_dung (ho_ten, email, dien_thoai, mat_khau, dia_chi, vai_tro) VALUES (?, ?, ?, ?, ?, 'admin')");
$hoTen = 'Quan tri vien';
$email = 'admin@lucake.vn';
$dt = '0901234567';
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
('Banh Kem Sinh Nhat', 'Cac loai banh kem trang tri dep mat danh cho ngay sinh nhat', 'banh-kem-sinh-nhat.jpg'),
('Banh Mi va Banh Man', 'Banh mi tuoi nuong hang ngay va cac loai banh man', 'banh-mi.jpg'),
('Banh Ngot Phap', 'Croissant, Macaron, Eclair va cac loai banh Phap tinh te', 'banh-phap.jpg'),
('Banh Cupcake', 'Cupcake nho xinh voi nhieu huong vi da dang', 'cupcake.jpg'),
('Banh Cookie va Quy', 'Cookie gion tan, banh quy bo thom ngon', 'cookie.jpg')");
echo "<p style='color:green;'>Da them 5 danh muc!</p>";

// Sản phẩm
$conn->query("INSERT INTO san_pham (danh_muc_id, ten_san_pham, mo_ta, gia, hinh_anh, noi_bat, moi, ton_kho) VALUES
(1, 'Banh Kem Socola Den', 'Banh kem socola den cao cap 3 tang, trang tri hoa kem tinh te. Phu hop cho tiec sinh nhat sang trong.', 450000, 'banh-kem-socola.jpg', 1, 1, 20),
(1, 'Banh Kem Dau Tay', 'Banh kem vi dau tay tuoi mat, phu kem whipping va dau tuoi. Nhe nhang, thanh mat.', 380000, 'banh-kem-dau.jpg', 1, 0, 15),
(1, 'Banh Kem Tra Xanh', 'Banh kem matcha Nhat Ban, vi tra xanh dam da hoa quyen kem beo ngay.', 420000, 'banh-kem-tra-xanh.jpg', 0, 1, 10),
(1, 'Banh Kem Vanilla Classic', 'Banh kem vanilla truyen thong, huong thom ngot ngao, trang tri hoa hong kem bo.', 350000, 'banh-kem-vanilla.jpg', 1, 0, 25),
(2, 'Banh Mi Baguette', 'Banh mi Phap gion rum, ruot mem xop, nuong tuoi moi ngay.', 35000, 'baguette.jpg', 0, 1, 50),
(2, 'Banh Croissant Bo', 'Croissant bo thom lung, nhieu lop mong gion tan trong mieng.', 45000, 'croissant.jpg', 1, 1, 40),
(3, 'Macaron Hop 12 Vien', 'Hop 12 vien macaron du mau sac va huong vi: dau, socola, matcha, vanilla, chanh day, viet quat.', 280000, 'macaron.jpg', 1, 0, 30),
(3, 'Eclair Socola', 'Banh su kem Phap phu socola den, nhan kem custard beo ngay.', 65000, 'eclair.jpg', 0, 1, 35),
(4, 'Cupcake Red Velvet', 'Cupcake red velvet mem min, phu kem cheese thom ngon. Ban le hoac set 6 vien.', 55000, 'cupcake-red-velvet.jpg', 1, 1, 45),
(4, 'Cupcake Hoa Hong', 'Cupcake trang tri hoa hong bang kem bo, nhieu mau sac lua chon.', 60000, 'cupcake-hoa-hong.jpg', 0, 0, 30),
(5, 'Cookie Socola Chip', 'Cookie gion ben ngoai, mem ben trong, day ap socola chip. Tui 10 cai.', 120000, 'cookie-socola.jpg', 1, 1, 50),
(5, 'Banh Quy Bo Hinh Thu', 'Banh quy bo gion tan hinh cac con thu de thuong, phu hop cho tre em. Hop 20 cai.', 95000, 'cookie-bo.jpg', 0, 0, 40)");
echo "<p style='color:green;'>Da them 12 san pham!</p>";

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
