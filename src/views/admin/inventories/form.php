<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Tạo Phiếu Nhập Kho</h3></div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <form action="<?= BASE_PATH ?>/admin/inventories/store" method="POST">
                
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">Thông tin phiếu nhập</div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 form-group mb-3">
                                <label class="fw-bold">Nhà cung cấp <span class="text-danger">*</span></label>
                                <select name="id_ncc" class="form-select" required>
                                    <option value="">-- Chọn Nhà cung cấp --</option>
                                    <?php foreach ($suppliers as $ncc): ?>
                                        <option value="<?= $ncc['ID_NCC'] ?>"><?= $ncc['TEN_NCC'] ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 form-group mb-3">
                                <label class="fw-bold">Ngày nhập</label>
                                <input type="datetime-local" name="ngay_lap" class="form-control" value="<?= date('Y-m-d\TH:i') ?>" required>
                            </div>
                            <div class="col-md-12 form-group">
                                <label class="fw-bold">Chứng từ gốc (Số hóa đơn giấy)</label>
                                <input type="text" name="chung_tu" class="form-control" placeholder="Ví dụ: HD00123...">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                        <span>Chi tiết hàng nhập</span>
                        <button type="button" class="btn btn-light btn-sm text-success fw-bold" onclick="addRow()">
                            <i class="bi bi-plus-circle"></i> Thêm dòng
                        </button>
                    </div>
                    <div class="card-body p-0">
                        <table class="table table-bordered mb-0" id="tbl-details">
                            <thead class="bg-light">
                                <tr>
                                    <th width="40%">Sản phẩm</th>
                                    <th width="15%">Số lượng</th>
                                    <th width="20%">Đơn giá nhập</th>
                                    <th width="20%">Thành tiền</th>
                                    <th width="5%">Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        <select name="product_id[]" class="form-select" required onchange="updateTotal()">
                                            <option value="">-- Chọn sản phẩm --</option>
                                            <?php foreach ($products as $p): ?>
                                                <option value="<?= $p['ID_HH'] ?>"><?= $p['ID_HH'] ?> - <?= htmlspecialchars($p['TEN_HH']) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="quantity[]" class="form-control" min="1" value="1" oninput="updateTotal()" required>
                                    </td>
                                    <td>
                                        <input type="number" name="price[]" class="form-control" min="0" value="0" oninput="updateTotal()" required>
                                    </td>
                                    <td class="align-middle fw-bold text-end row-total">0</td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)"><i class="bi bi-trash"></i></button>
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot class="bg-light fw-bold">
                                <tr>
                                    <td colspan="3" class="text-end">TỔNG CỘNG:</td>
                                    <td class="text-end text-danger fs-5" id="grand-total">0</td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <div class="card-footer text-end">
                        <a href="<?= BASE_PATH ?>/admin/inventories" class="btn btn-secondary">Hủy</a>
                        <button type="submit" class="btn btn-primary">Lưu phiếu nhập</button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>

<script>
    // JS đơn giản để thêm dòng và tính tiền
    function addRow() {
        const table = document.getElementById('tbl-details').getElementsByTagName('tbody')[0];
        const newRow = table.rows[0].cloneNode(true);
        // Reset giá trị inputs
        newRow.querySelectorAll('input').forEach(input => input.value = input.name.includes('quantity') ? 1 : 0);
        newRow.querySelector('select').value = "";
        newRow.querySelector('.row-total').innerText = "0";
        table.appendChild(newRow);
    }

    function removeRow(btn) {
        const row = btn.parentNode.parentNode;
        const tbody = document.getElementById('tbl-details').getElementsByTagName('tbody')[0];
        if (tbody.rows.length > 1) {
            row.parentNode.removeChild(row);
            updateTotal();
        } else {
            alert("Phải có ít nhất 1 dòng!");
        }
    }

    function updateTotal() {
        let grandTotal = 0;
        const rows = document.querySelectorAll('#tbl-details tbody tr');
        rows.forEach(row => {
            const qty = parseFloat(row.querySelector('input[name="quantity[]"]').value) || 0;
            const price = parseFloat(row.querySelector('input[name="price[]"]').value) || 0;
            const total = qty * price;
            
            row.querySelector('.row-total').innerText = new Intl.NumberFormat('vi-VN').format(total);
            grandTotal += total;
        });
        document.getElementById('grand-total').innerText = new Intl.NumberFormat('vi-VN').format(grandTotal) + ' đ';
    }
</script>