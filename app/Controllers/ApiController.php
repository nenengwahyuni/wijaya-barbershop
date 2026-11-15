<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Models\Order;
use App\Models\Loyalty;
use App\Services\WhatsAppService;

class ApiController extends Controller
{
    /**
     * Webhook Midtrans HTTP POST Notification Handler
     */
    public function midtransNotification(Request $request, Response $response)
    {
        $payload = $request->all();

        $orderId = $payload['order_id'] ?? null;
        $transactionStatus = $payload['transaction_status'] ?? null;
        $fraudStatus = $payload['fraud_status'] ?? null;

        if (!$orderId || !$transactionStatus) {
            $this->json(['status' => 'error', 'message' => 'Invalid notification payload'], 400);
        }

        $order = Order::findByCode($orderId);
        if (!$order) {
            $this->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        $newStatus = $order['status'];

        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $newStatus = 'pending';
            } elseif ($fraudStatus == 'accept') {
                $newStatus = 'settlement';
            }
        } elseif ($transactionStatus == 'settlement') {
            $newStatus = 'settlement';
        } elseif ($transactionStatus == 'pending') {
            $newStatus = 'pending';
        } elseif (in_array($transactionStatus, ['deny', 'expire', 'cancel'])) {
            $newStatus = 'cancelled';
        }

        Order::update($order['id'], [
            'status' => $newStatus,
            'payment_method' => 'qris'
        ]);

        // Kirim Notifikasi WhatsApp Otomatis jika status menjadi settlement
        if ($newStatus === 'settlement') {
            $updatedOrder = Order::findWithDetails($order['id']);
            if ($updatedOrder && !empty($updatedOrder['customer_phone'])) {
                $waService = new WhatsAppService();
                $waMsg = WhatsAppService::buildPaymentSuccessMessage($updatedOrder);
                $waService->sendWhatsApp($updatedOrder['customer_phone'], $waMsg);
            }
            if ($updatedOrder && !empty($updatedOrder['user_id'])) {
                Loyalty::recordOrderSpend((int)$updatedOrder['user_id'], (int)$updatedOrder['grand_total'], (int)$updatedOrder['id']);
            }
        }

        $this->json([
            'status' => 'success',
            'order_code' => $orderId,
            'updated_status' => $newStatus
        ]);
    }

    /**
     * Simulator Pembayaran Instan untuk Testing Lokal / Ujian Skripsi
     */
    public function simulatePaymentSuccess(Request $request, Response $response)
    {
        $orderCode = $request->input('order_code');
        if (!$orderCode) {
            $this->json(['status' => 'error', 'message' => 'Kode order tidak valid'], 400);
        }

        $order = Order::findByCode($orderCode);
        if (!$order) {
            $this->json(['status' => 'error', 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        Order::update($order['id'], [
            'status' => 'settlement'
        ]);

        // Kirim Notifikasi WhatsApp Otomatis
        $updatedOrder = Order::findWithDetails($order['id']);
        $waLink = '';
        $waDesktopLink = '';
        if ($updatedOrder && !empty($updatedOrder['customer_phone'])) {
            $waService = new WhatsAppService();
            $waMsg = WhatsAppService::buildPaymentSuccessMessage($updatedOrder);
            $waService->sendWhatsApp($updatedOrder['customer_phone'], $waMsg);
            $waLink = WhatsAppService::createWaLink($updatedOrder['customer_phone'], $waMsg);
            $waDesktopLink = WhatsAppService::createWaDesktopLink($updatedOrder['customer_phone'], $waMsg);
        }

        // Catat perolehan poin loyalitas pelanggan
        if ($updatedOrder && !empty($updatedOrder['user_id'])) {
            Loyalty::recordOrderSpend((int)$updatedOrder['user_id'], (int)$updatedOrder['grand_total'], (int)$updatedOrder['id']);
        }

        $this->json([
            'status' => 'success',
            'message' => 'Pembayaran berhasil disimulasikan (Settlement)!',
            'order_code' => $orderCode,
            'wa_link' => $waLink,
            'wa_desktop_link' => $waDesktopLink
        ]);
    }

    private static function getSignalFilePath(): string
    {
        return rtrim(sys_get_temp_dir(), '/\\') . '/wijaya_qris_paid_signal.json';
    }

    public static function recordQrisPaidSignal(?string $orderCode = null): void
    {
        $file = self::getSignalFilePath();
        $data = [];
        if (file_exists($file)) {
            $raw = @file_get_contents($file);
            $decoded = $raw ? json_decode($raw, true) : [];
            if (is_array($decoded)) $data = $decoded;
        }
        $now = time();
        $data['last_paid_ts'] = $now;
        if ($orderCode) {
            $data['orders'][$orderCode] = $now;
        }
        @file_put_contents($file, json_encode($data));
    }

    private static function isSignalPaidForOrder(string $orderCode, int $orderCreatedTs): bool
    {
        $file = self::getSignalFilePath();
        if (!file_exists($file)) return false;
        $raw = @file_get_contents($file);
        $data = $raw ? json_decode($raw, true) : null;
        if (!is_array($data)) return false;

        if (!empty($data['orders'][$orderCode])) {
            return true;
        }
        if (!empty($data['last_paid_ts']) && $orderCreatedTs > 0 && $data['last_paid_ts'] >= $orderCreatedTs) {
            return true;
        }
        return false;
    }

    /**
     * Cek status pesanan secara real-time via AJAX
     */
    public function orderStatus(Request $request, Response $response)
    {
        $code = trim($request->input('code', ''));
        $createdTs = (int) $request->input('ts', 0);
        if (!$code) {
            $this->json(['status' => 'error', 'message' => 'Kode order diperlukan'], 400);
        }

        $order = Order::findByCode($code);
        if (!$order) {
            $this->json(['status' => 'error', 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        if ($order['status'] === 'pending' && self::isSignalPaidForOrder($code, $createdTs)) {
            Order::update($order['id'], [
                'status' => 'settlement',
                'payment_method' => 'qris'
            ]);
            $order['status'] = 'settlement';
        }

        $isSettlement = in_array($order['status'], ['settlement', 'completed']);

        $this->json([
            'status' => 'success',
            'order_code' => $code,
            'order_status' => $order['status'],
            'is_settlement' => $isSettlement,
            'payment_method' => $order['payment_method']
        ]);
    }

    /**
     * Webhook / Remote Trigger Pembayaran QRIS (Bisa dipanggil dari HP Kasir atau Notifikasi Forwarder)
     */
    public function qrisWebhook(Request $request, Response $response)
    {
        $code = trim($request->input('code', $request->input('order_code', '')));
        self::recordQrisPaidSignal($code ?: null);

        try {
            $pdo = \App\Core\Database::getConnection();
            if ($code) {
                $stmt = $pdo->prepare("UPDATE orders SET status = 'settlement', payment_method = 'qris' WHERE order_code = ?");
                $stmt->execute([$code]);
            } else {
                $pdo->exec("UPDATE orders SET status = 'settlement' WHERE payment_method = 'qris' AND status = 'pending'");
            }
        } catch (\Throwable $e) {}

        $this->json([
            'status' => 'success',
            'message' => 'Sinyal pembayaran QRIS Lunas berhasil dikirim! Layar pelanggan otomatis berbunyi.',
            'order_code' => $code ?: 'ALL_PENDING_QRIS'
        ]);
    }

    /**
     * Konfirmasi otomatis QRIS tanpa perlu klik tombol
     */
    public function qrisAutoConfirm(Request $request, Response $response)
    {
        $orderCode = trim($request->input('order_code', ''));
        if (!$orderCode) {
            $this->json(['status' => 'error', 'message' => 'Kode order tidak valid'], 400);
        }

        $order = Order::findByCode($orderCode);
        if (!$order) {
            $this->json(['status' => 'error', 'message' => 'Pesanan tidak ditemukan'], 404);
        }

        if ($order['status'] === 'pending') {
            Order::update($order['id'], [
                'status' => 'settlement',
                'payment_method' => 'qris'
            ]);

            $updatedOrder = Order::findWithDetails($order['id']);

            // Catat poin loyalitas
            if ($updatedOrder && !empty($updatedOrder['user_id'])) {
                Loyalty::recordOrderSpend((int)$updatedOrder['user_id'], (int)$updatedOrder['grand_total'], (int)$updatedOrder['id']);
            }

            // WhatsApp link
            $waMsg = WhatsAppService::buildPaymentSuccessMessage($updatedOrder);
            $waLink = WhatsAppService::createWaLink($updatedOrder['customer_phone'] ?? '', $waMsg);
            $waDesktopLink = WhatsAppService::createWaDesktopLink($updatedOrder['customer_phone'] ?? '', $waMsg);

            $this->json([
                'status' => 'success',
                'message' => 'Pembayaran QRIS berhasil dikonfirmasi otomatis!',
                'order_code' => $orderCode,
                'redirect' => "/receipt?code={$orderCode}&auto_wa=1&payment_status=success",
                'wa_link' => $waLink,
                'wa_desktop_link' => $waDesktopLink
            ]);
        }

        $this->json([
            'status' => 'success',
            'message' => 'Pesanan sudah berstatus lunas.',
            'order_code' => $orderCode,
            'redirect' => "/receipt?code={$orderCode}&auto_wa=1&payment_status=success"
        ]);
    }
}

