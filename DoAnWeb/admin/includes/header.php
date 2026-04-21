<?php
/**
 * ADMIN - Header & Sidebar
 */
require_once '../config/database.php';
require_once '../includes/functions.php';

// Kiểm tra quyền admin
if (!daHienDangNhap() || !laAdmin()) {
    datThongBao('danger', 'Bạn không có quyền truy cập trang quản trị!');
    chuyenHuong(layURLGoc() . '/dang-nhap.php');
}

// Đếm các thống kê
$tongSP = $conn->query("SELECT COUNT(*) as tong FROM san_pham")->fetch_assoc()['tong'];
$tongDH = $conn->query("SELECT COUNT(*) as tong FROM don_hang")->fetch_assoc()['tong'];
$tongND = $conn->query("SELECT COUNT(*) as tong FROM nguoi_dung")->fetch_assoc()['tong'];
$dhMoi = $conn->query("SELECT COUNT(*) as tong FROM don_hang WHERE trang_thai = 'cho_xac_nhan'")->fetch_assoc()['tong'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($tieuDeTrang) ? $tieuDeTrang . ' | Admin' : 'Quản trị - Sweet Cake'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --admin-primary: #6C3483;
            --admin-primary-light: #8E44AD;
            --admin-dark: #1a1a2e;
            --admin-sidebar: #16213e;
            --admin-card: #ffffff;
            --admin-bg: #f0f2f5;
        }
        * { box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--admin-bg);
            margin: 0;
        }
        /* Sidebar */
        .admin-sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 260px;
            height: 100vh;
            background: linear-gradient(180deg, var(--admin-dark) 0%, var(--admin-sidebar) 100%);
            color: #fff;
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        .sidebar-brand {
            padding: 24px 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-brand h4 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #f093fb, #f5576c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .sidebar-brand small {
            color: rgba(255,255,255,0.5);
            font-size: 0.75rem;
        }
        .sidebar-menu {
            list-style: none;
            padding: 16px 12px;
            margin: 0;
        }
        .sidebar-menu .menu-label {
            color: rgba(255,255,255,0.35);
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            padding: 16px 16px 8px;
            font-weight: 600;
        }
        .sidebar-menu li a {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 16px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            border-radius: 10px;
            transition: all 0.3s ease;
            margin-bottom: 4px;
            font-size: 0.9rem;
        }
        .sidebar-menu li a:hover,
        .sidebar-menu li a.active {
            background: linear-gradient(135deg, rgba(108,52,131,0.5), rgba(142,68,173,0.3));
            color: #fff;
        }
        .sidebar-menu li a .badge {
            margin-left: auto;
        }
        .sidebar-menu li a i {
            font-size: 1.1rem;
            width: 20px;
            text-align: center;
        }
        /* Main Content */
        .admin-main {
            margin-left: 260px;
            min-height: 100vh;
        }
        .admin-topbar {
            background: #fff;
            padding: 16px 30px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0,0,0,0.06);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .admin-topbar .admin-user {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .admin-topbar .admin-user .avatar {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-primary-light));
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
        }
        .admin-content {
            padding: 30px;
        }
        /* Cards */
        .stat-card {
            background: #fff;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: all 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .stat-card .stat-icon {
            width: 56px;
            height: 56px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #fff;
        }
        .stat-card .stat-info h3 {
            font-size: 1.6rem;
            font-weight: 700;
            margin: 0;
            color: #333;
        }
        .stat-card .stat-info p {
            margin: 0;
            color: #888;
            font-size: 0.85rem;
        }
        /* Admin Table */
        .admin-table {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
            overflow: hidden;
        }
        .admin-table .table {
            margin: 0;
        }
        .admin-table .table th {
            background: linear-gradient(135deg, var(--admin-dark), var(--admin-sidebar));
            color: #fff;
            font-weight: 500;
            padding: 14px 16px;
            border: none;
            font-size: 0.85rem;
        }
        .admin-table .table td {
            padding: 14px 16px;
            vertical-align: middle;
            border-bottom: 1px solid #f0f0f0;
            font-size: 0.9rem;
        }
        .admin-table .table tbody tr:hover {
            background: rgba(108,52,131,0.03);
        }
        /* Admin buttons */
        .btn-admin-primary {
            background: linear-gradient(135deg, var(--admin-primary), var(--admin-primary-light));
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 10px;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        .btn-admin-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(108,52,131,0.3);
            color: #fff;
        }
        /* Admin form */
        .admin-form {
            background: #fff;
            border-radius: 16px;
            padding: 30px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        }
        .admin-form .form-control,
        .admin-form .form-select {
            border: 2px solid #eee;
            border-radius: 10px;
            padding: 10px 14px;
            transition: all 0.3s ease;
        }
        .admin-form .form-control:focus,
        .admin-form .form-select:focus {
            border-color: var(--admin-primary-light);
            box-shadow: 0 0 0 3px rgba(108,52,131,0.1);
        }
        .product-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
        }
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 24px;
        }
        .page-header h2 {
            margin: 0;
            font-size: 1.5rem;
            font-weight: 700;
            color: #333;
        }
        @media (max-width: 991px) {
            .admin-sidebar { width: 0; overflow: hidden; }
            .admin-main { margin-left: 0; }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <aside class="admin-sidebar">
        <div class="sidebar-brand">
            <h4>🎂 Lu Cake</h4>
            <small>Trang Quản Trị</small>
        </div>
        <ul class="sidebar-menu">
            <li class="menu-label">Tổng quan</li>
            <li><a href="<?php echo layURLGoc(); ?>/admin/index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
            
            <li class="menu-label">Quản lý</li>
            <li><a href="<?php echo layURLGoc(); ?>/admin/san-pham.php" class="<?php echo strpos(basename($_SERVER['PHP_SELF']), 'san-pham') !== false ? 'active' : ''; ?>"><i class="bi bi-box-seam"></i> Sản phẩm <span class="badge bg-primary rounded-pill"><?php echo $tongSP; ?></span></a></li>
            <li><a href="<?php echo layURLGoc(); ?>/admin/danh-muc.php" class="<?php echo strpos(basename($_SERVER['PHP_SELF']), 'danh-muc') !== false ? 'active' : ''; ?>"><i class="bi bi-grid"></i> Danh mục</a></li>
            <li><a href="<?php echo layURLGoc(); ?>/admin/don-hang.php" class="<?php echo strpos(basename($_SERVER['PHP_SELF']), 'don-hang') !== false ? 'active' : ''; ?>"><i class="bi bi-receipt"></i> Đơn hàng <?php if($dhMoi > 0): ?><span class="badge bg-danger rounded-pill"><?php echo $dhMoi; ?></span><?php endif; ?></a></li>
            <li><a href="<?php echo layURLGoc(); ?>/admin/tai-khoan.php" class="<?php echo strpos(basename($_SERVER['PHP_SELF']), 'tai-khoan') !== false ? 'active' : ''; ?>"><i class="bi bi-people"></i> Tài khoản <span class="badge bg-info rounded-pill"><?php echo $tongND; ?></span></a></li>
            
            <li class="menu-label">Hệ thống</li>
            <li><a href="<?php echo layURLGoc(); ?>/index.php" target="_blank"><i class="bi bi-globe"></i> Xem Website</a></li>
            <li><a href="<?php echo layURLGoc(); ?>/admin/dang-xuat.php"><i class="bi bi-box-arrow-left"></i> Đăng xuất</a></li>
        </ul>
    </aside>

    <!-- Main -->
    <div class="admin-main">
        <!-- Topbar -->
        <div class="admin-topbar">
            <div>
                <h5 class="mb-0"><?php echo isset($tieuDeTrang) ? $tieuDeTrang : 'Dashboard'; ?></h5>
            </div>
            <div class="admin-user">
                <span class="text-muted"><?php echo $_SESSION['ho_ten']; ?></span>
                <div class="avatar"><?php echo mb_substr($_SESSION['ho_ten'], 0, 1); ?></div>
            </div>
        </div>

        <!-- Content -->
        <div class="admin-content">
            <?php hienThongBao(); ?>
