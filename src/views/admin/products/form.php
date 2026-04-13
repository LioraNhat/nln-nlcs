<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';

// Kiểm tra trạng thái và chuẩn bị tiêu đề
$isEdit = $isEdit ?? false;
$formTitle = $isEdit ? "Cập nhật Sản phẩm: " . htmlspecialchars($product['ten_hh'] ?? '') : "Thêm Sản phẩm mới";
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6">
                    <h3 class="mb-0"><?php echo $formTitle; ?></h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="<?php echo BASE_PATH; ?>/admin/dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo BASE_PATH; ?>/admin/products">Hàng hóa</a></li>
                        <li class="breadcrumb-item active">Form</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <form action="<?php echo BASE_PATH; ?>/admin/products/store" method="POST" enctype="multipart/form-data">
                
                <input type="hidden" name="id" value="<?php echo $isEdit ? ($product['id_hh'] ?? '') : ''; ?>">

                <div class="card card-outline card-primary shadow-sm">
                    <div class="card-header">
                        <h3 class="card-title fw-bold">Thông tin chi tiết hàng hóa</h3>
                    </div>
                    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 border-end">
                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                                    <input type="text" name="ten_hh" class="form-control" required 
                                           value="<?php echo $isEdit ? htmlspecialchars($product['ten_hh'] ?? '') : ''; ?>"
                                           placeholder="Ví dụ: Ba Rọi Chiên Nước Mắm">
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-bold">Loại hàng <span class="text-danger">*</span></label>
                                            <select name="id_lhh" class="form-select" required>
                                                <option value="">-- Chọn loại hàng --</option>
                                                <?php foreach ($loai_hang as $lh): ?>
                                                    <option value="<?php echo $lh['id_loai2']; ?>" 
                                                        <?php echo ($isEdit && ($product['id_loai2'] ?? '') == $lh['id_loai2']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($lh['ten_loai']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-bold">Đơn vị tính <span class="text-danger">*</span></label>
                                            <select name="id_dvt" class="form-select" required>
                                                <option value="">-- Chọn ĐVT --</option>
                                                <?php foreach ($dvt as $d): ?>
                                                    <option value="<?php echo $d['id_dvt']; ?>" 
                                                        <?php echo ($isEdit && ($product['id_dvt'] ?? '') == $d['id_dvt']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($d['dvt']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Mô tả sản phẩm</label>
                                    <textarea name="mo_ta_hh" class="form-control" rows="6" 
                                              placeholder="Thành phần, định lượng, cách chế biến..."><?php echo $isEdit ? htmlspecialchars($product['mo_ta_hh'] ?? '') : ''; ?></textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="card bg-light mb-3">
                                    <div class="card-body">
                                        <h6 class="fw-bold text-primary"><i class="bi bi-info-circle"></i> Cơ chế giá bán</h6>
                                        <small class="text-muted d-block mb-2">
                                            Giá bán sẽ tự động tính khi bạn thực hiện <strong>Nhập lô hàng</strong>.
                                        </small>
                                        <hr>
                                        <div class="form-group mb-0">
                                            <label class="form-label fw-bold">% Lợi nhuận <span class="text-danger">*</span></label>
                                            <div class="input-group mb-2">
                                                <input type="number" name="phan_tram_loi_nhuan" class="form-control" 
                                                       min="0" max="1000" step="0.01" required
                                                       value="<?php echo $isEdit ? ($product['phan_tram_loi_nhuan'] ?? 30) : 30; ?>">
                                                <span class="input-group-text">%</span>
                                            </div>
                                            <div class="form-check form-switch mb-0">
                                                <input class="form-check-input" type="checkbox" name="duoc_phep_ban" value="1"
                                                    <?php echo (!$isEdit || ($isEdit && ($product['duoc_phep_ban'] ?? 0) == 1)) ? 'checked' : ''; ?>>
                                                <label class="form-check-label fw-bold small">Cho phép bán</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Hình ảnh đại diện</label>
                                    <input type="file" name="img" class="form-control mb-2" accept="image/*" onchange="previewImage(this)">
                                    
                                    <input type="hidden" name="old_img" value="<?php echo $isEdit ? ($product['link_anh'] ?? '') : ''; ?>">
                                    
                                    <div class="p-2 border rounded bg-light text-center">
                                        <?php 
                                            $imgSrc = ($isEdit && !empty($product['link_anh'])) 
                                                ? BASE_PATH . '/uploads/' . $product['link_anh'] 
                                                : BASE_PATH . '/admin_assets/assets/img/default-150x150.png'; 
                                        ?>
                                        <img id="preview" src="<?php echo $imgSrc; ?>" alt="Preview" 
                                             style="max-width: 100%; max-height: 200px; object-fit: contain;" class="rounded shadow-sm">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-end bg-white">
                        <a href="<?php echo BASE_PATH; ?>/admin/products" class="btn btn-secondary me-2">Hủy bỏ</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save"></i> Lưu dữ liệu
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<script>
// Hàm xử lý xem trước ảnh khi chọn file
function previewImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('preview').setAttribute('src', e.target.result);
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>