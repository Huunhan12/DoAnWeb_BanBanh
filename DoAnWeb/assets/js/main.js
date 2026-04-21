/**
 * SWEET CAKE SHOP - Main JavaScript
 */

document.addEventListener('DOMContentLoaded', function() {
    // === THÊM VÀO GIỎ HÀNG (Không AJAX, dùng form submit) ===
    
    // Bộ điều khiển số lượng trên trang chi tiết sản phẩm
    const btnTang = document.getElementById('btn-tang');
    const btnGiam = document.getElementById('btn-giam');
    const inputSoLuong = document.getElementById('so-luong');
    
    if (btnTang && btnGiam && inputSoLuong) {
        btnTang.addEventListener('click', function() {
            let val = parseInt(inputSoLuong.value) || 1;
            inputSoLuong.value = val + 1;
        });
        
        btnGiam.addEventListener('click', function() {
            let val = parseInt(inputSoLuong.value) || 1;
            if (val > 1) {
                inputSoLuong.value = val - 1;
            }
        });
    }

    // === TRANG GIỎ HÀNG: Cập nhật số lượng ===
    const qtyInputs = document.querySelectorAll('.cart-qty-input');
    qtyInputs.forEach(function(input) {
        input.addEventListener('change', function() {
            const form = this.closest('form');
            if (form) {
                form.submit();
            }
        });
    });

    // === XEM TRƯỚC ẢNH KHI TẢI LÊN ===
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

    // === XÁC NHẬN XÓA ===
    const deleteButtons = document.querySelectorAll('.btn-delete-confirm');
    deleteButtons.forEach(function(btn) {
        btn.addEventListener('click', function(e) {
            if (!confirm('Bạn có chắc chắn muốn xóa?')) {
                e.preventDefault();
            }
        });
    });

    // === CUỘN TRANG MƯỢT MÀ (SMOOTH SCROLL) ===
    document.querySelectorAll('a[href^="#"]').forEach(function(anchor) {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    // === HIỆU ỨNG THANH MENU KHI CUỘN ===
    let lastScroll = 0;
    const navbar = document.querySelector('.navbar-main');
    
    if (navbar) {
        window.addEventListener('scroll', function() {
            const currentScroll = window.pageYOffset;
            if (currentScroll > 100) {
                navbar.style.boxShadow = '0 4px 20px rgba(0,0,0,0.15)';
            } else {
                navbar.style.boxShadow = '0 2px 8px rgba(0,0,0,0.08)';
            }
            lastScroll = currentScroll;
        });
    }

    // === HIỆU ỨNG XUẤT HIỆN KHI CUỘN (ANIMATION) ===
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.product-card, .category-card, .feature-box').forEach(function(el) {
        el.style.opacity = '0';
        observer.observe(el);
    });
});
