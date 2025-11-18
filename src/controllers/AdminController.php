<?php

namespace App\Controllers;

use App\Core\BaseController;
use App\Core\Auth;
use App\Models\CategoryModel;

class AdminController extends BaseController {

    public function __construct() {
        parent::__construct(); 
        Auth::requireAdmin();
    }

    public function index() {
        // Trang Dashboard Admin
        $this->renderView('admin/index', [
            'title' => 'Bảng điều khiển',
            'user' => Auth::user()
        ], 'admin');
    }

    public function products() {
        // Quản lý sản phẩm
        $this->renderView('admin/products/index', [
            'title' => 'Quản lý sản phẩm'
        ], 'admin/layouts/admin'); // <-- ĐÃ SỬA
    }

    public function orders() {
        // Quản lý đơn hàng
        $this->renderView('admin/orders/index', [
            'title' => 'Quản lý đơn hàng'
        ], 'admin/layouts/admin'); // <-- ĐÃ SỬA
    }

    public function customers() {
        // Quản lý khách hàng
        $this->renderView('admin/customers/index', [
            'title' => 'Quản lý khách hàng'
        ], 'admin/layouts/admin'); // <-- ĐÃ SỬA
    }

    /**
     * =============================================
     * QUẢN LÝ DANH MỤC (CATEGORIES)
     * =============================================
     */

    public function categories() {
        $model = new \App\Models\CategoryModel();
        $categories = $model->getAllCategories();

        $this->renderView('admin/categories/index', [
            'title' => 'Quản lý Danh mục',
            'categories' => $categories
        ], 'admin/layouts/admin'); // <-- ĐÃ SỬA
    }

    public function manageCategory() {
        $model = new \App\Models\CategoryModel();
        $category = null; 
        $is_update = false;

        if (isset($_GET['id'])) {
            $id_dm = $_GET['id'];
            $is_update = true;
            $category = $model->findCategoryById($id_dm);
        }

        $this->renderView('admin/categories/manage', [
            'title' => $is_update ? 'Cập nhật Danh mục' : 'Thêm mới Danh mục',
            'category' => $category,
            'is_update' => $is_update
        ], 'admin/layouts/admin'); // <-- ĐÃ SỬA
    }

    public function saveCategory() {
        header('Content-Type: application/json');
        $model = new \App\Models\CategoryModel();
        
        $id_dm = $_POST['ID_DM'];
        $ten_dm = $_POST['TEN_DM'];
        $is_update = !empty($_POST['id_update']); 

        try {
            if ($is_update) {
                $result = $model->updateCategory($id_dm, $ten_dm);
            } else {
                if ($model->findCategoryById($id_dm)) {
                    echo json_encode(['status' => 'failed', 'msg' => 'Mã danh mục (ID_DM) này đã tồn tại.']);
                    return;
                }
                $result = $model->saveCategory($id_dm, $ten_dm);
            }

            echo json_encode($result ? ['status' => 'success']
                                        : ['status' => 'failed', 'msg' => 'Lỗi khi lưu vào CSDL']);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'failed', 'msg' => $e->getMessage()]);
        }
    }

    public function deleteCategory() {
        header('Content-Type: application/json');
        $model = new \App\Models\CategoryModel();
        $id_dm = $_POST['id'];

        try {
            $result = $model->deleteCategory($id_dm);
            echo json_encode($result ? ['status' => 'success']
                                        : ['status' => 'failed', 'msg' => 'Lỗi khi xóa']);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'failed', 'msg' => 'Không thể xóa danh mục này. Có thể nó đang được sử dụng.']);
        }
    }

    /**
     * =============================================
     * QUẢN LÝ LOẠI HÀNG HÓA (PRODUCT TYPES)
     * =============================================
     */

    public function productTypes() {
        $model = new \App\Models\CategoryModel();
        $productTypes = $model->getAllProductTypesWithCategory();

        $this->renderView('admin/product_types/index', [
            'title' => 'Quản lý Loại Hàng Hóa',
            'productTypes' => $productTypes
        ], 'admin/layouts/admin'); // <-- ĐÃ SỬA
    }

    public function manageProductType() {
        $model = new \App\Models\CategoryModel();
        $productType = null; 
        $is_update = false;

        $categories = $model->getAllCategories();

        if (isset($_GET['id'])) {
            $id_lhh = $_GET['id'];
            $is_update = true;
            $productType = $model->findProductTypeAndCategoryById($id_lhh);
        }

        $this->renderView('admin/product_types/manage', [
            'title' => $is_update ? 'Cập nhật Loại Hàng Hóa' : 'Thêm mới Loại Hàng Hóa',
            'productType' => $productType,
            'categories' => $categories,
            'is_update' => $is_update
        ], 'admin/layouts/admin'); // <-- ĐÃ SỬA
    }

    public function saveProductType() {
        header('Content-Type: application/json');
        $model = new \App\Models\CategoryModel();
        
        $id_lhh = $_POST['ID_LHH'];
        $ten_lhh = $_POST['TEN_LHH'];
        $id_dm = $_POST['ID_DM'];
        $is_update = !empty($_POST['id_update']); 

        try {
            if ($is_update) {
                $result = $model->updateProductType($id_lhh, $ten_lhh, $id_dm);
            } else {
                if ($model->findProductTypeAndCategoryById($id_lhh)) {
                    echo json_encode(['status' => 'failed', 'msg' => 'Mã LHH này đã tồn tại.']);
                    return;
                }
                $result = $model->saveProductType($id_lhh, $ten_lhh, $id_dm);
            }

            echo json_encode($result ? ['status' => 'success']
                                        : ['status' => 'failed', 'msg' => 'Lỗi khi lưu vào CSDL']);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'failed', 'msg' => $e->getMessage()]);
        }
    }

    public function deleteProductType() {
        header('Content-Type: application/json');
        $model = new \App\Models\CategoryModel();
        $id_lhh = $_POST['id'];

        try {
            $result = $model->deleteProductType($id_lhh);
            echo json_encode($result ? ['status' => 'success']
                                        : ['status' => 'failed', 'msg' => 'Lỗi khi xóa']);
        } catch (\Exception $e) {
            echo json_encode(['status' => 'failed', 'msg' => 'Không thể xóa. LHH này có thể đang được sản phẩm sử dụng.']);
        }
    }
}