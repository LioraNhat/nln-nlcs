// Chờ cho toàn bộ nội dung trang tải xong
document.addEventListener("DOMContentLoaded", function() {
    
    // ===============================================
    // KHAI BÁO CÁC BIẾN TOÀN CỤC
    // ===============================================
    const apiHost = "https://provinces.open-api.vn/api/";
    
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
    // 8. ĐỊNH NGHĨA CÁC HÀM HỖ TRỢ API ĐỊA CHÍNH (MỚI - TỐI ƯU)
    // ===============================================

    // Hàm tải Tỉnh
    function loadProvinces(selectId, selectedCode = null) {
        const provinceSelect = document.querySelector(selectId);
        if (!provinceSelect) return;
        
        fetch(apiHost + "?depth=1")
            .then(res => res.json())
            .then(data => {
                provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành --</option>';
                data.forEach(province => {
                    const option = new Option(province.name, province.code);
                    if (province.code == selectedCode) option.selected = true;
                    provinceSelect.options[provinceSelect.options.length] = option;
                });
            })
            .catch(err => console.error("Lỗi tải tỉnh:", err));
    }

    // Hàm tải Huyện (khi Tỉnh thay đổi)
    function attachDistrictListener(provinceId, districtId, wardId, hiddenNameFields = {}, selectedCode = null) {
        const provinceSelect = document.querySelector(provinceId);
        const districtSelect = document.querySelector(districtId);
        const wardSelect = document.querySelector(wardId);
        if (!provinceSelect) return;

        provinceSelect.addEventListener('change', function() {
            // Reset Huyện & Xã
            if (districtSelect) districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
            if (wardSelect) wardSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';
            
            // Lưu tên Tỉnh vào input ẩn
            if (hiddenNameFields.province && document.querySelector(hiddenNameFields.province)) {
                const selectedText = this.options[this.selectedIndex]?.text || '';
                document.querySelector(hiddenNameFields.province).value = (this.value ? selectedText : '');
            }
            
            // Reset tên Huyện & Xã ẩn
            if (hiddenNameFields.district && document.querySelector(hiddenNameFields.district)) 
                document.querySelector(hiddenNameFields.district).value = '';
            if (hiddenNameFields.ward && document.querySelector(hiddenNameFields.ward)) 
                document.querySelector(hiddenNameFields.ward).value = '';

            if (this.value) {
                fetch(apiHost + "p/" + this.value + "?depth=2")
                    .then(res => res.json())
                    .then(data => {
                        if (districtSelect) {
                            data.districts.forEach(district => {
                                const option = new Option(district.name, district.code);
                                if (district.code == selectedCode) option.selected = true;
                                districtSelect.options[districtSelect.options.length] = option;
                            });
                            districtSelect.disabled = false;
                            if (selectedCode) districtSelect.dispatchEvent(new Event('change'));
                        }
                    })
                    .catch(err => console.error("Lỗi tải huyện:", err));
            } else {
                if (districtSelect) districtSelect.disabled = true;
                if (wardSelect) wardSelect.disabled = true;
            }
        });
    }

    // Hàm tải Xã (khi Huyện thay đổi)
    function attachWardListener(districtId, wardId, hiddenNameFields = {}, selectedCode = null) {
        const districtSelect = document.querySelector(districtId);
        const wardSelect = document.querySelector(wardId);
        if (!districtSelect) return;

        districtSelect.addEventListener('change', function() {
            if (wardSelect) wardSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';
            
            // Lưu tên Huyện
            if (hiddenNameFields.district && document.querySelector(hiddenNameFields.district)) {
                const selectedText = this.options[this.selectedIndex]?.text || '';
                document.querySelector(hiddenNameFields.district).value = (this.value ? selectedText : '');
            }
            // Reset tên Xã ẩn
            if (hiddenNameFields.ward && document.querySelector(hiddenNameFields.ward)) 
                document.querySelector(hiddenNameFields.ward).value = '';

            if (this.value) {
                fetch(apiHost + "d/" + this.value + "?depth=2")
                    .then(res => res.json())
                    .then(data => {
                        if (wardSelect) {
                            data.wards.forEach(ward => {
                                const option = new Option(ward.name, ward.code);
                                if (ward.code == selectedCode) option.selected = true;
                                wardSelect.options[wardSelect.options.length] = option;
                            });
                            wardSelect.disabled = false;
                            if (selectedCode) wardSelect.dispatchEvent(new Event('change'));
                        }
                    })
                    .catch(err => console.error("Lỗi tải xã:", err));
            } else {
                if (wardSelect) wardSelect.disabled = true;
            }
        });

        // Sự kiện lưu tên Xã
        if (wardSelect && hiddenNameFields.ward) {
            wardSelect.addEventListener('change', function() {
                const inputHidden = document.querySelector(hiddenNameFields.ward);
                if (inputHidden) {
                    const selectedText = this.options[this.selectedIndex]?.text || '';
                    inputHidden.value = (this.value ? selectedText : '');
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
        if (e.target && e.target.classList.contains('btn-edit-address')) {
            e.preventDefault();
            const addressId = e.target.dataset.id;
            
            // Gọi API lấy dữ liệu địa chỉ
            fetch(BASE_PATH + '/account/getAddressJson/' + addressId)
                .then(res => res.json())
                .then(result => {
                    if (result.success) {
                        const addr = result.data;
                        
                        // 1. Điền các trường thông tin cơ bản
                        document.querySelector('#modal_ho_ten').value = addr.TEN_NGUOI_NHAN;
                        document.querySelector('#modal_sdt_gh').value = addr.SDT_GH;
                        document.querySelector('#modal_dia_chi_chi_tiet').value = addr.DIA_CHI_CHI_TIET;
                        document.querySelector('#modal_is_default').checked = (addr.IS_DEFAULT == 1);
                        document.querySelector('#modal-address-id').value = addressId;
                        
                        // 2. Load Tỉnh/Thành phố
                        return fetch(apiHost + "?depth=1")
                            .then(res => res.json())
                            .then(provinces => {
                                const provinceSelect = document.querySelector("#province-modal");
                                provinceSelect.innerHTML = '<option value="">-- Chọn Tỉnh/Thành --</option>';
                                
                                provinces.forEach(p => {
                                    const option = new Option(p.name, p.code);
                                    if (p.code == addr.ID_TINH_TP) option.selected = true;
                                    provinceSelect.options.add(option);
                                });
                                
                                // Cập nhật hidden input
                                document.querySelector('#province-name-modal').value = addr.TEN_TINH_TP;
                                
                                // 3. Load Quận/Huyện
                                return fetch(apiHost + "p/" + addr.ID_TINH_TP + "?depth=2");
                            })
                            .then(res => res.json())
                            .then(data => {
                                const districtSelect = document.querySelector("#district-modal");
                                districtSelect.innerHTML = '<option value="">-- Chọn Quận/Huyện --</option>';
                                
                                data.districts.forEach(d => {
                                    const option = new Option(d.name, d.code);
                                    if (d.code == addr.ID_QUAN_HUYEN) option.selected = true;
                                    districtSelect.options.add(option);
                                });
                                districtSelect.disabled = false;
                                
                                // Cập nhật hidden input
                                document.querySelector('#district-name-modal').value = addr.TEN_QUAN_HUYEN;
                                
                                // 4. Load Xã/Phường
                                return fetch(apiHost + "d/" + addr.ID_QUAN_HUYEN + "?depth=2");
                            })
                            .then(res => res.json())
                            .then(data => {
                                const wardSelect = document.querySelector("#ward-modal");
                                wardSelect.innerHTML = '<option value="">-- Chọn Xã/Phường --</option>';
                                
                                data.wards.forEach(w => {
                                    const option = new Option(w.name, w.code);
                                    if (w.code == addr.ID_XA_PHUONG) option.selected = true;
                                    wardSelect.options.add(option);
                                });
                                wardSelect.disabled = false;
                                
                                // Cập nhật hidden input
                                document.querySelector('#ward-name-modal').value = addr.TEN_XA_PHUONG;
                            });
                    } else {
                        showToast('Không tìm thấy thông tin địa chỉ', 'error');
                    }
                })
                .then(() => {
                    // 5. Chuyển sang chế độ SỬA và hiện modal
                    document.querySelector('#modal-title').textContent = 'Sửa địa chỉ';
                    document.querySelector('#modal-submit-button').textContent = 'Cập nhật địa chỉ';
                    document.querySelector('#form-address-modal').action = BASE_PATH + '/account/handleAddAddress';
                    document.querySelector('#address-modal-overlay').style.display = 'flex';
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
        if (e.target && e.target.classList.contains('delete-address-btn')) {
            e.preventDefault();

            const addressId = e.target.dataset.id;

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
                        const cartCountSpan = document.querySelector('.cart-count');
                        if (cartCountSpan) {
                            cartCountSpan.textContent = data.cartCount;
                        }
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

});

/* =======================================================
 * HÀM HELPER: VẼ LẠI DANH SÁCH ĐỊA CHỈ (TRANG CHECKOUT)
 * (Đặt các hàm này ở ngoài cùng, cuối file)
 * ======================================================= */
function updateAddressList(addresses) {
    const listElement = document.querySelector('.address-list-checkout');
    const checkoutButton = document.querySelector('.btn-checkout');
    
    if (!listElement) return; 
    
    listElement.innerHTML = ''; 
    
    if (!addresses || addresses.length === 0) {
        listElement.innerHTML = '<p style="color: red; font-weight: 600;">Bạn chưa có địa chỉ nào!</p><p>Vui lòng thêm địa chỉ mới.</p>';
        if (checkoutButton) {
            checkoutButton.disabled = true;
            checkoutButton.textContent = 'Vui lòng thêm địa chỉ';
        }
        return;
    }

    // Có địa chỉ -> Bật nút Đặt hàng
    if (checkoutButton) {
        checkoutButton.disabled = false;
        checkoutButton.textContent = 'ĐẶT HÀNG';
    }

    // Kiểm tra xem có địa chỉ mặc định không
    const hasDefault = addresses.some(a => a.IS_DEFAULT == 1);

    // Lặp và tạo HTML
    addresses.forEach((addr, index) => {
        let isChecked = false;
        // Logic chọn radio: Ưu tiên mặc định, nếu không có thì chọn cái đầu tiên
        if (hasDefault) {
            if (addr.IS_DEFAULT == 1) isChecked = true;
        } else {
            if (index === 0) isChecked = true;
        }

        const defaultTag = (addr.IS_DEFAULT == 1) ? '<span class="default-tag-small">Mặc định</span>' : '';
        const checkedAttr = isChecked ? 'checked' : '';

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