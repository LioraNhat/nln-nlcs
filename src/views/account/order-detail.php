<?php require_once __DIR__ . '/../partials/header.php'; ?>

<div class="container account-page-container">
    <div class="account-header">
        <h2>Chi tiết đơn hàng #<?php echo $order['id_dh']; ?></h2>
        <a href="<?php echo BASE_PATH; ?>/account/index" class="btn-secondary">&lt;&lt; Quay lại</a>
    </div>

    <div class="order-info-box" style="background:#fff;padding:20px;border:1px solid #ddd;border-radius:8px;margin-bottom:20px;">
        <div class="row" style="display:flex;flex-wrap:wrap;gap:20px;">
            
            <div style="flex:1;">
                <p>
                    <strong>Ngày đặt:</strong> 
                    <?php echo date('d/m/Y H:i', strtotime($order['ngay_gio_tao_don'])); ?>
                </p>

                <p>
                    <strong>Trạng thái đơn hàng:</strong>
                    <?php
                        $stt = $order['ten_trang_thai'];
                        $color = 'gray';

                        if($stt == 'Chờ xử lý') $color = 'orange';
                        if($stt == 'Đang giao hàng') $color = 'blue';
                        if($stt == 'Giao thành công') $color = 'green';
                        if($stt == 'Đã hủy') $color = 'red';
                    ?>
                    <span style="color:<?php echo $color; ?>;font-weight:bold;">
                        <?php echo $stt; ?>
                    </span>
                </p>

                <p>
                    <strong>Trạng thái thanh toán:</strong>

                    <?php if ($order['trang_thai_thanh_toan'] == 1): ?>

                        <span style="color:green;font-weight:bold;">
                            <i class="fa fa-check-circle"></i> Đã thanh toán
                        </span>

                        <?php if(!empty($order['ngay_thanh_toan']) && $order['ngay_thanh_toan'] != '0000-00-00 00:00:00'): ?>
                            <br>
                            <small>
                                (Lúc: <?php echo date('d/m/Y H:i', strtotime($order['ngay_thanh_toan'])); ?>)
                            </small>
                        <?php endif; ?>

                    <?php else: ?>
                        <span style="color:red;">Chưa thanh toán</span>

                        <?php if (($order['id_pttt'] ?? '') == 'PTTT4' && ($order['id_ttd'] ?? '') != 'TTD05'): ?>
                            <br><br>
                            <form action="<?= BASE_PATH ?>/payment/zalopay" method="POST">
                                <input type="hidden" name="id_dh" value="<?= $order['id_dh'] ?>">
                                <button type="submit" style="background:#0068ff;color:#fff;border:none;padding:10px 20px;border-radius:6px;font-weight:bold;cursor:pointer;">
                                    💙 Thanh toán qua ZaloPay
                                </button>
                            </form>
                        <?php endif; ?>
                    <?php endif; ?>
                </p>
            </div>

            <div style="flex:1;">
                <p>
                    <strong>Người nhận:</strong> 
                    <?php echo htmlspecialchars($order['ten_nguoi_nhan'] ?? $order['ho_ten']); ?>
                </p>

                <p>
                    <strong>Địa chỉ giao hàng:</strong><br>

                    <?php
                        $diaChi = 
                        $order['dia_chi_chi_tiet'] . ', ' .
                        $order['ten_xa_phuong'] . ', ' .
                        $order['ten_quan_huyen'] . ', ' .
                        $order['ten_tinh_tp'];

                        echo htmlspecialchars($diaChi);
                    ?>
                </p>
            </div>

        </div>
    </div>

    <h3>Sản phẩm đã mua</h3>

    <div class="data-table-wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Sản phẩm</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Thành tiền</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach ($items as $item): ?>
                <tr>

                    <td>
                        <div style="display:flex;align-items:center;">
                            
                            <?php if(!empty($item['link_anh'])): ?>
                                <img 
                                    src="<?php echo BASE_PATH . '/uploads/' . $item['link_anh']; ?>" 
                                    width="50"
                                    style="margin-right:10px;border:1px solid #eee;">
                            <?php endif; ?>

                            <div>
                                <strong>
                                    <?php echo htmlspecialchars($item['ten_hh']); ?>
                                </strong>

                                <br>

                                <small>
                                    <?php echo htmlspecialchars($item['dvt'] ?? ''); ?>
                                </small>
                            </div>

                        </div>
                    </td>

                    <td>
                        <?php echo number_format($item['don_gia']); ?> đ
                    </td>

                    <td style="text-align:center;">
                        <?php echo $item['so_luong_ban_ra']; ?>
                    </td>

                    <td style="font-weight:bold;">
                        <?php echo number_format($item['don_gia'] * $item['so_luong_ban_ra']); ?> đ
                    </td>

                </tr>
                <?php endforeach; ?>
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="3" style="text-align:right;">
                        Tổng tiền hàng:
                    </td>
                    <td style="font-weight:bold;">
                        <?php echo number_format($order['tong_gia_tri_don']); ?> đ
                    </td>
                </tr>

                <tr>
                    <td colspan="3" style="text-align:right;color:green;">
                        Giảm giá:
                    </td>
                    <td style="font-weight:bold;color:green;">
                        -<?php echo number_format($order['tien_giam_gia']); ?> đ
                    </td>
                </tr>

                <tr style="background-color:#f9f9f9;">
                    <td colspan="3" style="text-align:right;font-size:1.2em;">
                        TỔNG THANH TOÁN:
                    </td>

                    <td style="font-weight:bold;color:#d32f2f;font-size:1.2em;">
                        <?php echo number_format($order['thanh_tien']); ?> đ
                    </td>
                </tr>
            </tfoot>

        </table>
    </div>
</div>

<?php require_once __DIR__ . '/../partials/footer.php'; ?>