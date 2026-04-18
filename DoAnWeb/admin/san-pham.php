<?php
/**
 * ADMIN - Quản lý sản phẩm (Danh sách)
 */
$tieuDeTrang = 'Quản lý sản phẩm';
require_once 'includes/header.php';

// Lấy danh sách sản phẩm
$sql = "SELECT sp.*, dm.ten_danh_muc FROM san_pham sp 
        JOIN danh_muc dm ON sp.danh_muc_id = dm.id 
        ORDER BY sp.ngay_tao DESC";
$dsSanPham = $conn->query($sql);
?>

<div class="page-header">
    <h2><i class="bi bi-box-seam me-2"></i>Quản lý sản phẩm</h2>
    <a href="them-san-pham.php" class="btn btn-admin-primary">
        <i class="bi bi-plus-circle me-1"></i> Thêm sản phẩm
    </a>
</div>

<div class="admin-table">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ảnh</th>
                <th>Tên sản phẩm</th>
                <th>Danh mục</th>
                <th>Giá</th>
                <th>Tồn kho</th>
                <th>Nổi bật</th>
                <th>Mới</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($dsSanPham->num_rows > 0): ?>
                <?php while ($sp = $dsSanPham->fetch_assoc()): ?>
                    <tr>
                        <td><strong>#<?php echo $sp['id']; ?></strong></td>
                        <td>
                            <?php if ($sp['hinh_anh'] && file_exists(dirname(__DIR__) . '/assets/images/products/' . $sp['hinh_anh'])): ?>
                                <img src="<?php echo layURLGoc(); ?>/assets/images/products/<?php echo $sp['hinh_anh']; ?>" class="product-thumb" alt="">
                            <?php else: ?>
                                <div class="product-thumb d-flex align-items-center justify-content-center" style="background:#f0f0f0;"><i class="bi bi-image"></i></div>
                            <?php endif; ?>
                        </td>
                        <td><strong><?php echo $sp['ten_san_pham']; ?></strong></td>
                        <td><span class="badge bg-secondary"><?php echo $sp['ten_danh_muc']; ?></span></td>
                        <td class="fw-bold" style="color:var(--admin-primary);"><?php echo formatGia($sp['gia']); ?></td>
                        <td>
                            <?php if ($sp['ton_kho'] > 0): ?>
                                <span class="badge bg-success"><?php echo $sp['ton_kho']; ?></span>
                            <?php else: ?>
                                <span class="badge bg-danger">Hết</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $sp['noi_bat'] ? '<i class="bi bi-star-fill text-warning"></i>' : '<i class="bi bi-star text-muted"></i>'; ?></td>
                        <td><?php echo $sp['moi'] ? '<span class="badge bg-success">Mới</span>' : '-'; ?></td>
                        <td>
                            <a href="sua-san-pham.php?id=<?php echo $sp['id']; ?>" class="btn btn-sm btn-outline-primary" title="Sửa"><i class="bi bi-pencil"></i></a>
                            <a href="xoa-san-pham.php?id=<?php echo $sp['id']; ?>" class="btn btn-sm btn-outline-danger btn-delete-confirm" title="Xóa"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="9" class="text-center text-muted py-4">Chưa có sản phẩm nào</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php require_once 'includes/footer.php'; ?>
