<!doctype html>
<html lang="vi">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>Quản trị Admin | NLN_NLCS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fontsource/source-sans-3@5.0.12/index.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.min.css" crossorigin="anonymous" />
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/admin_assets/css/overlayscrollbars.min.css" />
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/admin_assets/css/adminlte.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/apexcharts@3.37.1/dist/apexcharts.css" />
</head>
<body class="layout-fixed sidebar-expand-lg sidebar-open bg-body-tertiary">
    <div class="app-wrapper">
        <nav class="app-header navbar navbar-expand bg-body">
            <div class="container-fluid">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" data-lte-toggle="sidebar" href="#" role="button">
                            <i class="bi bi-list"></i>
                        </a>
                    </li>
                    <li class="nav-item d-none d-md-block"><a href="<?php echo BASE_PATH; ?>/" class="nav-link">Xem Website</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown user-menu">
                        <a href="#" class="nav-link dropdown-toggle" data-bs-toggle="dropdown">
                            <img src="<?php echo BASE_PATH; ?>/admin_assets/assets/img/user2-160x160.jpg" class="user-image rounded-circle shadow" alt="User Image" />
                            <span class="d-none d-md-inline"><?php echo $_SESSION['user']['HO_TEN'] ?? 'Admin'; ?></span>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-end">
                            <li class="user-header text-bg-primary">
                                <img src="<?php echo BASE_PATH; ?>/admin_assets/assets/img/user2-160x160.jpg" class="rounded-circle shadow" alt="User Image" />
                                <p>
                                    <?php echo $_SESSION['user']['HO_TEN'] ?? 'Quản trị viên'; ?>
                                    <small>Admin System</small>
                                </p>
                            </li>
                            <li class="user-footer">
                                <a href="<?= BASE_PATH ?>/admin/profile" class="btn btn-default btn-flat">Hồ sơ</a>
                                
                                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-default btn-flat float-end">
                                    Đăng xuất
                                </a>
                                <form id="logout-form" action="<?php echo BASE_PATH; ?>/auth/logout" method="POST" style="display: none;">
                                    </form>
                            </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>