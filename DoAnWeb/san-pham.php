<?php
/**
 * TRANG CHI TIẾT SẢN PHẨM
 */
require_once 'config/database.php';
require_once 'includes/functions.php';

$sanPhamId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy thông tin sản phẩm
$sql = "SELECT sp.*, dm.ten_danh_muc FROM san_pham sp 
        JOIN danh_muc dm ON sp.danh_muc_id = dm.id 
        WHERE sp.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $sanPhamId);
$stmt->execute();
$sanPham = $stmt->get_result()->fetch_assoc();

if (!$sanPham) {
    datThongBao('danger', 'Không tìm thấy sản phẩm!');
    chuyenHuong('index.php');
}

$tieuDeTrang = $sanPham['ten_san_pham'];

// Sản phẩm liên quan (cùng danh mục)
$sqlLienQuan = "SELECT sp.*, dm.ten_danh_muc FROM san_pham sp 
                JOIN danh_muc dm ON sp.danh_muc_id = dm.id 
                WHERE sp.danh_muc_id = ? AND sp.id != ? 
                ORDER BY RAND() LIMIT 4";
$stmtLQ = $conn->prepare($sqlLienQuan);
$stmtLQ->bind_param("ii", $sanPham['danh_muc_id'], $sanPhamId);
$stmtLQ->execute();
$dsLienQuan = $stmtLQ->get_result();

require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="danh-muc.php?id=<?php echo $sanPham['danh_muc_id']; ?>"><?php echo $sanPham['ten_danh_muc']; ?></a></li>
                <li class="breadcrumb-item active"><?php echo $sanPham['ten_san_pham']; ?></li>
            </ol>
        </nav>
    </div>
</section>

<!-- Chi tiết sản phẩm -->
<section class="product-detail">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-6">
                <div class="product-image">
                    <?php if ($sanPham['hinh_anh'] && file_exists('assets/images/products/' . $sanPham['hinh_anh'])): ?>
                        <img src="assets/images/products/<?php echo $sanPham['hinh_anh']; ?>" alt="<?php echo $sanPham['ten_san_pham']; ?>">
                    <?php else: ?>
                        <div class="img-placeholder" style="height:400px;"><i class="bi bi-cake2" style="font-size:5rem;"></i></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="col-lg-6">
                <span class="product-category-label"><?php echo $sanPham['ten_danh_muc']; ?></span>
                <h1 class="product-name"><?php echo $sanPham['ten_san_pham']; ?></h1>
                
                <div class="d-flex gap-2 mb-3">
                    <?php if ($sanPham['moi']): ?><span class="badge-new" style="display:inline-block;">Mới</span><?php endif; ?>
                    <?php if ($sanPham['noi_bat']): ?><span class="badge-featured" style="display:inline-block;">Nổi bật</span><?php endif; ?>
                    <?php if ($sanPham['ton_kho'] > 0): ?>
                        <span class="badge bg-success">Còn hàng (<?php echo $sanPham['ton_kho']; ?>)</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Hết hàng</span>
                    <?php endif; ?>
                </div>

                <div class="product-price"><?php echo formatGia($sanPham['gia']); ?></div>
                
                <div class="product-description">
                    <?php echo nl2br($sanPham['mo_ta']); ?>
                </div>

                <?php if ($sanPham['ton_kho'] > 0): ?>
                    <form action="gio-hang.php" method="POST">
                        <input type="hidden" name="san_pham_id" value="<?php echo $sanPham['id']; ?>">
                        <input type="hidden" name="hanh_dong" value="them">
                        
                        <div class="quantity-control">
                            <label class="me-2 fw-bold">Số lượng:</label>
                            <button type="button" class="btn-qty" id="btn-giam">−</button>
                            <input type="number" name="so_luong" id="so-luong" value="1" min="1" max="<?php echo $sanPham['ton_kho']; ?>">
                            <button type="button" class="btn-qty" id="btn-tang">+</button>
                        </div>

                        <div class="d-flex gap-3">
                            <button type="submit" class="btn btn-primary-custom">
                                <i class="bi bi-cart-plus me-2"></i>Thêm vào giỏ hàng
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</section>

<!-- Sản phẩm liên quan -->
<?php if ($dsLienQuan->num_rows > 0): ?>
<section class="page-section" style="background: linear-gradient(135deg, var(--cream), var(--bg-light));">
    <div class="container">
        <div class="section-title">
            <h2>Sản Phẩm Liên Quan</h2>
            <p>Có thể bạn cũng thích</p>
        </div>
        <div class="row g-4">
            <?php while ($sp = $dsLienQuan->fetch_assoc()): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card">
                        <div class="card-img-wrapper">
                            <?php if ($sp['hinh_anh'] && file_exists('assets/images/products/' . $sp['hinh_anh'])): ?>
                                <img src="assets/images/products/<?php echo $sp['hinh_anh']; ?>" alt="<?php echo $sp['ten_san_pham']; ?>">
                            <?php else: ?>
                                <div class="img-placeholder"><i class="bi bi-cake2"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="card-body">
                            <span class="card-category"><?php echo $sp['ten_danh_muc']; ?></span>
                            <h5 class="card-title"><a href="san-pham.php?id=<?php echo $sp['id']; ?>"><?php echo $sp['ten_san_pham']; ?></a></h5>
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
    </div>
</section>
<?php endif; ?>

<?php require_once 'includes/footer.php'; ?>
