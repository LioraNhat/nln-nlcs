<?php
require_once __DIR__ . '/../layouts/header.php';
require_once __DIR__ . '/../layouts/sidebar.php';

// Mảng các trạng thái có thể có
$statusList = ['Chờ xử lý', 'Đã xác nhận', 'Đang giao hàng', 'Giao hàng thành công', 'Đã hủy'];
?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Chi tiết Đơn hàng: #<?= $order['ID_DH'] ?></h3></div>
                <div class="col-sm-6 text-end">
                    <a href="<?= BASE_PATH ?>/admin/orders" class="btn btn-secondary">Quay lại danh sách</a>
                </div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success alert-dismissible fade show"><?= $success ?> <button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white">
                            <i class="bi bi-info-circle"></i> Thông tin chung
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Người đặt:</strong> <?= htmlspecialchars($order['HO_TEN']) ?> <br>
                                    <strong>SĐT:</strong> <?= htmlspecialchars($order['SDT_TK']) ?> <br>
                                    <strong>Email:</strong> <?= htmlspecialchars($order['EMAIL']) ?>
                                </div>
                                <div class="col-md-6 text-md-end">
                                    <strong>Ngày đặt:</strong> <?= date('d/m/Y H:i', strtotime($order['NGAY_GIO_TAO_DON'])) ?> <br>
                                    <strong>Phương thức TT:</strong> <?= $order['ID_PTTT'] == 'PTTT1' ? 'COD (Tiền mặt)' : 'Chuyển khoản/Ví' ?>
                                </div>
                            </div>
                            <hr>
                            <strong>Địa chỉ giao hàng:</strong>
                            <p class="text-muted"><?= nl2br(htmlspecialchars($order['DIA_CHI_GIAO_DH'])) ?></p>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Danh sách sản phẩm</h3>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Sản phẩm</th>
                                        <th class="text-center">ĐVT</th>
                                        <th class="text-center">SL</th>
                                        <th class="text-end">Đơn giá</th>
                                        <th class="text-end">Thành tiền</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($items as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php if($item['link_anh']): ?>
                                                        <img src="<?= BASE_PATH ?>/uploads/<?= $item['link_anh'] ?>" width="40" height="40" class="me-2 rounded">
                                                    <?php endif; ?>
                                                    <?= htmlspecialchars($item['TEN_HH']) ?>
                                                </div>
                                            </td>
                                            <td class="text-center"><?= $item['DVT'] ?></td>
                                            <td class="text-center fw-bold"><?= $item['SO_LUONG_BAN_RA'] ?></td>
                                            <td class="text-end"><?= number_format($item['don_gia_ban']) ?>đ</td>
                                            <td class="text-end fw-bold"><?= number_format($item['SO_LUONG_BAN_RA'] * $item['don_gia_ban']) ?>đ</td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-end">Tổng giá trị đơn hàng:</td>
                                        <td class="text-end fw-bold"><?= number_format($order['TONG_GIA_TRI_DH']) ?>đ</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end text-success">Giảm giá:</td>
                                        <td class="text-end fw-bold text-success">-<?= number_format($order['TIEN_GIAM_GIA']) ?>đ</td>
                                    </tr>
                                    <tr class="bg-light fs-5">
                                        <td colspan="4" class="text-end fw-bold text-danger">THỰC THU:</td>
                                        <td class="text-end fw-bold text-danger"><?= number_format($order['SO_TIEN_THANH_TOAN']) ?>đ</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card card-outline card-warning mb-3">
                        <div class="card-header">
                            <h3 class="card-title fw-bold">Trạng thái xử lý</h3>
                        </div>
                        <div class="card-body">
                            <form action="<?= BASE_PATH ?>/admin/order-update-status" method="POST">
                                <input type="hidden" name="id_dh" value="<?= $order['ID_DH'] ?>">
                                
                                <div class="mb-3">
                                    <label class="form-label">Trạng thái hiện tại:</label>
                                    <select name="trang_thai" class="form-select form-select-lg mb-3">
                                        <?php foreach ($statusList as $st): ?>
                                            <option value="<?= $st ?>" <?= $order['TRANG_THAI_DHHT'] == $st ? 'selected' : '' ?>>
                                                <?= $st ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        <i class="bi bi-check2-circle"></i> Cập nhật trạng thái
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card card-outline card-secondary">
                        <div class="card-header">
                            <h3 class="card-title">Thông tin thanh toán</h3>
                        </div>
                        <div class="card-body">
                            <p><strong>Trạng thái:</strong> <?= $order['TRANG_THAI_THANH_TOAN'] ?></p>
                            <?php if ($order['NGAY_THANH_TOAN'] && $order['NGAY_THANH_TOAN'] != '0000-00-00 00:00:00'): ?>
                                <p><strong>Ngày TT:</strong> <?= date('d/m/Y H:i', strtotime($order['NGAY_THANH_TOAN'])) ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>