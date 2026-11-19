<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;
use App\Core\Database;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Item;
use App\Models\Category;
use App\Models\User;
use App\Models\Role;
use App\Models\Voucher;
use App\Models\Loyalty;
use App\Services\WhatsAppService;

class AdminController extends Controller
{
    private function checkStaffAuth(Response $response)
    {
        if (!Auth::check() || !Auth::isStaff()) {
            $this->setFlash('error', 'Akses ditolak. Silakan login sebagai staf/kasir/admin.');
            $response->redirect('/login');
        }
    }

    public function dashboard(Request $request, Response $response)
    {
        $this->checkStaffAuth($response);

        $pdo = Database::getConnection();

        // 1. Total Pendapatan Bulan Ini
        $stmtRev = $pdo->query("SELECT SUM(CAST(grand_total AS UNSIGNED)) as total FROM orders WHERE status IN ('settlement', 'completed') AND deleted_at IS NULL");
        $totalRevenue = (int) ($stmtRev->fetch()['total'] ?? 0);

        // 2. Total Pesanan Hari Ini
        $stmtOrdersToday = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE DATE(created_at) = CURRENT_DATE AND deleted_at IS NULL");
        $ordersToday = (int) ($stmtOrdersToday->fetch()['total'] ?? 0);

        // 3. Antrean Menunggu Hari Ini
        $stmtPending = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE status = 'pending' AND deleted_at IS NULL");
        $pendingOrders = (int) ($stmtPending->fetch()['total'] ?? 0);

        // 4. Barber Bertugas
        $barbersOnDuty = User::getBarbersOnDuty();

        // 5. Total Pesanan Home Service (Cukur Panggilan)
        $stmtHomeService = $pdo->query("SELECT COUNT(*) as total FROM orders WHERE service_type = 'home_service' AND deleted_at IS NULL");
        $homeServiceCount = (int) ($stmtHomeService->fetch()['total'] ?? 0);

        // 6. Pesanan Terbaru & Rekap Penghasilan Multi-Periode
        $revenueRecap = \App\Models\SmartAnalytics::getRevenueRecapSummary();
        $recentOrders = array_slice(Order::allWithCustomer(), 0, 6);

        return $this->render('admin.dashboard', [
            'title' => 'Dashboard Karyawan - Wijaya Barbershop',
            'user' => Auth::user(),
            'totalRevenue' => $totalRevenue,
            'ordersToday' => $ordersToday,
            'pendingOrders' => $pendingOrders,
            'homeServiceCount' => $homeServiceCount,
            'barbersOnDuty' => $barbersOnDuty,
            'recentOrders' => $recentOrders,
            'revenueRecap' => $revenueRecap,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ], 'admin');
    }

    public function orders(Request $request, Response $response)
    {
        $this->checkStaffAuth($response);

        $statusFilter = $request->input('status') ?: null;
        $serviceTypeFilter = $request->input('service_type') ?: null;
        $orders = Order::allWithCustomer($statusFilter, $serviceTypeFilter);

        return $this->render('admin.orders', [
            'title' => 'Kelola Pesanan - Data Order',
            'orders' => $orders,
            'selectedStatus' => $statusFilter,
            'selectedServiceType' => $serviceTypeFilter,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ], 'admin');
    }

    public function orderDetail(Request $request, Response $response, array $params)
    {
        $this->checkStaffAuth($response);
        $orderId = (int) ($params['id'] ?? $request->input('id'));

        $order = Order::findWithDetails($orderId);
        if (!$order) {
            $this->setFlash('error', 'Data pesanan tidak ditemukan.');
            $response->redirect('/admin/orders');
        }

        $barbers = User::getBarbersOnDuty();

        // Siapkan Link WhatsApp Manual untuk Kasir/Admin
        $waConfirmMsg = WhatsAppService::buildPaymentSuccessMessage($order);
        $waPendingMsg = WhatsAppService::buildNewOrderMessage($order);
        $waCompletedMsg = WhatsAppService::buildOrderCompletedMessage($order);

        $waConfirmLink = WhatsAppService::createWaLink($order['customer_phone'] ?? '', $waConfirmMsg);
        $waPendingLink = WhatsAppService::createWaLink($order['customer_phone'] ?? '', $waPendingMsg);
        $waCompletedLink = WhatsAppService::createWaLink($order['customer_phone'] ?? '', $waCompletedMsg);

        return $this->render('admin.order_detail', [
            'title' => "Detail Pesanan #{$order['order_code']}",
            'order' => $order,
            'barbers' => $barbers,
            'waConfirmLink' => $waConfirmLink,
            'waPendingLink' => $waPendingLink,
            'waCompletedLink' => $waCompletedLink,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ], 'admin');
    }

    public function assignBarber(Request $request, Response $response)
    {
        $this->checkStaffAuth($response);

        $orderId = (int) $request->input('order_id');
        $barberId = $request->input('barber_id') ? (int) $request->input('barber_id') : null;

        Order::update($orderId, ['barber_id' => $barberId]);
        $this->setFlash('success', 'Capster / Barber bertugas berhasil diperbarui untuk pesanan ini.');
        $response->redirect("/admin/orders/{$orderId}");
    }

    public function updateOrderStatus(Request $request, Response $response)
    {
        $this->checkStaffAuth($response);

        $orderId = (int) $request->input('order_id');
        $newStatus = $request->input('status');

        $allowedStatuses = ['pending', 'settlement', 'completed', 'cancelled'];
        if (!in_array($newStatus, $allowedStatuses)) {
            $this->setFlash('error', 'Status pesanan tidak valid.');
            $response->redirect('/admin/orders');
        }

        Order::update($orderId, ['status' => $newStatus]);

        // Kirim Notifikasi WhatsApp Otomatis ke Pelanggan saat status berubah
        $order = Order::findWithDetails($orderId);
        if ($newStatus === 'settlement') {
            \App\Controllers\ApiController::recordQrisPaidSignal($order['order_code'] ?? null);
        }
        if ($order && !empty($order['customer_phone'])) {
            $waService = new WhatsAppService();
            if ($newStatus === 'settlement') {
                $msg = WhatsAppService::buildPaymentSuccessMessage($order);
                $waService->sendWhatsApp($order['customer_phone'], $msg);
                if (!empty($order['user_id'])) {
                    Loyalty::recordOrderSpend((int)$order['user_id'], (int)$order['grand_total'], (int)$order['id']);
                }
            } elseif ($newStatus === 'completed') {
                $msg = WhatsAppService::buildOrderCompletedMessage($order);
                $waService->sendWhatsApp($order['customer_phone'], $msg);
            }
        }

        $this->setFlash('success', "Status pesanan berhasil diperbarui menjadi {$newStatus} & notifikasi WhatsApp otomatis dikirimkan.");
        $response->redirect('/admin/orders');
    }

    public function items(Request $request, Response $response)
    {
        $this->checkStaffAuth($response);

        $items = Item::allWithCategory();

        return $this->render('admin.items', [
            'title' => 'Kelola Data Items - Wijaya Barbershop',
            'items' => $items,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ], 'admin');
    }

    public function createItem(Request $request, Response $response)
    {
        $this->checkStaffAuth($response);

        $categories = Category::all();

        return $this->render('admin.item_form', [
            'title' => 'Tambah Data Item - Wijaya Barbershop',
            'categories' => $categories,
            'item' => null,
            'error' => $this->getFlash('error')
        ], 'admin');
    }

    public function storeItem(Request $request, Response $response)
    {
        $this->checkStaffAuth($response);

        $itemName = trim($request->input('item_name', ''));
        $categoryId = (int) $request->input('category_id');
        $description = trim($request->input('description', ''));
        $price = (int) $request->input('price', 0);
        $isActive = $request->input('is_active') ? 1 : 0;

        if (empty($itemName) || empty($categoryId) || $price <= 0) {
            $this->setFlash('error', 'Nama item, kategori, dan harga wajib diisi.');
            $response->redirect('/admin/items/create');
        }

        // Handle upload foto atau input URL foto
        $imageUrl = trim($request->input('image_url', ''));
        $imagePath = !empty($imageUrl) ? $imageUrl : 'default-item.jpg';

        $file = $request->file('image');
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (in_array($ext, $allowed)) {
                $fileName = 'item_' . time() . '_' . rand(100, 999) . '.' . $ext;
                $targetDir = dirname(__DIR__, 2) . '/public/uploads/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                if (move_uploaded_file($file['tmp_name'], $targetDir . $fileName)) {
                    $imagePath = $fileName;
                }
            }
        }

        Item::create([
            'item_name' => $itemName,
            'category_id' => $categoryId,
            'description' => $description,
            'price' => $price,
            'image_path' => $imagePath,
            'is_active' => $isActive,
        ]);

        $this->setFlash('success', 'Data item/layanan berhasil disimpan.');
        $response->redirect('/admin/items');
    }

    public function editItem(Request $request, Response $response, array $params)
    {
        $this->checkStaffAuth($response);
        $id = (int) ($params['id'] ?? $request->input('id'));

        $item = Item::find($id);
        if (!$item) {
            $this->setFlash('error', 'Item tidak ditemukan.');
            $response->redirect('/admin/items');
        }

        $categories = Category::all();

        return $this->render('admin.item_form', [
            'title' => 'Edit Data Item - ' . $item['item_name'],
            'categories' => $categories,
            'item' => $item,
            'error' => $this->getFlash('error')
        ], 'admin');
    }

    public function updateItem(Request $request, Response $response, array $params)
    {
        $this->checkStaffAuth($response);
        $id = (int) ($params['id'] ?? $request->input('id'));

        $item = Item::find($id);
        if (!$item) {
            $this->setFlash('error', 'Item tidak ditemukan.');
            $response->redirect('/admin/items');
        }

        $itemName = trim($request->input('item_name', ''));
        $categoryId = (int) $request->input('category_id');
        $description = trim($request->input('description', ''));
        $price = (int) $request->input('price', 0);
        $isActive = $request->input('is_active') ? 1 : 0;

        $updateData = [
            'item_name' => $itemName,
            'category_id' => $categoryId,
            'description' => $description,
            'price' => $price,
            'is_active' => $isActive,
        ];

        // Handle URL gambar jika diisi
        $imageUrl = trim($request->input('image_url', ''));
        if (!empty($imageUrl)) {
            $updateData['image_path'] = $imageUrl;
        }

        // Handle upload file gambar
        $file = $request->file('image');
        if ($file && $file['error'] === UPLOAD_ERR_OK) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
            if (in_array($ext, $allowed)) {
                $fileName = 'item_' . time() . '_' . rand(100, 999) . '.' . $ext;
                $targetDir = dirname(__DIR__, 2) . '/public/uploads/';
                if (!is_dir($targetDir)) {
                    mkdir($targetDir, 0777, true);
                }
                if (move_uploaded_file($file['tmp_name'], $targetDir . $fileName)) {
                    $updateData['image_path'] = $fileName;
                }
            }
        }

        Item::update($id, $updateData);
        $this->setFlash('success', 'Data item/layanan berhasil diperbarui.');
        $response->redirect('/admin/items');
    }

    public function deleteItem(Request $request, Response $response)
    {
        $this->checkStaffAuth($response);
        $id = (int) $request->input('id');

        Item::delete($id);
        $this->setFlash('success', 'Item berhasil dihapus.');
        $response->redirect('/admin/items');
    }

    public function categories(Request $request, Response $response)
    {
        $this->checkStaffAuth($response);

        if ($request->isPost()) {
            $action = $request->input('action');
            if ($action === 'create') {
                $name = trim($request->input('category_name', ''));
                $desc = trim($request->input('description', ''));
                if (!empty($name)) {
                    Category::create(['category_name' => $name, 'description' => $desc]);
                    $this->setFlash('success', 'Kategori baru berhasil ditambahkan.');
                }
            } elseif ($action === 'delete') {
                $catId = (int) $request->input('category_id');
                Category::delete($catId);
                $this->setFlash('success', 'Kategori berhasil dihapus.');
            }
            $response->redirect('/admin/categories');
        }

        $categories = Category::withItemsCount();

        return $this->render('admin.categories', [
            'title' => 'Kelola Kategori - Wijaya Barbershop',
            'categories' => $categories,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ], 'admin');
    }

    public function users(Request $request, Response $response)
    {
        $this->checkStaffAuth($response);

        if ($request->isPost()) {
            $action = $request->input('action');
            if ($action === 'toggle_duty') {
                $userId = (int) $request->input('user_id');
                $user = User::find($userId);
                if ($user) {
                    $newDuty = $user['on_duty'] ? 0 : 1;
                    User::update($userId, ['on_duty' => $newDuty]);
                    $this->setFlash('success', "Status tugas barber {$user['fullname']} berhasil diubah.");
                }
            }
            $response->redirect('/admin/users');
        }

        $users = User::allWithRoles();

        return $this->render('admin.users', [
            'title' => 'Kelola Pengguna & Barber - Wijaya Barbershop',
            'users' => $users,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ], 'admin');
    }

    public function roles(Request $request, Response $response)
    {
        $this->checkStaffAuth($response);

        $roles = Role::all();

        return $this->render('admin.roles', [
            'title' => 'Kelola Role - Wijaya Barbershop',
            'roles' => $roles,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ], 'admin');
    }

    public function vouchers(Request $request, Response $response)
    {
        $this->checkStaffAuth($response);
        $vouchers = Voucher::all('id DESC');

        return $this->render('admin.vouchers', [
            'title' => 'Manajemen Voucher & Promo - Wijaya Barbershop',
            'vouchers' => $vouchers,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ], 'admin');
    }

    public function storeVoucher(Request $request, Response $response)
    {
        $this->checkStaffAuth($response);

        $code = strtoupper(trim($request->input('code', '')));
        $title = trim($request->input('title', ''));
        $description = trim($request->input('description', ''));
        $discountType = $request->input('discount_type', 'fixed');
        $discountValue = (int) $request->input('discount_value', 0);
        $minPurchase = (int) $request->input('min_purchase', 0);
        $maxDiscount = $request->input('max_discount') ? (int) $request->input('max_discount') : null;
        $usageLimit = (int) $request->input('usage_limit', 100);
        $serviceType = $request->input('service_type', 'all');
        $expiresAt = $request->input('expires_at') ? date('Y-m-d 23:59:59', strtotime($request->input('expires_at'))) : null;

        if (empty($code) || empty($title) || $discountValue <= 0) {
            $this->setFlash('error', 'Kode voucher, judul, dan nilai diskon wajib diisi dengan benar.');
            $response->redirect('/admin/vouchers');
        }

        $existing = Voucher::findByCode($code);
        if ($existing) {
            $this->setFlash('error', "Kode voucher '{$code}' sudah ada. Silakan gunakan kode lain.");
            $response->redirect('/admin/vouchers');
        }

        Voucher::create([
            'code' => $code,
            'title' => $title,
            'description' => $description,
            'discount_type' => $discountType,
            'discount_value' => $discountValue,
            'min_purchase' => $minPurchase,
            'max_discount' => $maxDiscount,
            'usage_limit' => $usageLimit,
            'used_count' => 0,
            'is_active' => 1,
            'service_type' => $serviceType,
            'expires_at' => $expiresAt
        ]);

        $this->setFlash('success', "Voucher promosi '{$code}' berhasil ditambahkan!");
        $response->redirect('/admin/vouchers');
    }

    public function toggleVoucher(Request $request, Response $response)
    {
        $this->checkStaffAuth($response);
        $id = (int) $request->input('id');
        $voucher = Voucher::find($id);

        if ($voucher) {
            $newStatus = $voucher['is_active'] ? 0 : 1;
            Voucher::update($id, ['is_active' => $newStatus]);
            $label = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
            $this->setFlash('success', "Status voucher '{$voucher['code']}' berhasil {$label}.");
        }

        $response->redirect('/admin/vouchers');
    }

    public function loyalty(Request $request, Response $response)
    {
        $this->checkStaffAuth($response);
        $leaderboard = Loyalty::getLeaderboard(20);
        $customers = User::where('role_id', 4);

        return $this->render('admin.loyalty', [
            'title' => 'Program Loyalitas & Membership Pelanggan - Wijaya Barbershop',
            'leaderboard' => $leaderboard,
            'customers' => $customers,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ], 'admin');
    }

    public function addRewardPoints(Request $request, Response $response)
    {
        $this->checkStaffAuth($response);
        $userId = (int) $request->input('user_id');
        $points = (int) $request->input('points', 0);
        $note = trim($request->input('note', 'Bonus reward apresiasi pelanggan dari staf/kasir'));

        if ($userId && $points > 0) {
            $user = User::find($userId);
            Loyalty::addPoints($userId, $points, $note);
            $this->setFlash('success', "Berhasil memberikan {$points} poin reward kepada pelanggan {$user['fullname']}!");
        } else {
            $this->setFlash('error', 'Pilih pelanggan dan tentukan jumlah poin yang valid.');
        }

        $response->redirect('/admin/loyalty');
    }
}

