<?php

namespace App\Services;

class MidtransService
{
    private string $serverKey;
    private string $clientKey;
    private bool $isProduction;
    private string $snapApiUrl;

    public function __construct()
    {
        $this->serverKey = getenv('MIDTRANS_SERVER_KEY') ?: 'SB-Mid-server-WIJAYA-BARBERSHOP-DEMO-KEY';
        $this->clientKey = getenv('MIDTRANS_CLIENT_KEY') ?: 'SB-Mid-client-WIJAYA-BARBERSHOP-CLIENT-KEY';
        $this->isProduction = filter_var(getenv('MIDTRANS_IS_PRODUCTION') ?: false, FILTER_VALIDATE_BOOLEAN);
        $this->snapApiUrl = $this->isProduction
            ? 'https://app.midtrans.com/snap/v1/transactions'
            : 'https://app.sandbox.midtrans.com/snap/v1/transactions';
    }

    public function getClientKey(): string
    {
        return $this->clientKey;
    }

    public function getSnapToken(array $order, array $orderItems): array
    {
        $params = [
            'transaction_details' => [
                'order_id' => $order['order_code'],
                'gross_amount' => (int) $order['grand_total'],
            ],
            'customer_details' => [
                'first_name' => $order['customer_name'] ?? 'Pelanggan Wijaya Barber',
                'email' => $order['customer_email'] ?? 'pelanggan@wijayabarber.com',
                'phone' => $order['customer_phone'] ?? '081278412312',
            ],
            'item_details' => array_map(function ($item) {
                return [
                    'id' => (string) $item['item_id'],
                    'price' => (int) $item['price'],
                    'quantity' => (int) $item['quantity'],
                    'name' => mb_substr($item['item_name'], 0, 50),
                ];
            }, $orderItems)
        ];

        // Jika ada pajak, masukkan sebagai item detail
        if (!empty($order['tax']) && (int)$order['tax'] > 0) {
            $params['item_details'][] = [
                'id' => 'TAX-10',
                'price' => (int) $order['tax'],
                'quantity' => 1,
                'name' => 'PPN (10%)'
            ];
        }

        // Coba request ke API Midtrans jika cURL & internet aktif
        if (function_exists('curl_init')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->snapApiUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($params));
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Accept: application/json',
                'Authorization: Basic ' . base64_encode($this->serverKey . ':')
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

            $response = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);

            if ($httpCode === 200 || $httpCode === 201) {
                $resData = json_decode($response, true);
                if (isset($resData['token'])) {
                    return [
                        'token' => $resData['token'],
                        'redirect_url' => $resData['redirect_url'] ?? '',
                        'mode' => 'live_snap'
                    ];
                }
            }
        }

        // Mode Demo / Fallback Snap Token (Simulasi Midtrans jika di lingkungan offline/testing)
        $simulatedToken = 'SNAP-MOCK-' . md5($order['order_code'] . microtime(true));
        return [
            'token' => $simulatedToken,
            'redirect_url' => '',
            'mode' => 'mock_snap'
        ];
    }
}

