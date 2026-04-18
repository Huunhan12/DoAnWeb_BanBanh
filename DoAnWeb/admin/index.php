<?php
/**
 * ADMIN - Dashboard
 */
$tieuDeTrang = 'Dashboard';
require_once 'includes/header.php';

// Thống kê doanh thu
$doanhThu = $conn->query("SELECT COALESCE(SUM(tong_tien), 0) as tong FROM don_hang WHERE trang_thai = 'hoan_thanh'")->fetch_assoc()['tong'];

// Đơn hàng gần đây
$sqlDHGanDay = "SELECT dh.*, nd.ho_ten as ten_nguoi_dung FROM don_hang dh 
                LEFT JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id 
                ORDER BY dh.ngay_tao DESC LIMIT 5";
$dsDHGanDay = $conn->query($sqlDHGanDay);

// Sản phẩm bán chạy
$sqlBanChay = "SELECT sp.ten_san_pham, SUM(ct.so_luong) as tong_ban 
               FROM chi_tiet_don_hang ct 
               JOIN san_pham sp ON ct.san_pham_id = sp.id 
               GROUP BY ct.san_pham_id ORDER BY tong_ban DESC LIMIT 5";
$dsBanChay = $conn->query($sqlBanChay);
?>

<!-- Statistics Cards -->
<div class="row g-4 mb-4">
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $tongSP; ?></h3>
                <p>Sản phẩm</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                <i class="bi bi-receipt"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $tongDH; ?></h3>
                <p>Đơn hàng</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                <i class="bi bi-people"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $tongND; ?></h3>
                <p>Tài khoản</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="stat-card">
            <div class="stat-icon" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo number_format($doanhThu, 0, ',', '.'); ?>₫</h3>
                <p>Doanh thu</p>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Đơn hàng gần đây -->
    <div class="col-lg-8">
        <div class="admin-table">
            <div class="p-3 d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Đơn hàng gần đây</h5>
                <a href="don-hang.php" class="btn btn-sm btn-admin-primary">Xem tất cả</a>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Mã ĐH</th>
                        <th>Khách hàng</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày đặt</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($dsDHGanDay->num_rows > 0): ?>
                        <?php while ($dh = $dsDHGanDay->fetch_assoc()): ?>
                            <tr>
                                <td><strong>#<?php echo $dh['id']; ?></strong></td>
                                <td><?php echo $dh['ho_ten']; ?></td>
                                <td class="fw-bold" style="color:var(--admin-primary);"><?php echo formatGia($dh['tong_tien']); ?></td>
                                <td><span class="badge bg-<?php echo classTrangThai($dh['trang_thai']); ?>"><?php echo tenTrangThai($dh['trang_thai']); ?></span></td>
                                <td><?php echo date('d/m/Y H:i', strtotime($dh['ngay_tao'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Chưa có đơn hàng</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Thông tin nhanh -->
    <div class="col-lg-4">
        <div class="admin-table">
            <div class="p-3">
                <h5 class="mb-0"><i class="bi bi-fire me-2"></i>Sản phẩm bán chạy</h5>
            </div>
            <table class="table">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Đã bán</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($dsBanChay && $dsBanChay->num_rows > 0): ?>
                        <?php while ($sp = $dsBanChay->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $sp['ten_san_pham']; ?></td>
                                <td><span class="badge bg-success"><?php echo $sp['tong_ban']; ?></span></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="2" class="text-center text-muted py-3">Chưa có dữ liệu</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Đơn chờ xử lý -->
        <div class="stat-card mt-4" style="background: linear-gradient(135deg, #fff5f5, #ffe0e0);">
            <div class="stat-icon" style="background: linear-gradient(135deg, #ff6b6b, #ee5a24);">
                <i class="bi bi-bell"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo $dhMoi; ?></h3>
                <p>Đơn chờ xác nhận</p>
            </div>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
