<?php // File: src/views/admin/partials/sidebar.php ?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="/admin" class="brand-link text-sm">
        <span class="brand-text font-weight-light">Trang Quản Trị</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo \App\Core\Auth::user()['HO_TEN'] ?? 'Admin'; ?></a>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column nav-flat" data-widget="treeview" role="menu" data-accordion="false">
                
                <li class="nav-item">
                    <a href="/admin" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li> 

                <li class="nav-header">QUẢN LÝ</li>
                
                <li class="nav-item">
                    <a href="/admin/categories" class="nav-link">
                        <i class="nav-icon fas fa-tags"></i>
                        <p>Quản lý Danh mục</p>
                    </a>
                </li>
                
                <li class="nav-item">
                    <a href="/admin/productTypes" class="nav-link">
                        <i class="nav-icon fas fa-stream"></i>
                        <p>Quản lý Loại Hàng Hóa</p>
                    </a>
                </li>

                </ul>
        </nav>
        </div>
    </aside>