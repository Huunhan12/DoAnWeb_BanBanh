<?php
/**
 * ADMIN - Quản lý tài khoản
 */
$tieuDeTrang = 'Quản lý tài khoản';
require_once 'includes/header.php';

// Cập nhật vai trò
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cap_nhat_vai_tro'])) {
    $userId = (int)$_POST['user_id'];
    $vaiTro = lamSach($_POST['vai_tro']);
    
    // Không cho phép tự thay đổi vai trò của mình
    if ($userId == $_SESSION['nguoi_dung_id']) {
        datThongBao('danger', 'Không thể thay đổi vai trò của chính mình!');
    } else {
        $stmt = $conn->prepare("UPDATE nguoi_dung SET vai_tro = ? WHERE id = ?");
        $stmt->bind_param("si", $vaiTro, $userId);
        if ($stmt->execute()) {
            datThongBao('success', 'Cập nhật vai trò thành công!');
        }
    }
    chuyenHuong('tai-khoan.php');
}

// Xóa tài khoản
if (isset($_GET['xoa'])) {
    $userId = (int)$_GET['xoa'];
    if ($userId == $_SESSION['nguoi_dung_id']) {
        datThongBao('danger', 'Không thể xóa tài khoản của chính mình!');
    } else {
        $stmt = $conn->prepare("DELETE FROM nguoi_dung WHERE id = ?");
        $stmt->bind_param("i", $userId);
        if ($stmt->execute()) {
            datThongBao('success', 'Đã xóa tài khoản!');
        }
    }
    chuyenHuong('tai-khoan.php');
}

// Lấy danh sách tài khoản
$sql = "SELECT * FROM nguoi_dung ORDER BY ngay_tao DESC";
$dsNguoiDung = $conn->query($sql);
?>

<div class="page-header">
    <h2><i class="bi bi-people me-2"></i>Quản lý tài khoản</h2>
</div>

<div class="admin-table">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Họ tên</th>
                <th>Email</th>
                <th>Điện thoại</th>
                <th>Vai trò</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($nd = $dsNguoiDung->fetch_assoc()): ?>
                <tr>
                    <td><strong>#<?php echo $nd['id']; ?></strong></td>
                    <td>
                        <strong><?php echo $nd['ho_ten']; ?></strong>
                        <?php if ($nd['id'] == $_SESSION['nguoi_dung_id']): ?>
                            <span class="badge bg-info">Bạn</span>
                        <?php endif; ?>
                    </td>
                    <td><?php echo $nd['email']; ?></td>
                    <td><?php echo $nd['dien_thoai'] ?: 'N/A'; ?></td>
                    <td>
                        <form method="POST" class="d-flex gap-1" style="min-width:160px;">
                            <input type="hidden" name="user_id" value="<?php echo $nd['id']; ?>">
                            <select name="vai_tro" class="form-select form-select-sm" style="border-radius:8px;" <?php echo $nd['id'] == $_SESSION['nguoi_dung_id'] ? 'disabled' : ''; ?>>
                                <option value="user" <?php echo $nd['vai_tro'] == 'user' ? 'selected' : ''; ?>>👤 User</option>
                                <option value="admin" <?php echo $nd['vai_tro'] == 'admin' ? 'selected' : ''; ?>>👑 Admin</option>
                            </select>
                            <?php if ($nd['id'] != $_SESSION['nguoi_dung_id']): ?>
                                <button type="submit" name="cap_nhat_vai_tro" class="btn btn-sm btn-outline-success" title="Cập nhật"><i class="bi bi-check-lg"></i></button>
                            <?php endif; ?>
                        </form>
                    </td>
                    <td><?php echo date('d/m/Y', strtotime($nd['ngay_tao'])); ?></td>
                    <td>
                        <?php if ($nd['id'] != $_SESSION['nguoi_dung_id']): ?>
                            <a href="tai-khoan.php?xoa=<?php echo $nd['id']; ?>" class="btn btn-sm btn-outline-danger btn-delete-confirm" title="Xóa"><i class="bi bi-trash"></i></a>
                        <?php else: ?>
                            <span class="text-muted">-</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
