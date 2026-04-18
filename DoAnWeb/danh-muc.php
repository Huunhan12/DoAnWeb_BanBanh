<?php
/**
 * TRANG DANH MỤC - Hiển thị sản phẩm theo danh mục
 */
require_once 'config/database.php';
require_once 'includes/functions.php';

$danhMucId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy thông tin danh mục
$sqlDM = "SELECT * FROM danh_muc WHERE id = ?";
$stmt = $conn->prepare($sqlDM);
$stmt->bind_param("i", $danhMucId);
$stmt->execute();
$danhMuc = $stmt->get_result()->fetch_assoc();

if (!$danhMuc) {
    // Nếu không có id, hiển thị tất cả sản phẩm
    $tieuDeTrang = 'Tất cả sản phẩm';
    $tenDanhMuc = 'Tất cả sản phẩm';
} else {
    $tieuDeTrang = $danhMuc['ten_danh_muc'];
    $tenDanhMuc = $danhMuc['ten_danh_muc'];
}

// Phân trang
$soSPTrang = 12;
$trangHienTai = isset($_GET['trang']) ? max(1, (int)$_GET['trang']) : 1;
$batDau = ($trangHienTai - 1) * $soSPTrang;

// Đếm tổng sản phẩm
if ($danhMucId > 0) {
    $sqlDem = "SELECT COUNT(*) as tong FROM san_pham WHERE danh_muc_id = ?";
    $stmtDem = $conn->prepare($sqlDem);
    $stmtDem->bind_param("i", $danhMucId);
} else {
    $sqlDem = "SELECT COUNT(*) as tong FROM san_pham";
    $stmtDem = $conn->prepare($sqlDem);
}
$stmtDem->execute();
$tongSP = $stmtDem->get_result()->fetch_assoc()['tong'];
$tongTrang = ceil($tongSP / $soSPTrang);

// Lấy danh sách sản phẩm
if ($danhMucId > 0) {
    $sqlSP = "SELECT sp.*, dm.ten_danh_muc FROM san_pham sp 
              JOIN danh_muc dm ON sp.danh_muc_id = dm.id 
              WHERE sp.danh_muc_id = ? 
              ORDER BY sp.ngay_tao DESC LIMIT ?, ?";
    $stmtSP = $conn->prepare($sqlSP);
    $stmtSP->bind_param("iii", $danhMucId, $batDau, $soSPTrang);
} else {
    $sqlSP = "SELECT sp.*, dm.ten_danh_muc FROM san_pham sp 
              JOIN danh_muc dm ON sp.danh_muc_id = dm.id 
              ORDER BY sp.ngay_tao DESC LIMIT ?, ?";
    $stmtSP = $conn->prepare($sqlSP);
    $stmtSP->bind_param("ii", $batDau, $soSPTrang);
}
$stmtSP->execute();
$dsSanPham = $stmtSP->get_result();

// Lấy tất cả danh mục cho sidebar
$sqlAllDM = "SELECT dm.*, COUNT(sp.id) as so_sp FROM danh_muc dm 
             LEFT JOIN san_pham sp ON dm.id = sp.danh_muc_id 
             GROUP BY dm.id ORDER BY dm.ten_danh_muc";
$dsAllDM = $conn->query($sqlAllDM);

require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                <li class="breadcrumb-item active"><?php echo $tenDanhMuc; ?></li>
            </ol>
        </nav>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <div class="row">
            <!-- Sidebar danh mục -->
            <div class="col-lg-3 mb-4">
                <div class="account-sidebar">
                    <h5 class="mb-3"><i class="bi bi-grid-fill me-2"></i>Danh mục</h5>
                    <div class="list-group">
                        <a href="danh-muc.php" class="list-group-item list-group-item-action <?php echo $danhMucId == 0 ? 'active' : ''; ?>">
                            Tất cả sản phẩm
                        </a>
                        <?php while ($dm = $dsAllDM->fetch_assoc()): ?>
                            <a href="danh-muc.php?id=<?php echo $dm['id']; ?>" 
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?php echo $danhMucId == $dm['id'] ? 'active' : ''; ?>">
                                <?php echo $dm['ten_danh_muc']; ?>
                                <span class="badge bg-secondary rounded-pill"><?php echo $dm['so_sp']; ?></span>
                            </a>
                        <?php endwhile; ?>
                    </div>
                </div>
            </div>

            <!-- Danh sách sản phẩm -->
            <div class="col-lg-9">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="page-title mb-0" style="font-size:1.5rem;"><?php echo $tenDanhMuc; ?></h1>
                    <span class="text-muted"><?php echo $tongSP; ?> sản phẩm</span>
                </div>

                <?php if ($dsSanPham->num_rows > 0): ?>
                    <div class="row g-4">
                        <?php while ($sp = $dsSanPham->fetch_assoc()): ?>
                            <div class="col-lg-4 col-md-6">
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
                                                <button type="submit" class="btn-add-cart" title="Thêm vào giỏ"><i class="bi bi-cart-plus"></i></button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>

                    <!-- Phân trang -->
                    <?php if ($tongTrang > 1): ?>
                        <nav class="mt-5 d-flex justify-content-center">
                            <ul class="pagination">
                                <?php if ($trangHienTai > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?id=<?php echo $danhMucId; ?>&trang=<?php echo $trangHienTai - 1; ?>">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php for ($i = 1; $i <= $tongTrang; $i++): ?>
                                    <li class="page-item <?php echo $i == $trangHienTai ? 'active' : ''; ?>">
                                        <a class="page-link" href="?id=<?php echo $danhMucId; ?>&trang=<?php echo $i; ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                <?php if ($trangHienTai < $tongTrang): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?id=<?php echo $danhMucId; ?>&trang=<?php echo $trangHienTai + 1; ?>">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>

                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon"><i class="bi bi-inbox"></i></div>
                        <h3>Chưa có sản phẩm</h3>
                        <p>Danh mục này chưa có sản phẩm nào.</p>
                        <a href="index.php" class="btn btn-primary-custom">Về trang chủ</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
