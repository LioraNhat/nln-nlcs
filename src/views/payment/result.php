<?php require_once __DIR__ . '/../layouts/header.php'; ?>

<main class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 text-center">
            <?php if (($order['trang_thai_thanh_toan'] ?? 0) == 1): ?>
                <i class="bi bi-check-circle-fill text-success" style="font-size:5rem"></i>
                <h2 class="mt-3 text-success">Thanh toán thành công!</h2>
                <p>Đơn hàng <strong>#<?= $id_dh ?></strong> đã được thanh toán.</p>
            <?php else: ?>
                <i class="bi bi-x-circle-fill text-danger" style="font-size:5rem"></i>
                <h2 class="mt-3 text-danger">Thanh toán thất bại!</h2>
                <p>Đơn hàng <strong>#<?= $id_dh ?></strong> chưa được thanh toán.</p>
            <?php endif; ?>

            <a href="<?= BASE_PATH ?>/account/orders" class="btn btn-primary mt-3">
                Xem đơn hàng của tôi
            </a>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>