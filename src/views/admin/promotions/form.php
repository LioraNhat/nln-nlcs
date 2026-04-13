<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
$isEdit = $isEdit ?? false;
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0"><?= $isEdit ? "Cập nhật" : "Thêm mới" ?> Khuyến mãi</h3>
                </div>
                <div class="col-sm-6">
                     <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/admin/dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= BASE_PATH ?>/admin/promotions">Khuyến mãi</a></li>
                        <li class="breadcrumb-item active">Form</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-outline card-info">
                <div class="card-body">
                    <form action="<?= BASE_PATH ?>/admin/promotions/<?= $isEdit ? 'update' : 'store' ?>" method="POST">
                        <?php if ($isEdit && isset($promotion)): ?>
                            <input type="hidden" name="id" value="<?= $promotion['id_km'] ?>">
                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Mã Khuyến Mãi:</label>
                                <input type="text" class="form-control" value="<?= $promotion['id_km'] ?>" disabled>
                            </div>
                        <?php endif; ?>

                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Tên chương trình <span class="text-danger">*</span></label>
                            <input type="text" name="ten_km" class="form-control" 
                                   value="<?= $isEdit ? htmlspecialchars($promotion['ten_km'] ?? '') : '' ?>" required>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Phần trăm giảm (%) <span class="text-danger">*</span></label>
                            <input type="number" name="phan_tram_km" class="form-control" min="0" max="100" step="0.01"
                                   value="<?= $isEdit ? ($promotion['phan_tram_km'] ?? '') : '' ?>" required placeholder="Ví dụ: 10">
                        </div>

                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label fw-bold">Ngày bắt đầu <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="ngay_bd" class="form-control" 
                                       value="<?= ($isEdit && !empty($promotion['ngay_bd_km'])) ? date('Y-m-d\TH:i', strtotime($promotion['ngay_bd_km'])) : '' ?>" required>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="form-label fw-bold">Ngày kết thúc <span class="text-danger">*</span></label>
                                <input type="datetime-local" name="ngay_kt" class="form-control" 
                                       value="<?= ($isEdit && !empty($promotion['ngay_kt_km'])) ? date('Y-m-d\TH:i', strtotime($promotion['ngay_kt_km'])) : '' ?>" required>
                            </div>
                        </div>

                        <div class="form-group mb-3 border p-3 rounded bg-light">
                            <label class="form-label fw-bold text-primary">Phạm vi áp dụng (Tùy chọn)</label>
                            
                            <div class="form-check mb-2">
                                <input class="form-check-input scope-radio" type="radio" name="scope" id="scope_none" value="none" checked>
                                <label class="form-check-label" for="scope_none">
                                    Chỉ tạo khuyến mãi (Sẽ gán thủ công cho từng lô hàng sau)
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input scope-radio" type="radio" name="scope" id="scope_category" value="category">
                                <label class="form-check-label" for="scope_category">
                                    Áp dụng ngay cho Lô hàng sắp hết hạn của Sản phẩm:
                                </label>
                            </div>
                            
                            <div class="ms-4 mt-2" id="category_select_box" style="display: none;">
                                <select name="id_lhh" class="form-select">
                                    <option value="">-- Chọn Sản phẩm --</option>
                                    <?php 
                                        if(!empty($loai_hang)): 
                                            foreach($loai_hang as $hh):
                                    ?>
                                        <option value="<?= $hh['id_hh'] ?>"><?= $hh['ten_hh'] ?></option>
                                    <?php 
                                            endforeach; 
                                        endif; 
                                    ?>
                                </select>
                                <small class="text-danger d-block mt-1">
                                    <i class="bi bi-info-circle"></i> Hệ thống tự động gán KM vào Lô hàng có HSD gần nhất của sản phẩm này.
                                </small>
                            </div>
                        </div>

                        <?php if ($isEdit && isset($promotion)): ?>
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">Trạng thái đặc biệt</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="trang_thai" value="Đã hủy" 
                                    <?= ($promotion['trang_thai_km'] == 'Đã hủy') ? 'checked' : '' ?>>
                                <label class="form-check-label text-danger">Hủy chương trình này (Ngừng áp dụng)</label>
                            </div>
                            <small class="text-muted">Lưu ý: Trạng thái sẽ tự động cập nhật theo thời gian nếu không chọn Hủy.</small>
                        </div>
                        <?php endif; ?>

                        <div class="card-footer bg-white ps-0 text-end">
                            <a href="<?= BASE_PATH ?>/admin/promotions" class="btn btn-default border">Thoát</a>
                            <button type="submit" class="btn btn-primary"><i class="bi bi-save"></i> Lưu lại</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<script>
    // Hiển thị/Ẩn mục chọn sản phẩm dựa trên lựa chọn phạm vi
    document.querySelectorAll('.scope-radio').forEach(radio => {
        radio.addEventListener('change', function() {
            const catBox = document.getElementById('category_select_box');
            if (this.value === 'category') {
                catBox.style.display = 'block';
            } else {
                catBox.style.display = 'none';
            }
        });
    });
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>