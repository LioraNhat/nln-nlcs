<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="app-main">
    <div class="app-content-header">
        <div class="container-fluid">
            <div class="row">
                <div class="col-sm-6"><h3 class="mb-0">Quản lý Người dùng</h3></div>
            </div>
        </div>
    </div>

    <div class="app-content">
        <div class="container-fluid">
            
            <?php if(isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <?php if(isset($error)): ?>
                <div class="alert alert-danger"><?= $error ?></div>
            <?php endif; ?>

            <div class="card mb-3">
                <div class="card-body py-2">
                    <form method="GET" class="row g-2">
                        <div class="col-auto"><input type="text" name="search" class="form-control form-control-sm" placeholder="Tìm kiếm..." value="<?= htmlspecialchars($searchKeyword) ?>"></div>
                        <div class="col-auto"><button type="submit" class="btn btn-sm btn-primary">Tìm</button></div>
                    </form>
                </div>
            </div>

            <div class="card mb-4 card-outline card-success">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-people"></i> Danh sách Khách hàng (KH)</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Họ tên</th>
                                <th>Email</th>
                                <th>SĐT</th>
                                <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($customers)): ?>
                                <tr><td colspan="5" class="text-center">Không có khách hàng nào.</td></tr>
                            <?php else: ?>
                                <?php foreach ($customers as $u): ?>
                                <tr>
                                    <td><?= $u['ID_TK'] ?></td>
                                    <td><?= htmlspecialchars($u['HO_TEN']) ?></td>
                                    <td><?= htmlspecialchars($u['EMAIL']) ?></td>
                                    <td><?= htmlspecialchars($u['SDT_TK']) ?></td>
                                    <td class="text-center">
                                        <a href="<?= BASE_PATH ?>/admin/users/edit/<?= $u['ID_TK'] ?>" class="btn btn-sm btn-warning" title="Sửa"><i class="bi bi-pencil"></i></a>
                                        <a href="<?= BASE_PATH ?>/admin/users/delete/<?= $u['ID_TK'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa khách hàng này?');" title="Xóa"><i class="bi bi-trash"></i></a>
                                        
                                        <form action="<?= BASE_PATH ?>/admin/users/update-role" method="POST" style="display:inline-block;" onsubmit="return confirm('Bạn muốn cấp quyền Admin cho người này?');">
                                            <input type="hidden" name="user_id" value="<?= $u['ID_TK'] ?>">
                                            <input type="hidden" name="role_id" value="AD">
                                            <button type="submit" class="btn btn-sm btn-dark" title="Thăng chức Admin"><i class="bi bi-arrow-up-circle"></i></button>
                                        </form>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?php if($totalPages > 1): ?>
                <div class="card-footer clearfix">
                    <ul class="pagination pagination-sm m-0 float-end">
                        <?php for($i=1; $i<=$totalPages; $i++): ?>
                        <li class="page-item <?= $i==$currentPage ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i ?>&search=<?= $searchKeyword ?>"><?= $i ?></a>
                        </li>
                        <?php endfor; ?>
                    </ul>
                </div>
                <?php endif; ?>
            </div>

            <div class="card card-outline card-primary">
                <div class="card-header">
                    <h3 class="card-title"><i class="bi bi-shield-lock"></i> Danh sách Quản trị viên (AD)</h3>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Họ tên</th>
                                <th>Email</th>
                                <th>Phân quyền</th> <th class="text-center">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($admins)): ?>
                                <tr><td colspan="5" class="text-center">Chưa có Admin nào khác.</td></tr>
                            <?php else: ?>
                                <?php foreach ($admins as $ad): ?>
                                <tr>
                                    <td><?= $ad['ID_TK'] ?></td>
                                    <td class="fw-bold text-primary"><?= htmlspecialchars($ad['HO_TEN']) ?></td>
                                    <td><?= htmlspecialchars($ad['EMAIL']) ?></td>
                                    
                                    <td>
                                        <form action="<?= BASE_PATH ?>/admin/users/update-role" method="POST" class="d-flex">
                                            <input type="hidden" name="user_id" value="<?= $ad['ID_TK'] ?>">
                                            <select name="role_id" class="form-select form-select-sm me-2" style="width: 130px;">
                                                <option value="AD" selected>Admin</option>
                                                <option value="KH">Khách hàng</option>
                                            </select>
                                            <button type="submit" class="btn btn-sm btn-outline-primary">Lưu</button>
                                        </form>
                                    </td>

                                    <td class="text-center">
                                        <a href="<?= BASE_PATH ?>/admin/users/edit/<?= $ad['ID_TK'] ?>" class="btn btn-sm btn-warning"><i class="bi bi-pencil"></i></a>
                                        <?php if($ad['ID_TK'] !== $_SESSION['user']['ID_TK']): ?>
                                            <a href="<?= BASE_PATH ?>/admin/users/delete/<?= $ad['ID_TK'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xóa Admin này?');"><i class="bi bi-trash"></i></a>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Bạn</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>