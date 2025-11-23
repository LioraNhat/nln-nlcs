<?php
// Lấy user từ session theo cấu trúc mới
$currentUser = $_SESSION['user'] ?? [];
?>

<div class="modal-overlay" id="address-modal-overlay">
    <div class="modal-content auth-form-container">
        <div class="modal-header">
            <h3 id="modal-title">Địa chỉ mới</h3> 
            <button type="button" class="btn-close-modal" id="btn-close-address-modal">X</button>
        </div>
        
        <form action="<?php echo BASE_PATH; ?>/account/handleAddAddress" method="POST" id="form-address-modal">
            
            <input type="hidden" name="id_dia_chi" id="modal-address-id" aria-hidden="true">
            
            <div class="form-group">
                <label for="modal_ho_ten">Họ và Tên người nhận:</label>
                <input type="text" id="modal_ho_ten" name="ten_nguoi_nhan" 
                       value="<?php echo htmlspecialchars($currentUser['HO_TEN'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="modal_sdt_gh">Số điện thoại nhận hàng:</label>
                <input type="tel" id="modal_sdt_gh" name="sdt_gh" 
                       value="<?php echo htmlspecialchars($currentUser['SDT_TK'] ?? ''); ?>" required>
            </div>

            <div class="form-group">
                <label for="province-modal">Tỉnh / Thành phố:</label>
                <select id="province-modal" name="province" class="form-control-select" required>
                    <option value="">-- Chọn Tỉnh/Thành --</option>
                </select>
                <input type="hidden" name="tinh_tp" id="province-name-modal" aria-hidden="true">
            </div>

            <div class="form-group">
                <label for="district-modal">Quận / Huyện:</label>
                <select id="district-modal" name="district" class="form-control-select" required>
                    <option value="">-- Chọn Quận/Huyện --</option>
                </select>
                <input type="hidden" name="quan_huyen" id="district-name-modal" aria-hidden="true">
            </div>

            <div class="form-group">
                <label for="ward-modal">Xã / Phường:</label>
                <select id="ward-modal" name="ward" class="form-control-select" required>
                    <option value="">-- Chọn Xã/Phường --</option>
                </select>
                <input type="hidden" name="xa_phuong" id="ward-name-modal" aria-hidden="true">
            </div>
            
            <div class="form-group">
                <label for="modal_dia_chi_chi_tiet">Địa chỉ chi tiết (Số nhà, tên đường...):</label>
                <input type="text" id="modal_dia_chi_chi_tiet" name="dia_chi_chi_tiet" 
                       placeholder="Ví dụ: 123/4B Hẻm 123, đường 30/4" required>
            </div>

            <div class="form-group">
                <input type="checkbox" id="modal_is_default" name="is_default" value="1" style="width: auto; height: auto;">
                <label for="modal_is_default" style="display: inline; font-weight: 400;">Đặt làm địa chỉ mặc định</label>
            </div>
            
            <button type="submit" class="btn-submit-auth" id="modal-submit-button">Lưu địa chỉ</button>
        </form>
    </div>
</div>