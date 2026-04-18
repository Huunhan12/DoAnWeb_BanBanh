<?php
/**
 * TRANG ĐẶT HÀNG (CHECKOUT)
 */
require_once 'config/database.php';
require_once 'includes/functions.php';

// Kiểm tra giỏ hàng
if (empty($_SESSION['gio_hang'])) {
    datThongBao('warning', 'Giỏ hàng trống! Vui lòng thêm sản phẩm trước khi đặt hàng.');
    chuyenHuong('gio-hang.php');
}

// Xử lý đặt hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hoTen = lamSach($_POST['ho_ten']);
    $email = lamSach($_POST['email']);
    $dienThoai = lamSach($_POST['dien_thoai']);
    $diaChi = lamSach($_POST['dia_chi']);
    $ghiChu = lamSach(isset($_POST['ghi_chu']) ? $_POST['ghi_chu'] : '');
    $tongTien = tongTienGioHang();
    $nguoiDungId = daHienDangNhap() ? $_SESSION['nguoi_dung_id'] : null;

    // Validate
    $loi = array();
    if (empty($hoTen)) $loi[] = 'Vui lòng nhập họ tên.';
    if (empty($dienThoai)) $loi[] = 'Vui lòng nhập số điện thoại.';
    if (empty($diaChi)) $loi[] = 'Vui lòng nhập địa chỉ giao hàng.';

    if (empty($loi)) {
        // Tạo đơn hàng
        $sql = "INSERT INTO don_hang (nguoi_dung_id, ho_ten, email, dien_thoai, dia_chi, tong_tien, ghi_chu) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issssds", $nguoiDungId, $hoTen, $email, $dienThoai, $diaChi, $tongTien, $ghiChu);
        
        if ($stmt->execute()) {
            $donHangId = $conn->insert_id;
            
            // Thêm chi tiết đơn hàng
            $sqlCT = "INSERT INTO chi_tiet_don_hang (don_hang_id, san_pham_id, so_luong, gia) VALUES (?, ?, ?, ?)";
            $stmtCT = $conn->prepare($sqlCT);
            
            foreach ($_SESSION['gio_hang'] as $spId => $sp) {
                $stmtCT->bind_param("iiid", $donHangId, $spId, $sp['so_luong'], $sp['gia']);
                $stmtCT->execute();
                
                // Cập nhật tồn kho
                $sqlTK = "UPDATE san_pham SET ton_kho = ton_kho - ? WHERE id = ?";
                $stmtTK = $conn->prepare($sqlTK);
                $stmtTK->bind_param("ii", $sp['so_luong'], $spId);
                $stmtTK->execute();
            }
            
            // Xóa giỏ hàng
            $_SESSION['gio_hang'] = array();
            
            datThongBao('success', '<i class="bi bi-check-circle me-1"></i> Đặt hàng thành công! Mã đơn hàng: <strong>#' . $donHangId . '</strong>. Chúng tôi sẽ liên hệ bạn sớm nhất.');
            chuyenHuong('index.php');
        } else {
            $loi[] = 'Có lỗi xảy ra. Vui lòng thử lại!';
        }
    }
}

// Lấy thông tin user nếu đã đăng nhập
$thongTinUser = array();
if (daHienDangNhap()) {
    $sql = "SELECT * FROM nguoi_dung WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $_SESSION['nguoi_dung_id']);
    $stmt->execute();
    $thongTinUser = $stmt->get_result()->fetch_assoc();
}

$tieuDeTrang = 'Đặt hàng';
require_once 'includes/header.php';
?>

<!-- Breadcrumb -->
<section class="breadcrumb-section">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="index.php">Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="gio-hang.php">Giỏ hàng</a></li>
                <li class="breadcrumb-item active">Đặt hàng</li>
            </ol>
        </nav>
    </div>
</section>

<section class="page-section">
    <div class="container">
        <h1 class="page-title"><i class="bi bi-credit-card me-2"></i>Đặt hàng</h1>

        <?php if (!empty($loi)): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($loi as $l): ?>
                        <li><?php echo $l; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="row g-4">
                <!-- Form thông tin -->
                <div class="col-lg-8">
                    <div class="cart-summary" style="position:static;">
                        <h4 class="mb-4"><i class="bi bi-person-fill me-2"></i>Thông tin giao hàng</h4>
                        
                        <?php if (!daHienDangNhap()): ?>
                            <div class="alert alert-info">
                                <i class="bi bi-info-circle me-1"></i> 
                                <a href="dang-nhap.php">Đăng nhập</a> để tự động điền thông tin.
                            </div>
                        <?php endif; ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="ho_ten" class="form-control" required
                                       value="<?php echo isset($thongTinUser['ho_ten']) ? $thongTinUser['ho_ten'] : ''; ?>"
                                       placeholder="Nhập họ và tên">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" name="dien_thoai" class="form-control" required
                                       value="<?php echo isset($thongTinUser['dien_thoai']) ? $thongTinUser['dien_thoai'] : ''; ?>"
                                       placeholder="Nhập số điện thoại">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Email</label>
                                <input type="email" name="email" class="form-control"
                                       value="<?php echo isset($thongTinUser['email']) ? $thongTinUser['email'] : ''; ?>"
                                       placeholder="Nhập email (tùy chọn)">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Địa chỉ giao hàng <span class="text-danger">*</span></label>
                                <textarea name="dia_chi" class="form-control" rows="3" required
                                          placeholder="Nhập địa chỉ giao hàng chi tiết"><?php echo isset($thongTinUser['dia_chi']) ? $thongTinUser['dia_chi'] : ''; ?></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Ghi chú</label>
                                <textarea name="ghi_chu" class="form-control" rows="2" 
                                          placeholder="Ghi chú thêm (ví dụ: giao hàng giờ hành chính)"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tóm tắt đơn hàng -->
                <div class="col-lg-4">
                    <div class="checkout-summary">
                        <h4><i class="bi bi-bag-check me-2"></i>Đơn hàng của bạn</h4>
                        
                        <?php foreach ($_SESSION['gio_hang'] as $sp): ?>
                            <div class="checkout-item">
                                <span><?php echo catNganChuoi($sp['ten'], 22); ?> <strong>x<?php echo $sp['so_luong']; ?></strong></span>
                                <span class="fw-semibold"><?php echo formatGia($sp['gia'] * $sp['so_luong']); ?></span>
                            </div>
                        <?php endforeach; ?>
                        
                        <div class="checkout-item" style="border:none;">
                            <span>Phí vận chuyển</span>
                            <span class="text-success fw-semibold">Miễn phí</span>
                        </div>

                        <hr>
                        <div class="d-flex justify-content-between">
                            <strong>Tổng cộng:</strong>
                            <span class="checkout-total"><?php echo formatGia(tongTienGioHang()); ?></span>
                        </div>

                        <button type="submit" class="btn btn-primary-custom w-100 mt-4">
                            <i class="bi bi-check-circle me-2"></i>Xác nhận đặt hàng
                        </button>
                        
                        <a href="gio-hang.php" class="btn btn-outline-custom w-100 mt-2">
                            <i class="bi bi-arrow-left me-1"></i>Quay lại giỏ hàng
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
