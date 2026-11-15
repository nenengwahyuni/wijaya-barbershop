<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Request;
use App\Core\Response;
use App\Core\Auth;
use App\Models\Voucher;
use App\Models\Loyalty;

class PromotionController extends Controller
{
    /**
     * Halaman Dashboard Membership VIP & Poin Loyalitas Pelanggan
     */
    public function membership(Request $request, Response $response)
    {
        Auth::initSession();
        $isLoggedIn = Auth::check();
        $userId = Auth::id();

        $loyalty = null;
        $history = [];
        if ($isLoggedIn && $userId) {
            $loyalty = Loyalty::getOrCreate($userId);
            $history = Loyalty::getPointHistory($userId, 15);
        }

        $activeVouchers = Voucher::getActiveVouchers('all');
        $leaderboard = Loyalty::getLeaderboard(5);

        return $this->render('customer.membership', [
            'title' => 'Wijaya VIP Club & Poin Loyalitas - Wijaya Barbershop',
            'isLoggedIn' => $isLoggedIn,
            'loyalty' => $loyalty,
            'history' => $history,
            'vouchers' => $activeVouchers,
            'leaderboard' => $leaderboard,
            'success' => $this->getFlash('success'),
            'error' => $this->getFlash('error')
        ], 'main');
    }

    /**
     * Halaman Publik Katalog Promo & Voucher Diskon
     */
    public function promotions(Request $request, Response $response)
    {
        Auth::initSession();
        $vouchers = Voucher::getActiveVouchers();

        return $this->render('customer.promotions', [
            'title' => 'Promo & Kupon Diskon Spesial - Wijaya Barbershop',
            'vouchers' => $vouchers,
            'isLoggedIn' => Auth::check(),
            'success' => $this->getFlash('success')
        ], 'main');
    }

    /**
     * API: Validasi & Terapkan Kode Voucher saat Checkout
     */
    public function apiApplyVoucher(Request $request, Response $response)
    {
        header('Content-Type: application/json');
        Auth::initSession();

        $code = trim($request->input('code', ''));
        $subtotal = (int) $request->input('subtotal', 0);
        $serviceType = $request->input('service_type', 'barbershop');
        $userId = Auth::id();

        if (empty($code)) {
            echo json_encode([
                'status' => 'error',
                'message' => 'Silakan ketikkan kode voucher.'
            ]);
            exit;
        }

        $result = Voucher::validateVoucher($code, $subtotal, $userId, $serviceType);

        if (!$result['valid']) {
            echo json_encode([
                'status' => 'error',
                'message' => $result['message']
            ]);
            exit;
        }

        echo json_encode([
            'status' => 'success',
            'voucher' => [
                'id' => $result['id'],
                'code' => $result['code'],
                'title' => $result['title'],
                'discount' => $result['discount'],
                'discount_formatted' => $result['discount_formatted']
            ],
            'message' => $result['message']
        ]);
        exit;
    }

    /**
     * API: Cek Poin Loyalitas & Diskon Member Otomatis
     */
    public function apiLoyaltyInfo(Request $request, Response $response)
    {
        header('Content-Type: application/json');
        Auth::initSession();

        $userId = Auth::id();
        if (!$userId) {
            echo json_encode([
                'status' => 'guest',
                'points' => 0,
                'point_discount_value' => 0,
                'tier' => 'bronze',
                'member_discount_percent' => 0
            ]);
            exit;
        }

        $loyalty = Loyalty::getOrCreate($userId);
        $maxRedeemPoints = min($loyalty['points'], 500); // Batas maksimal redeem per order
        $pointRupiah = $maxRedeemPoints * 50; // 1 poin = Rp 50

        echo json_encode([
            'status' => 'success',
            'user_name' => $loyalty['fullname'],
            'tier' => $loyalty['tier'],
            'tier_name' => $loyalty['benefits']['name'],
            'member_discount_percent' => $loyalty['benefits']['discount_percent'],
            'points' => (int) $loyalty['points'],
            'max_redeem_points' => $maxRedeemPoints,
            'point_discount_value' => $pointRupiah,
            'point_rate' => 50
        ]);
        exit;
    }
}

