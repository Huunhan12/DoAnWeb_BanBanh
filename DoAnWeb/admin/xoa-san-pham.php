<?php
/**
 * ADMIN - Xóa sản phẩm
 */
require_once dirname(__DIR__) . '/config/database.php';
require_once dirname(__DIR__) . '/includes/functions.php';

if (!daHienDangNhap() || !laAdmin()) {
    chuyenHuong(layURLGoc() . '/dang-nhap.php');
}

$spId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($spId > 0) {
    // Lấy ảnh để xóa
    $stmt = $conn->prepare("SELECT hinh_anh FROM san_pham WHERE id = ?");
    $stmt->bind_param("i", $spId);
    $stmt->execute();
    $sp = $stmt->get_result()->fetch_assoc();
    
    if ($sp) {
        // Xóa ảnh
        if ($sp['hinh_anh'] && file_exists(dirname(__DIR__) . '/assets/images/products/' . $sp['hinh_anh'])) {
            unlink(dirname(__DIR__) . '/assets/images/products/' . $sp['hinh_anh']);
        }
        
        // Xóa sản phẩm
        $stmt = $conn->prepare("DELETE FROM san_pham WHERE id = ?");
        $stmt->bind_param("i", $spId);
        
        if ($stmt->execute()) {
            datThongBao('success', 'Đã xóa sản phẩm!');
        } else {
            datThongBao('danger', 'Lỗi khi xóa sản phẩm: ' . $conn->error);
        }
    }
}

chuyenHuong('san-pham.php');
