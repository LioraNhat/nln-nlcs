<?php
namespace App\Controllers;
use App\Core\BaseController;
use App\Models\ProductModel;
use App\Models\CategoryModel;

class ProductController extends BaseController {

    private $productModel;
    private $categoryModel;

    public function __construct() {
        parent::__construct();
        $this->productModel = new ProductModel();
        $this->categoryModel = new CategoryModel();
    }

    /**
     * Xử lý tìm kiếm
     */
    public function search() {
        $keyword = $_GET['q'] ?? '';
        
        $products = [];
        if (!empty($keyword)) {
            $products = $this->productModel->searchProducts($keyword);
        }

        $categories = $this->categoryModel->getAllCategories();

        $this->renderView('search/index', [
            'title' => 'Kết quả tìm kiếm: ' . $keyword,
            'products' => $products,
            'searchTerm' => $keyword,
            'categories' => $categories,
            'filters' => ['price' => '', 'sort' => ''] 
        ]);
    }

    public function detail($productId) {
        $product = $this->productModel->findProductById($productId);
        if (!$product) {
            echo "Sản phẩm không tồn tại!"; return;
        }
        
        // SỬA TẠI ĐÂY: ID_LHH đổi thành id_loai2 (hoặc kiểm tra linh hoạt cả hai)
        $productTypeId = $product['id_loai2'] ?? $product['ID_LHH'] ?? null; 
        
        $relatedProducts = $this->productModel->getRelatedProducts($productId, $productTypeId, 6); 
        
        $data = [
            'product' => $product,
            'relatedProducts' => $relatedProducts
        ];
        $this->renderView('products/detail', $data);
    } 

    public function category($categoryId) {
        $subCategories = $this->categoryModel->getProductTypesByCategoryId($categoryId);
        $currentCategory = $this->categoryModel->findCategoryById($categoryId); 
        
        $filters = [
            'sort' => $_GET['sort'] ?? 'name_asc', 
            'price' => $_GET['price'] ?? null
        ];
        
        $products = $this->productModel->getProductsByCategoryId($categoryId, $filters, 12, 0); 
        
        $data = [
            'currentCategory' => $currentCategory, 
            'subCategories' => $subCategories,    
            'products' => $products,              
            'filters' => $filters                  
        ];
        $this->renderView('products/category', $data);
    }

    /**
     * Xử lý URL: /product/productType/{id_lhh}
     */
    public function productType($id_lhh) {
        $typeInfo = $this->categoryModel->findProductTypeAndCategoryById($id_lhh);

        if (!$typeInfo) {
            $this->redirect('/');
            return;
        }

        $filters = [
            'price' => $_GET['price'] ?? '',
            'sort' => $_GET['sort'] ?? 'name_asc'
        ];

        $products = $this->productModel->getProductsByProductType($id_lhh, $filters);

        $parentId = $typeInfo['id_dm'] ?? null; // ✅
        $subCategories = $this->categoryModel->getProductTypesByCategoryId($parentId);

        $data = [
            'products' => $products,
            'currentCategory' => [ 
                'ten_dm' => $typeInfo['ten_dm'] ?? '',  // ✅ chữ thường
                'id_dm' => $parentId
            ],
            'subCategories' => $subCategories, 
            'filters' => $filters
        ];

        $this->renderView('products/category', $data);
    }
}