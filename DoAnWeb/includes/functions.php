<?php
/**
 * Các hàm tiện ích dùng chung cho toàn bộ website
 */

// Khởi động session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ob_start();

/**
 * Format giá tiền theo định dạng VNĐ
 */
function formatGia($gia) {
    return number_format($gia, 0, ',', '.') . ' ₫';
}

/**
 * Kiểm tra người dùng đã đăng nhập chưa
 */
function daHienDangNhap() {
    return isset($_SESSION['nguoi_dung_id']);
}

/**
 * Kiểm tra người dùng có phải admin không
 */
function laAdmin() {
    return isset($_SESSION['vai_tro']) && $_SESSION['vai_tro'] === 'admin';
}

/**
 * Chuyển hướng trang
 */
function chuyenHuong($url) {
    header("Location: $url");
    exit();
}

/**
 * Lọc dữ liệu đầu vào (chống XSS)
 */
function lamSach($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Upload ảnh sản phẩm
 * @return string|false Tên file đã upload hoặc false nếu lỗi
 */
function uploadAnh($file, $thuMuc = 'assets/images/products/') {
    $duoiChoPhep = array('jpg', 'jpeg', 'png', 'gif', 'webp');
    $tenFile = $file['name'];
    $kichThuoc = $file['size'];
    $tmpName = $file['tmp_name'];
    $duoiFile = strtolower(pathinfo($tenFile, PATHINFO_EXTENSION));

    // Kiểm tra đuôi file
    if (!in_array($duoiFile, $duoiChoPhep)) {
        return false;
    }

    // Xử lý lỗi từ server (php.ini)
    if ($file['error'] !== UPLOAD_ERR_OK) {
        if ($file['error'] == UPLOAD_ERR_INI_SIZE || $file['error'] == UPLOAD_ERR_FORM_SIZE) {
            return false; // Lỗi dung lượng quá lớn
        }
        return false;
    }

    // Kiểm tra kích thước (tối đa 5MB)
    if ($kichThuoc > 5 * 1024 * 1024) {
        return false;
    }

    // Tạo tên file duy nhất
    $tenMoi = uniqid('img_') . '.' . $duoiFile;
    $duongDan = dirname(__DIR__) . '/' . $thuMuc . $tenMoi;

    // Tạo thư mục nếu chưa có
    if (!is_dir(dirname($duongDan))) {
        mkdir(dirname($duongDan), 0777, true);
    }

    if (move_uploaded_file($tmpName, $duongDan)) {
        return $tenMoi;
    }

    return false;
}

/**
 * Lấy số lượng sản phẩm trong giỏ hàng
 */
function demGioHang() {
    if (!isset($_SESSION['gio_hang'])) {
        return 0;
    }
    $tong = 0;
    foreach ($_SESSION['gio_hang'] as $sp) {
        $tong += $sp['so_luong'];
    }
    return $tong;
}

/**
 * Tính tổng tiền giỏ hàng
 */
function tongTienGioHang() {
    if (!isset($_SESSION['gio_hang'])) {
        return 0;
    }
    $tong = 0;
    foreach ($_SESSION['gio_hang'] as $sp) {
        $tong += $sp['gia'] * $sp['so_luong'];
    }
    return $tong;
}

/**
 * Hiển thị thông báo
 */
function hienThongBao() {
    if (isset($_SESSION['thong_bao'])) {
        $loai = $_SESSION['thong_bao']['loai'];
        $noiDung = $_SESSION['thong_bao']['noi_dung'];
        echo '<div class="alert alert-' . $loai . ' alert-dismissible fade show" role="alert">';
        echo $noiDung;
        echo '<button type="button" class="btn-close" data-bs-dismiss="alert"></button>';
        echo '</div>';
        unset($_SESSION['thong_bao']);
    }
}

/**
 * Đặt thông báo
 */
function datThongBao($loai, $noiDung) {
    $_SESSION['thong_bao'] = array(
        'loai' => $loai,
        'noi_dung' => $noiDung
    );
}

/**
 * Lấy URL gốc của website
 */
function layURLGoc() {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    // Tìm thư mục gốc dựa trên vị trí file config
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    // Nếu đang trong admin, đi lên 1 cấp
    if (strpos($scriptDir, '/admin') !== false) {
        $baseDir = dirname($scriptDir);
    } else {
        $baseDir = $scriptDir;
    }
    return $protocol . '://' . $host . rtrim($baseDir, '/');
}

/**
 * Cắt ngắn chuỗi
 */
function catNganChuoi($chuoi, $doDai = 100) {
    if (mb_strlen($chuoi) <= $doDai) {
        return $chuoi;
    }
    return mb_substr($chuoi, 0, $doDai) . '...';
}

/**
 * Hiển thị trạng thái đơn hàng bằng tiếng Việt
 */
function tenTrangThai($trangThai) {
    $danhSach = array(
        'cho_xac_nhan' => 'Chờ xác nhận',
        'da_xac_nhan' => 'Đã xác nhận',
        'dang_giao' => 'Đang giao hàng',
        'hoan_thanh' => 'Hoàn thành',
        'da_huy' => 'Đã hủy'
    );
    return isset($danhSach[$trangThai]) ? $danhSach[$trangThai] : $trangThai;
}

/**
 * Lấy class badge cho trạng thái đơn hàng
 */
function classTrangThai($trangThai) {
    $danhSach = array(
        'cho_xac_nhan' => 'warning',
        'da_xac_nhan' => 'info',
        'dang_giao' => 'primary',
        'hoan_thanh' => 'success',
        'da_huy' => 'danger'
    );
    return isset($danhSach[$trangThai]) ? $danhSach[$trangThai] : 'secondary';
}
