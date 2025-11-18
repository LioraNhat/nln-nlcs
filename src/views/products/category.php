<?php 
require_once __DIR__ . '/../partials/header.php'; 
?>

<nav class="category-sub-nav">
    <div class="container">
        <ul>
            <li>
                <span><?php echo htmlspecialchars($currentCategory['TEN_DM']); ?>:</span>
            </li>
            
            <?php foreach($subCategories as $sub): ?>
                <li>
                    <a href="<?php echo BASE_PATH; ?>/product/productType/<?php echo $sub['ID_LHH']; ?>">
                        <?php echo htmlspecialchars($sub['TEN_LHH']); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</nav>

<div class="container">
    <main class="product-listing-page">
        
        <div class="filter-bar">
            <form action="" method="GET" class="filter-form-combined">
                
                <div class="filter-group">
                    <label for="price-filter">Lọc giá:</label>
                    <select name="price" id="price-filter">
                        <option value="">Tất cả giá</option>
                        <option value="under_100k" <?php if($filters['price'] == 'under_100k') echo 'selected'; ?>>Dưới 100.000đ</option>
                        <option value="100000-200000" <?php if($filters['price'] == '100000-200000') echo 'selected'; ?>>100.000đ - 200.000đ</option>
                        <option value="200000-300000" <?php if($filters['price'] == '200000-300000') echo 'selected'; ?>>200.000đ - 300.000đ</option>
                        <option value="300000-400000" <?php if($filters['price'] == '300000-400000') echo 'selected'; ?>>300.000đ - 400.000đ</option>
                        <option value="over_400k" <?php if($filters['price'] == 'over_400k') echo 'selected'; ?>>Trên 400.000đ</option>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label for="sort-filter">Sắp xếp:</label>
                    <select name="sort" id="sort-filter">
                        <option value="name_asc" <?php if($filters['sort'] == 'name_asc') echo 'selected'; ?>>Tên: A-Z</option>
                        <option value="name_desc" <?php if($filters['sort'] == 'name_desc') echo 'selected'; ?>>Tên: Z-A</option>
                        <option value="price_asc" <?php if($filters['sort'] == 'price_asc') echo 'selected'; ?>>Giá: Tăng dần</option>
                        <option value="price_desc" <?php if($filters['sort'] == 'price_desc') echo 'selected'; ?>>Giá: Giảm dần</option>
                    </select>
                </div>

                <button type="submit" class="btn-filter-submit">Lọc</button>

            </form>
        </div>
        <div class="product-grid">
            <?php 
            if (!empty($products)):
                foreach ($products as $product):
                    require __DIR__ . '/../partials/product-card.php';
                endforeach; 
            else:
            ?>
                <p style="grid-column: 1 / -1; text-align: center; padding: 40px;">Không tìm thấy sản phẩm nào phù hợp với bộ lọc.</p>
            <?php 
            endif; 
            ?>
        </div>
    </main>
</div>

<?php 
require_once __DIR__ . '/../partials/footer.php'; 
?>