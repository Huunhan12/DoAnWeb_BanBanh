<?php
/**
 * TRANG CHỦ - Sweet Cake Shop
 */
$tieuDeTrang = 'Trang Chủ';
require_once 'includes/header.php';

// Lấy sản phẩm mới
$sqlMoi = "SELECT sp.*, dm.ten_danh_muc FROM san_pham sp 
           JOIN danh_muc dm ON sp.danh_muc_id = dm.id 
           WHERE sp.moi = 1 ORDER BY sp.ngay_tao DESC LIMIT 8";
$dsMoi = $conn->query($sqlMoi);

// Lấy sản phẩm nổi bật
$sqlNoiBat = "SELECT sp.*, dm.ten_danh_muc FROM san_pham sp 
              JOIN danh_muc dm ON sp.danh_muc_id = dm.id 
              WHERE sp.noi_bat = 1 ORDER BY sp.ngay_tao DESC LIMIT 8";
$dsNoiBat = $conn->query($sqlNoiBat);

// Lấy danh mục
$sqlDM = "SELECT dm.*, COUNT(sp.id) as so_san_pham FROM danh_muc dm 
          LEFT JOIN san_pham sp ON dm.id = sp.danh_muc_id 
          GROUP BY dm.id ORDER BY dm.ten_danh_muc";
$dsDanhMuc = $conn->query($sqlDM);

// Icon cho từng danh mục
$iconDanhMuc = array('🎂', '🍞', '🥐', '🧁', '🍪');
?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <p class="hero-subtitle" style="opacity:0; animation: fadeIn 0.8s 0.2s forwards;">✨ Chào mừng đến với Sweet Cake</p>
                <h1 class="hero-title" style="opacity:0; animation: fadeIn 0.8s 0.4s forwards;">Hương Vị Ngọt Ngào<br>Cho Mọi Khoảnh Khắc</h1>
                <p class="hero-subtitle" style="opacity:0; animation: fadeIn 0.8s 0.6s forwards;">
                    Khám phá bộ sưu tập bánh ngọt cao cấp được chế biến từ nguyên liệu tươi ngon nhất, mang đến trải nghiệm vị giác tuyệt vời.
                </p>
                <div style="opacity:0; animation: fadeIn 0.8s 0.8s forwards;">
                    <a href="#san-pham-moi" class="btn btn-hero">
                        <i class="bi bi-bag-heart-fill me-2"></i>Mua sắm ngay
                    </a>
                </div>
            </div>
            <div class="col-lg-6 d-none d-lg-block">
                <div class="hero-decoration">
                    <span class="hero-emoji">🎂</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Features -->
<section class="features-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6">
                <div class="feature-box">
                    <div class="feature-icon"><i class="bi bi-award-fill"></i></div>
                    <h5>Chất Lượng Cao</h5>
                    <p>Nguyên liệu nhập khẩu, đảm bảo an toàn vệ sinh</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="feature-box">
                    <div class="feature-icon"><i class="bi bi-truck"></i></div>
                    <h5>Giao Hàng Nhanh</h5>
                    <p>Miễn phí giao hàng cho đơn từ 500.000₫</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="feature-box">
                    <div class="feature-icon"><i class="bi bi-heart-fill"></i></div>
                    <h5>Làm Từ Tâm</h5>
                    <p>Mỗi chiếc bánh đều được làm thủ công tỉ mỉ</p>
                </div>
            </div>
            <div class="col-md-3 col-6">
                <div class="feature-box">
                    <div class="feature-icon"><i class="bi bi-shield-check"></i></div>
                    <h5>Đảm Bảo Hài Lòng</h5>
                    <p>Cam kết hoàn tiền nếu không hài lòng</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Danh mục sản phẩm -->
<section class="page-section">
    <div class="container">
        <div class="section-title">
            <h2>Danh Mục Sản Phẩm</h2>
            <p>Khám phá các loại bánh ngọt đa dạng của chúng tôi</p>
        </div>
        <div class="row g-4">
            <?php 
            $i = 0;
            if ($dsDanhMuc->num_rows > 0):
                while ($dm = $dsDanhMuc->fetch_assoc()): 
                    $icon = $iconDanhMuc[$i % count($iconDanhMuc)];
            ?>
                <div class="col-lg-2 col-md-4 col-6">
                    <a href="danh-muc.php?id=<?php echo $dm['id']; ?>" style="text-decoration:none; color:inherit;">
                        <div class="category-card">
                            <div class="cat-icon"><?php echo $icon; ?></div>
                            <h5><?php echo $dm['ten_danh_muc']; ?></h5>
                            <p><?php echo $dm['so_san_pham']; ?> sản phẩm</p>
                        </div>
                    </a>
                </div>
            <?php 
                    $i++;
                endwhile; 
            endif; ?>
        </div>
    </div>
</section>

<!-- Sản phẩm mới -->
<section class="page-section" style="background: linear-gradient(135deg, var(--cream), var(--bg-light));" id="san-pham-moi">
    <div class="container">
        <div class="section-title">
            <h2>🆕 Sản Phẩm Mới</h2>
            <p>Những chiếc bánh mới nhất vừa ra lò</p>
        </div>
        <div class="row g-4">
            <?php if ($dsMoi->num_rows > 0): while ($sp = $dsMoi->fetch_assoc()): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="product-card">
                        <div class="card-img-wrapper">
                            <?php if ($sp['hinh_anh'] && file_exists('assets/images/products/' . $sp['hinh_anh'])): ?>
                                <img src="assets/images/products/<?php echo $sp['hinh_anh']; ?>" alt="<?php echo $sp['ten_san_pham']; ?>">
                            <?php else: ?>
                                <div class="img-placeholder"><i class="bi bi-cake2"></i></div>
                            <?php endif; ?>
                            <div class="card-badges">
                                <span class="badge-new">Mới</span>
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
            <?php endwhile; endif; ?>
        </div>
    </div>
</section>

<!-- Sản phẩm nổi bật -->
<section class="page-section">
    <div class="container">
        <div class="section-title">
            <h2>⭐ Sản Phẩm Nổi Bật</h2>
            <p>Những chiếc bánh được yêu thích nhất</p>
        </div>
        <div class="row g-4">
            <?php if ($dsNoiBat->num_rows > 0): while ($sp = $dsNoiBat->fetch_assoc()): ?>
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
                                <span class="badge-featured">Nổi bật</span>
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
            <?php endwhile; endif; ?>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
