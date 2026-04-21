<?php
/**
 * ADMIN - Quản lý đơn hàng
 */
$tieuDeTrang = 'Quản lý đơn hàng';
require_once 'includes/header.php';

// Cập nhật trạng thái
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cap_nhat_trang_thai'])) {
    $dhId = (int)$_POST['don_hang_id'];
    $trangThai = lamSach($_POST['trang_thai']);
    
    $stmt = $conn->prepare("UPDATE don_hang SET trang_thai = ? WHERE id = ?");
    $stmt->bind_param("si", $trangThai, $dhId);
    if ($stmt->execute()) {
        datThongBao('success', 'Cập nhật trạng thái đơn hàng #' . $dhId . ' thành công!');
    }
    chuyenHuong('don-hang.php');
}

// Lọc theo trạng thái
$locTrangThai = isset($_GET['trang_thai']) ? lamSach($_GET['trang_thai']) : '';

if (!empty($locTrangThai)) {
    $sql = "SELECT dh.*, nd.ho_ten as ten_nd FROM don_hang dh 
            LEFT JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id 
            WHERE dh.trang_thai = ? ORDER BY dh.ngay_tao DESC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $locTrangThai);
    $stmt->execute();
    $dsDonHang = $stmt->get_result();
} else {
    $sql = "SELECT dh.*, nd.ho_ten as ten_nd FROM don_hang dh 
            LEFT JOIN nguoi_dung nd ON dh.nguoi_dung_id = nd.id 
            ORDER BY dh.ngay_tao DESC";
    $dsDonHang = $conn->query($sql);
}
?>

<div class="page-header">
    <h2><i class="bi bi-receipt me-2"></i>Quản lý đơn hàng</h2>
</div>

<!-- Lọc trạng thái -->
<div class="mb-4">
    <a href="don-hang.php" class="btn btn-sm <?php echo empty($locTrangThai) ? 'btn-admin-primary' : 'btn-outline-secondary'; ?> me-1">Tất cả</a>
    <a href="don-hang.php?trang_thai=cho_xac_nhan" class="btn btn-sm <?php echo $locTrangThai == 'cho_xac_nhan' ? 'btn-warning' : 'btn-outline-warning'; ?> me-1">Chờ xác nhận</a>
    <a href="don-hang.php?trang_thai=da_xac_nhan" class="btn btn-sm <?php echo $locTrangThai == 'da_xac_nhan' ? 'btn-info' : 'btn-outline-info'; ?> me-1">Đã xác nhận</a>
    <a href="don-hang.php?trang_thai=dang_giao" class="btn btn-sm <?php echo $locTrangThai == 'dang_giao' ? 'btn-primary' : 'btn-outline-primary'; ?> me-1">Đang giao</a>
    <a href="don-hang.php?trang_thai=hoan_thanh" class="btn btn-sm <?php echo $locTrangThai == 'hoan_thanh' ? 'btn-success' : 'btn-outline-success'; ?> me-1">Hoàn thành</a>
    <a href="don-hang.php?trang_thai=da_huy" class="btn btn-sm <?php echo $locTrangThai == 'da_huy' ? 'btn-danger' : 'btn-outline-danger'; ?> me-1">Đã hủy</a>
</div>

<div class="admin-table">
    <table class="table">
        <thead>
            <tr>
                <th>Mã ĐH</th>
                <th>Khách hàng</th>
                <th>Điện thoại</th>
                <th>Tổng tiền</th>
                <th>Trạng thái</th>
                <th>Ngày đặt</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($dsDonHang->num_rows > 0): ?>
                <?php while ($dh = $dsDonHang->fetch_assoc()): ?>
                    <tr>
                        <td><strong>#<?php echo $dh['id']; ?></strong></td>
                        <td><?php echo $dh['ho_ten']; ?></td>
                        <td><?php echo $dh['dien_thoai']; ?></td>
                        <td class="fw-bold" style="color:var(--admin-primary);"><?php echo formatGia($dh['tong_tien']); ?></td>
                        <td>
                            <form method="POST" class="d-flex gap-1" style="min-width:200px;">
                                <input type="hidden" name="don_hang_id" value="<?php echo $dh['id']; ?>">
                                <select name="trang_thai" class="form-select form-select-sm" style="border-radius:8px;">
                                    <option value="cho_xac_nhan" <?php echo $dh['trang_thai'] == 'cho_xac_nhan' ? 'selected' : ''; ?>>Chờ xác nhận</option>
                                    <option value="da_xac_nhan" <?php echo $dh['trang_thai'] == 'da_xac_nhan' ? 'selected' : ''; ?>>Đã xác nhận</option>
                                    <option value="dang_giao" <?php echo $dh['trang_thai'] == 'dang_giao' ? 'selected' : ''; ?>>Đang giao</option>
                                    <option value="hoan_thanh" <?php echo $dh['trang_thai'] == 'hoan_thanh' ? 'selected' : ''; ?>>Hoàn thành</option>
                                    <option value="da_huy" <?php echo $dh['trang_thai'] == 'da_huy' ? 'selected' : ''; ?>>Đã hủy</option>
                                </select>
                                <button type="submit" name="cap_nhat_trang_thai" class="btn btn-sm btn-outline-success" title="Cập nhật"><i class="bi bi-check-lg"></i></button>
                            </form>
                        </td>
                        <td><?php echo date('d/m/Y H:i', strtotime($dh['ngay_tao'])); ?></td>
                        <td>
                            <a href="chi-tiet-don-hang.php?id=<?php echo $dh['id']; ?>" class="btn btn-sm btn-outline-primary" title="Chi tiết"><i class="bi bi-eye"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="7" class="text-center text-muted py-4">Không có đơn hàng</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
