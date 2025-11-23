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
// 1. NHÓM AUTHENTICATION (Đã cập nhật theo tên hàm của bạn)
// ==================================================================

// Đăng nhập
$router->add('GET', 'auth/login', 'AuthController@login');        // Hiển thị form
$router->add('POST', 'auth/handleLogin', 'AuthController@handleLogin'); // Xử lý POST (Khớp với action form)

// Đăng ký
$router->add('GET', 'auth/register', 'AuthController@register');  // Hiển thị form
$router->add('POST', 'auth/handleRegister', 'AuthController@handleRegister'); // Xử lý POST

// Đăng xuất (Lưu ý: Controller của bạn yêu cầu POST cho logout)
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

// ==================================================================
// 3. NHÓM ADMIN (Quan trọng để vào Dashboard)
// ==================================================================
$router->add('GET', 'admin/dashboard', 'AdminController@dashboard');

// Quản lý Đơn hàng
$router->add('GET', 'admin/orders', 'AdminController@orders');
$router->add('GET', 'admin/order-detail/{id}', 'AdminController@orderDetail');
$router->add('POST', 'admin/order-update-status', 'AdminController@updateOrderStatus');

// Quản lý Sản phẩm
$router->add('GET', 'admin/products', 'AdminController@products');
$router->add('GET', 'admin/products/create', 'AdminController@createProduct');
$router->add('GET', 'admin/products/delete/{id}', 'AdminController@deleteProduct');
$router->add('GET', 'admin/products/edit/{id}', 'AdminController@editProduct'); // Hiện form sửa
$router->add('POST', 'admin/products/store', 'AdminController@storeProduct');   // Xử lý lưu (Thêm/Sửa)

// Quản lý Người dùng
$router->add('GET', 'admin/users', 'AdminController@users');

// KHỞI CHẠY
$router->run();