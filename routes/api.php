<?php

use App\Controllers\ApiController;
use App\Controllers\PromotionController;
use App\Controllers\SmartController;

/** @var \App\Core\Router $router */

$router->post('/api/midtrans/notification', [ApiController::class, 'midtransNotification']);
$router->post('/api/midtrans/simulate-success', [ApiController::class, 'simulatePaymentSuccess']);

// Order Status & QRIS Auto-Confirm API
$router->get('/api/order/status', [ApiController::class, 'orderStatus']);
$router->post('/api/order/qris-auto-confirm', [ApiController::class, 'qrisAutoConfirm']);
$router->get('/api/qris/webhook', [ApiController::class, 'qrisWebhook']);
$router->post('/api/qris/webhook', [ApiController::class, 'qrisWebhook']);

// Vouchers & Loyalty API
$router->post('/api/vouchers/apply', [PromotionController::class, 'apiApplyVoucher']);
$router->get('/api/loyalty/info', [PromotionController::class, 'apiLoyaltyInfo']);

// Smart System API
$router->post('/api/smart/chat', [SmartController::class, 'chatbot']);
$router->get('/api/smart/recommendations', [SmartController::class, 'recommendations']);

