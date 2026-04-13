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
                    <h3 class="mb-0">
                        <?php echo $isEdit ? "Cập nhật" : "Thêm mới"; ?> Loại hàng hóa
                    </h3>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-end">
                        <li class="breadcrumb-item">
                            <a href="<?php echo BASE_PATH; ?>/admin/dashboard">Home</a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="<?php echo BASE_PATH; ?>/admin/product-types">Loại hàng</a>
                        </li>
                        <li class="breadcrumb-item active">Form</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-outline card-info">
                <div class="card-header">
                    <h3 class="card-title">Thông tin loại hàng</h3>
                </div>

                <div class="card-body">
                    <form action="<?php echo BASE_PATH; ?>/admin/product-types/<?php echo $isEdit ? 'update' : 'store'; ?>" method="POST">

                        <?php if ($isEdit && isset($productType)): ?>
                            <input type="hidden" name="id" value="<?php echo $productType['id_loai2']; ?>">

                            <div class="form-group mb-3">
                                <label class="form-label fw-bold">Mã Loại:</label>
                                <input type="text" class="form-control"
                                       value="<?php echo $productType['id_loai2']; ?>" disabled>
                            </div>
                        <?php endif; ?>

                        <!-- Danh mục -->
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">
                                Chọn Danh mục cha <span class="text-danger">*</span>
                            </label>
                            <select name="id_dm" class="form-select" required>
                                <option value="">-- Chọn danh mục --</option>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?php echo $cat['id_dm']; ?>"
                                        <?php echo (isset($productType['id_dm']) && $productType['id_dm'] == $cat['id_dm']) ? 'selected' : ''; ?>>
                                        <?php echo $cat['ten_dm']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <!-- Tên loại -->
                        <div class="form-group mb-3">
                            <label class="form-label fw-bold">
                                Tên Loại hàng <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="ten_loai" class="form-control"
                                   value="<?php echo isset($productType['ten_loai']) ? htmlspecialchars($productType['ten_loai']) : ''; ?>"
                                   placeholder="Nhập tên loại..." required>
                        </div>

                        <!-- ===== PHẠM VI ÁP DỤNG ===== -->
                        <div class="form-group mb-3 border p-3 rounded bg-light">
                            <label class="form-label fw-bold text-primary">
                                Phạm vi áp dụng (Tùy chọn)
                            </label>

                            <!-- MANUAL -->
                            <div class="form-check mb-2">
                                <input class="form-check-input scope-radio"
                                       type="radio"
                                       name="scope"
                                       id="scope_manual"
                                       value="manual"
                                       checked>
                                <label class="form-check-label" for="scope_manual">
                                    Chỉ tạo khuyến mãi (Tìm và chọn lô hàng cụ thể)
                                </label>
                            </div>

                            <div class="ms-4 mb-3" id="manual_search_box">
                                <div class="input-group input-group-sm" style="max-width: 400px;">
                                    <input type="text" id="search_product_input"
                                           class="form-control"
                                           placeholder="Nhập mã hoặc tên hàng hóa...">
                                    <button class="btn btn-outline-primary"
                                            type="button"
                                            id="btn_search_batch">
                                        Tìm lô hàng
                                    </button>
                                </div>

                                <div id="batch_list_results"
                                     class="mt-2 border rounded bg-white"
                                     style="max-height: 250px; overflow-y: auto; display: none;">
                                </div>
                            </div>

                            <!-- AUTO -->
                            <div class="form-check mb-2">
                                <input class="form-check-input scope-radio"
                                       type="radio"
                                       name="scope"
                                       id="scope_auto"
                                       value="category">
                                <label class="form-check-label" for="scope_auto">
                                    Áp dụng tự động cho lô gần hết hạn của sản phẩm:
                                </label>
                            </div>

                            <div class="ms-4 mt-2" id="category_select_box" style="display: none;">
                                <select name="id_hh" class="form-select">
                                    <option value="">-- Chọn sản phẩm --</option>
                                    <?php if(!empty($loai_hang)): foreach($loai_hang as $hh): ?>
                                        <option value="<?= $hh['id_hh'] ?>">
                                            <?= $hh['ten_hh'] ?>
                                        </option>
                                    <?php endforeach; endif; ?>
                                </select>
                            </div>
                        </div>
                        <!-- ===== END ===== -->

                        <!-- BUTTON -->
                        <div class="card-footer bg-white ps-0 text-end">
                            <a href="<?php echo BASE_PATH; ?>/admin/product-types"
                               class="btn btn-default border">
                                Thoát
                            </a>

                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Lưu lại
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- ===== JAVASCRIPT ===== -->
<script>
// Toggle hiển thị
document.querySelectorAll('.scope-radio').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('manual_search_box').style.display =
            (this.value === 'manual') ? 'block' : 'none';

        document.getElementById('category_select_box').style.display =
            (this.value === 'category') ? 'block' : 'none';
    });
});

// AJAX tìm lô hàng
document.getElementById('btn_search_batch').addEventListener('click', function() {
    const keyword = document.getElementById('search_product_input').value;
    const resultBox = document.getElementById('batch_list_results');

    if (keyword.length < 2) {
        alert('Nhập ít nhất 2 ký tự');
        return;
    }

    fetch('<?= BASE_PATH ?>/admin/promotions/search-batches?query=' + encodeURIComponent(keyword))
        .then(res => res.text())
        .then(data => {
            resultBox.innerHTML = data;
            resultBox.style.display = 'block';
        });
});
</script>

<?php
require_once __DIR__ . '/../layouts/footer.php';
?>