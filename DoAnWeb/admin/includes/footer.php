        </div><!-- /admin-content -->
    </div><!-- /admin-main -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Confirm delete
        document.querySelectorAll('.btn-delete-confirm').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                if (!confirm('Bạn có chắc chắn muốn xóa?')) {
                    e.preventDefault();
                }
            });
        });
        // Image preview
        const inputHinhAnh = document.getElementById('hinh_anh');
        const previewImg = document.getElementById('preview-img');
        if (inputHinhAnh && previewImg) {
            inputHinhAnh.addEventListener('change', function(e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        previewImg.src = e.target.result;
                        previewImg.style.display = 'block';
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    </script>
</body>
</html>
