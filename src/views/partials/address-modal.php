<div class="modal-overlay" id="address-modal-overlay">
    <div class="modal-content auth-form-container">
        <div class="modal-header">
            <h3 id="modal-title">Địa chỉ mới</h3> <button type="button" class="btn-close-modal" id="btn-close-address-modal">X</button>
        </div>
        
        <form action="" method="POST" id="form-address-modal">
            <input type="hidden" name="address_id" id="modal-address-id">
            <input type="hidden" name="_method" id="modal-method-field">
            <div class="form-group"><label for="modal_ho_ten">Họ và Tên người nhận:</label><input type="text" id="modal_ho_ten" name="ho_ten" value="<?php echo htmlspecialchars($user['name']); ?>" required></div>
            <div class="form-group"><label for="modal_sdt_gh">Số điện thoại nhận hàng:</label><input type="tel" id="modal_sdt_gh" name="sdt_gh" value="<?php echo htmlspecialchars($user['phone']); ?>" required></div>

            <div class="form-group"><label for="province-modal">Tỉnh / Thành phố:</label><select id="province-modal" name="province_code" class="form-control-select" required><option value="">-- Chọn Tỉnh/Thành --</option></select><input type="hidden" name="province_name" id="province-name-modal"></div>
            <div class="form-group"><label for="district-modal">Quận / Huyện:</label><select id="district-modal" name="district_code" class="form-control-select" required><option value="">-- Chọn Quận/Huyện --</option></select><input type="hidden" name="district_name" id="district-name-modal"></div>
            <div class="form-group"><label for="ward-modal">Xã / Phường:</label><select id="ward-modal" name="ward_code" class="form-control-select" required><option value="">-- Chọn Xã/Phường --</option></select><input type="hidden" name="ward_name" id="ward-name-modal"></div>
            
            <div class="form-group"><label for="modal_dia_chi_chi_tiet">Địa chỉ chi tiết (Số nhà, tên đường...):</label><input type="text" id="modal_dia_chi_chi_tiet" name="dia_chi_chi_tiet" placeholder="Ví dụ: 123/4B Hẻm 123, đường 30/4" required></div>
            <div class="form-group"><input type="checkbox" id="modal_is_default" name="is_default" value="1" style="width: auto; height: auto;"><label for="modal_is_default" style="display: inline; font-weight: 400;">Đặt làm địa chỉ mặc định</label></div>
            
            <button type="submit" class="btn-submit-auth" id="modal-submit-button">Lưu địa chỉ</button>
        </form>
    </div>
</div>