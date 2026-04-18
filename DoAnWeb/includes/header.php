<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

// Lấy danh mục cho menu
$sqlDanhMuc = "SELECT * FROM danh_muc ORDER BY ten_danh_muc";
$ketQuaDanhMuc = $conn->query($sqlDanhMuc);
$danhMucMenu = array();
if ($ketQuaDanhMuc) {
    while ($row = $ketQuaDanhMuc->fetch_assoc()) {
        $danhMucMenu[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Sweet Cake Shop - Tiệm bánh ngọt cao cấp, bánh kem sinh nhật, bánh Pháp, cupcake, cookie">
    <title><?php echo isset($tieuDeTrang) ? $tieuDeTrang . ' | Sweet Cake Shop' : 'Sweet Cake Shop - Tiệm Bánh Ngọt Cao Cấp'; ?></title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600;700&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link href="<?php echo layURLGoc(); ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <span><i class="bi bi-telephone-fill me-1"></i> 0901 234 567</span>
                    <span class="ms-3"><i class="bi bi-envelope-fill me-1"></i> contact@sweetcake.vn</span>
                </div>
                <div class="col-md-6 text-end">
                    <?php if (daHienDangNhap()): ?>
                        <span class="me-3"><i class="bi bi-person-fill me-1"></i> Xin chào, <?php echo $_SESSION['ho_ten']; ?></span>
                        <?php if (laAdmin()): ?>
                            <a href="<?php echo layURLGoc(); ?>/admin/index.php" class="badge bg-warning text-dark me-2" style="text-decoration:none; padding:5px 8px;"><i class="bi bi-shield-lock-fill me-1"></i>Quản Trị Hệ Thống</a>
                        <?php endif; ?>
                        <a href="<?php echo layURLGoc(); ?>/tai-khoan.php" class="text-white me-2">Tài khoản</a>
                        <a href="<?php echo layURLGoc(); ?>/dang-xuat.php" class="text-white">Đăng xuất</a>
                    <?php else: ?>
                        <a href="<?php echo layURLGoc(); ?>/dang-nhap.php" class="text-white me-2"><i class="bi bi-box-arrow-in-right me-1"></i>Đăng nhập</a>
                        <a href="<?php echo layURLGoc(); ?>/dang-ky.php" class="text-white"><i class="bi bi-person-plus-fill me-1"></i>Đăng ký</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-main sticky-top">
        <div class="container">
            <a class="navbar-brand" href="<?php echo layURLGoc(); ?>/index.php">
                <i class="bi bi-cake2-fill brand-icon"></i>
                <span>Sweet Cake</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo layURLGoc(); ?>/index.php"><i class="bi bi-house-fill me-1"></i>Trang chủ</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                            <i class="bi bi-grid-fill me-1"></i>Danh mục
                        </a>
                        <ul class="dropdown-menu">
                            <?php foreach ($danhMucMenu as $dm): ?>
                                <li><a class="dropdown-item" href="<?php echo layURLGoc(); ?>/danh-muc.php?id=<?php echo $dm['id']; ?>"><?php echo $dm['ten_danh_muc']; ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                </ul>
                <!-- Form tìm kiếm -->
                <form class="d-flex search-form me-3" action="<?php echo layURLGoc(); ?>/tim-kiem.php" method="GET">
                    <div class="input-group">
                        <input class="form-control" type="search" name="tu_khoa" placeholder="Tìm kiếm bánh..." aria-label="Tìm kiếm" id="search-input">
                        <button class="btn btn-search" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
                <!-- Giỏ hàng -->
                <a href="<?php echo layURLGoc(); ?>/gio-hang.php" class="btn btn-cart position-relative" id="btn-cart">
                    <i class="bi bi-cart3"></i> Giỏ hàng
                    <span class="badge bg-danger cart-badge" id="cart-count"><?php echo demGioHang(); ?></span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <div class="container mt-3">
            <?php hienThongBao(); ?>
        </div>
