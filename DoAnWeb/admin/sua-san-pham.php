<?php
/**
 * ADMIN - Sửa sản phẩm
 */
$tieuDeTrang = 'Sửa sản phẩm';
require_once 'includes/header.php';

$spId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Lấy thông tin sản phẩm
$sql = "SELECT * FROM san_pham WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $spId);
$stmt->execute();
$sanPham = $stmt->get_result()->fetch_assoc();

if (!$sanPham) {
    datThongBao('danger', 'Không tìm thấy sản phẩm!');
    chuyenHuong('san-pham.php');
}

// Lấy danh mục
$dsDanhMuc = $conn->query("SELECT * FROM danh_muc ORDER BY ten_danh_muc");

$loi = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tenSP = lamSach($_POST['ten_san_pham']);
    $danhMucId = (int)$_POST['danh_muc_id'];
    $gia = (float)$_POST['gia'];
    $moTa = lamSach($_POST['mo_ta']);
    $tonKho = (int)$_POST['ton_kho'];
    $noiBat = isset($_POST['noi_bat']) ? 1 : 0;
    $moi = isset($_POST['moi']) ? 1 : 0;
    $hinhAnh = $sanPham['hinh_anh'];

    if (empty($tenSP)) $loi[] = 'Vui lòng nhập tên sản phẩm.';
    if ($gia <= 0) $loi[] = 'Giá phải lớn hơn 0.';

    // Upload ảnh mới nếu có
    if (!empty($_FILES['hinh_anh']['name'])) {
        $anhMoi = uploadAnh($_FILES['hinh_anh']);
        if ($anhMoi) {
            // Xóa ảnh cũ
            if ($hinhAnh && file_exists(dirname(__DIR__) . '/assets/images/products/' . $hinhAnh)) {
                unlink(dirname(__DIR__) . '/assets/images/products/' . $hinhAnh);
            }
            $hinhAnh = $anhMoi;
        } else {
            $loi[] = 'Lỗi upload ảnh.';
        }
    }

    if (empty($loi)) {
        $sql = "UPDATE san_pham SET danh_muc_id=?, ten_san_pham=?, mo_ta=?, gia=?, hinh_anh=?, noi_bat=?, moi=?, ton_kho=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issdsiiii", $danhMucId, $tenSP, $moTa, $gia, $hinhAnh, $noiBat, $moi, $tonKho, $spId);
        
        if ($stmt->execute()) {
            datThongBao('success', 'Cập nhật sản phẩm thành công!');
            chuyenHuong('san-pham.php');
        } else {
            $loi[] = 'Lỗi: ' . $conn->error;
        }
    }
}
?>

<div class="page-header">
    <h2><i class="bi bi-pencil me-2"></i>Sửa sản phẩm: <?php echo $sanPham['ten_san_pham']; ?></h2>
    <a href="san-pham.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Quay lại</a>
</div>

<?php if (!empty($loi)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0"><?php foreach ($loi as $l) echo "<li>$l</li>"; ?></ul>
    </div>
<?php endif; ?>

<div class="admin-form">
    <form method="POST" enctype="multipart/form-data">
        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Tên sản phẩm <span class="text-danger">*</span></label>
                <input type="text" name="ten_san_pham" class="form-control" required
                       value="<?php echo $sanPham['ten_san_pham']; ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Danh mục <span class="text-danger">*</span></label>
                <select name="danh_muc_id" class="form-select" required>
                    <?php while ($dm = $dsDanhMuc->fetch_assoc()): ?>
                        <option value="<?php echo $dm['id']; ?>" <?php echo $sanPham['danh_muc_id'] == $dm['id'] ? 'selected' : ''; ?>>
                            <?php echo $dm['ten_danh_muc']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Giá (VNĐ) <span class="text-danger">*</span></label>
                <input type="number" name="gia" class="form-control" required min="0"
                       value="<?php echo $sanPham['gia']; ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Tồn kho</label>
                <input type="number" name="ton_kho" class="form-control" min="0"
                       value="<?php echo $sanPham['ton_kho']; ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Hình ảnh mới</label>
                <input type="file" name="hinh_anh" id="hinh_anh" class="form-control" accept="image/*">
            </div>
            <div class="col-12">
                <?php if ($sanPham['hinh_anh'] && file_exists(dirname(__DIR__) . '/assets/images/products/' . $sanPham['hinh_anh'])): ?>
                    <p class="text-muted mb-1">Ảnh hiện tại:</p>
                    <img src="<?php echo layURLGoc(); ?>/assets/images/products/<?php echo $sanPham['hinh_anh']; ?>" alt="" style="max-height:150px; border-radius:12px;">
                <?php endif; ?>
                <img id="preview-img" src="" alt="" style="display:none; max-height:150px; border-radius:12px; margin-top:10px;">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Mô tả</label>
                <textarea name="mo_ta" class="form-control" rows="4"><?php echo $sanPham['mo_ta']; ?></textarea>
            </div>
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="noi_bat" id="noi_bat" <?php echo $sanPham['noi_bat'] ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="noi_bat">Sản phẩm nổi bật ⭐</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="moi" id="moi" <?php echo $sanPham['moi'] ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="moi">Sản phẩm mới 🆕</label>
                </div>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-admin-primary"><i class="bi bi-check-circle me-1"></i> Cập nhật</button>
                <a href="san-pham.php" class="btn btn-outline-secondary ms-2">Hủy</a>
            </div>
        </div>
    </form>
</div>
<script>
document.getElementById('hinh_anh').addEventListener('change', function(e) {
    if (e.target.files && e.target.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview-img').src = e.target.result;
            document.getElementById('preview-img').style.display = 'block';
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
