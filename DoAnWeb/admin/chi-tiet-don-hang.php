<?php
/**
 * ADMIN - Chi tiết đơn hàng
 */
$tieuDeTrang = 'Chi tiết đơn hàng';
require_once 'includes/header.php';

$dhId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy thông tin đơn hàng
$sql = "SELECT dh.*, nd.email as email_nd FROM don_hang dh 
        LEFT JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id 
        WHERE dh.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $dhId);
$stmt->execute();
$donHang = $stmt->get_result()->fetch_assoc();

if (!$donHang) {
    datThongBao('danger', 'Không tìm thấy đơn hàng!');
    chuyenHuong('don-hang.php');
}

// Lấy chi tiết đơn hàng
$sqlCT = "SELECT ct.*, sp.ten_san_pham, sp.hinh_anh FROM chi_tiet_don_hang ct 
          JOIN san_pham sp ON ct.san_pham_id = sp.id 
          WHERE ct.don_hang_id = ?";
$stmtCT = $conn->prepare($sqlCT);
$stmtCT->bind_param("i", $dhId);
$stmtCT->execute();
$dsChiTiet = $stmtCT->get_result();
?>

<div class="page-header">
    <h2><i class="bi bi-receipt-cutoff me-2"></i>Đơn hàng #<?php echo $donHang['id']; ?></h2>
    <a href="don-hang.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Quay lại</a>
</div>

<div class="row g-4">
    <!-- Thông tin khách hàng -->
    <div class="col-lg-4">
        <div class="admin-form" style="height:100%;">
            <h5 class="mb-3"><i class="bi bi-person me-2"></i>Thông tin khách hàng</h5>
            <p><strong>Họ tên:</strong> <?php echo $donHang['ho_ten']; ?></p>
            <p><strong>Email:</strong> <?php echo $donHang['email'] ?: 'N/A'; ?></p>
            <p><strong>Điện thoại:</strong> <?php echo $donHang['dien_thoai']; ?></p>
            <p><strong>Địa chỉ:</strong> <?php echo $donHang['dia_chi']; ?></p>
            <?php if ($donHang['ghi_chu']): ?>
                <p><strong>Ghi chú:</strong> <?php echo $donHang['ghi_chu']; ?></p>
            <?php endif; ?>
            <hr>
            <p><strong>Ngày đặt:</strong> <?php echo date('d/m/Y H:i', strtotime($donHang['ngay_tao'])); ?></p>
            <p><strong>Trạng thái:</strong> 
                <span class="badge bg-<?php echo classTrangThai($donHang['trang_thai']); ?> fs-6">
                    <?php echo tenTrangThai($donHang['trang_thai']); ?>
                </span>
            </p>
        </div>
    </div>

    <!-- Chi tiết sản phẩm -->
    <div class="col-lg-8">
        <div class="admin-table">
            <div class="p-3">
                <h5 class="mb-0"><i class="bi bi-box-seam me-2"></i>Sản phẩm đã đặt</h5>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th class="text-center">Đơn giá</th>
                        <th class="text-center">Số lượng</th>
                        <th class="text-center">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($ct = $dsChiTiet->fetch_assoc()): ?>
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if ($ct['hinh_anh'] && file_exists(dirname(__DIR__) . '/assets/images/products/' . $ct['hinh_anh'])): ?>
                                        <img src="<?php echo layURLGoc(); ?>/assets/images/products/<?php echo $ct['hinh_anh']; ?>" class="product-thumb" alt="">
                                    <?php endif; ?>
                                    <strong><?php echo $ct['ten_san_pham']; ?></strong>
                                </div>
                            </td>
                            <td class="text-center"><?php echo formatGia($ct['gia']); ?></td>
                            <td class="text-center"><?php echo $ct['so_luong']; ?></td>
                            <td class="text-center fw-bold"><?php echo formatGia($ct['gia'] * $ct['so_luong']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                    <tr style="background:#f8f9fa;">
                        <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                        <td class="text-center"><strong style="font-size:1.3rem; color:var(--admin-primary);"><?php echo formatGia($donHang['tong_tien']); ?></strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
