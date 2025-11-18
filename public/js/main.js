// Chờ cho toàn bộ nội dung trang tải xong
document.addEventListener("DOMContentLoaded", function() {
    
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
    if (menuToggle && verticalNav) {
        menuToggle.addEventListener('click', function() {
            verticalNav.classList.toggle('open');
        });
    }

    const modalForm = document.querySelector('#form-address-modal');

    // 3. XỬ LÝ HEADER (Đã sửa lỗi cho trang con)
    let lastScrollTop = 0;
    const mainHeader = document.querySelector('.main-header');
    const subNav = document.querySelector('.sub-nav'); 
    const topBar = document.querySelector('.top-bar-shipping');
    if (mainHeader && topBar) { 
        let headerHeight = mainHeader.offsetHeight;
        if (subNav) {
            subNav.style.top = headerHeight + 'px'; 
        }
        if (verticalNav) { 
             verticalNav.style.top = headerHeight + 'px';
        }
        const topBarHeight = topBar.offsetHeight;
        mainHeader.style.top = '0'; 
        window.addEventListener('scroll', function() {
            let scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            if (scrollTop > lastScrollTop && scrollTop > topBarHeight) {
                mainHeader.classList.add('header-hidden');
                if (subNav) subNav.classList.add('header-hidden'); 
            } else {
                mainHeader.classList.remove('header-hidden');
                if (subNav) subNav.classList.remove('header-hidden');
            }
            lastScrollTop = scrollTop <= 0 ? 0 : scrollTop; 
        });
    }

    // =======================================================
    // ⬇️ BẠN ĐẶT CODE MỚI VÀO ĐÂY ⬇️
    // =======================================================
    const authForm = document.getElementById('auth-form');
    if (authForm) {
        authForm.scrollIntoView({
            behavior: 'auto', 
            block: 'start'   
        });
    }

    // =======================================================
    // 4. XỬ LÝ QUICK ADD (ĐÃ NÂNG CẤP LÊN TOAST)
    // =======================================================
    document.querySelectorAll('.btn-add-to-cart-quick').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); // Ngăn chặn mọi hành vi mặc định
            
            const productId = this.dataset.id; 
            const formData = new FormData();
            formData.append('id_hh', productId);
            formData.append('quantity', 1); // Nút "thêm nhanh" sẽ luôn thêm 1 sản phẩm

            // Bắt đầu gọi AJAX
            fetch(BASE_PATH + '/cart/add', {
                method: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // 1. Cập nhật icon giỏ hàng trên header
                    const cartCountSpan = document.querySelector('.cart-count');
                    if (cartCountSpan) {
                        cartCountSpan.textContent = data.cartCount;
                    }
                    
                    // 2. HIỂN THỊ TOAST THAY VÌ ALERT
                    showToast('Đã thêm vào giỏ!');

                } else {
                    // HIỂN THỊ TOAST LỖI
                    showToast(data.message || 'Có lỗi xảy ra!', 'error');
                }
            })
            .catch(error => { 
                console.error('Lỗi AJAX:', error); 
                // HIỂN THỊ TOAST LỖI KẾT NỐI
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
            if (checkbox && checkbox.checked) {
                const originalSubtotal = parseFloat(row.dataset.subtotal);
                const discountPercent = parseFloat(row.dataset.discountPercent);
                const quantity = parseInt(row.querySelector('.quantity-field').value);
                subtotal += originalSubtotal * quantity;
                totalDiscount += (originalSubtotal * discountPercent / 100) * quantity;
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
                document.querySelector('.cart-count').textContent = data.cartCount;
                const itemRow = document.querySelector('#cart-item-' + id_hh);
                if (itemRow) {
                    if (quantity > 0) {
                        itemRow.querySelector('.cart-item-subtotal').textContent = data.itemSubtotal;
                    } else {
                        itemRow.remove(); 
                    }
                }
                recalculateClientTotals(); 
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
    document.querySelectorAll('.btn-remove-item').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault(); 
            const id = this.href.split('/').pop(); 
            if (confirm('Bé iu có chắc muốn xóa món này?')) {
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
    // 8. SỬA LẠI: API TỈNH/THÀNH (ĐỂ LƯU CẢ ID VÀ TÊN)
    // ===============================================
    const apiHost = "https://provinces.open-api.vn/api/";

    // Hàm chung để tải Tỉnh
    function loadProvinces(selectId, selectedCode = null) {
        const provinceSelect = document.querySelector(selectId);
        if (!provinceSelect) return;
        fetch(apiHost + "?depth=1")
            .then(res => res.json())
            .then(data => {
                provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành --</option>'; // Reset
                data.forEach(province => {
                    const option = new Option(province.name, province.code);
                    // Tự động chọn nếu có selectedCode
                    if (province.code == selectedCode) option.selected = true; 
                    provinceSelect.options[provinceSelect.options.length] = option;
                });
            });
    }

    // Hàm chung để tải Huyện (khi Tỉnh thay đổi)
    function attachDistrictListener(provinceId, districtId, wardId, hiddenNameFields, selectedCode = null) {
        const provinceSelect = document.querySelector(provinceId);
        const districtSelect = document.querySelector(districtId);
        const wardSelect = document.querySelector(wardId);
        if (!provinceSelect) return;

        provinceSelect.addEventListener('change', function() {
            districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
            wardSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';
            
            const selectedText = this.options[this.selectedIndex].text;
            if (hiddenNameFields.province) document.querySelector(hiddenNameFields.province).value = (this.value ? selectedText : '');
            if (hiddenNameFields.district) document.querySelector(hiddenNameFields.district).value = '';
            if (hiddenNameFields.ward) document.querySelector(hiddenNameFields.ward).value = '';

            if (this.value) {
                fetch(apiHost + "p/" + this.value + "?depth=2")
                    .then(res => res.json())
                    .then(data => {
                        data.districts.forEach(district => {
                            const option = new Option(district.name, district.code);
                            // Tự động chọn nếu có
                            if (district.code == selectedCode) option.selected = true; 
                            districtSelect.options[districtSelect.options.length] = option;
                        });
                        // Tự động trigger (gọi) sự kiện change của Huyện (để tải Xã)
                        if (selectedCode) districtSelect.dispatchEvent(new Event('change'));
                    });
            }
        });
    }

    // Hàm chung để tải Xã (khi Huyện thay đổi)
    function attachWardListener(districtId, wardId, hiddenNameFields, selectedCode = null) {
        const districtSelect = document.querySelector(districtId);
        const wardSelect = document.querySelector(wardId);
        if (!districtSelect) return;

        districtSelect.addEventListener('change', function() {
            wardSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';
            
            const selectedText = this.options[this.selectedIndex].text;
            if (hiddenNameFields.district) document.querySelector(hiddenNameFields.district).value = (this.value ? selectedText : '');
            if (hiddenNameFields.ward) document.querySelector(hiddenNameFields.ward).value = '';

            if (this.value) {
                fetch(apiHost + "d/" + this.value + "?depth=2")
                    .then(res => res.json())
                    .then(data => {
                        data.wards.forEach(ward => {
                            const option = new Option(ward.name, ward.code);
                            // Tự động chọn nếu có
                            if (ward.code == selectedCode) option.selected = true; 
                            wardSelect.options[wardSelect.options.length] = option;
                        });
                        // Tự động trigger (gọi) sự kiện change của Xã (để lưu tên)
                        if (selectedCode) wardSelect.dispatchEvent(new Event('change'));
                    });
            }
        });
        
        // Sự kiện cuối cùng: Lưu tên Xã
        if (wardSelect && hiddenNameFields.ward) {
            wardSelect.addEventListener('change', function() {
                const selectedText = this.options[this.selectedIndex].text;
                document.querySelector(hiddenNameFields.ward).value = (this.value ? selectedText : '');
            });
        }
    }

    // A. Chạy API cho trang CHECKOUT (Không cần lưu tên)
    loadProvinces("#province");
    attachDistrictListener("#province", "#district", "#ward", {}); 
    attachWardListener("#district", "#ward", {});
    
    // B. Chạy API cho MODAL trang PROFILE (Cần lưu tên)
    const modalHiddens = {
        province: '#province-name-modal',
        district: '#district-name-modal',
        ward: '#ward-name-modal'
    };
    loadProvinces("#province-modal");
    attachDistrictListener("#province-modal", "#district-modal", "#ward-modal", modalHiddens);
    attachWardListener("#district-modal", "#ward-modal", modalHiddens);
    

    // ===============================================
    // 9. SỬA LẠI: LOGIC MODAL (CHO CẢ THÊM VÀ SỬA)
    // ===============================================
    const modalOverlay = document.querySelector('#address-modal-overlay');
    const modalTitle = document.querySelector('#modal-title');
    const modalSubmitBtn = document.querySelector('#modal-submit-button');
    const modalAddressIdInput = document.querySelector('#modal-address-id');
    const modalMethodInput = document.querySelector('#modal-method-field');

    // Nút "+ Thêm địa chỉ mới"
    document.querySelector('#btn-show-add-modal')?.addEventListener('click', () => {
        // 1. Reset form về rỗng
        modalForm.reset(); 
        document.querySelector("#district-modal").innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
        document.querySelector("#ward-modal").innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';
        
        // 2. Chuyển sang chế độ "THÊM MỚI"
        modalTitle.textContent = 'Địa chỉ mới';
        modalSubmitBtn.textContent = 'Lưu địa chỉ';
        modalForm.action = BASE_PATH + '/account/handleAddAddress'; // URL để Thêm
        modalMethodInput.value = ''; // Không cần _method
        modalAddressIdInput.value = ''; // Xóa ID

        // 3. Hiện Modal
        modalOverlay.style.display = 'flex';
    });
    
    // Nút "Sửa" (Tất cả các nút sửa)
    document.querySelectorAll('.btn-edit-address').forEach(button => {
        button.addEventListener('click', (e) => {
            e.preventDefault();
            const addressId = e.target.dataset.id;
            
            // 1. Gọi API (Giai đoạn 3) để lấy dữ liệu
            fetch(BASE_PATH + '/account/getAddressJson/' + addressId)
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        const addr = result.data;
                        
                        // 2. Điền (Populate) dữ liệu vào Form
                        document.querySelector('#modal_ho_ten').value = addr.TEN_NGUOI_NHAN;
                        document.querySelector('#modal_sdt_gh').value = addr.SDT_GH;
                        document.querySelector('#modal_dia_chi_chi_tiet').value = addr.DIA_CHI_CHI_TIET;
                        document.querySelector('#modal_is_default').checked = (addr.IS_DEFAULT == 1);
                        
                        // 3. Xử lý API Tỉnh/Huyện/Xã (Tự động tải lại)
                        // (Vì API load chậm, chúng ta sẽ trigger Tỉnh, Huyện, Xã)
                        loadProvinces("#province-modal", addr.ID_TINH_TP);
                        attachDistrictListener("#province-modal", "#district-modal", "#ward-modal", modalHiddens, addr.ID_QUAN_HUYEN);
                        attachWardListener("#district-modal", "#ward-modal", modalHiddens, addr.ID_XA_PHUONG);
                        // Trigger Tỉnh để tải Huyện
                        setTimeout(() => { 
                            document.querySelector("#province-modal").dispatchEvent(new Event('change'));
                        }, 500); // Chờ 0.5s để Tỉnh tải xong
                        
                        // 4. Chuyển sang chế độ "SỬA"
                        modalTitle.textContent = 'Sửa địa chỉ';
                        modalSubmitBtn.textContent = 'Cập nhật địa chỉ';
                        modalForm.action = BASE_PATH + '/account/handleUpdateAddress'; // URL để Sửa
                        modalAddressIdInput.value = addressId; // Đặt ID để gửi
                        
                        // 5. Hiện Modal
                        modalOverlay.style.display = 'flex';
                    }
                });
        });
    });

    // Nút "X" (Đóng Modal)
    document.querySelector('#btn-close-address-modal')?.addEventListener('click', () => {
        modalOverlay.style.display = 'none';
    });
    // Bấm ra ngoài (Đóng Modal)
    modalOverlay?.addEventListener('click', (e) => {
        if (e.target === modalOverlay) {
            modalOverlay.style.display = 'none';
        }
    });

    // Tự động submit form khi chọn Radio (Giữ nguyên)
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

    // =======================================================
    // 10. XỬ LÝ AJAX CHO FORM "THÊM VÀO GIỎ" (TRANG CHI TIẾT)
    // =======================================================

    // 1. Tìm form trên trang chi tiết (form có class .add-to-cart-form)
    const detailAddToCartForm = document.querySelector('.add-to-cart-form');
    
    // 2. Chỉ chạy code nếu tìm thấy form này
    if (detailAddToCartForm) {
        
        // 3. Gắn sự kiện "submit" cho form
        detailAddToCartForm.addEventListener('submit', function(event) {
            
            // 4. NGĂN CHẶN form gửi đi và tải lại trang
            event.preventDefault(); 
            
            // 5. Lấy dữ liệu từ form (bao gồm cả ID và số lượng)
            const formData = new FormData(detailAddToCartForm);
            const formAction = detailAddToCartForm.getAttribute('action'); 

            // 6. Gửi dữ liệu bằng AJAX (fetch)
            fetch(formAction, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(response => response.json()) 
            .then(data => {
                if (data.success) {
                    
                    // ===============================================
                    // SỬA LỖI: Thêm 4 dòng này
                    // ===============================================
                    // 1. Cập nhật icon giỏ hàng trên header
                    const cartCountSpan = document.querySelector('.cart-count');
                    if (cartCountSpan) {
                        cartCountSpan.textContent = data.cartCount;
                    }
                    // ===============================================
                    
                    showToast('Đã thêm vào giỏ!');

                } else {
                    showToast(data.message || 'Có lỗi xảy ra!', 'error');
                }
            })
            .catch(error => {
                console.error('Lỗi AJAX:', error);
                alert('Có lỗi kết nối, vui lòng thử lại.');
            });
        });
    }

    // ... (Phần còn lại của file main.js giữ nguyên) ...

    // HÀM HELPER: HIỂN THỊ TOAST NOTIFICATION
    function showToast(message, type = 'success') {
        // 1. Tạo 1 div mới
        const toast = document.createElement('div');
        
        // 2. Thêm class cho nó
        toast.classList.add('toast-notification');
        if (type === 'error') {
            toast.classList.add('toast-error');
        }

        // 3. Đặt nội dung
        toast.innerText = message;

        // 4. Thêm vào trang web
        document.body.appendChild(toast);

        // 5. Tự động mờ đi sau 3 giây
        setTimeout(() => {
            toast.classList.add('fade-out');
        }, 2000); // 2000ms = 2 giây

        // 6. Tự động xóa khỏi DOM sau khi mờ xong (3.5 giây)
        setTimeout(() => {
            toast.remove();
        }, 3500); // 3000ms + 500ms (animation)
    }

    // =======================================================
    // 11. XỬ LÝ NÚT LÀM TRỐNG GIỎ HÀNG (AJAX)
    // (Đây là code mới bạn cần thêm)
    // =======================================================
    const btnClearCart = document.querySelector('#btn-clear-cart');
    
    if (btnClearCart) {
        btnClearCart.addEventListener('click', function() {
            
            // 1. Hỏi xác nhận
            if (confirm('Bạn có chắc muốn xóa tất cả sản phẩm khỏi giỏ hàng?')) {
                
                // 2. Gọi AJAX đến Controller (hàm clear() bạn vừa tạo)
                fetch(BASE_PATH + '/cart/clear', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // 3. Cập nhật icon giỏ hàng trên header
                        const cartCountSpan = document.querySelector('.cart-count');
                        if (cartCountSpan) {
                            cartCountSpan.textContent = data.cartCount; // Sẽ là 0
                        }
                        
                        // 4. HIỂN THỊ GIỎ HÀNG TRỐNG:
                        location.reload();
                        
                    } else {
                        // Sử dụng hàm showToast (nếu bạn đã thêm)
                        showToast('Có lỗi xảy ra, không thể làm trống giỏ hàng!', 'error');
                    }
                })
                .catch(error => {
                    console.error('Lỗi AJAX:', error);
                    showToast('Có lỗi kết nối!', 'error');
                });
            }
        });
    }

    // =======================================================
    // 12. TỰ ĐỘNG ĐÓNG MENU KHI BẤM RA NGOÀI
    // =======================================================
    
    // Lấy 2 đối tượng: Nút mở (menu-toggle) và Menu (vertical-nav)
    const verticalNavForClose = document.querySelector('.vertical-nav');
    const menuToggleForClose = document.querySelector('.menu-toggle');

    // Chỉ chạy nếu 2 đối tượng này tồn tại
    if (verticalNavForClose && menuToggleForClose) {
        
        document.addEventListener('click', function(event) {
            // Kiểm tra xem menu có đang mở không
            const isMenuOpen = verticalNavForClose.classList.contains('open');
            
            // Kiểm tra xem vị trí bấm có phải là BÊN NGOÀI menu
            const isClickOutsideNav = !verticalNavForClose.contains(event.target);
            
            // Kiểm tra xem vị trí bấm có phải là BÊN NGOÀI nút 3 gạch
            const isClickOutsideToggle = !menuToggleForClose.contains(event.target);

            // Nếu menu đang mở VÀ bấm ra ngoài cả 2
            if (isMenuOpen && isClickOutsideNav && isClickOutsideToggle) {
                verticalNavForClose.classList.remove('open'); // Đóng menu
            }
        });
    }

    // =======================================================
    // 13. KIỂM TRA GIỎ HÀNG TRƯỚC KHI SUBMIT (ĐÃ SỬA LỖI)
    // =======================================================
    const cartCheckoutForm = document.querySelector('#form-cart-checkout');
    
    if (cartCheckoutForm) {
        cartCheckoutForm.addEventListener('submit', function(event) {
            const checkedItems = cartCheckoutForm.querySelectorAll('.cart-item-select:checked');
            
            // 2. Nếu không có ô nào được tick
            if (checkedItems.length === 0) {
                
                // 2a. Ngăn form gửi đi
                event.preventDefault(); 
                
                // 2b. Cảnh báo người dùng (dùng toast hoặc alert)
                if (typeof showToast === 'function') {
                    showToast('Bạn ơi, vui lòng chọn ít nhất 1 sản phẩm!', 'error');
                } else {
                    alert('Bạn ơi, vui lòng chọn ít nhất 1 sản phẩm để thanh toán nhé!');
                }
            }
        });
    }

    // =======================================================
    // 14. XỬ LÝ SUBMIT MODAL ĐỊA CHỈ BẰNG AJAX
    // =======================================================
    
    if (modalForm) {
        modalForm.addEventListener('submit', function(e) {
            // 1. Ngăn chặn form submit (gây reload)
            e.preventDefault(); 
            const formData = new FormData(modalForm);
            const formAction = modalForm.getAttribute('action');
            const submitButton = modalForm.querySelector('#modal-submit-button');
            submitButton.disabled = true; // Vô hiệu hóa nút
            submitButton.textContent = 'Đang lưu...';

            // 2. Gửi dữ liệu bằng AJAX
            fetch(formAction, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    showToast('Đã lưu địa chỉ thành công!');
                    document.querySelector('#address-modal-overlay').style.display = 'none';
                    updateAddressList(data.newAddresses);
                    
                } else {
                    showToast('Lỗi! Không thể lưu địa chỉ.', 'error');
                }
            })
            .catch(error => {
                console.error('Lỗi AJAX (Modal):', error);
                showToast('Lỗi kết nối!', 'error');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.textContent = 'Lưu địa chỉ';
            });
        });
    }
});

/* =======================================================
 * HÀM HELPER: VẼ LẠI DANH SÁCH ĐỊA CHỈ (TRANG CHECKOUT)
 * ======================================================= */
function updateAddressList(addresses) {
    const listElement = document.querySelector('.address-list-checkout');
    const checkoutButton = document.querySelector('.btn-checkout');
    if (!listElement) {
        return; 
    }
    listElement.innerHTML = ''; 
    if (!addresses || addresses.length === 0) {
        listElement.innerHTML = '<p style="color: red; font-weight: 600;">Bạn chưa có địa chỉ nào!</p><p>Vui lòng thêm địa chỉ mới.</p>';
        if (checkoutButton) {
            checkoutButton.disabled = true;
            checkoutButton.textContent = 'Vui lòng thêm địa chỉ';
        }
        return;
    }

    // 5. Nếu có địa chỉ -> Bật nút Đặt hàng
    if (checkoutButton) {
        checkoutButton.disabled = false;
        checkoutButton.textContent = 'ĐẶT HÀNG';
    }

    // 6. Kiểm tra xem có địa chỉ mặc định không
    const hasDefault = addresses.some(a => a.IS_DEFAULT == 1);

    // 7. Lặp và tạo lại HTML cho từng địa chỉ
    addresses.forEach((addr, index) => {
        let isChecked = false;
        if (hasDefault) {
            if (addr.IS_DEFAULT == 1) isChecked = true;
        } else {
            if (index === 0) isChecked = true; // Tự động check cái đầu tiên
        }

        const defaultTag = (addr.IS_DEFAULT == 1) ? '<span class="default-tag-small">Mặc định</span>' : '';
        const checkedAttr = isChecked ? 'checked' : '';

        // Tạo HTML
        const itemHtml = `
            <div class="form-group-radio">
                <input type="radio" 
                       id="addr_${addr.ID_DIA_CHI}" 
                       name="selected_address_id" 
                       value="${addr.ID_DIA_CHI}"
                       ${checkedAttr}
                >
                <label for="addr_${addr.ID_DIA_CHI}" class="radio-label">
                    <strong>
                        ${escapeHTML(addr.TEN_NGUOI_NHAN)}
                        ${defaultTag}
                    </strong><br>
                    SĐT: ${escapeHTML(addr.SDT_GH)}<br>
                    ĐC: ${escapeHTML(addr.DIA_CHI_CHI_TIET)}, 
                        ${escapeHTML(addr.TEN_XA_PHUONG)}, 
                        ${escapeHTML(addr.TEN_QUAN_HUYEN)}, 
                        ${escapeHTML(addr.TEN_TINH_TP)}
                </label>
            </div>
        `;
        listElement.innerHTML += itemHtml;
    });
}

// Hàm bảo mật nhỏ để tránh lỗi XSS khi chèn HTML
function escapeHTML(str) {
    if (str === null || str === undefined) return '';
    return str.toString()
              .replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/"/g, '&quot;')
              .replace(/'/g, '&#039;');
}
    