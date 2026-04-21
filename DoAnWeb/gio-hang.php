<?php
/**
 * TRANG GIỎ HÀNG - Sử dụng SESSION
 */
require_once 'config/database.php';
require_once 'includes/functions.php';

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['gio_hang'])) {
    $_SESSION['gio_hang'] = array();
}

// Xử lý hành động
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hanhDong = isset($_POST['hanh_dong']) ? $_POST['hanh_dong'] : '';
    
    switch ($hanhDong) {
        case 'them':
            $spId = (int)$_POST['san_pham_id'];
            $soLuong = max(1, (int)$_POST['so_luong']);
            
            // Lấy thông tin sản phẩm từ DB
            $sql = "SELECT * FROM san_pham WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $spId);
            $stmt->execute();
            $sp = $stmt->get_result()->fetch_assoc();
            
            if ($sp) {
                if (isset($_SESSION['gio_hang'][$spId])) {
                    $_SESSION['gio_hang'][$spId]['so_luong'] += $soLuong;
                } else {
                    $_SESSION['gio_hang'][$spId] = array(
                        'id' => $sp['id'],
                        'ten' => $sp['ten_san_pham'],
                        'gia' => $sp['gia'],
                        'hinh_anh' => $sp['hinh_anh'],
                        'so_luong' => $soLuong
                    );
                }
                datThongBao('success', '<i class="bi bi-check-circle me-1"></i> Đã thêm <strong>' . $sp['ten_san_pham'] . '</strong> vào giỏ hàng!');
            }
            chuyenHuong('gio-hang.php');
            break;
            
        case 'cap_nhat':
            $spId = (int)$_POST['san_pham_id'];
            $soLuong = max(1, (int)$_POST['so_luong']);
            
            if (isset($_SESSION['gio_hang'][$spId])) {
                $_SESSION['gio_hang'][$spId]['so_luong'] = $soLuong;
                datThongBao('info', '<i class="bi bi-arrow-repeat me-1"></i> Đã cập nhật số lượng!');
            }
            chuyenHuong('gio-hang.php');
            break;
            
        case 'xoa':
            $spId = (int)$_POST['san_pham_id'];
            if (isset($_SESSION['gio_hang'][$spId])) {
                $tenSP = $_SESSION['gio_hang'][$spId]['ten'];
                unset($_SESSION['gio_hang'][$spId]);
                datThongBao('warning', '<i class="bi bi-trash me-1"></i> Đã xóa <strong>' . $tenSP . '</strong> khỏi giỏ hàng.');
            }
            chuyenHuong('gio-hang.php');
            break;
            
        case 'xoa_het':
            $_SESSION['gio_hang'] = array();
            datThongBao('warning', '<i class="bi bi-trash me-1"></i> Đã xóa toàn bộ giỏ hàng.');
            chuyenHuong('gio-hang.php');
            break;
    }
}

$tieuDeTrang = 'Giỏ hàng';
require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                <li class="breadcrumb-item active">Giỏ hàng</li>
            </ol>
        </nav>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <h1 class="page-title"><i class="bi bi-cart3 me-2"></i>Giỏ hàng của bạn</h1>

        <?php if (!empty($_SESSION['gio_hang'])): ?>
            <div class="row g-4">
                <div class="col-lg-8">
                    <div class="cart-table">
                        <table class="table table-borderless mb-0">
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th class="text-center">Giá</th>
                                    <th class="text-center">Số lượng</th>
                                    <th class="text-center">Thành tiền</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($_SESSION['gio_hang'] as $spId => $sp): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-3">
                                                <?php if ($sp['hinh_anh'] && file_exists('assets/images/products/' . $sp['hinh_anh'])): ?>
                                                    <img src="assets/images/products/<?php echo $sp['hinh_anh']; ?>" class="cart-product-img" alt="">
                                                <?php else: ?>
                                                    <div class="cart-product-img img-placeholder" style="width:70px;height:70px;border-radius:8px;font-size:1.5rem;"><i class="bi bi-cake2"></i></div>
                                                <?php endif; ?>
                                                <div>
                                                    <a href="san-pham.php?id=<?php echo $spId; ?>" class="fw-semibold text-dark"><?php echo $sp['ten']; ?></a>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center"><?php echo formatGia($sp['gia']); ?></td>
                                        <td class="text-center">
                                            <form action="gio-hang.php" method="POST" class="d-inline">
                                                <input type="hidden" name="hanh_dong" value="cap_nhat">
                                                <input type="hidden" name="san_pham_id" value="<?php echo $spId; ?>">
                                                <input type="number" name="so_luong" value="<?php echo $sp['so_luong']; ?>" 
                                                       min="1" max="99" class="form-control cart-qty-input" style="width:70px;display:inline;">
                                            </form>
                                        </td>
                                        <td class="text-center fw-bold" style="color:var(--primary);">
                                            <?php echo formatGia($sp['gia'] * $sp['so_luong']); ?>
                                        </td>
                                        <td>
                                            <form action="gio-hang.php" method="POST" class="d-inline">
                                                <input type="hidden" name="hanh_dong" value="xoa">
                                                <input type="hidden" name="san_pham_id" value="<?php echo $spId; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle" title="Xóa">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 d-flex justify-content-between">
                        <a href="danh-muc.php" class="btn btn-outline-custom"><i class="bi bi-arrow-left me-1"></i>Tiếp tục mua sắm</a>
                        <form action="gio-hang.php" method="POST">
                            <input type="hidden" name="hanh_dong" value="xoa_het">
                            <button type="submit" class="btn btn-outline-danger"><i class="bi bi-trash me-1"></i>Xóa tất cả</button>
                        </form>
                    </div>
                </div>

                <!-- Tóm tắt giỏ hàng -->
                <div class="col-lg-4">
                    <div class="cart-summary">
                        <h4><i class="bi bi-receipt me-2"></i>Tóm tắt đơn hàng</h4>
                        
                        <?php foreach ($_SESSION['gio_hang'] as $sp): ?>
                            <div class="d-flex justify-content-between py-2" style="border-bottom: 1px solid #f0f0f0;">
                                <span><?php echo catNganChuoi($sp['ten'], 25); ?> x<?php echo $sp['so_luong']; ?></span>
                                <span><?php echo formatGia($sp['gia'] * $sp['so_luong']); ?></span>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="d-flex justify-content-between mt-3 pt-3" style="border-top: 2px solid var(--primary);">
                            <strong>Tổng cộng:</strong>
                            <span class="total-price"><?php echo formatGia(tongTienGioHang()); ?></span>
                        </div>

                        <a href="dat-hang.php" class="btn btn-primary-custom w-100 mt-4">
                            <i class="bi bi-credit-card me-2"></i>Tiến hành đặt hàng
                        </a>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <div class="empty-icon"><i class="bi bi-cart-x"></i></div>
                <h3>Giỏ hàng trống</h3>
                <p>Bạn chưa có sản phẩm nào trong giỏ hàng. Hãy khám phá các sản phẩm của chúng tôi!</p>
                <a href="danh-muc.php" class="btn btn-primary-custom"><i class="bi bi-bag me-2"></i>Mua sắm ngay</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
