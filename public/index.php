<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// File: public/index.php
session_start();
define('BASE_PATH', dirname($_SERVER['SCRIPT_NAME']));

require_once __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

use App\Core\Router;
$router = new Router();

// ==================================================================
// 1. NHÓM AUTHENTICATION
// ==================================================================

// Đăng nhập
$router->add('GET', 'auth/login', 'AuthController@login');        // Hiển thị form
$router->add('POST', 'auth/handleLogin', 'AuthController@handleLogin'); // Xử lý POST

// Đăng ký
$router->add('GET', 'auth/register', 'AuthController@register');  // Hiển thị form
$router->add('POST', 'auth/handleRegister', 'AuthController@handleRegister'); // Xử lý POST

// Đăng xuất
$router->add('POST', 'auth/logout', 'AuthController@logout'); 

// ==================================================================
// 2. NHÓM CHỨC NĂNG TÀI KHOẢN
// ==================================================================
$router->add('GET', 'account/deleteAddress/{id}', 'AccountController@deleteAddress');
$router->add('POST', 'account/handleSetDefaultAddress', 'AccountController@handleSetDefaultAddress');
$router->add('GET', 'account/getAddressJson/{id}', 'AccountController@getAddressJson');
$router->add('POST', 'account/handleUpdateProfile', 'AccountController@handleUpdateProfile');
$router->add('POST', 'account/handleChangePassword', 'AccountController@handleChangePassword');
$router->add('POST', 'account/cancel-order', 'AccountController@cancelOrder');
$router->add('POST', 'account/handleUpdateAddress', 'AccountController@handleUpdateAddress');
$router->add('POST', 'account/handleAddAddress', 'AccountController@handleAddAddress');
$router->add('GET', 'account/order-detail/{id}', 'AccountController@orderDetail');
// ==================================================================
// 3. NHÓM ADMIN
// ==================================================================
$router->add('GET', 'admin/dashboard', 'AdminController@dashboard');

// --- Quản lý Sản phẩm ---
$router->add('GET', 'admin/products', 'AdminController@products');
$router->add('GET', 'admin/products/create', 'AdminController@createProduct');
$router->add('GET', 'admin/products/delete/{id}', 'AdminController@deleteProduct');
$router->add('GET', 'admin/products/edit/{id}', 'AdminController@editProduct');
$router->add('POST', 'admin/products/store', 'AdminController@storeProduct');

// --- Quản lý Danh mục (MỚI THÊM) ---
$router->add('GET', 'admin/categories', 'AdminController@categories');           // Danh sách
$router->add('GET', 'admin/categories/create', 'AdminController@createCategory'); // Form thêm
$router->add('POST', 'admin/categories/store', 'AdminController@storeCategory');  // Xử lý lưu
$router->add('GET', 'admin/categories/edit/{id}', 'AdminController@editCategory'); // Form sửa
$router->add('GET', 'admin/categories/delete/{id}', 'AdminController@deleteCategory'); // Xóa

// --- Quản lý Loại hàng hóa (MỚI THÊM) ---
$router->add('GET', 'admin/product-types', 'AdminController@productTypes');          // Danh sách
$router->add('GET', 'admin/product-types/create', 'AdminController@createProductType'); // Form thêm
$router->add('POST', 'admin/product-types/store', 'AdminController@storeProductType');  // Xử lý lưu
$router->add('GET', 'admin/product-types/edit/{id}', 'AdminController@editProductType'); // Form sửa
$router->add('GET', 'admin/product-types/delete/{id}', 'AdminController@deleteProductType'); // Xóa

// --- Quản lý Khuyến mãi (PROMOTIONS) ---
$router->add('GET', 'admin/promotions', 'AdminController@promotions');
$router->add('GET', 'admin/promotions/create', 'AdminController@createPromotion');
$router->add('POST', 'admin/promotions/store', 'AdminController@storePromotion');
$router->add('GET', 'admin/promotions/edit/{id}', 'AdminController@editPromotion');
$router->add('GET', 'admin/promotions/delete/{id}', 'AdminController@deletePromotion');

// --- Quản lý Tồn kho (INVENTORIES) ---
$router->add('GET', 'admin/inventories', 'AdminController@inventories');
$router->add('GET', 'admin/inventories/create', 'AdminController@createInventory');
$router->add('POST', 'admin/inventories/store', 'AdminController@storeInventory');
$router->add('GET', 'admin/inventories/detail/{id}', 'AdminController@inventoryDetail');

// --- Quản lý Nhà cung cấp (SUPPLIERS) ---
$router->add('GET', 'admin/suppliers', 'AdminController@suppliers');
$router->add('GET', 'admin/suppliers/create', 'AdminController@createSupplier');
$router->add('POST', 'admin/suppliers/store', 'AdminController@storeSupplier');
$router->add('GET', 'admin/suppliers/edit/{id}', 'AdminController@editSupplier');
$router->add('GET', 'admin/suppliers/delete/{id}', 'AdminController@deleteSupplier');

$router->add('GET', 'admin/orders', 'AdminController@orders');
$router->add('GET', 'admin/order-detail/{id}', 'AdminController@orderDetail');
$router->add('POST', 'admin/order-update-status', 'AdminController@updateOrderStatus');

// --- Quản lý Khách hàng (Users) ---
$router->add('GET', 'admin/users', 'AdminController@users');
$router->add('GET', 'admin/users/edit/{id}', 'AdminController@editUser');
$router->add('POST', 'admin/users/update', 'AdminController@updateUser');
$router->add('GET', 'admin/users/delete/{id}', 'AdminController@deleteUser');
$router->add('POST', 'admin/users/update-role', 'AdminController@updateUserRole');

// --- Thống kê ---
$router->add('GET', 'admin/statistics', 'AdminController@statistics');
$router->add('POST', 'admin/statistics', 'AdminController@statistics'); // Cần POST để gửi form lọc

// KHỞI CHẠY
$router->run();