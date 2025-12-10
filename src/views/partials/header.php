<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NLN - Thực Phẩm Sạch</title>
    <link rel="stylesheet" href="<?php echo BASE_PATH; ?>/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>const BASE_PATH ="<?php echo BASE_PATH; ?>";</script>
</head>
<body>

<div class="top-bar-shipping">
    <div class="container">
        <i class="fa-solid fa-truck-fast"></i> FREESHIP nội thành cho đơn hàng từ 500K
    </div>
</div>

<header class="main-header">
    <div class="container">
        
        <div class="menu-toggle">
            <i class="fa-solid fa-bars"></i>
        </div>
        
        <a href="<?php echo BASE_PATH; ?>/" class="logo">
            <img src="<?php echo BASE_PATH; ?>/images/logo-green-meal.png" alt="NLN Foods Logo" class="logo-image">
        </a>
        
        <form class="search-bar" action="<?php echo BASE_PATH; ?>/search" method="GET">
            <input type="text" placeholder="Tìm tôm, cua, cá, rau củ..." name="q" value="<?php echo htmlspecialchars($_GET['q'] ?? ''); ?>">
            <button type="submit">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button> 
        </form>
        
        <div class="header-icons">

            <?php if (isset($_SESSION['user'])): ?>
                
                <a href="<?php echo BASE_PATH; ?>/account/index" class="header-icon-item">
                    <img src="<?php echo BASE_PATH; ?>/images/user-icon-2.png" alt="Tài khoản" class="header-main-icon">
                    <span class="icon-label">Chào, <?php echo htmlspecialchars($_SESSION['user']['HO_TEN'] ?? 'Bạn'); ?></span> 
                </a>

                <?php if (isset($_SESSION['user']['ID_ND']) && $_SESSION['user']['ID_ND'] === 'AD'): ?>
                    <a href="<?php echo BASE_PATH; ?>/admin/dashboard" class="header-icon-item admin-btn">
                        <i class="fa-solid fa-user-shield header-main-icon" style="font-size: 24px; color: #d32f2f;"></i>
                        <span class="icon-label" style="color: #d32f2f; font-weight: bold;">Quản trị</span>
                    </a>
                <?php endif; ?>
                
                <form action="<?php echo BASE_PATH; ?>/auth/logout" method="POST" class="logout-form">
                    <button type="submit" class="header-icon-item-button">
                        <img src="<?php echo BASE_PATH; ?>/images/exit.png" alt="Đăng xuất" class="header-main-icon">
                        <span class="icon-label">Thoát</span>
                    </button>
                </form>

            <?php else: ?>
                <a href="<?php echo BASE_PATH; ?>/auth/login" class="header-icon-item">
                    <img src="<?php echo BASE_PATH; ?>/images/user-icon.png" alt="Đăng nhập" class="header-main-icon">
                    <span class="icon-label">Đăng nhập</span>
                </a>
            <?php endif; ?>
            
            <a href="<?php echo BASE_PATH; ?>/cart/index" class="header-icon-item cart-wrapper">
                <div class="cart-icon-box">
                    <img src="<?php echo BASE_PATH; ?>/images/shopping-cart.png" alt="Giỏ hàng" class="header-main-icon">
                    <span class="cart-count"><?php echo $_SESSION['cart_count'] ?? 0; ?></span>
                </div>
                <span class="icon-label">Giỏ hàng</span>
            </a>
        </div>
    </div>
</header>

<aside class="vertical-nav">
    <ul>
        <li>
            <a href="#" style="background: #f8f9fa; font-weight: 800; text-transform: uppercase; pointer-events: none;">
                <i class="fa-solid fa-list nav-category-icon"></i> Danh mục sản phẩm
            </a>
        </li>

        <?php if (isset($categories) && !empty($categories)): ?>
            <?php foreach ($categories as $category): ?>
                <li>
                    <a href="<?php echo BASE_PATH; ?>/product/category/<?php echo $category['ID_DM']; ?>">
                        <i class="fa-solid fa-caret-right nav-category-icon" style="font-size: 14px; color: #888;"></i>
                        <?php echo htmlspecialchars($category['TEN_DM']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li><a href="#">Đang cập nhật danh mục...</a></li>
        <?php endif; ?>
        
        <!-- <li style="border-top: 5px solid #f1f1f1;"><a href="#">Khuyến mãi Hot</a></li>
        <li><a href="#">Tin tức & Mẹo vặt</a></li>
        <li><a href="#">Liên hệ</a></li> -->
    </ul>
</aside>
<nav class="sub-nav">
    <div class="container">
        <ul>
            <li>
                <a href="<?php echo BASE_PATH; ?>/" class="sub-nav-home-icon">
                    <img src="<?php echo BASE_PATH; ?>/images/home.png" alt="Trang chủ">
                </a>
            </li>
            <li><a href="#">HOT DEAL</a></li>
            <?php if (isset($categories) && !empty($categories)): 
                foreach ($categories as $category): ?>
                <li>
                    <a href="<?php echo BASE_PATH; ?>/product/category/<?php echo $category['ID_DM']; ?>">
                        <?php echo htmlspecialchars($category['TEN_DM']); ?>
                    </a>
                </li>
            <?php endforeach; endif; ?>
            <li><a href="#">Gợi ý món ăn</a></li>
        </ul>
    </div>
</nav>
<main></main>