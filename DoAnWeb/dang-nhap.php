<?php
/**
 * TRANG ĐĂNG NHẬP
 */
require_once 'config/database.php';
require_once 'includes/functions.php';

// Nếu đã đăng nhập thì chuyển về trang chủ
if (daHienDangNhap()) {
    chuyenHuong('index.php');
}

$loi = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = lamSach($_POST['email']);
    $matKhau = $_POST['mat_khau'];
    
    if (empty($email) || empty($matKhau)) {
        $loi = 'Vui lòng nhập đầy đủ email và mật khẩu.';
    } else {
        $sql = "SELECT * FROM nguoi_dung WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        if ($user && password_verify($matKhau, $user['mat_khau'])) {
            // Đăng nhập thành công
            $_SESSION['nguoi_dung_id'] = $user['id'];
            $_SESSION['ho_ten'] = $user['ho_ten'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['vai_tro'] = $user['vai_tro'];
            
            datThongBao('success', '<i class="bi bi-check-circle me-1"></i> Đăng nhập thành công! Chào mừng ' . $user['ho_ten']);
            
            // Nếu là admin, chuyển đến trang admin
            if ($user['vai_tro'] === 'admin') {
                chuyenHuong('admin/index.php');
            }
            chuyenHuong('index.php');
        } else {
            $loi = 'Email hoặc mật khẩu không chính xác!';
        }
    }
}

$tieuDeTrang = 'Đăng nhập';
require_once 'includes/header.php';
?>

<section class="auth-section">
    <div class="container">
        <div class="auth-card">
            <div class="auth-title">
                <div style="font-size:3rem; margin-bottom:10px;">🔐</div>
                <h2>Đăng nhập</h2>
                <p>Chào mừng trở lại! Vui lòng đăng nhập.</p>
            </div>

            <?php if ($loi): ?>
                <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-1"></i> <?php echo $loi; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" name="email" class="form-control" placeholder="Nhập email" required
                               value="<?php echo isset($_POST['email']) ? lamSach($_POST['email']) : ''; ?>">
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" name="mat_khau" class="form-control" placeholder="Nhập mật khẩu" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary-custom w-100 mb-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập
                </button>
            </form>

            <div class="text-center mt-3">
                <p class="text-muted">Chưa có tài khoản? <a href="dang-ky.php" style="color:var(--primary); font-weight:600;">Đăng ký ngay</a></p>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
