<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NLN - Thực Phẩm Sạch</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/style.css">
    <script>const BASE_PATH = "<?php echo BASE_PATH; ?>";</script>
</head>
<body>

<div class="top-bar-shipping">
    <div class="container">
        FREESHIP nội thành cho đơn hàng từ 500K
    </div>
</div>

<header class="main-header">
    <div class="container">
        
        <div class="menu-toggle">
            <img src="<?php echo BASE_PATH; ?>/images/menu-burger.png" alt="MENU" class="header-main-icon">
            <span>DANH MỤC</span>
        </div>
        
        <a href="<?php echo BASE_PATH; ?>/" class="logo">
            <img src="<?php echo BASE_PATH; ?>/images/logo-green-meal.png" alt="NLN Foods Logo" class="logo-image">
        </a>
        
        <form class="search-bar" action="<?php echo BASE_PATH; ?>/search" method="GET">
            <input type="text" placeholder="Tìm kiếm sản phẩm..." name="q" value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
            
            <button type="submit">
                <img src="<?php echo BASE_PATH; ?>/images/search.png" alt="Tìm" class="search-icon">
            </button> 
        </form>
        
        <div class="header-icons">

            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['user_role'] === 'AD'): ?>
                    <a href="<?php echo BASE_PATH; ?>/admin/index" class="header-icon-item" style="color: #dc3545; font-weight: 700;">
                        <img src="<?php echo BASE_PATH; ?>/images/admin-icon.png" alt="Admin" class="header-main-icon">
                        <span>Trang Admin</span>
                    </a>
                <?php endif; ?>

                <a href="<?php echo BASE_PATH; ?>/account/index" class="header-icon-item">
                    <img src="<?php echo BASE_PATH; ?>/images/user-icon-2.png" alt="Tài khoản" class="header-main-icon">
                    <span>Chào, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span> 
                </a>
                
                <form action="<?php echo BASE_PATH; ?>/auth/logout" method="POST" class="logout-form">
                    <button type="submit" class="header-icon-item-button">
                        <img src="<?php echo BASE_PATH; ?>/images/exit.png" alt="Đăng xuất" class="header-main-icon">
                        <span>Đăng xuất</span>
                    </button>
                </form>

            <?php else: ?>
                <a href="<?php echo BASE_PATH; ?>/auth/login" class="header-icon-item">
                    <img src="<?php echo BASE_PATH; ?>/images/user-icon.png" alt="Tài khoản" class="header-main-icon">
                    <span>Tài khoản</span>
                </a>
            <?php endif; ?>
            
            <a href="<?php echo BASE_PATH; ?>/cart/index" class="header-icon-item">
                <img src="<?php echo BASE_PATH; ?>/images/shopping-cart.png" alt="Giỏ hàng" class="header-main-icon">
                <span>Giỏ hàng</span>
                
                <!-- =============================================== -->
                <!-- SỬA LỖI ICON: ĐỌC TỪ SESSION COUNT MỚI -->
                <!-- =============================================== -->
                <span class="cart-count">
                    <?php echo $_SESSION['cart_count'] ?? 0; ?>
                </span>
                <!-- =============================================== -->

            </a>
        </div>
    </div>
</header>

<nav class="sub-nav">
    <div class="container">
        <ul>
            <li>
                <a href="<?php echo BASE_PATH; ?>/" class="sub-nav-home-icon">
                    <img src="<?php echo BASE_PATH; ?>/images/home.png" alt="Trang chủ">
                </a>
            </li>
            
            <li><a href="#">HOT DEAL</a></li>
            
            <?php 
            // Dùng biến $categories mà HomeController đã gửi
            if (isset($categories) && !empty($categories)):
                foreach ($categories as $category): 
            ?>
                <li>
                    <a href="<?php echo BASE_PATH; ?>/product/category/<?php echo $category['ID_DM']; ?>">
                        <?php echo htmlspecialchars($category['TEN_DM']); ?>
                    </a>
                </li>
            <?php 
                endforeach; 
            endif; 
            ?>
            
            <li><a href="#">Dịch vụ đặt tiệc BBQ</a></li>
            
            <li><a href="#">Gợi ý món ăn</a></li>
        </ul>
    </div>
</nav>

<nav class="vertical-nav">
    <ul>
        <?php 
        // Dùng biến $categories mà HomeController đã gửi
        if (isset($categories) && !empty($categories)):
            foreach ($categories as $category): 
        ?>
            <li>
                <a href="<?php echo BASE_PATH; ?>/product/category/<?php echo $category['ID_DM']; ?>">
                    
                    <img src="<?php echo BASE_PATH; ?>/images/icon-<?php echo $category['ID_DM']; ?>.png" 
                         alt="" 
                         class="nav-category-icon">
                    
                    <span><?php echo htmlspecialchars($category['TEN_DM']); ?></span>

                </a>
            </li>
        <?php 
            endforeach; 
        endif; 
        ?>
    </ul>
</nav>

<main></main>