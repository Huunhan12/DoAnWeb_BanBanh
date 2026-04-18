<?php
/**
 * ADMIN - Thêm sản phẩm mới
 */
$tieuDeTrang = 'Thêm sản phẩm';
require_once 'includes/header.php';

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
    $hinhAnh = '';

    if (empty($tenSP)) $loi[] = 'Vui lòng nhập tên sản phẩm.';
    if ($danhMucId <= 0) $loi[] = 'Vui lòng chọn danh mục.';
    if ($gia <= 0) $loi[] = 'Giá phải lớn hơn 0.';

    // Upload ảnh
    if (!empty($_FILES['hinh_anh']['name'])) {
        $hinhAnh = uploadAnh($_FILES['hinh_anh']);
        if (!$hinhAnh) {
            $loi[] = 'Lỗi upload ảnh. Chỉ chấp nhận jpg, png, gif, webp (tối đa 5MB).';
        }
    }

    if (empty($loi)) {
        $sql = "INSERT INTO san_pham (danh_muc_id, ten_san_pham, mo_ta, gia, hinh_anh, noi_bat, moi, ton_kho) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("issdsiii", $danhMucId, $tenSP, $moTa, $gia, $hinhAnh, $noiBat, $moi, $tonKho);
        
        if ($stmt->execute()) {
            datThongBao('success', 'Thêm sản phẩm thành công!');
            chuyenHuong('san-pham.php');
        } else {
            $loi[] = 'Lỗi: ' . $conn->error;
        }
    }
}
?>

<div class="page-header">
    <h2><i class="bi bi-plus-circle me-2"></i>Thêm sản phẩm mới</h2>
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
                       value="<?php echo isset($_POST['ten_san_pham']) ? $_POST['ten_san_pham'] : ''; ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Danh mục <span class="text-danger">*</span></label>
                <select name="danh_muc_id" class="form-select" required>
                    <option value="">-- Chọn danh mục --</option>
                    <?php while ($dm = $dsDanhMuc->fetch_assoc()): ?>
                        <option value="<?php echo $dm['id']; ?>" <?php echo (isset($_POST['danh_muc_id']) && $_POST['danh_muc_id'] == $dm['id']) ? 'selected' : ''; ?>>
                            <?php echo $dm['ten_danh_muc']; ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Giá (VNĐ) <span class="text-danger">*</span></label>
                <input type="number" name="gia" class="form-control" required min="0"
                       value="<?php echo isset($_POST['gia']) ? $_POST['gia'] : ''; ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Tồn kho</label>
                <input type="number" name="ton_kho" class="form-control" min="0"
                       value="<?php echo isset($_POST['ton_kho']) ? $_POST['ton_kho'] : '0'; ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Hình ảnh</label>
                <input type="file" name="hinh_anh" id="hinh_anh" class="form-control" accept="image/*">
            </div>
            <div class="col-12">
                <img id="preview-img" src="" alt="" style="display:none; max-height:200px; border-radius:12px; margin-top:10px;">
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Mô tả</label>
                <textarea name="mo_ta" class="form-control" rows="4"><?php echo isset($_POST['mo_ta']) ? $_POST['mo_ta'] : ''; ?></textarea>
            </div>
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="noi_bat" id="noi_bat" <?php echo isset($_POST['noi_bat']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="noi_bat">Sản phẩm nổi bật ⭐</label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="moi" id="moi" <?php echo isset($_POST['moi']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="moi">Sản phẩm mới 🆕</label>
                </div>
            </div>
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-admin-primary"><i class="bi bi-check-circle me-1"></i> Thêm sản phẩm</button>
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
            let preview = document.getElementById('preview-img');
            if(!preview) {
                preview = document.createElement('img');
                preview.id = 'preview-img';
                preview.style.maxHeight = '150px';
                preview.style.borderRadius = '12px';
                preview.style.marginTop = '10px';
                e.target.parentNode.appendChild(preview);
            }
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(e.target.files[0]);
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
