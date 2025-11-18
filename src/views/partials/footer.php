</main> <footer class="main-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-column">
                <h5>Về NLN Foods</h5>
                <p>Chúng tôi cung cấp thực phẩm sạch, tươi ngon và đã qua sơ chế, giúp bữa ăn gia đình bạn tiện lợi và an toàn hơn.</p>
            </div>
            
            <div class="footer-column">
                <h5>Danh Mục</h5>
                <ul>
                    <?php 
                    // Tái sử dụng $categories từ header
                    if (isset($categories) && !empty($categories)):
                        foreach ($categories as $category): 
                    ?>
                        <li>
                            <a href="/NLN_NLCS/public/product/category/<?php echo $category['ID_DM']; ?>">
                                <?php echo htmlspecialchars($category['TEN_DM']); ?>
                            </a>
                        </li>
                    <?php 
                        endforeach; 
                    endif; 
                    ?>
                </ul>
            </div>
            
            <div class="footer-column">
                <h5>Hỗ Trợ Khách Hàng</h5>
                <ul>
                    <li><a href="#">Câu hỏi thường gặp</a></li>
                    <li><a href="#">Chính sách đổi trả</a></li>
                    <li><a href="#">Chính sách bảo mật</a></li>
                    <li><a href="#">Chính sách giao hàng</a></li>
                </ul>
            </div>

            <div class="footer-column">
                <h5>Liên Hệ</h5>
                <p>
                    Địa chỉ: Ninh Kieu, Can Tho <br>
                    Email: greenmeal@gmail.com<br>
                    Điện thoại: 0999999999
                </p>
            </div>
        </div>

        <div class="footer-bottom">
            <p>&copy; <?php echo date('Y'); ?> Bản quyền thuộc về Green Meal.</p>
        </div>
    </div>
</footer>

<script src="<?php echo BASE_PATH; ?>/js/main.js"></script>
</body>
</html>