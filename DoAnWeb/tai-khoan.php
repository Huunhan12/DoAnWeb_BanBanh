<?php
/**
 * TRANG TÀI KHOẢN - Xem đơn hàng
 */
require_once 'config/database.php';
require_once 'includes/functions.php';

if (!daHienDangNhap()) {
    datThongBao('warning', 'Vui lòng đăng nhập để xem tài khoản.');
    chuyenHuong('dang-nhap.php');
}

// Lấy thông tin user
$sql = "SELECT * FROM nguoi_dung WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['nguoi_dung_id']);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

// Lấy đơn hàng
$sqlDH = "SELECT * FROM don_hang WHERE nguoi_dung_id = ? ORDER BY ngay_tao DESC";
$stmtDH = $conn->prepare($sqlDH);
$stmtDH->bind_param("i", $_SESSION['nguoi_dung_id']);
$stmtDH->execute();
$dsDonHang = $stmtDH->get_result();

$tieuDeTrang = 'Tài khoản';
require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                <li class="breadcrumb-item active">Tài khoản</li>
            </ol>
        </nav>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <div class="row g-4">
            <!-- Thông tin tài khoản -->
            <div class="col-lg-4">
                <div class="cart-summary" style="position:static;">
                    <div class="text-center mb-4">
                        <div style="width:80px;height:80px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--primary-light));display:flex;align-items:center;justify-content:center;margin:0 auto 15px;font-size:2rem;color:white;">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <h4><?php echo $user['ho_ten']; ?></h4>
                        <p class="text-muted mb-0"><?php echo $user['email']; ?></p>
                    </div>
                    <hr>
                    <p><i class="bi bi-telephone me-2"></i><?php echo $user['dien_thoai'] ?: 'Chưa cập nhật'; ?></p>
                    <p><i class="bi bi-geo-alt me-2"></i><?php echo $user['dia_chi'] ?: 'Chưa cập nhật'; ?></p>
                    <p><i class="bi bi-calendar me-2"></i>Tham gia: <?php echo date('d/m/Y', strtotime($user['ngay_tao'])); ?></p>
                </div>
            </div>

            <!-- Đơn hàng -->
            <div class="col-lg-8">
                <h3 class="mb-4"><i class="bi bi-bag-check me-2"></i>Đơn hàng của tôi</h3>
                
                <?php if ($dsDonHang->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table" style="background:white; border-radius:var(--radius-md); overflow:hidden; box-shadow:var(--shadow-sm);">
                            <thead style="background:linear-gradient(135deg, var(--primary-dark), var(--primary)); color:white;">
                                <tr>
                                    <th>Mã ĐH</th>
                                    <th>Ngày đặt</th>
                                    <th>Tổng tiền</th>
                                    <th>Trạng thái</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($dh = $dsDonHang->fetch_assoc()): ?>
                                    <tr>
                                        <td><strong>#<?php echo $dh['id']; ?></strong></td>
                                        <td><?php echo date('d/m/Y H:i', strtotime($dh['ngay_tao'])); ?></td>
                                        <td class="fw-bold" style="color:var(--primary);"><?php echo formatGia($dh['tong_tien']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo classTrangThai($dh['trang_thai']); ?>">
                                                <?php echo tenTrangThai($dh['trang_thai']); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state" style="padding:40px;">
                        <div class="empty-icon" style="font-size:3rem;"><i class="bi bi-bag"></i></div>
                        <h3>Chưa có đơn hàng</h3>
                        <p>Bạn chưa đặt đơn hàng nào.</p>
                        <a href="danh-muc.php" class="btn btn-primary-custom">Mua sắm ngay</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
