<?php
/**
 * ADMIN - Đăng nhập
 */
require_once '../config/database.php';
require_once '../includes/functions.php';

// Nếu đã đăng nhập admin
if (daHienDangNhap() && laAdmin()) {
    chuyenHuong('index.php');
}

$loi = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = lamSach($_POST['email']);
    $matKhau = $_POST['mat_khau'];
    
    $sql = "SELECT * FROM nguoi_dung WHERE email = ? AND vai_tro = 'admin'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if ($user && password_verify($matKhau, $user['mat_khau'])) {
        $_SESSION['nguoi_dung_id'] = $user['id'];
        $_SESSION['ho_ten'] = $user['ho_ten'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['vai_tro'] = $user['vai_tro'];
        
        datThongBao('success', 'Đăng nhập quản trị thành công!');
        chuyenHuong('index.php');
    } else {
        $loi = 'Email hoặc mật khẩu không đúng, hoặc bạn không có quyền admin!';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập Quản trị | Sweet Cake</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background: linear-gradient(135deg, #1a1a2e, #16213e, #0f3460);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 48px 40px;
            width: 100%;
            max-width: 440px;
            box-shadow: 0 25px 60px rgba(0,0,0,0.3);
        }
        .login-card h2 { font-weight: 700; margin-bottom: 8px; }
        .login-card p { color: #888; }
        .form-control {
            border: 2px solid #eee;
            border-radius: 12px;
            padding: 12px 16px;
        }
        .form-control:focus {
            border-color: #6C3483;
            box-shadow: 0 0 0 3px rgba(108,52,131,0.1);
        }
        .btn-login {
            background: linear-gradient(135deg, #6C3483, #8E44AD);
            color: #fff;
            border: none;
            padding: 14px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(108,52,131,0.4);
            color: #fff;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="text-center mb-4">
            <div style="font-size:3.5rem;">🔒</div>
            <h2>Quản trị viên</h2>
            <p>Đăng nhập trang quản trị Sweet Cake</p>
        </div>

        <?php if ($loi): ?>
            <div class="alert alert-danger"><i class="bi bi-exclamation-circle me-1"></i> <?php echo $loi; ?></div>
        <?php endif; ?>
        <?php hienThongBao(); ?>

        <form method="POST">
            <div class="mb-3">
                <label class="form-label fw-semibold">Email</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                    <input type="email" name="email" class="form-control" placeholder="admin@sweetcake.vn" required>
                </div>
            </div>
            <div class="mb-4">
                <label class="form-label fw-semibold">Mật khẩu</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                    <input type="password" name="mat_khau" class="form-control" placeholder="Nhập mật khẩu" required>
                </div>
            </div>
            <button type="submit" class="btn btn-login w-100">
                <i class="bi bi-box-arrow-in-right me-2"></i>Đăng nhập
            </button>
        </form>

        <div class="text-center mt-4">
            <a href="<?php echo layURLGoc(); ?>/index.php" style="color:#6C3483;">← Về trang chủ</a>
        </div>
    </div>
</body>
</html>
