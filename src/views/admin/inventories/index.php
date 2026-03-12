<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Quản lý tồn kho</h3></div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            <?php if (!empty($success)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <?= $success ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <?= $error ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- BỘ LỌC -->
            <div class="card card-outline card-secondary mb-3">
                <div class="card-body">
                    <form method="GET" action="">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-3">
                                <label class="form-label">Tìm kiếm</label>
                                <input type="text" name="search" class="form-control" 
                                       placeholder="Mã / Tên sản phẩm..."
                                       value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Trạng thái</label>
                                <select name="trang_thai" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="TTL01" <?= ($_GET['trang_thai'] ?? '') == 'TTL01' ? 'selected' : '' ?>>Còn hàng</option>
                                    <option value="TTL02" <?= ($_GET['trang_thai'] ?? '') == 'TTL02' ? 'selected' : '' ?>>Sắp hết</option>
                                    <option value="TTL03" <?= ($_GET['trang_thai'] ?? '') == 'TTL03' ? 'selected' : '' ?>>Hết hàng</option>
                                    <option value="TTL04" <?= ($_GET['trang_thai'] ?? '') == 'TTL04' ? 'selected' : '' ?>>Sắp hết hạn</option>
                                    <option value="TTL05" <?= ($_GET['trang_thai'] ?? '') == 'TTL05' ? 'selected' : '' ?>>Hết hạn</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Tồn kho</label>
                                <select name="ton_kho" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="con_hang" <?= ($_GET['ton_kho'] ?? '') == 'con_hang' ? 'selected' : '' ?>>Còn hàng (> 0)</option>
                                    <option value="het_hang" <?= ($_GET['ton_kho'] ?? '') == 'het_hang' ? 'selected' : '' ?>>Hết hàng (= 0)</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">HSD</label>
                                <select name="hsd" class="form-select">
                                    <option value="">-- Tất cả --</option>
                                    <option value="7" <?= ($_GET['hsd'] ?? '') == '7' ? 'selected' : '' ?>>Trong 7 ngày</option>
                                    <option value="30" <?= ($_GET['hsd'] ?? '') == '30' ? 'selected' : '' ?>>Trong 30 ngày</option>
                                </select>
                            </div>
                            <div class="col-md-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-search"></i> Lọc
                                </button>
                                <a href="<?= BASE_PATH ?>/admin/inventories" class="btn btn-secondary w-100">
                                    <i class="bi bi-x"></i> Xóa lọc
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title">Danh sách hàng hóa & lô hàng</h3>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-hover align-middle">
                        <thead>
                            <tr class="text-center">
                                <th>Mã HH</th>
                                <th>Tên sản phẩm</th>
                                <th>Loại</th>
                                <th>Lô hiện tại</th>
                                <th>HSD gần nhất</th>
                                <th>Tồn kho</th>
                                <th>Trạng thái</th>
                                <th>Giá bán</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php 
                        // Lọc phía PHP
                        $filtered = $products ?? [];

                        // Lọc search
                        if (!empty($_GET['search'])) {
                            $kw = strtolower($_GET['search']);
                            $filtered = array_filter($filtered, fn($r) => 
                                str_contains(strtolower($r['id_hh']), $kw) || 
                                str_contains(strtolower($r['ten_hh']), $kw)
                            );
                        }

                        // Lọc trạng thái
                        if (!empty($_GET['trang_thai'])) {
                            $filtered = array_filter($filtered, fn($r) => 
                                ($r['id_trang_thai_lo'] ?? '') == $_GET['trang_thai']
                            );
                        }

                        // Lọc tồn kho
                        if (!empty($_GET['ton_kho'])) {
                            $filtered = array_filter($filtered, fn($r) => 
                                $_GET['ton_kho'] == 'con_hang' 
                                    ? ($r['so_luong_con_lai'] ?? 0) > 0 
                                    : ($r['so_luong_con_lai'] ?? 0) == 0
                            );
                        }

                        // Lọc HSD
                        if (!empty($_GET['hsd'])) {
                            $days = (int)$_GET['hsd'];
                            $filtered = array_filter($filtered, fn($r) => 
                                !empty($r['hsd_lo']) && 
                                strtotime($r['hsd_lo']) <= strtotime("+{$days} days")
                            );
                        }
                        ?>

                        <?php if (!empty($filtered)): foreach ($filtered as $row): ?>
                            <tr>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?= $row['id_hh'] ?></span>
                                </td>
                                <td>
                                    <p class="mb-0 fw-bold"><?= htmlspecialchars($row['ten_hh']) ?></p>
                                </td>
                                <td><?= htmlspecialchars($row['ten_loai'] ?? '') ?></td>
                                <td class="text-center">
                                    <?= !empty($row['id_lo']) 
                                        ? '<span class="badge bg-info">'.$row['id_lo'].'</span>' 
                                        : '<span class="text-muted">—</span>' ?>
                                </td>
                                <td class="text-center">
                                    <?php if (!empty($row['hsd_lo'])): ?>
                                        <?php
                                            $hsd = strtotime($row['hsd_lo']);
                                            $diff = $hsd - time();
                                            $days = ceil($diff / 86400);
                                            $cls = $days <= 7 ? 'text-danger fw-bold' : ($days <= 30 ? 'text-warning' : 'text-success');
                                        ?>
                                        <span class="<?= $cls ?>">
                                            <?= date('d/m/Y', $hsd) ?>
                                            <br><small>(<?= $days ?> ngày)</small>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php $ton = $row['so_luong_con_lai'] ?? 0; ?>
                                    <span class="fw-bold <?= $ton == 0 ? 'text-danger' : ($ton <= 10 ? 'text-warning' : 'text-success') ?>">
                                        <?= $ton ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php
                                        $ttl = $row['ten_trang_thai_lo'] ?? 'Chưa nhập';
                                        $badge = match($row['id_trang_thai_lo'] ?? '') {
                                            'TTL01' => 'bg-success',
                                            'TTL02' => 'bg-warning text-dark',
                                            'TTL03' => 'bg-secondary',
                                            'TTL04' => 'bg-danger',
                                            'TTL05' => 'bg-dark',
                                            default => 'bg-light text-dark border'
                                        };
                                    ?>
                                    <span class="badge <?= $badge ?>"><?= $ttl ?></span>
                                </td>
                                <td class="text-end fw-bold text-success">
                                    <?= !empty($row['gia_hien_tai']) 
                                        ? number_format($row['gia_hien_tai'],0,',','.').'đ' 
                                        : '<span class="text-muted">—</span>' ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= BASE_PATH ?>/admin/inventories/create?id_hh=<?= $row['id_hh'] ?>" 
                                       class="btn btn-sm btn-success" title="Nhập lô mới">
                                        <i class="bi bi-plus-circle"></i> Nhập lô
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="9" class="text-center text-muted">Không có dữ liệu.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>