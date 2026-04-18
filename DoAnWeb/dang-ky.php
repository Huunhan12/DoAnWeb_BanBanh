<?php
/**
 * TRANG ĐĂNG KÝ TÀI KHOẢN
 */
require_once 'config/database.php';
require_once 'includes/functions.php';

if (daHienDangNhap()) {
    chuyenHuong('index.php');
}

$loi = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $hoTen = lamSach($_POST['ho_ten']);
    $email = lamSach($_POST['email']);
    $dienThoai = lamSach($_POST['dien_thoai']);
    $matKhau = $_POST['mat_khau'];
    $xacNhanMK = $_POST['xac_nhan_mat_khau'];
    $diaChi = lamSach(isset($_POST['dia_chi']) ? $_POST['dia_chi'] : '');

    // Validate
    if (empty($hoTen)) $loi[] = 'Vui lòng nhập họ tên.';
    if (empty($email)) $loi[] = 'Vui lòng nhập email.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $loi[] = 'Email không hợp lệ.';
    if (strlen($matKhau) < 6) $loi[] = 'Mật khẩu phải có ít nhất 6 ký tự.';
    if ($matKhau !== $xacNhanMK) $loi[] = 'Xác nhận mật khẩu không khớp.';

    // Kiểm tra email trùng
    if (empty($loi)) {
        $sql = "SELECT id FROM nguoi_dung WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $loi[] = 'Email đã được sử dụng. Vui lòng dùng email khác.';
        }
    }

    if (empty($loi)) {
        $matKhauHash = password_hash($matKhau, PASSWORD_DEFAULT);
        $sql = "INSERT INTO nguoi_dung (ho_ten, email, dien_thoai, mat_khau, dia_chi, vai_tro) VALUES (?, ?, ?, ?, ?, 'user')";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $hoTen, $email, $dienThoai, $matKhauHash, $diaChi);
        
        if ($stmt->execute()) {
            datThongBao('success', '<i class="bi bi-check-circle me-1"></i> Đăng ký thành công! Vui lòng đăng nhập.');
            chuyenHuong('dang-nhap.php');
        } else {
            $loi[] = 'Có lỗi xảy ra. Vui lòng thử lại!';
        }
    }
}

$tieuDeTrang = 'Đăng ký';
require_once 'includes/header.php';
?>

<section class="auth-section">
    <div class="container">
        <div class="auth-card" style="max-width:520px;">
            <div class="auth-title">
                <div style="font-size:3rem; margin-bottom:10px;">📝</div>
                <h2>Đăng ký tài khoản</h2>
                <p>Tạo tài khoản để đặt hàng nhanh hơn</p>
            </div>

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
                <div class="mb-3">
                    <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" name="ho_ten" class="form-control" placeholder="Nhập họ và tên" required
                           value="<?php echo isset($_POST['ho_ten']) ? $_POST['ho_ten'] : ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="Nhập email" required
                           value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="tel" name="dien_thoai" class="form-control" placeholder="Nhập số điện thoại"
                           value="<?php echo isset($_POST['dien_thoai']) ? $_POST['dien_thoai'] : ''; ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Địa chỉ</label>
                    <textarea name="dia_chi" class="form-control" rows="2" placeholder="Nhập địa chỉ"><?php echo isset($_POST['dia_chi']) ? $_POST['dia_chi'] : ''; ?></textarea>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="mat_khau" class="form-control" placeholder="Ít nhất 6 ký tự" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Xác nhận mật khẩu <span class="text-danger">*</span></label>
                        <input type="password" name="xac_nhan_mat_khau" class="form-control" placeholder="Nhập lại mật khẩu" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary-custom w-100 mb-3">
                    <i class="bi bi-person-plus me-2"></i>Đăng ký
                </button>
            </form>

            <div class="text-center mt-3">
                <p class="text-muted">Đã có tài khoản? <a href="dang-nhap.php" style="color:var(--primary); font-weight:600;">Đăng nhập</a></p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
