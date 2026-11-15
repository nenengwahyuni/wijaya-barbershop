<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;
use App\Models\Item;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Voucher;
use App\Models\Loyalty;
use App\Services\QueueService;
use App\Services\WhatsAppService;

class BookingController extends Controller
{
    public function cart(Request $request, Response $response)
    {
        Auth::initSession();
        $cart = $_SESSION['cart'] ?? [];

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $tax = (int) round($subtotal * 0.10);
        $grandTotal = $subtotal + $tax;

        return $this->render('customer.cart', [
            'title' => 'Keranjang Layanan - Wijaya Barbershop',
            'cart' => $cart,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'grandTotal' => $grandTotal,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ], 'main');
    }

    public function addToCart(Request $request, Response $response)
    {
        Auth::initSession();
        $itemId = (int) $request->input('item_id');
        $item = Item::find($itemId);

        if (!$item) {
            $this->setFlash('error', 'Item layanan tidak ditemukan.');
            $response->redirect('/services');
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        if (isset($_SESSION['cart'][$itemId])) {
            $_SESSION['cart'][$itemId]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$itemId] = [
                'id' => $item['id'],
                'name' => $item['item_name'],
                'price' => (int) $item['price'],
                'image_path' => $item['image_path'],
                'quantity' => 1
            ];
        }

        $this->setFlash('success', "{$item['item_name']} berhasil dimasukkan ke keranjang.");
        
        if ($request->input('direct_checkout')) {
            $response->redirect('/checkout');
        } else {
            $response->redirect('/cart');
        }
    }

    public function updateCart(Request $request, Response $response)
    {
        Auth::initSession();
        $itemId = (int) $request->input('item_id');
        $action = $request->input('action');

        if (isset($_SESSION['cart'][$itemId])) {
            if ($action === 'increase') {
                $_SESSION['cart'][$itemId]['quantity'] += 1;
            } elseif ($action === 'decrease') {
                $_SESSION['cart'][$itemId]['quantity'] -= 1;
                if ($_SESSION['cart'][$itemId]['quantity'] <= 0) {
                    unset($_SESSION['cart'][$itemId]);
                }
            } elseif ($action === 'remove') {
                unset($_SESSION['cart'][$itemId]);
            }
        }

        $response->redirect('/cart');
    }

    public static function getAreaZones(): array
    {
        return [
            'Metro Pusat' => [
                'label' => 'Area 1 - Metro Pusat (Imopuro, Metro, Yosomulyo, Hadimulyo)',
                'fee' => 1000,
                'distance' => 'Radius 0 - 3 KM'
            ],
            'Metro Timur / Barat / Utara / Selatan' => [
                'label' => 'Area 2 - Metro Timur / Barat / Utara / Selatan (Iringmulyo, Tejoagung, Ganjar Agung, Mulyojati)',
                'fee' => 1200,
                'distance' => 'Radius 3 - 7 KM'
            ],
            'Luar Kota Metro (Sekitar)' => [
                'label' => 'Area 3 - Sekitar Kota Metro (Pekalongan, Batanghari, Trimurjo, Kibang)',
                'fee' => 1500,
                'distance' => 'Radius 7 - 15 KM'
            ]
        ];
    }

    public function homeService(Request $request, Response $response)
    {
        Auth::initSession();
        $user = Auth::user();
        $items = Item::allActiveWithCategory();
        $barbers = \App\Models\User::getBarbersOnDuty();
        $areaZones = self::getAreaZones();
        $queueInfo = QueueService::generateQueueInfo();

        $loyalty = null;
        if ($user && isset($user['id'])) {
            $loyalty = Loyalty::getOrCreate((int)$user['id']);
        }
        $vouchers = Voucher::getActiveVouchers('home_service');

        return $this->render('customer.home_service', [
            'title' => 'Cukur Panggilan (Home Service) - Wijaya Barbershop',
            'items' => $items,
            'barbers' => $barbers,
            'areaZones' => $areaZones,
            'user' => $user,
            'loyalty' => $loyalty,
            'vouchers' => $vouchers,
            'queueInfo' => $queueInfo,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ], 'main');
    }

    public function storeHomeService(Request $request, Response $response)
    {
        Auth::initSession();

        $selectedItems = $_POST['items'] ?? [];
        $quantities = $_POST['qty'] ?? [];

        $cartItems = [];
        if (is_array($selectedItems)) {
            foreach ($selectedItems as $itemId) {
                $itemId = (int) $itemId;
                $item = Item::find($itemId);
                if ($item) {
                    $qty = isset($quantities[$itemId]) ? max(1, (int) $quantities[$itemId]) : 1;
                    $cartItems[] = [
                        'id' => $item['id'],
                        'name' => $item['item_name'],
                        'price' => (int) $item['price'],
                        'quantity' => $qty
                    ];
                }
            }
        }

        if (empty($cartItems)) {
            $this->setFlash('error', 'Silakan pilih minimal 1 layanan pangkas rambut / perawatan untuk Cukur Panggilan.');
            $response->redirect('/home-service');
        }

        // Simpan ke session cart sementara lalu proses via mekanisme order Home Service
        $_SESSION['cart'] = [];
        foreach ($cartItems as $ci) {
            $_SESSION['cart'][$ci['id']] = $ci;
        }
        $_POST['service_type'] = 'home_service';

        return $this->storeOrder($request, $response);
    }

    public function checkout(Request $request, Response $response)
    {
        Auth::initSession();
        $cart = $_SESSION['cart'] ?? [];

        if (empty($cart)) {
            $this->setFlash('error', 'Keranjang Anda masih kosong. Silakan pilih layanan terlebih dahulu.');
            $response->redirect('/services');
        }

        $user = Auth::user();
        $barbers = \App\Models\User::getBarbersOnDuty();
        $areaZones = self::getAreaZones();
        $selectedType = $request->input('type') === 'home_service' ? 'home_service' : 'barbershop';

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $tax = (int) round($subtotal * 0.10);
        $grandTotal = $subtotal + $tax;

        // Simulasi estimasi nomor antrean
        $queueInfo = QueueService::generateQueueInfo();

        $loyalty = null;
        if ($user && isset($user['id'])) {
            $loyalty = Loyalty::getOrCreate((int)$user['id']);
        }
        $vouchers = Voucher::getActiveVouchers($selectedType);

        return $this->render('customer.checkout', [
            'title' => 'Formulir Pemesanan & Pembayaran - Wijaya Barbershop',
            'cart' => $cart,
            'subtotal' => $subtotal,
            'tax' => $tax,
            'grandTotal' => $grandTotal,
            'user' => $user,
            'loyalty' => $loyalty,
            'vouchers' => $vouchers,
            'barbers' => $barbers,
            'areaZones' => $areaZones,
            'selectedType' => $selectedType,
            'queueInfo' => $queueInfo,
            'error' => $this->getFlash('error')
        ], 'main');
    }

    public function storeOrder(Request $request, Response $response)
    {
        Auth::initSession();
        $cart = $_SESSION['cart'] ?? [];

        if (empty($cart)) {
            $this->setFlash('error', 'Keranjang Anda masih kosong.');
            $response->redirect('/services');
        }

        $fullname = trim($request->input('fullname', ''));
        $phone = trim($request->input('phone', ''));
        $notes = trim($request->input('notes', ''));
        $paymentMethod = $request->input('payment_method', 'tunai'); // 'tunai' atau 'qris'
        $serviceType = $request->input('service_type', 'barbershop') === 'home_service' ? 'home_service' : 'barbershop';
        $barberId = $request->input('barber_id') ? (int) $request->input('barber_id') : null;

        $redirectBackUrl = ($request->getPath() === '/home-service') ? '/home-service' : '/checkout';

        if (empty($fullname) || empty($phone)) {
            $this->setFlash('error', 'Nama lengkap dan nomor WhatsApp wajib diisi.');
            $response->redirect($redirectBackUrl);
        }

        // Validasi khusus untuk Home Service (Cukur Panggilan)
        $customerAddress = null;
        $areaZone = null;
        $deliveryFee = 0;
        $scheduledAt = null;

        if ($serviceType === 'home_service') {
            $customerAddress = trim($request->input('customer_address', ''));
            $landmark = trim($request->input('landmark', ''));
            if (!empty($landmark)) {
                $customerAddress .= " (Patokan: {$landmark})";
            }

            $areaZone = trim($request->input('area_zone', 'Metro Pusat'));
            $zones = self::getAreaZones();
            $deliveryFee = $zones[$areaZone]['fee'] ?? 1200;

            $scheduledDate = trim($request->input('scheduled_date', date('Y-m-d')));
            $scheduledTime = trim($request->input('scheduled_time', date('H:i', strtotime('+1 hour'))));
            $scheduledAt = date('Y-m-d H:i:s', strtotime("{$scheduledDate} {$scheduledTime}"));

            if (empty($customerAddress)) {
                $this->setFlash('error', 'Alamat lengkap kunjungan wajib diisi untuk layanan Cukur Panggilan (Home Service).');
                $response->redirect($redirectBackUrl);
            }

            // Jika pelanggan belum memilih capster untuk home service, pilih otomatis capster yang bertugas
            if (!$barberId) {
                $onDutyBarbers = \App\Models\User::getBarbersOnDuty();
                if (!empty($onDutyBarbers)) {
                    $barberId = (int) $onDutyBarbers[0]['id'];
                }
            }
        }

        // Sinkronkan data customer dari input formulir
        $userId = Auth::id();
        if ($userId) {
            // Selalu update profil user dengan nama dan nomor WhatsApp yang diisi pada formulir pemesanan
            $updateUser = [];
            if (!empty($fullname)) $updateUser['fullname'] = $fullname;
            if (!empty($phone)) $updateUser['phone'] = $phone;
            if (!empty($updateUser)) {
                \App\Models\User::update($userId, $updateUser);
                if (isset($_SESSION['user'])) {
                    $_SESSION['user']['fullname'] = $fullname;
                    $_SESSION['user']['phone'] = $phone;
                }
            }
        } else {
            // Cek apakah nomor HP / customer sudah ada
            $user = \App\Models\User::whereFirst('phone', $phone);
            if ($user) {
                $userId = $user['id'];
                \App\Models\User::update($userId, ['fullname' => $fullname]);
            } else {
                $username = 'cust_' . substr(preg_replace('/[^0-9]/', '', $phone), -6);
                if (\App\Models\User::whereFirst('username', $username)) {
                    $username .= '_' . rand(10, 99);
                }
                $userId = \App\Models\User::create([
                    'username' => $username,
                    'password' => password_hash('password123', PASSWORD_BCRYPT),
                    'fullname' => $fullname,
                    'email' => $username . '@wijayabarber.com',
                    'phone' => $phone,
                    'on_duty' => 0,
                    'role_id' => 4
                ]);
            }
        }

        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        // Kalkulasi Diskon Promo, Diskon Member VIP, dan Penukaran Poin
        $voucherCode = trim($request->input('voucher_code', ''));
        $redeemPoints = (int) $request->input('redeem_points', 0);

        $loyalty = $userId ? Loyalty::getOrCreate($userId) : null;
        $voucherDiscount = 0;
        $memberDiscount = 0;
        $pointsDiscount = 0;

        // 1. Validasi Voucher
        if (!empty($voucherCode)) {
            $vRes = Voucher::validateVoucher($voucherCode, $subtotal, $userId, $serviceType);
            if ($vRes['valid']) {
                $voucherDiscount = $vRes['discount'];
                Voucher::incrementUsage($vRes['id']);
            }
        }

        // 2. Diskon Member Otomatis berdasarkan Tier (Silver 5%, Gold 10%, Platinum 15%)
        if ($loyalty && !empty($loyalty['benefits']['discount_percent'])) {
            $memberDiscount = (int) round(($subtotal * $loyalty['benefits']['discount_percent']) / 100);
        }

        // 3. Penukaran Poin Loyalitas
        if ($loyalty && $redeemPoints > 0) {
            $maxCanRedeem = min($loyalty['points'], 500);
            $redeemPoints = min($redeemPoints, $maxCanRedeem);
            if ($redeemPoints > 0) {
                $pointsDiscount = $redeemPoints * 50; // 1 poin = Rp 50
                Loyalty::redeemPoints($userId, $redeemPoints);
            }
        }

        $totalDiscount = min($subtotal, $voucherDiscount + $memberDiscount + $pointsDiscount);
        $taxableAmount = max(0, $subtotal - $totalDiscount);
        $tax = (int) round($taxableAmount * 0.10);
        $grandTotal = max(0, $taxableAmount + $tax + $deliveryFee);

        // Hitung estimasi poin yang didapat
        $multiplier = $loyalty ? $loyalty['benefits']['point_multiplier'] : 1.0;
        $pointsEarned = (int) round(floor($grandTotal / 1000) * $multiplier);

        // Generate nomor antrean dan kode order
        $queueInfo = QueueService::generateQueueInfo();
        $orderCode = 'ORD' . date('ymd') . rand(1000, 9999);

        // Untuk Home Service, queue_time mengikuti jadwal kunjungan yang dipilih pelanggan
        $queueTime = ($serviceType === 'home_service' && $scheduledAt) ? $scheduledAt : $queueInfo['queue_time'];

        // Status awal: jika QRIS pending menunggu pembayaran, jika tunai pending menunggu bayar di kasir/barber
        $status = 'pending';

        $orderId = Order::create([
            'order_code' => $orderCode,
            'user_id' => $userId,
            'customer_name' => $fullname,
            'customer_phone' => $phone,
            'service_type' => $serviceType,
            'barber_id' => $barberId,
            'subtotal' => (string) $subtotal,
            'discount' => (string) $totalDiscount,
            'voucher_code' => $voucherCode ?: null,
            'points_redeemed' => $redeemPoints,
            'points_earned' => $pointsEarned,
            'tax' => (string) $tax,
            'delivery_fee' => $deliveryFee,
            'grand_total' => (string) $grandTotal,
            'status' => $status,
            'payment_method' => $paymentMethod,
            'notes' => $notes,
            'customer_address' => $customerAddress,
            'area_zone' => $areaZone,
            'queue_number' => $queueInfo['queue_number'],
            'queue_time' => $queueTime,
            'scheduled_at' => $scheduledAt,
        ]);

        // Simpan order items
        $orderItemsList = [];
        foreach ($cart as $cartItem) {
            $itemTax = (int) round($cartItem['price'] * 0.10);
            $totalItemPrice = ($cartItem['price'] + $itemTax) * $cartItem['quantity'];

            OrderItem::create([
                'order_id' => $orderId,
                'item_id' => $cartItem['id'],
                'quantity' => $cartItem['quantity'],
                'price' => $cartItem['price'],
                'tax' => $itemTax,
                'total_price' => $totalItemPrice
            ]);

            $orderItemsList[] = [
                'item_id' => $cartItem['id'],
                'item_name' => $cartItem['name'],
                'price' => $cartItem['price'],
                'quantity' => $cartItem['quantity']
            ];
        }

        // Hapus keranjang
        unset($_SESSION['cart']);

        // Kirim Notifikasi Otomatis WhatsApp ke Pelanggan
        $orderRecord = Order::findWithDetails($orderId);
        if ($orderRecord && !empty($orderRecord['customer_phone'])) {
            $waService = new WhatsAppService();
            $waMessage = WhatsAppService::buildNewOrderMessage($orderRecord);
            $waService->sendWhatsApp($orderRecord['customer_phone'], $waMessage);
        }

        if ($serviceType === 'home_service') {
            $this->setFlash('success', 'Pesanan Cukur Panggilan (Home Service) berhasil dibuat! Capster kami akan datang sesuai jadwal.');
        } else {
            $this->setFlash('success', 'Pemesanan berhasil dikonfirmasi! Silakan simpan nomor antrean Anda.');
        }

        $showQrisParam = ($paymentMethod === 'qris') ? '&show_qris=1' : '';
        $response->redirect("/receipt?code={$orderCode}&auto_wa=1{$showQrisParam}");
    }

    public function receipt(Request $request, Response $response)
    {
        $code = $request->input('code');
        if (empty($code)) {
            $response->redirect('/');
        }

        $order = Order::findByCode($code);
        if (!$order) {
            $this->setFlash('error', 'Pesanan tidak ditemukan.');
            $response->redirect('/');
        }

        Auth::initSession();

        // Tampilkan modal QRIS otomatis jika metode bayar QRIS dan status masih pending
        $showQris = ($request->input('show_qris') === '1') || ($order['payment_method'] === 'qris' && $order['status'] === 'pending');

        // WhatsApp direct link untuk kemudahan konfirmasi pelanggan
        $waMessage = ($order['status'] === 'settlement' || $order['status'] === 'completed')
            ? WhatsAppService::buildPaymentSuccessMessage($order)
            : WhatsAppService::buildNewOrderMessage($order);
        $waLink = WhatsAppService::createWaLink($order['customer_phone'] ?? '', $waMessage);
        $waDesktopLink = WhatsAppService::createWaDesktopLink($order['customer_phone'] ?? '', $waMessage);
        $autoWa = $request->input('auto_wa') === '1' || $request->input('payment_status') === 'success';

        return $this->render('customer.receipt', [
            'title' => "Resi Pemesanan #{$order['order_code']} - Wijaya Barbershop",
            'order' => $order,
            'showQris' => $showQris,
            'waLink' => $waLink,
            'waDesktopLink' => $waDesktopLink,
            'waMessage' => $waMessage,
            'autoWa' => $autoWa,
            'success' => $this->getFlash('success')
        ], 'main');
    }

    public function confirmQrisPayment(Request $request, Response $response)
    {
        $code = trim($request->input('order_code', ''));
        if (empty($code)) {
            if ($request->isAjax()) {
                return $response->json(['status' => 'error', 'message' => 'Kode pesanan tidak valid.'], 400);
            }
            $this->setFlash('error', 'Kode pesanan tidak valid.');
            $response->redirect('/');
        }

        $order = Order::findByCode($code);
        if (!$order) {
            if ($request->isAjax()) {
                return $response->json(['status' => 'error', 'message' => 'Pesanan tidak ditemukan.'], 404);
            }
            $this->setFlash('error', 'Pesanan tidak ditemukan.');
            $response->redirect('/');
        }

        if ($order['status'] === 'pending') {
            Order::update($order['id'], [
                'status' => 'settlement',
                'payment_method' => 'qris'
            ]);

            // Tambahkan poin loyalitas jika order milik customer terdaftar
            if (!empty($order['user_id'])) {
                Loyalty::recordOrderSpend((int)$order['user_id'], (int)$order['grand_total'], (int)$order['id']);
            }

            $this->setFlash('success', 'Pembayaran QRIS berhasil diverifikasi! Pesanan Anda telah lunas.');
        }

        if ($request->isAjax()) {
            $updatedOrder = Order::findWithDetails($order['id']);
            $waMsg = WhatsAppService::buildPaymentSuccessMessage($updatedOrder);
            $waLink = WhatsAppService::createWaLink($updatedOrder['customer_phone'] ?? '', $waMsg);
            $waDesktopLink = WhatsAppService::createWaDesktopLink($updatedOrder['customer_phone'] ?? '', $waMsg);

            return $response->json([
                'status' => 'success',
                'message' => 'Pembayaran QRIS berhasil diverifikasi otomatis!',
                'order_code' => $code,
                'redirect' => "/receipt?code={$code}&auto_wa=1&payment_status=success",
                'wa_link' => $waLink,
                'wa_desktop_link' => $waDesktopLink
            ]);
        }

        $response->redirect("/receipt?code={$code}&auto_wa=1&payment_status=success");
    }

    public function myOrders(Request $request, Response $response)
    {
        if (!Auth::check()) {
            $this->setFlash('error', 'Silakan masuk terlebih dahulu untuk melihat pesanan Anda.');
            $response->redirect('/login');
        }

        $userId = Auth::id();
        $orders = Order::getByUser($userId);

        return $this->render('customer.my_orders', [
            'title' => 'Riwayat Pesanan Saya - Wijaya Barbershop',
            'orders' => $orders,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ], 'main');
    }
}

