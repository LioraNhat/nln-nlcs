<?php 
require_once __DIR__ . '/../layouts/header.php'; 
require_once __DIR__ . '/../layouts/sidebar.php';
?>

<main class="app-main">
    <div class="app-content">
        <div class="container-fluid">
            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Danh sách Hàng hóa</h3>
                    <div class="card-tools">
                        <a href="<?php echo BASE_PATH; ?>/admin/products/create" class="btn btn-primary btn-sm">+ Thêm mới</a>
                    </div>
                </div>
                
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover align-middle">
                            <thead>
                                <tr class="text-center">
                                    <th>STT</th>
                                    <th>Hình ảnh</th>
                                    <th>Tên sản phẩm</th>
                                    <th>Danh mục</th>
                                    <th>ĐVT</th>
                                    <th>Lô & HSD sắp hết</th>
                                    <th>Tổng tồn</th>
                                    <th>Giá bán</th>
                                    <th>Hành động</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php 
                            if (!empty($products)):
                                $i = ($currentPage - 1) * 20 + 1;
                                foreach ($products as $row):
                                    $imgSrc = !empty($row['link_anh']) 
                                        ? BASE_PATH . '/uploads/' . $row['link_anh'] 
                                        : BASE_PATH . '/admin_assets/assets/img/default-150x150.png';
                            ?>
                            <tr>
                                <td class="text-center"><?php echo $i++; ?></td>
                                <td class="text-center">
                                    <img src="<?php echo $imgSrc; ?>" class="img-thumbnail" style="width:50px;height:50px;object-fit:cover;">
                                </td>
                                <td>
                                    <p class="mb-0 fw-bold"><?php echo htmlspecialchars($row['ten_hh'] ?? ''); ?></p>
                                    <small class="text-muted">Mã: <?php echo $row['id_hh'] ?? ''; ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($row['ten_loai'] ?? 'Chưa phân loại'); ?></td>
                                <td class="text-center"><?php echo htmlspecialchars($row['dvt'] ?? ''); ?></td>
                                
                                <td>
                                    <?php if(!empty($row['id_lo'])): ?>
                                        <span class="badge bg-warning text-dark"><?php echo $row['id_lo']; ?></span><br>
                                        <small class="text-danger">HSD: <?php echo date('d/m/Y', strtotime($row['hsd_lo'])); ?></small>
                                    <?php else: ?>
                                        <span class="text-muted">N/A</span>
                                    <?php endif; ?>
                                </td>

                                <td class="text-center">
                                    <a href="javascript:void(0);" 
                                       class="fw-bold text-decoration-none view-batch-details" 
                                       data-id="<?php echo $row['id_hh']; ?>"
                                       data-name="<?php echo htmlspecialchars($row['ten_hh']); ?>">
                                        <?php echo $row['tong_ton'] ?? 0; ?>
                                    </a>
                                </td>

                                <td class="text-end fw-bold text-success">
                                    <?php echo number_format($row['gia_hien_tai'] ?? 0, 0, ',', '.'); ?>đ
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <a href="<?php echo BASE_PATH; ?>/admin/inventories/create?id_hh=<?php echo $row['id_hh']; ?>" class="btn btn-sm btn-success" title="Nhập lô mới"><i class="bi bi-plus-circle"></i></a>
                                        <a href="<?php echo BASE_PATH; ?>/admin/products/edit/<?php echo $row['id_hh']; ?>" class="btn btn-sm btn-info"><i class="bi bi-pencil"></i></a>
                                        <a href="javascript:void(0);" onclick="confirmDelete('<?php echo $row['id_hh']; ?>')" class="btn btn-sm btn-danger"><i class="bi bi-trash"></i></a>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; else: ?>
                                <tr><td colspan="9" class="text-center">Trống.</td></tr>
                            <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<div class="modal fade" id="batchDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chi tiết tồn kho: <span id="modalProductName"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <table class="table table-sm table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Mã Lô</th>
                            <th>Ngày nhập</th>
                            <th>Hạn sử dụng</th>
                            <th>Số lượng tồn</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody id="batchTableBody">
                        </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const viewLinks = document.querySelectorAll('.view-batch-details');

    viewLinks.forEach(link => {
        link.addEventListener('click', function() {
            const idHh = this.getAttribute('data-id');
            const productName = this.getAttribute('data-name');

            document.getElementById('modalProductName').innerText = productName;

            fetch(`<?= BASE_PATH ?>/admin/products/get-batches?id_hh=${idHh}`)
                .then(response => {
                    if (!response.ok) throw new Error("HTTP error " + response.status);
                    return response.json();
                })
                .then(data => {
                    let html = '';

                    if (Array.isArray(data) && data.length > 0) {
                        data.forEach(batch => {

                            // fallback nếu backend chưa có field mới
                            const ngayNhap = batch.ngay_nhap_hien_thi 
                                ?? batch.ngay_nhap 
                                ?? 'N/A';

                            html += `<tr>
                                <td>${batch.id_lo ?? ''}</td>
                                <td>${ngayNhap}</td>
                                <td>${batch.hsd_lo ?? ''}</td>
                                <td class="text-center">${batch.so_luong_con_lai ?? 0}</td>
                                <td>
                                    <span class="badge bg-${batch.color ?? 'secondary'}">
                                        ${batch.ten_trang_thai ?? 'Không xác định'}
                                    </span>
                                </td>
                            </tr>`;
                        });
                    } else {
                        html = '<tr><td colspan="5" class="text-center">Không còn lô hàng nào tồn kho.</td></tr>';
                    }

                    document.getElementById('batchTableBody').innerHTML = html;

                    const modalEl = document.getElementById('batchDetailModal');
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();
                })
                .catch(err => {
                    console.error("Lỗi lấy dữ liệu lô:", err);
                    document.getElementById('batchTableBody').innerHTML =
                        '<tr><td colspan="5" class="text-center text-danger">Lỗi tải dữ liệu!</td></tr>';
                });
        });
    });
});

function confirmDelete(id) {
    if (confirm('Bạn có chắc muốn xóa sản phẩm ' + id + '?')) {
        window.location.href = '<?= BASE_PATH ?>/admin/products/delete/' + id;
    }
}
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>