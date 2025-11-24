<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';

// Xác định tiêu đề và action form
$formTitle = $isEdit ? "Cập nhật Sản phẩm: " . htmlspecialchars($product['TEN_HH']) : "Thêm Sản phẩm mới";
$currentId = $isEdit ? $product['ID_HH'] : '';
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0"><?php echo $isEdit ? "Cập nhật" : "Thêm mới"; ?></h3></div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item"><a href="<?php echo BASE_PATH; ?>/admin/dashboard">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo BASE_PATH; ?>/admin/products">Sản phẩm</a></li>
                        <li class="breadcrumb-item active">Form</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <form action="<?php echo BASE_PATH; ?>/admin/products/store" method="POST" enctype="multipart/form-data" id="product-form">
                <input type="hidden" name="id" value="<?php echo $currentId; ?>">
                
                <div class="card card-outline card-primary">
                    <div class="card-header">
                        <h3 class="card-title"><?php echo $formTitle; ?></h3>
                    </div>
                    
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group mb-3">
                                    <label for="ten_hh" class="form-label fw-bold">Tên sản phẩm <span class="text-danger">*</span></label>
                                    <input type="text" name="ten_hh" id="ten_hh" class="form-control" required 
                                           value="<?php echo $isEdit ? htmlspecialchars($product['TEN_HH']) : ''; ?>" placeholder="Nhập tên hàng hóa...">
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="id_lhh" class="form-label fw-bold">Danh mục / Loại hàng <span class="text-danger">*</span></label>
                                            <select name="id_lhh" id="id_lhh" class="form-select" required>
                                                <option value="">-- Chọn loại hàng --</option>
                                                <?php foreach ($loai_hang as $lh): ?>
                                                    <option value="<?php echo $lh['ID_LHH']; ?>" 
                                                        <?php echo ($isEdit && $product['ID_LHH'] == $lh['ID_LHH']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($lh['TEN_LHH']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label for="id_dvt" class="form-label fw-bold">Đơn vị tính <span class="text-danger">*</span></label>
                                            <select name="id_dvt" id="id_dvt" class="form-select" required>
                                                <?php foreach ($dvt as $d): ?>
                                                    <option value="<?php echo $d['ID_DVT']; ?>" 
                                                        <?php echo ($isEdit && $product['ID_DVT'] == $d['ID_DVT']) ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($d['DVT']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <label for="mo_ta_hh" class="form-label fw-bold">Mô tả sản phẩm</label>
                                    <textarea name="mo_ta_hh" id="mo_ta_hh" class="form-control" rows="5"><?php echo $isEdit ? htmlspecialchars($product['MO_TA_HH']) : ''; ?></textarea>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group mb-3">
                                    <label for="gia_ban" class="form-label fw-bold">Giá bán hiện tại (VNĐ) <span class="text-danger">*</span></label>
                                    <input type="number" name="gia_ban" id="gia_ban" class="form-control" required 
                                           value="<?php echo $isEdit ? intval($product['GIA_HIEN_TAI']) : ''; ?>" placeholder="0">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="so_luong_ton" class="form-label fw-bold">Số lượng tồn kho <span class="text-danger">*</span></label>
                                    <input type="number" name="so_luong_ton" id="so_luong_ton" class="form-control" required 
                                           value="<?php echo $isEdit ? intval($product['SO_LUONG_TON_HH']) : '0'; ?>">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="hsd" class="form-label fw-bold">Hạn sử dụng <span class="text-danger">*</span></label>
                                    <?php 
                                        $hsdValue = '';
                                        if($isEdit && !empty($product['HSD'])) {
                                            $hsdValue = date('Y-m-d', strtotime($product['HSD']));
                                        }
                                    ?>
                                    <input type="date" name="hsd" id="hsd" class="form-control" required value="<?php echo $hsdValue; ?>">
                                </div>

                                <div class="form-group mb-3">
                                    <label for="id_km" class="form-label fw-bold">Chương trình khuyến mãi</label>
                                    <select name="id_km" id="id_km" class="form-select">
                                        <option value="">-- Không áp dụng --</option>
                                        <?php foreach ($khuyen_mai as $km): ?>
                                            <option value="<?php echo $km['ID_KM']; ?>" 
                                                <?php echo ($isEdit && $product['ID_KM'] == $km['ID_KM']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($km['TEN_KM']); ?> (-<?php echo intval($km['PHAN_TRAM_KM']); ?>%)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="form-group mb-3">
                                    <label class="form-label fw-bold">Ảnh sản phẩm</label>
                                    <input type="file" name="img" class="form-control" accept="image/*" onchange="previewImage(this)">
                                    <input type="hidden" name="old_img" value="<?php echo $isEdit ? $product['link_anh'] : ''; ?>">
                                    
                                    <div class="mt-2 text-center border p-2" style="min-height: 150px; background: #f8f9fa;">
                                        <?php 
                                            $imgShow = ($isEdit && !empty($product['link_anh'])) 
                                                ? BASE_PATH . '/uploads/' . $product['link_anh'] 
                                                : BASE_PATH . '/admin_assets/assets/img/default-150x150.png';
                                        ?>
                                        <img id="preview" src="<?php echo $imgShow; ?>" style="max-width: 100%; max-height: 200px; object-fit: contain;">
                                    </div>
                                </div>

                                <div class="form-group mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" role="switch" id="duoc_phep_ban" name="duoc_phep_ban" 
                                            <?php echo (!$isEdit || ($isEdit && $product['DUOC_PHEP_BAN'] == 1)) ? 'checked' : ''; ?>>
                                        <label class="form-check-label fw-bold" for="duoc_phep_ban">Đang kinh doanh</label>
                                    </div>
                                    <small class="text-muted">Tắt nếu muốn ngừng bán sản phẩm này.</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer text-end">
                        <a href="<?php echo BASE_PATH; ?>/admin/products" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Lưu dữ liệu
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<script>
    // Hàm xem trước ảnh khi chọn file
    function previewImage(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function (e) {
                document.getElementById('preview').src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>