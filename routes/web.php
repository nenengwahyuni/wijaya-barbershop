<?php

use App\Controllers\HomeController;
use App\Controllers\AuthController;
use App\Controllers\BookingController;
use App\Controllers\AdminController;
use App\Controllers\PromotionController;
use App\Controllers\SmartController;

/** @var \App\Core\Router $router */

// Customer & Public Routes
$router->get('/', [HomeController::class, 'index']);
$router->get('/services', [HomeController::class, 'services']);
$router->get('/home-service', [BookingController::class, 'homeService']);
$router->post('/home-service', [BookingController::class, 'storeHomeService']);
$router->get('/membership', [PromotionController::class, 'membership']);
$router->get('/loyalty', [PromotionController::class, 'membership']);
$router->get('/promotions', [PromotionController::class, 'promotions']);

// Authentication Routes (Gambar 31 & 32)
$router->get('/login', [AuthController::class, 'showLogin']);
$router->post('/login', [AuthController::class, 'login']);
$router->get('/register', [AuthController::class, 'showRegister']);
$router->post('/register', [AuthController::class, 'register']);
$router->get('/logout', [AuthController::class, 'logout']);
$router->get('/switch-account', [AuthController::class, 'switchAccount']);

// Cart & Booking Checkout Routes (Gambar 30, 36, 37)
$router->get('/cart', [BookingController::class, 'cart']);
$router->post('/cart/add', [BookingController::class, 'addToCart']);
$router->post('/cart/update', [BookingController::class, 'updateCart']);
$router->get('/checkout', [BookingController::class, 'checkout']);
$router->post('/checkout', [BookingController::class, 'storeOrder']);
$router->get('/receipt', [BookingController::class, 'receipt']);
$router->post('/receipt/confirm-qris', [BookingController::class, 'confirmQrisPayment']);
$router->get('/my-orders', [BookingController::class, 'myOrders']);

// Admin / Karyawan Dashboard Routes (Gambar 34 & 35)
$router->get('/admin', [AdminController::class, 'dashboard']);
$router->get('/admin/orders', [AdminController::class, 'orders']);
$router->get('/admin/orders/{id}', [AdminController::class, 'orderDetail']);
$router->post('/admin/orders/status', [AdminController::class, 'updateOrderStatus']);
$router->post('/admin/orders/assign-barber', [AdminController::class, 'assignBarber']);

// Admin Items Management (Gambar 35)
$router->get('/admin/items', [AdminController::class, 'items']);
$router->get('/admin/items/create', [AdminController::class, 'createItem']);
$router->post('/admin/items/create', [AdminController::class, 'storeItem']);
$router->get('/admin/items/{id}/edit', [AdminController::class, 'editItem']);
$router->post('/admin/items/{id}/edit', [AdminController::class, 'updateItem']);
$router->post('/admin/items/delete', [AdminController::class, 'deleteItem']);

// Admin Categories, Users & Roles
$router->get('/admin/categories', [AdminController::class, 'categories']);
$router->post('/admin/categories', [AdminController::class, 'categories']);
$router->get('/admin/users', [AdminController::class, 'users']);
$router->post('/admin/users', [AdminController::class, 'users']);
$router->get('/admin/roles', [AdminController::class, 'roles']);

// Admin Vouchers & Loyalty Management
$router->get('/admin/vouchers', [AdminController::class, 'vouchers']);
$router->post('/admin/vouchers/create', [AdminController::class, 'storeVoucher']);
$router->post('/admin/vouchers/toggle', [AdminController::class, 'toggleVoucher']);
$router->get('/admin/loyalty', [AdminController::class, 'loyalty']);
$router->post('/admin/loyalty/reward', [AdminController::class, 'addRewardPoints']);

// Smart System Routes
$router->get('/smart', [SmartController::class, 'customerDashboard']);
$router->get('/admin/analytics', [SmartController::class, 'adminAnalytics']);

