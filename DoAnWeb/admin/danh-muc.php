<?php
/**
 * ADMIN - Quản lý danh mục
 */
$tieuDeTrang = 'Quản lý danh mục';
require_once 'includes/header.php';

// Xử lý thêm danh mục
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['them_danh_muc'])) {
    $tenDM = lamSach($_POST['ten_danh_muc']);
    $moTa = lamSach(isset($_POST['mo_ta']) ? $_POST['mo_ta'] : '');
    $hinhAnh = '';

    if (!empty($_FILES['hinh_anh']['name'])) {
        $hinhAnh = uploadAnh($_FILES['hinh_anh']);
    }

    if (!empty($tenDM)) {
        $sql = "INSERT INTO danh_muc (ten_danh_muc, mo_ta, hinh_anh) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $tenDM, $moTa, $hinhAnh);
        if ($stmt->execute()) {
            datThongBao('success', 'Thêm danh mục thành công!');
        } else {
            datThongBao('danger', 'Lỗi: ' . $conn->error);
        }
        chuyenHuong('danh-muc.php');
    }
}

// Xử lý sửa danh mục
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sua_danh_muc'])) {
    $dmId = (int)$_POST['id'];
    $tenDM = lamSach($_POST['ten_danh_muc']);
    $moTa = lamSach(isset($_POST['mo_ta']) ? $_POST['mo_ta'] : '');

    if (!empty($tenDM)) {
        $sql = "UPDATE danh_muc SET ten_danh_muc=?, mo_ta=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $tenDM, $moTa, $dmId);
        if ($stmt->execute()) {
            datThongBao('success', 'Cập nhật danh mục thành công!');
        }
        chuyenHuong('danh-muc.php');
    }
}

// Xử lý xóa danh mục
if (isset($_GET['xoa'])) {
    $dmId = (int)$_GET['xoa'];
    // Kiểm tra có sản phẩm không
    $stmt = $conn->prepare("SELECT COUNT(*) as dem FROM san_pham WHERE danh_muc_id = ?");
    $stmt->bind_param("i", $dmId);
    $stmt->execute();
    $dem = $stmt->get_result()->fetch_assoc()['dem'];
    
    if ($dem > 0) {
        datThongBao('danger', 'Không thể xóa! Danh mục còn ' . $dem . ' sản phẩm.');
    } else {
        $stmt = $conn->prepare("DELETE FROM danh_muc WHERE id = ?");
        $stmt->bind_param("i", $dmId);
        if ($stmt->execute()) {
            datThongBao('success', 'Đã xóa danh mục!');
        }
    }
    chuyenHuong('danh-muc.php');
}

// Lấy danh sách danh mục
$sql = "SELECT dm.*, COUNT(sp.id) as so_sp FROM danh_muc dm 
        LEFT JOIN san_pham sp ON dm.id = sp.danh_muc_id 
        GROUP BY dm.id ORDER BY dm.ten_danh_muc";
$dsDanhMuc = $conn->query($sql);
?>

<div class="page-header">
    <h2><i class="bi bi-grid me-2"></i>Quản lý danh mục</h2>
    <button class="btn btn-admin-primary" data-bs-toggle="modal" data-bs-target="#modalThemDM">
        <i class="bi bi-plus-circle me-1"></i> Thêm danh mục
    </button>
</div>

<div class="admin-table">
    <table class="table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Tên danh mục</th>
                <th>Mô tả</th>
                <th>Số sản phẩm</th>
                <th>Ngày tạo</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($dm = $dsDanhMuc->fetch_assoc()): ?>
                <tr>
                    <td><strong>#<?php echo $dm['id']; ?></strong></td>
                    <td><strong><?php echo $dm['ten_danh_muc']; ?></strong></td>
                    <td><?php echo catNganChuoi(isset($dm['mo_ta']) ? $dm['mo_ta'] : '', 60); ?></td>
                    <td><span class="badge bg-primary"><?php echo $dm['so_sp']; ?></span></td>
                    <td><?php echo date('d/m/Y', strtotime($dm['ngay_tao'])); ?></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#modalSuaDM<?php echo $dm['id']; ?>"><i class="bi bi-pencil"></i></button>
                        <a href="danh-muc.php?xoa=<?php echo $dm['id']; ?>" class="btn btn-sm btn-outline-danger btn-delete-confirm"><i class="bi bi-trash"></i></a>
                    </td>
                </tr>

                <!-- Modal Sửa -->
                <div class="modal fade" id="modalSuaDM<?php echo $dm['id']; ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content" style="border-radius:16px;">
                            <div class="modal-header"><h5 class="modal-title">Sửa danh mục</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                            <form method="POST">
                                <div class="modal-body">
                                    <input type="hidden" name="id" value="<?php echo $dm['id']; ?>">
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Tên danh mục</label>
                                        <input type="text" name="ten_danh_muc" class="form-control" value="<?php echo $dm['ten_danh_muc']; ?>" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">Mô tả</label>
                                        <textarea name="mo_ta" class="form-control" rows="3"><?php echo $dm['mo_ta']; ?></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                                    <button type="submit" name="sua_danh_muc" class="btn btn-admin-primary">Cập nhật</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal Thêm Danh Mục -->
<div class="modal fade" id="modalThemDM" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:16px;">
            <div class="modal-header"><h5 class="modal-title">Thêm danh mục mới</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" enctype="multipart/form-data">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="ten_danh_muc" class="form-control" required placeholder="Nhập tên danh mục">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả</label>
                        <textarea name="mo_ta" class="form-control" rows="3" placeholder="Mô tả danh mục"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Hình ảnh</label>
                        <input type="file" name="hinh_anh" class="form-control" accept="image/*">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" name="them_danh_muc" class="btn btn-admin-primary">Thêm</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
