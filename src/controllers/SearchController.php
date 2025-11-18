<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Models\ProductModel;

class SearchController extends BaseController {

    private $productModel;

    public function __construct() {
        parent::__construct(); // QUAN TRỌNG: Để tải $categories cho header
        $this->productModel = new ProductModel();
    }

    /**
     * Xử lý URL: /search
     */
    public function index() {
        
        // 1. Lấy từ khóa tìm kiếm (từ ?q=...)
        $searchTerm = $_GET['q'] ?? '';

        // 2. Lấy bộ lọc (giống hệt trang category)
        $filters = [
            'sort' => $_GET['sort'] ?? 'name_asc', 
            'price' => $_GET['price'] ?? null
        ];

        // 3. Gọi Model để tìm sản phẩm
        $products = [];
        if (!empty(trim($searchTerm))) {
            $products = $this->productModel->searchProductsByName($searchTerm, $filters, 12, 0); 
        }

        // 4. Chuẩn bị dữ liệu và render View
        $data = [
            'products' => $products,
            'searchTerm' => $searchTerm, // Gửi từ khóa ra View
            'filters' => $filters,
            // 'categories' sẽ tự động được tải bởi BaseController
        ];

        // 5. Render view (tạo file ở bước 4)
        $this->renderView('search/index', $data);
    }
}