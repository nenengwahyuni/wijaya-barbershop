<?php

namespace App\Services;

class WhatsAppService
{
    private string $gatewayUrl;
    private string $apiKey;
    private bool $autoSendEnabled;

    public function __construct()
    {
        $this->gatewayUrl = getenv('FONNTE_URL') ?: 'https://api.fonnte.com/send';
        $this->apiKey = getenv('FONNTE_TOKEN') ?: (getenv('WA_API_KEY') ?: '');
        $this->autoSendEnabled = filter_var(getenv('WA_AUTO_SEND') ?? true, FILTER_VALIDATE_BOOLEAN);
    }

    /**
     * Format nomor ponsel menjadi format internasional (misal: 62812xxxxxxxx)
     */
    public static function formatPhone(string $phone): string
    {
        $cleaned = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($cleaned, '0')) {
            $cleaned = '62' . substr($cleaned, 1);
        } elseif (str_starts_with($cleaned, '8')) {
            $cleaned = '62' . $cleaned;
        }
        return $cleaned;
    }

    /**
     * Buat URL link langsung WhatsApp (api.whatsapp.com / wa.me)
     */
    public static function createWaLink(string $phone, string $message): string
    {
        $formatted = self::formatPhone($phone);
        return 'https://api.whatsapp.com/send?phone=' . $formatted . '&text=' . rawurlencode($message);
    }

    /**
     * Buat link langsung untuk membuka Aplikasi WhatsApp Desktop di Laptop Windows (whatsapp://)
     */
    public static function createWaDesktopLink(string $phone, string $message): string
    {
        $formatted = self::formatPhone($phone);
        return 'whatsapp://send?phone=' . $formatted . '&text=' . rawurlencode($message);
    }

    /**
     * Template pesan untuk Pesanan Baru Masuk (Pending)
     */
    public static function buildNewOrderMessage(array $order): string
    {
        $custName = $order['customer_name'] ?? 'Pelanggan';
        $orderCode = $order['order_code'] ?? '-';
        $isHome = ($order['service_type'] ?? 'barbershop') === 'home_service';
        $queueNo = str_pad($order['queue_number'] ?? '1', 2, '0', STR_PAD_LEFT);
        $waktu = date('d M Y, H:i', strtotime($order['scheduled_at'] ?: ($order['queue_time'] ?? 'now'))) . ' WIB';
        $grandTotal = number_format((int)($order['grand_total'] ?? 0), 0, ',', '.');
        $metode = strtoupper($order['payment_method'] ?? 'TUNAI');

        $msg = "*KONFIRMASI PEMESANAN — WIJAYA BARBERSHOP*\n";
        $msg .= "----------------------------------------\n";
        $msg .= "Halo Kak *{$custName}*,\n";
        $msg .= "Terima kasih telah melakukan pemesanan di *Wijaya Barbershop Kota Metro*.\n\n";
        $msg .= "📋 *Detail Pesanan:*\n";
        $msg .= "• Kode Order: *#{$orderCode}*\n";
        $msg .= "• Layanan: *" . ($isHome ? 'Cukur Panggilan (Home Service)' : 'Pangkas Rambut di Studio') . "*\n";
        $msg .= "• No Antrean: *#{$queueNo}*\n";
        $msg .= "• Jadwal: *{$waktu}*\n";

        if ($isHome) {
            $alamat = $order['customer_address'] ?? '-';
            $barber = $order['barber_name'] ?? 'Akan Ditugaskan';
            $msg .= "• Capster Bertugas: *{$barber}*\n";
            $msg .= "• Alamat Tujuan: {$alamat}\n";
        }

        if (!empty($order['discount']) && (int)$order['discount'] > 0) {
            $discFormatted = number_format((int)$order['discount'], 0, ',', '.');
            $msg .= "• Diskon Promosi/Poin: *-Rp {$discFormatted}* 🎟️\n";
        }
        if (!empty($order['voucher_code'])) {
            $msg .= "• Kupon Promo: *#{$order['voucher_code']}*\n";
        }

        $msg .= "• Total Tagihan: *Rp {$grandTotal}*\n";
        if (!empty($order['points_earned']) && (int)$order['points_earned'] > 0) {
            $msg .= "• Estimasi Poin Didapat: *+{$order['points_earned']} Poin VIP* ⭐\n";
        }
        $msg .= "• Metode Bayar: *{$metode}*\n";
        $msg .= "• Status: *MENUNGGU PEMBAYARAN (Pending)*\n\n";

        if ($metode === 'QRIS') {
            $msg .= "ℹ️ _Silakan selesaikan pembayaran QRIS Anda agar antrean/jadwal langsung terverifikasi._\n\n";
        } else {
            if ($isHome) {
                $msg .= "ℹ️ _Pembayaran tunai dapat diserahkan langsung ke Capster kami saat tiba di lokasi._\n\n";
            } else {
                $msg .= "ℹ️ _Silakan datang ke studio dan tunjukkan nomor antrean ini ke kasir (Mely/Riles)._\n\n";
            }
        }

        $appUrl = getenv('APP_URL') ?: 'http://localhost:8000';
        $msg .= "🔗 Cek Resi & Tiket Digital: {$appUrl}/receipt?code={$orderCode}\n\n";
        $msg .= "Salam hangat,\n*Wijaya Barbershop Metro Lampung*\n_Modern Haircut & Grooming_";

        return $msg;
    }

    /**
     * Template pesan untuk Konfirmasi Pembayaran Berhasil (Settlement/Lunas)
     */
    public static function buildPaymentSuccessMessage(array $order): string
    {
        $custName = $order['customer_name'] ?? 'Pelanggan';
        $orderCode = $order['order_code'] ?? '-';
        $isHome = ($order['service_type'] ?? 'barbershop') === 'home_service';
        $queueNo = str_pad($order['queue_number'] ?? '1', 2, '0', STR_PAD_LEFT);
        $waktu = date('d M Y, H:i', strtotime($order['scheduled_at'] ?: ($order['queue_time'] ?? 'now'))) . ' WIB';
        $grandTotal = number_format((int)($order['grand_total'] ?? 0), 0, ',', '.');

        $msg = "*PEMBAYARAN TERKONFIRMASI LUNAS — WIJAYA BARBERSHOP*\n";
        $msg .= "----------------------------------------\n";
        $msg .= "Halo Kak *{$custName}*,\n";
        $msg .= "Pembayaran Anda sebesar *Rp {$grandTotal}* untuk pesanan *#{$orderCode}* telah *BERHASIL TERVERIFIKASI (Lunas)*. ✅\n\n";

        if ($isHome) {
            $barber = $order['barber_name'] ?? 'Capster Wijaya Barber';
            $msg .= "🛵 *Status Cukur Panggilan:*\n";
            $msg .= "• Jadwal Kunjungan: *{$waktu}*\n";
            $msg .= "• Capster: *{$barber}*\n";
            $msg .= "• Alamat: " . ($order['customer_address'] ?? '-') . "\n\n";
            $msg .= "Capster kami akan menghubungi nomor ini saat hendak berangkat menuju lokasi Anda.\n\n";
        } else {
            $msg .= "💈 *Status Antrean Studio:*\n";
            $msg .= "• Nomor Antrean: *#{$queueNo}*\n";
            $msg .= "• Perkiraan Jam Layanan: *{$waktu}*\n\n";
            $msg .= "Silakan datang ke Wijaya Barbershop tepat waktu dan tunjukkan tiket digital Anda.\n\n";
        }

        $appUrl = getenv('APP_URL') ?: 'http://localhost:8000';
        $msg .= "🔗 Cetak / Lihat Resi Lunas: {$appUrl}/receipt?code={$orderCode}\n\n";
        $msg .= "Terima kasih!\n*Wijaya Barbershop*";

        return $msg;
    }

    /**
     * Template pesan untuk Pesanan Selesai (Completed)
     */
    public static function buildOrderCompletedMessage(array $order): string
    {
        $custName = $order['customer_name'] ?? 'Pelanggan';
        $orderCode = $order['order_code'] ?? '-';

        $msg = "*TERIMA KASIH — WIJAYA BARBERSHOP*\n";
        $msg .= "----------------------------------------\n";
        $msg .= "Halo Kak *{$custName}*,\n";
        $msg .= "Layanan pangkas rambut untuk pesanan *#{$orderCode}* telah *SELESAI DILAKUKAN*! ✂️💈\n\n";
        $msg .= "Semoga puas dengan penampilan baru dan pelayanan kami. Jangan lupa untuk berkunjung kembali atau memesan Cukur Panggilan di lain waktu.\n\n";
        $msg .= "Salam ganteng,\n*Wijaya Barbershop Metro*";

        return $msg;
    }

    /**
     * Kirim pesan WhatsApp melalui Fonnte / API Gateway (jika API key diset),
     * atau simpan ke log/notifikasi sistem untuk simulasi jika offline.
     */
    public function sendWhatsApp(string $phone, string $message): array
    {
        $target = self::formatPhone($phone);
        $result = [
            'success' => false,
            'phone' => $target,
            'message' => $message,
            'wa_link' => self::createWaLink($phone, $message),
            'log' => ''
        ];

        // Jika Fonnte / WhatsApp Gateway token tersedia dan cURL aktif
        if (!empty($this->apiKey) && $this->autoSendEnabled && function_exists('curl_init')) {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $this->gatewayUrl,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_TIMEOUT => 5,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => [
                    'target' => $target,
                    'message' => $message,
                    'countryCode' => '62'
                ],
                CURLOPT_HTTPHEADER => [
                    'Authorization: ' . $this->apiKey
                ],
                CURLOPT_SSL_VERIFYPEER => false
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if (!$err && ($httpCode === 200 || $httpCode === 201)) {
                $result['success'] = true;
                $result['log'] = 'Terkirim via WhatsApp Gateway API: ' . $response;
                self::logNotification($target, $message, 'SENT_API');
                return $result;
            }
        }

        // Mode Simulasi Otomatis / Log Notifikasi WhatsApp
        $result['success'] = true;
        $result['log'] = 'Notifikasi WhatsApp disiapkan otomatis (Simulasi / Direct Link).';
        self::logNotification($target, $message, 'SIMULATED');

        return $result;
    }

    /**
     * Catat log notifikasi WhatsApp ke file log
     */
    private static function logNotification(string $target, string $message, string $status): void
    {
        $logDir = dirname(__DIR__, 2) . '/storage/logs';
        if (!is_dir($logDir)) {
            mkdir($logDir, 0777, true);
        }
        $logFile = $logDir . '/whatsapp.log';
        $timestamp = date('Y-m-d H:i:s');
        $logEntry = "[{$timestamp}] [{$status}] Target: {$target}\n{$message}\n" . str_repeat('=', 50) . "\n\n";
        file_put_contents($logFile, $logEntry, FILE_APPEND);
    }
}

