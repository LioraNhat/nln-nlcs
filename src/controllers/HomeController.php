<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\ProductModel; 

class HomeController extends BaseController {

    public function index() {
        $productModel = new ProductModel();

        // 3. Lấy dữ liệu cho nội dung trang chủ
        $hotdealProducts = $productModel->getProductsOnSale(6);
        $homeCategories = $this->viewData['categories']; // Dùng dữ liệu từ BaseController
        
        $homeCategorySections = [];
        foreach ($homeCategories as $category) {
            $homeCategorySections[] = [
                'info' => $category,
                'products' => $productModel->getProductsByCategoryId($category['ID_DM'], 6)
            ];
        }

        // 4. Gộp tất cả dữ liệu lại
        $data = [
            'hotdealProducts' => $hotdealProducts, 
            'homeCategorySections' => $homeCategorySections
        ];

        // 5. Gọi view 'home' (BaseController sẽ tự gộp $categories vào)
        $this->renderView('home', $data);
    }
}