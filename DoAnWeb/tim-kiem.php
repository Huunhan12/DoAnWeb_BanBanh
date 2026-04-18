<?php
/**
 * TRANG TÌM KIẾM SẢN PHẨM
 */
$tieuDeTrang = 'Tìm kiếm';
require_once 'includes/header.php';

$tuKhoa = isset($_GET['tu_khoa']) ? lamSach($_GET['tu_khoa']) : '';
$dsSanPham = null;
$tongKetQua = 0;

if (!empty($tuKhoa)) {
    $tuKhoaTimKiem = '%' . $tuKhoa . '%';
    
    // Đếm kết quả
    $sqlDem = "SELECT COUNT(*) as tong FROM san_pham WHERE ten_san_pham LIKE ? OR mo_ta LIKE ?";
    $stmtDem = $conn->prepare($sqlDem);
    $stmtDem->bind_param("ss", $tuKhoaTimKiem, $tuKhoaTimKiem);
    $stmtDem->execute();
    $tongKetQua = $stmtDem->get_result()->fetch_assoc()['tong'];
    
    // Lấy kết quả
    $sql = "SELECT sp.*, dm.ten_danh_muc FROM san_pham sp 
            JOIN danh_muc dm ON sp.danh_muc_id = dm.id 
            WHERE sp.ten_san_pham LIKE ? OR sp.mo_ta LIKE ?
            ORDER BY sp.ten_san_pham";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $tuKhoaTimKiem, $tuKhoaTimKiem);
    $stmt->execute();
    $dsSanPham = $stmt->get_result();
}
?>

<!-- Breadcrumb -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                <li class="breadcrumb-item active">Tìm kiếm</li>
            </ol>
        </nav>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <?php if (!empty($tuKhoa)): ?>
            <div class="mb-4">
                <h1 class="page-title" style="font-size:1.5rem;">
                    Kết quả tìm kiếm cho: "<span class="text-primary"><?php echo $tuKhoa; ?></span>"
                </h1>
                <p class="text-muted">Tìm thấy <?php echo $tongKetQua; ?> sản phẩm</p>
            </div>

            <?php if ($dsSanPham && $dsSanPham->num_rows > 0): ?>
                <div class="row g-4">
                    <?php while ($sp = $dsSanPham->fetch_assoc()): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="product-card">
                                <div class="card-img-wrapper">
                                    <?php if ($sp['hinh_anh'] && file_exists('assets/images/products/' . $sp['hinh_anh'])): ?>
                                        <img src="assets/images/products/<?php echo $sp['hinh_anh']; ?>" alt="<?php echo $sp['ten_san_pham']; ?>">
                                    <?php else: ?>
                                        <div class="img-placeholder"><i class="bi bi-cake2"></i></div>
                                    <?php endif; ?>
                                    <div class="card-badges">
                                        <?php if ($sp['moi']): ?><span class="badge-new">Mới</span><?php endif; ?>
                                        <?php if ($sp['noi_bat']): ?><span class="badge-featured">Nổi bật</span><?php endif; ?>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <span class="card-category"><?php echo $sp['ten_danh_muc']; ?></span>
                                    <h5 class="card-title"><a href="san-pham.php?id=<?php echo $sp['id']; ?>"><?php echo $sp['ten_san_pham']; ?></a></h5>
                                    <p class="card-desc"><?php echo catNganChuoi($sp['mo_ta'], 80); ?></p>
                                    <div class="card-footer-custom">
                                        <span class="card-price"><?php echo formatGia($sp['gia']); ?></span>
                                        <form action="gio-hang.php" method="POST" style="display:inline;">
                                            <input type="hidden" name="san_pham_id" value="<?php echo $sp['id']; ?>">
                                            <input type="hidden" name="hanh_dong" value="them">
                                            <input type="hidden" name="so_luong" value="1">
                                            <button type="submit" class="btn-add-cart"><i class="bi bi-cart-plus"></i></button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <div class="empty-icon"><i class="bi bi-search"></i></div>
                    <h3>Không tìm thấy kết quả</h3>
                    <p>Thử tìm với từ khóa khác hoặc duyệt theo danh mục.</p>
                    <a href="danh-muc.php" class="btn btn-primary-custom">Xem danh mục</a>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon"><i class="bi bi-search"></i></div>
                <h3>Tìm kiếm sản phẩm</h3>
                <p>Nhập từ khóa vào ô tìm kiếm ở trên để bắt đầu.</p>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
