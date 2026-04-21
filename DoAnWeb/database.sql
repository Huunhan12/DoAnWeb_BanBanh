-- =============================================
-- HỆ THỐNG WEBSITE KINH DOANH BÁNH NGỌT
-- Database: banh_ngot_db
-- Charset: utf8mb4
-- =============================================

CREATE DATABASE IF NOT EXISTS banh_ngot_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE banh_ngot_db;

-- Xóa bảng theo thứ tự đúng (do khóa ngoại)
DROP TABLE IF EXISTS chi_tiet_don_hang;
DROP TABLE IF EXISTS don_hang;
DROP TABLE IF EXISTS san_pham;
DROP TABLE IF EXISTS danh_muc;
DROP TABLE IF EXISTS nguoi_dung;


-- =============================================
-- BẢNG 1: DANH MỤC (categories)
-- =============================================
CREATE TABLE danh_muc (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ten_danh_muc VARCHAR(255) NOT NULL,
    mo_ta TEXT,
    hinh_anh VARCHAR(255),
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- BẢNG 2: SẢN PHẨM (products)
-- =============================================
CREATE TABLE san_pham (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- BẢNG 3: NGƯỜI DÙNG (users)
-- =============================================
CREATE TABLE nguoi_dung (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ho_ten VARCHAR(255) NOT NULL,
    email VARCHAR(191) UNIQUE NOT NULL,
    dien_thoai VARCHAR(20),
    mat_khau VARCHAR(255) NOT NULL,
    dia_chi TEXT,
    vai_tro ENUM('user','admin') DEFAULT 'user',
    ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- BẢNG 4: ĐƠN HÀNG (orders)
-- =============================================
CREATE TABLE don_hang (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- =============================================
-- BẢNG 5: CHI TIẾT ĐƠN HÀNG (order_details)
-- =============================================
CREATE TABLE chi_tiet_don_hang (
    id INT AUTO_INCREMENT PRIMARY KEY,
    don_hang_id INT NOT NULL,
    san_pham_id INT NOT NULL,
    so_luong INT NOT NULL,
    gia DECIMAL(12,0) NOT NULL,
    FOREIGN KEY (don_hang_id) REFERENCES don_hang(id) ON DELETE CASCADE,
    FOREIGN KEY (san_pham_id) REFERENCES san_pham(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;



-- =============================================
-- DỮ LIỆU MẪU
-- =============================================



-- Tài khoản Admin mặc định (mật khẩu: admin123)
-- Hash được tạo bằng password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO nguoi_dung (ho_ten, email, dien_thoai, mat_khau, dia_chi, vai_tro) VALUES
('Quản trị viên', 'admin@lucake.vn', '0382150012', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Long Xuyên, An Giang', 'admin');

-- Tài khoản User mẫu (mật khẩu: user123)
INSERT INTO nguoi_dung (ho_ten, email, dien_thoai, mat_khau, dia_chi, vai_tro) VALUES
('Nguyễn Văn A', 'user@lucake.vn', '0912345678', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Long Xuyên, An Giang', 'user');

-- Danh mục bánh
INSERT INTO danh_muc (ten_danh_muc, mo_ta, hinh_anh) VALUES
('Bánh Kem Sinh Nhật', 'Các loại bánh kem trang trí đẹp mắt dành cho ngày sinh nhật', NULL),
('Bánh Mì & Bánh Mặn', 'Bánh mì tươi nướng hàng ngày và các loại bánh mặn', NULL),
('Bánh Ngọt Pháp', 'Croissant, Macaron, Éclair và các loại bánh Pháp tinh tế', NULL),
('Bánh Cupcake', 'Cupcake nhỏ xinh với nhiều hương vị đa dạng', NULL),
('Bánh Cookie & Quy', 'Cookie giòn tan, bánh quy bơ thơm ngon', NULL);

-- Sản phẩm bánh
INSERT INTO san_pham (danh_muc_id, ten_san_pham, mo_ta, gia, hinh_anh, noi_bat, moi, ton_kho) VALUES
-- Bánh Kem Sinh Nhật
(1, 'Bánh Kem Socola Đen', 'Bánh kem socola đen cao cấp 3 tầng, trang trí hoa kem tinh tế. Phù hợp cho tiệc sinh nhật sang trọng.', 450000, 'Bánh Kem Socola Đen.jpg', 1, 1, 20),
(1, 'Bánh Kem Dâu Tây', 'Bánh kem vị dâu tây tươi mát, phủ kem whipping và dâu tươi. Nhẹ nhàng, thanh mát.', 380000, 'Bánh Kem Dâu Tây.jpg', 1, 0, 15),
(1, 'Bánh Kem Trà Xanh', 'Bánh kem matcha Nhật Bản, vị trà xanh đậm đà hòa quyện kem béo ngậy.', 420000, 'Bánh Kem Trà Xanh.jpg', 0, 1, 10),
(1, 'Bánh Kem Vanilla Classic', 'Bánh kem vanilla truyền thống, hương thơm ngọt ngào, trang trí hoa hồng kem bơ.', 350000, 'Bánh Kem Vanilla Classic.jpg', 1, 0, 25),

-- Bánh Mì & Bánh Mặn
(2, 'Bánh Mì Baguette', 'Bánh mì Pháp giòn rụm, ruột mềm xốp, nướng tươi mỗi ngày.', 35000, 'Bánh Mì Baguette.png', 0, 1, 50),
(2, 'Bánh Croissant Bơ', 'Croissant bơ thơm lừng, nhiều lớp mỏng giòn tan trong miệng.', 45000, 'Bánh Croissant Bơ.jpg', 1, 1, 40),

-- Bánh Ngọt Pháp
(3, 'Macaron Hộp 12 Viên', 'Hộp 12 viên macaron đủ màu sắc và hương vị: dâu, socola, matcha, vanilla, chanh dây, việt quất.', 280000, 'Macaron Hộp 12 Viên.jpg', 1, 0, 30),
(3, 'Éclair Socola', 'Bánh su kem Pháp phủ socola đen, nhân kem custard béo ngậy.', 65000, 'Éclair Socola.jpg', 0, 1, 35),

-- Cupcake
(4, 'Cupcake Red Velvet', 'Cupcake red velvet mềm mịn, phủ kem cheese thơm ngon. Bán lẻ hoặc set 6 viên.', 55000, 'Cupcake Red Velve.jpg', 1, 1, 45),
(4, 'Cupcake Hoa Hồng', 'Cupcake trang trí hoa hồng bằng kem bơ, nhiều màu sắc lựa chọn.', 60000, 'Cupcake Hoa Hồng.jpg', 0, 0, 30),

-- Cookie
(5, 'Cookie Socola Chip', 'Cookie giòn bên ngoài, mềm bên trong, đầy ắp socola chip. Túi 10 cái.', 120000, 'Cookie Socola Chip.jpg', 1, 1, 50),
(5, 'Bánh Quy Bơ Hình Thú', 'Bánh quy bơ giòn tan hình các con thú dễ thương, phù hợp cho trẻ em. Hộp 20 cái.', 95000, 'Bánh Quy Bơ Hình Thú.jpg', 0, 0, 40);
