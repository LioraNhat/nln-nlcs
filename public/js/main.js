// Chờ cho toàn bộ nội dung trang tải xong
document.addEventListener("DOMContentLoaded", function() {
    
    // ===============================================
    // KHAI BÁO CÁC BIẾN TOÀN CỤC
    // ===============================================
    const API_PROVINCE = "https://esgoo.net/api-tinhthanh/";
    // Các biến DOM Modal
    const modalOverlay = document.querySelector('#address-modal-overlay');
    const modalForm = document.querySelector('#form-address-modal');
    const modalTitle = document.querySelector('#modal-title');
    const modalSubmitBtn = document.querySelector('#modal-submit-button');
    const modalAddressIdInput = document.querySelector('#modal-address-id');
    const modalMethodInput = document.querySelector('#modal-method-field');

    // 1. XỬ LÝ SLIDER (Giữ nguyên)
    const slider = document.querySelector('.hero-slider');
    if (slider) {
        const slides = slider.querySelectorAll('.slider-slide');
        let currentSlide = 0;
        function showNextSlide() {
            if (slides.length > 0) {
                slides[currentSlide].classList.remove('active');
                currentSlide = (currentSlide + 1) % slides.length;
                slides[currentSlide].classList.add('active');
            }
        }
        setInterval(showNextSlide, 3000);
    }

    // 2. XỬ LÝ NÚT MENU 3 GẠCH (Giữ nguyên)
    const menuToggle = document.querySelector('.menu-toggle');
    const verticalNav = document.querySelector('.vertical-nav'); 
    
    // Kiểm tra xem tìm thấy phần tử không
    if (!menuToggle) console.error("Lỗi: Không tìm thấy class .menu-toggle");
    if (!verticalNav) console.error("Lỗi: Không tìm thấy class .vertical-nav");

    if (menuToggle && verticalNav) {
        menuToggle.addEventListener('click', function(e) {
            e.stopPropagation(); // Ngăn chặn sự kiện nổi bọt
            verticalNav.classList.toggle('open');
            console.log("Đã click menu! Class list hiện tại:", verticalNav.classList);
        });

        // Thêm tính năng: Click ra ngoài thì đóng menu
        document.addEventListener('click', function(e) {
            if (!verticalNav.contains(e.target) && !menuToggle.contains(e.target)) {
                verticalNav.classList.remove('open');
            }
        });
    }

    // 2. XỬ LÝ HEADER SCROLL (Đã tối ưu)
    let lastScrollTop = 0;
    const mainHeader = document.querySelector('.main-header');
    const subNav = document.querySelector('.sub-nav'); 
    const topBar = document.querySelector('.top-bar-shipping');

    if (mainHeader) { 
        // Cập nhật vị trí top cho menu dọc khi header thay đổi
        function updateMenuPosition() {
            const headerHeight = mainHeader.offsetHeight + (topBar ? topBar.offsetHeight : 0);
            // Nếu header đang sticky top=0 thì chỉ cần lấy height header
            const currentHeaderHeight = mainHeader.getBoundingClientRect().height;
            
            // Nếu bạn muốn menu nằm ngay dưới header
            if (verticalNav) verticalNav.style.top = currentHeaderHeight + 'px';
        }

        // Gọi lần đầu
        updateMenuPosition();
        window.addEventListener('resize', updateMenuPosition);

        // Xử lý ẩn hiện header
        window.addEventListener('scroll', function() {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            // Chỉ ẩn khi scroll xuống quá 100px
            if (scrollTop > 100) {
                if (scrollTop > lastScrollTop) {
                    // Scroll Down -> Ẩn
                    mainHeader.classList.add('header-hidden');
                    if(verticalNav) verticalNav.classList.remove('open'); // Đóng menu nếu đang mở
                } else {
                    // Scroll Up -> Hiện
                    mainHeader.classList.remove('header-hidden');
                }
            } else {
                mainHeader.classList.remove('header-hidden');
            }
            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; 
        });
    }

    // Tự động scroll đến form đăng nhập/đăng ký
    const authForm = document.getElementById('auth-form');
    if (authForm) {
        authForm.scrollIntoView({
            behavior: 'auto', 
            block: 'start'   
        });
    }

    // 4. XỬ LÝ QUICK ADD (ĐÃ NÂNG CẤP LÊN TOAST)
    document.querySelectorAll('.btn-add-to-cart-quick').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            const productId = this.dataset.id; 
            const formData = new FormData();
            formData.append('id_hh', productId);
            formData.append('quantity', 1);

            fetch(BASE_PATH + '/cart/add', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const cartCountSpan = document.querySelector('.cart-count');
                    if (cartCountSpan) {
                        cartCountSpan.textContent = data.cartCount;
                    }
                    showToast('Đã thêm vào giỏ!');
                } else {
                    showToast(data.message || 'Có lỗi xảy ra!', 'error');
                }
            })
            .catch(error => { 
                console.error('Lỗi AJAX:', error); 
                showToast('Lỗi kết nối!', 'error');
            });
        });
    });
    
    // 5. XỬ LÝ ACCORDION (Giữ nguyên)
    document.querySelectorAll('.accordion-header').forEach(header => {
        header.addEventListener('click', function() {
            const item = this.parentElement; 
            const content = this.nextElementSibling; 
            const toggleBtn = this.querySelector('.accordion-toggle');
            if (item.classList.contains('active')) {
                item.classList.remove('active');
                content.style.maxHeight = '0';
                toggleBtn.textContent = '+';
            } else {
                item.classList.add('active');
                content.style.maxHeight = content.scrollHeight + 'px'; 
                toggleBtn.textContent = '−'; 
            }
        });
    });

    // 6. XỬ LÝ MẮT (Giữ nguyên)
    document.querySelectorAll('.toggle-password-icon').forEach(icon => {
        const iconPathOpen = BASE_PATH + '/images/vision.png';
        const iconPathClosed = BASE_PATH + '/images/eyelashes.png';
        icon.addEventListener('click', function() {
            const wrapper = this.parentElement;
            const input = wrapper.querySelector('input'); 
            if (input.type === "password") {
                input.type = "text";
                this.src = iconPathOpen; 
                this.alt = "Ẩn mật khẩu";
            } else {
                input.type = "password";
                this.src = iconPathClosed; 
                this.alt = "Hiện mật khẩu";
            }
        });
    });

    // 7. XỬ LÝ TĂNG/GIẢM VÀ TÍNH TIỀN GIỎ HÀNG (Giữ nguyên)
    function recalculateClientTotals() {
        let subtotal = 0;
        let totalDiscount = 0;
        const cartTbody = document.querySelector('#cart-tbody');
        if (!cartTbody) return;

        cartTbody.querySelectorAll('tr').forEach(row => {
            const checkbox = row.querySelector('.cart-item-select');
            const quantityField = row.querySelector('.quantity-field');
            
            // 1. Cập nhật "Tạm tính" cho từng hàng (cột thứ 5 của bảng)
            const unitPrice = parseFloat(row.dataset.subtotal) || 0;
            const discountPercent = parseFloat(row.dataset.discountPercent) || 0;
            const quantity = parseInt(quantityField.value) || 0;
            const itemTotal = unitPrice * quantity;
            
            // Cập nhật text hiển thị tạm tính cho riêng hàng này (nếu bạn có class hiển thị)
            const subtotalCell = row.querySelector('.cart-item-subtotal .product-price');
            if(subtotalCell) subtotalCell.textContent = itemTotal.toLocaleString('vi-VN') + ' đ';

            // 2. Tính tổng giỏ hàng (chỉ tính những hàng được chọn)
            if (checkbox && checkbox.checked) {
                subtotal += itemTotal;
                totalDiscount += (unitPrice * discountPercent / 100) * quantity;
            }
        });

        let total = subtotal - totalDiscount;
        document.querySelector('#cart-subtotal').textContent = subtotal.toLocaleString('vi-VN') + ' đ';
        document.querySelector('#cart-discount').textContent = '-' + totalDiscount.toLocaleString('vi-VN') + ' đ';
        document.querySelector('#cart-total').textContent = total.toLocaleString('vi-VN') + ' đ';
    }
    
    function updateCart(id_hh, quantity) {
        const formData = new FormData();
        formData.append('id_hh', id_hh);
        formData.append('quantity', quantity);
        fetch(BASE_PATH + '/cart/update', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateHeaderCartCount(data.cartCount);
                const itemRow = document.querySelector('#cart-item-' + id_hh);
                if (itemRow) {
                    if (quantity > 0) {
                        // Cập nhật quantity field
                        itemRow.querySelector('.quantity-field').value = quantity;
                        
                    } else {
                        itemRow.remove();
                    }
                }
                recalculateClientTotals(); // Hàm này đã tính đúng rồi
            }
        })
        .catch(error => console.error('Lỗi AJAX:', error));
    }
    
    document.querySelectorAll('.btn-quantity-change').forEach(button => {
        button.addEventListener('click', function() {
            const id = this.dataset.id;
            const change = parseInt(this.dataset.change);
            const input = this.parentElement.querySelector('.quantity-field');
            let newQuantity = parseInt(input.value) + change;
            if (newQuantity < 0) newQuantity = 0;
            input.value = newQuantity;
            updateCart(id, newQuantity); 
        });
    });
    
    document.querySelectorAll('.btn-remove').forEach(link => {
    link.addEventListener('click', function(e) {
        e.preventDefault();
        const id = this.href.split('/').pop();
        if (confirm('Bạn có chắc muốn xóa sản phẩm này?')) {
            updateCart(id, 0);
        }
    });
});
    
    document.querySelectorAll('.cart-item-select').forEach(checkbox => {
        checkbox.addEventListener('change', recalculateClientTotals);
    });
    
    const selectAllCheckbox = document.querySelector('#cart-select-all');
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            const isChecked = this.checked;
            document.querySelectorAll('.cart-item-select').forEach(checkbox => {
                checkbox.checked = isChecked;
            });
            recalculateClientTotals();
        });
    }
    recalculateClientTotals();

    // ===============================================
    // 8. ĐỊNH NGHĨA CÁC HÀM HỖ TRỢ API ĐỊA CHÍNH (MỚI - TỐI ƯU)
    // ===============================================

    // Hàm tải Tỉnh
    function loadProvinces(selectId) {
        const provinceSelect = document.querySelector(selectId);
        if (!provinceSelect) return;

        fetch(API_PROVINCE + "1/0.htm")
            .then(res => res.json())
            .then(result => {
                provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành --</option>';
                result.data.forEach(p => {
                    provinceSelect.options.add(new Option(p.full_name, p.id));
                });
            })
            .catch(err => console.error("Lỗi tải tỉnh:", err));
    }

    function attachDistrictListener(provinceId, districtId, wardId, hiddenNameFields = {}) {
        const provinceSelect = document.querySelector(provinceId);
        const districtSelect = document.querySelector(districtId);
        const wardSelect = document.querySelector(wardId);
        if (!provinceSelect) return;

        provinceSelect.addEventListener('change', function () {
            if (districtSelect) districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
            if (wardSelect) wardSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';

            if (hiddenNameFields.province && document.querySelector(hiddenNameFields.province)) {
                const text = this.options[this.selectedIndex]?.text || '';
                document.querySelector(hiddenNameFields.province).value = this.value ? text : '';
            }
            if (hiddenNameFields.district) document.querySelector(hiddenNameFields.district).value = '';
            if (hiddenNameFields.ward) document.querySelector(hiddenNameFields.ward).value = '';

            if (this.value) {
                fetch(API_PROVINCE + "2/" + this.value + ".htm")
                    .then(res => res.json())
                    .then(result => {
                        result.data.forEach(d => {
                            const option = new Option(d.full_name, d.id);
                            districtSelect.options[districtSelect.options.length] = option;
                        });
                        if (districtSelect) districtSelect.disabled = false;
                    })
                    .catch(err => console.error("Lỗi tải huyện:", err));
            } else {
                if (districtSelect) districtSelect.disabled = true;
                if (wardSelect) wardSelect.disabled = true;
            }
        });
    }

    function attachWardListener(districtId, wardId, hiddenNameFields = {}) {
        const districtSelect = document.querySelector(districtId);
        const wardSelect = document.querySelector(wardId);
        if (!districtSelect) return;

        districtSelect.addEventListener('change', function () {
            if (wardSelect) wardSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';

            if (hiddenNameFields.district && document.querySelector(hiddenNameFields.district)) {
                const text = this.options[this.selectedIndex]?.text || '';
                document.querySelector(hiddenNameFields.district).value = this.value ? text : '';
            }
            if (hiddenNameFields.ward) document.querySelector(hiddenNameFields.ward).value = '';

            if (this.value) {
                fetch(API_PROVINCE + "3/" + this.value + ".htm")
                    .then(res => res.json())
                    .then(result => {
                        result.data.forEach(w => {
                            const option = new Option(w.full_name, w.id);
                            wardSelect.options[wardSelect.options.length] = option;
                        });
                        if (wardSelect) wardSelect.disabled = false;
                    })
                    .catch(err => console.error("Lỗi tải xã:", err));
            } else {
                if (wardSelect) wardSelect.disabled = true;
            }
        });

        if (wardSelect && hiddenNameFields.ward) {
            wardSelect.addEventListener('change', function () {
                const input = document.querySelector(hiddenNameFields.ward);
                if (input) {
                    const text = this.options[this.selectedIndex]?.text || '';
                    input.value = this.value ? text : '';
                }
            });
        }
    }

    // ===============================================
    // 9. KHỞI CHẠY LOGIC ĐỊA CHỈ
    // ===============================================

    // A. Trang CHECKOUT
    if (document.querySelector("#province")) {
        loadProvinces("#province");
        attachDistrictListener("#province", "#district", "#ward", {}); 
        attachWardListener("#district", "#ward", {});
    }

    // B. Trang PROFILE (Modal)
    if (document.querySelector("#province-modal")) {
        const modalHiddens = {
            province: '#province-name-modal',
            district: '#district-name-modal',
            ward: '#ward-name-modal'
        };
        loadProvinces("#province-modal");
        attachDistrictListener("#province-modal", "#district-modal", "#ward-modal", modalHiddens);
        attachWardListener("#district-modal", "#ward-modal", modalHiddens);
    }

    // ===============================================
    // 10. XỬ LÝ MODAL: THÊM & SỬA ĐỊA CHỈ (MỚI - TỐI ƯU)
    // ===============================================

    // Nút "+ Thêm địa chỉ mới"
    const btnAddAddr = document.querySelector('#btn-show-add-modal');
    if (btnAddAddr && modalOverlay && modalForm) {
        btnAddAddr.addEventListener('click', () => {
            // Reset form
            modalForm.reset(); 
            if (document.querySelector("#district-modal")) {
                document.querySelector("#district-modal").innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                document.querySelector("#district-modal").disabled = true;
            }
            if (document.querySelector("#ward-modal")) {
                document.querySelector("#ward-modal").innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';
                document.querySelector("#ward-modal").disabled = true;
            }
            
            // Chuyển sang chế độ THÊM
            if (modalTitle) modalTitle.textContent = 'Địa chỉ mới';
            if (modalSubmitBtn) modalSubmitBtn.textContent = 'Lưu địa chỉ';
            modalForm.action = BASE_PATH + '/account/handleAddAddress';
            if (modalAddressIdInput) modalAddressIdInput.value = '';

            modalOverlay.style.display = 'flex';
        });
    }

    // Nút "Sửa" (Event Delegation - TỐI ƯU HƠN)
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.btn-edit-address');
        if (btn) {
            e.preventDefault();

            // Đọc data-* từ thẻ <a> trong profile.php
            const addressId  = btn.dataset.id;
            const provinceId = btn.dataset.province;
            const districtId = btn.dataset.district;
            const wardId     = btn.dataset.ward;

            // Điền thông tin cơ bản
            document.querySelector('#modal_ho_ten').value           = btn.dataset.name;
            document.querySelector('#modal_sdt_gh').value           = btn.dataset.phone;
            document.querySelector('#modal_dia_chi_chi_tiet').value = btn.dataset.detail;
            document.querySelector('#modal_is_default').checked     = (btn.dataset.default == 1);
            document.querySelector('#modal-address-id').value       = addressId;
            // Load Tỉnh
            fetch(API_PROVINCE + "1/0.htm")
            .then(res => res.json())
            .then(result => {

                const provinceSelect = document.querySelector("#province-modal");
                provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành --</option>';

                result.data.forEach(p => {                      
                    const option = new Option(p.full_name, p.id); 
                    if (p.id == provinceId) option.selected = true;
                    provinceSelect.options.add(option);
                });

                // ✅ Cập nhật hidden tỉnh
                document.querySelector('#province-name-modal').value =
                    provinceSelect.options[provinceSelect.selectedIndex]?.text || '';

                return fetch(API_PROVINCE + "2/" + provinceId + ".htm"); 
            })

            .then(res => res.json())
            .then(result => {

                const districtSelect = document.querySelector("#district-modal");
                districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';

                result.data.forEach(d => {                      
                    const option = new Option(d.full_name, d.id); 
                    if (d.id == districtId) option.selected = true;
                    districtSelect.options.add(option);
                });

                districtSelect.disabled = false;

                // ✅ Cập nhật hidden huyện
                document.querySelector('#district-name-modal').value =
                    districtSelect.options[districtSelect.selectedIndex]?.text || '';

                return fetch(API_PROVINCE + "3/" + districtId + ".htm"); 
            })

            .then(res => res.json())
            .then(result => {

                const wardSelect = document.querySelector("#ward-modal");
                wardSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';

                result.data.forEach(w => {                      
                    const option = new Option(w.full_name, w.id); 
                    if (w.id == wardId) option.selected = true;
                    wardSelect.options.add(option);
                });

                wardSelect.disabled = false;

                // ✅ Cập nhật hidden xã
                document.querySelector('#ward-name-modal').value =
                    wardSelect.options[wardSelect.selectedIndex]?.text || '';

                if (modalTitle) modalTitle.textContent = 'Sửa địa chỉ';
                if (modalSubmitBtn) modalSubmitBtn.textContent = 'Cập nhật địa chỉ';
                modalForm.action = BASE_PATH + '/account/handleAddAddress';
                modalOverlay.style.display = 'flex';
            })

            .catch(err => {
                console.error('Lỗi load địa chỉ:', err);
                showToast('Lỗi khi tải dữ liệu địa chỉ!', 'error');
            });
        }
    });

    // Đóng Modal
    document.querySelector('#btn-close-address-modal')?.addEventListener('click', () => {
        if (modalOverlay) modalOverlay.style.display = 'none';
    });
    if (modalOverlay) {
        modalOverlay.addEventListener('click', (e) => {
            if (e.target === modalOverlay) modalOverlay.style.display = 'none';
        });
    }

    // Tự động submit form khi chọn Radio
    const formSetDefault = document.querySelector('#form-set-default-address');
    if (formSetDefault) {
        document.querySelectorAll('.address-radio').forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    formSetDefault.submit();
                }
            });
        });
    }

    // ===============================================
    // 11. XỬ LÝ AJAX SUBMIT FORM ĐỊA CHỈ (MỚI - TỐI ƯU)
    // ===============================================
    if (modalForm) {
        modalForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            // Validate
            if (!document.querySelector("#province-modal").value) {
                showToast("Vui lòng chọn Tỉnh/Thành phố", "error");
                return;
            }
            
            const formData = new FormData(modalForm);
            const formAction = modalForm.getAttribute('action');
            const submitButton = modalForm.querySelector('#modal-submit-button');
            
            submitButton.disabled = true;
            submitButton.textContent = 'Đang lưu...';

            fetch(formAction, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Lưu địa chỉ thành công!');
                    if (modalOverlay) modalOverlay.style.display = 'none';
                    
                    // Cập nhật hoặc reload
                    if (document.querySelector('.address-list-checkout')) {
                        updateAddressList(data.newAddresses);
                    } else {
                        location.reload(); 
                    }
                } else {
                    showToast('Lỗi! Không thể lưu địa chỉ.', 'error');
                }
            })
            .catch(error => {
                console.error('Lỗi AJAX:', error);
                showToast('Lỗi kết nối!', 'error');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.textContent = 'Lưu địa chỉ';
            });
        });
    }
    // Xử lý nút Xóa địa chỉ
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.delete-address-btn');
        if (btn) {
            e.preventDefault();
            const addressId = btn.dataset.id;
            if (confirm('Bạn có chắc muốn xóa địa chỉ này?')) {
                window.location.href = BASE_PATH + '/account/deleteAddress/' + addressId;
            }
        }
    });
    // ===============================================
    // 12. XỬ LÝ AJAX CHO FORM "THÊM VÀO GIỎ" (TRANG CHI TIẾT)
    // ===============================================
    const detailAddToCartForm = document.querySelector('.add-to-cart-form');
    if (detailAddToCartForm) {
        detailAddToCartForm.addEventListener('submit', function(event) {
            event.preventDefault(); 
            
            const formData = new FormData(detailAddToCartForm);
            const formAction = detailAddToCartForm.getAttribute('action'); 

            fetch(formAction, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json()) 
            .then(data => {
                if (data.success) {
                    const cartCountSpan = document.querySelector('.cart-count');
                    if (cartCountSpan) {
                        cartCountSpan.textContent = data.cartCount;
                    }
                    showToast('Đã thêm vào giỏ!');
                } else {
                    showToast(data.message || 'Có lỗi xảy ra!', 'error');
                }
            })
            .catch(error => {
                console.error('Lỗi AJAX:', error);
                showToast('Có lỗi kết nối!', 'error');
            });
        });
    }

    // ===============================================
    // 13. XỬ LÝ NÚT LÀM TRỐNG GIỎ HÀNG
    // ===============================================
    const btnClearCart = document.querySelector('#btn-clear-cart');
    if (btnClearCart) {
        btnClearCart.addEventListener('click', function() {
            if (confirm('Bạn có chắc muốn xóa tất cả sản phẩm khỏi giỏ hàng?')) {
                fetch(BASE_PATH + '/cart/clear', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        updateHeaderCartCount(0);
                        location.reload();
                    } else {
                        showToast('Có lỗi xảy ra!', 'error');
                    }
                })
                .catch(error => {
                    console.error('Lỗi AJAX:', error);
                    showToast('Có lỗi kết nối!', 'error');
                });
            }
        });
    }

    // ===============================================
    // 14. TỰ ĐỘNG ĐÓNG MENU KHI BẤM RA NGOÀI
    // ===============================================
    const verticalNavForClose = document.querySelector('.vertical-nav');
    const menuToggleForClose = document.querySelector('.menu-toggle');
    if (verticalNavForClose && menuToggleForClose) {
        document.addEventListener('click', function(event) {
            const isMenuOpen = verticalNavForClose.classList.contains('open');
            const isClickOutsideNav = !verticalNavForClose.contains(event.target);
            const isClickOutsideToggle = !menuToggleForClose.contains(event.target);

            if (isMenuOpen && isClickOutsideNav && isClickOutsideToggle) {
                verticalNavForClose.classList.remove('open');
            }
        });
    }

    // ===============================================
    // 15. KIỂM TRA GIỎ HÀNG TRƯỚC KHI SUBMIT
    // ===============================================
    const cartCheckoutForm = document.querySelector('#form-cart-checkout');
    if (cartCheckoutForm) {
        cartCheckoutForm.addEventListener('submit', function(event) {
            const checkedItems = cartCheckoutForm.querySelectorAll('.cart-item-select:checked');
            if (checkedItems.length === 0) {
                event.preventDefault(); 
                showToast('Bạn ơi, vui lòng chọn ít nhất 1 sản phẩm!', 'error');
            }
        });
    }

    // ===============================================
    // HÀM HELPER: HIỂN THỊ TOAST NOTIFICATION
    // ===============================================
    window.showToast = function(message, type = 'success') {
        const toast = document.createElement('div');
        toast.classList.add('toast-notification');
        if (type === 'error') toast.classList.add('toast-error');
        toast.innerText = message;
        document.body.appendChild(toast);
        setTimeout(() => toast.classList.add('fade-out'), 2000);
        setTimeout(() => toast.remove(), 3500);
    }

    window.updateHeaderCartCount = function(count) {
        const cartCountSpan = document.querySelector('.cart-count');
        if (cartCountSpan) {
            cartCountSpan.textContent = count;
            
            // Mẹo nhỏ: Nếu số lượng là 0 thì ẩn đi hoặc để số 0 tùy giao diện của bạn
            if (parseInt(count) <= 0) {
                cartCountSpan.style.display = 'none'; 
            } else {
                cartCountSpan.style.display = 'inline-block'; // Hoặc block
            }
        }
    }
});

/* =======================================================
 * HÀM HELPER: VẼ LẠI DANH SÁCH ĐỊA CHỈ (TRANG CHECKOUT)
 * ======================================================= */
function updateAddressList(addresses) {
    const listElement = document.querySelector('.address-list-checkout');
    const checkoutButton = document.querySelector('.btn-checkout');
    
    if (!listElement) return; 
    
    listElement.innerHTML = ''; 
    
    if (!addresses || addresses.length === 0) {
        listElement.innerHTML = '<p style="color:red;font-weight:600;">Bạn chưa có địa chỉ nào!</p><p>Vui lòng thêm địa chỉ mới.</p>';
        if (checkoutButton) {
            checkoutButton.disabled = true;
            checkoutButton.textContent = 'Vui lòng thêm địa chỉ';
        }
        return;
    }

    // Có địa chỉ -> bật nút đặt hàng
    if (checkoutButton) {
        checkoutButton.disabled = false;
        checkoutButton.textContent = 'ĐẶT HÀNG';
    }

    // Kiểm tra địa chỉ mặc định
    const hasDefault = addresses.some(a => a.mac_dinh == 1);

    addresses.forEach((addr, index) => {
        let isChecked = false;

        if (hasDefault) {
            if (addr.mac_dinh == 1) isChecked = true;
        } else {
            if (index === 0) isChecked = true;
        }

        const checkedAttr = isChecked ? 'checked' : '';
        const defaultTag = (addr.mac_dinh == 1) ? '<span class="default-tag-small">Mặc định</span>' : '';

        const itemHtml = `
            <div class="form-group-radio">
                <input type="radio" 
                       id="addr_${addr.id_dc}" 
                       name="selected_address_id" 
                       value="${addr.id_dc}"
                       ${checkedAttr}>
                <label for="addr_${addr.id_dc}" class="radio-label">
                    <strong>${escapeHTML(addr.ten_nguoi_nhan)} ${defaultTag}</strong><br>
                    SĐT: ${escapeHTML(addr.sdt_gh)}<br>
                    ĐC: ${escapeHTML(addr.dia_chi_chi_tiet)}, 
                        ${escapeHTML(addr.ten_xa_phuong)}, 
                        ${escapeHTML(addr.ten_quan_huyen)}, 
                        ${escapeHTML(addr.ten_tinh_tp)}
                </label>
            </div>
        `;
        listElement.innerHTML += itemHtml;
    });
}

// Hàm bảo mật tránh lỗi XSS
function escapeHTML(str) {
    if (str === null || str === undefined) return '';
    return str.toString()
              .replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/"/g, '&quot;')
              .replace(/'/g, '&#039;');
}