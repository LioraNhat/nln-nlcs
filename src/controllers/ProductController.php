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
        // 1. Lấy từ khóa từ URL (ví dụ: ?q=Cá)
        $keyword = $_GET['q'] ?? '';
        
        // 2. Gọi Model tìm kiếm
        $products = [];
        if (!empty($keyword)) {
            $products = $this->productModel->searchProducts($keyword);
        }

        // 3. Lấy danh mục (để hiển thị Menu Sidebar nếu cần)
        $categories = $this->categoryModel->getAllCategories();

        // 4. Hiển thị View kết quả
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
        $categoryId = $product['ID_LHH']; 
        $relatedProducts = $this->productModel->getRelatedProducts($productId, $categoryId, 6); 
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
        
        // 1. Lấy thông tin về Loại Hàng Hóa (LHH) và Danh Mục (DM) cha
        // (Cần hàm findProductTypeAndCategoryById trong CategoryModel)
        $typeInfo = $this->categoryModel->findProductTypeAndCategoryById($id_lhh);

        if (!$typeInfo) {
            $this->redirect('/'); // Loại hàng hóa không tồn tại
            return;
        }

        // 2. Lấy bộ lọc từ URL (giống hệt trang category)
        $filters = [
            'price' => $_GET['price'] ?? '',
            'sort' => $_GET['sort'] ?? 'name_asc'
        ];

        // 3. Lấy sản phẩm (đã lọc)
        // (Cần nâng cấp hàm getProductsByProductType trong ProductModel)
        $products = $this->productModel->getProductsByProductType($id_lhh, $filters);

        // 4. Lấy các Loại Hàng Hóa "anh em" (để hiển thị sub-nav)
        $subCategories = $this->categoryModel->getProductTypesByCategoryId($typeInfo['ID_DM']);

        // 5. Chuẩn bị dữ liệu cho View
        $data = [
            'products' => $products,
            'currentCategory' => [ // Thông tin DM cha
                'TEN_DM' => $typeInfo['TEN_DM'],
                'ID_DM' => $typeInfo['ID_DM']
            ],
            'subCategories' => $subCategories, // Các LHH "anh em"
            'filters' => $filters
        ];

        // 6. Render LẠI view 'category.php' (quan trọng)
        $this->renderView('products/category', $data);
    }
}