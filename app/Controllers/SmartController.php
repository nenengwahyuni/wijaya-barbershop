<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Auth;
use App\Models\SmartAnalytics;

/**
 * SmartController — Sistem Cerdas Wijaya Barbershop
 * Menangani analytics admin dan asisten pintar pelanggan.
 */
class SmartController extends Controller
{
    // ─────────────────────────────────────────────────────────────
    //  ADMIN: Halaman Analitik Cerdas
    // ─────────────────────────────────────────────────────────────

    public function adminAnalytics(): void
    {
        $user = Auth::user();
        if (!$user || !in_array($user['role_name'] ?? '', ['admin', 'direktur', 'kasir', 'capster', 'barber'])) {
            header('Location: /login');
            exit;
        }

        $data = [
            'title'          => 'Analitik Cerdas — AI Insights',
            'revenue_chart'  => SmartAnalytics::getRevenueChart(7),
            'revenue_trend'  => SmartAnalytics::getRevenueTrend(),
            'busy_hours'     => SmartAnalytics::getBusyHours(),
            'peak_days'      => SmartAnalytics::getPeakDayOfWeek(),
            'top_services'   => SmartAnalytics::getTopServices(5),
            'rfm_segments'   => SmartAnalytics::getRFMSegments(),
            'churn_risk'     => SmartAnalytics::getChurnRiskCustomers(14),
            'smart_alerts'   => SmartAnalytics::getSmartAlerts(),
            'avg_order'      => SmartAnalytics::getAverageOrderValue(),
            'conversion'     => SmartAnalytics::getConversionRate(),
            'today_stats'    => SmartAnalytics::getTodayStats(),
            'ai_roadmap'     => SmartAnalytics::getAIRoadmap(),
            'action_plans'   => SmartAnalytics::getStrategicActionPlans(),
        ];

        $this->render('admin.analytics', $data, 'admin');
    }

    // ─────────────────────────────────────────────────────────────
    //  CUSTOMER: Halaman Asisten Pintar
    // ─────────────────────────────────────────────────────────────

    public function customerDashboard(): void
    {
        $user   = Auth::user();
        $userId = $user ? (int)($user['id'] ?? 0) : 0;

        $data = [
            'title'       => 'Asisten Pintar — Rekomendasi Layanan & Inovasi AI',
            'user'        => $user,
            'recs'        => $userId ? SmartAnalytics::getServiceRecommendations($userId) : [],
            'favorites'   => $userId ? SmartAnalytics::getUserFavoriteServices($userId) : [],
            'busy_hours'  => SmartAnalytics::getBusyHours(),
            'top_services'=> SmartAnalytics::getTopServices(4),
            'ai_roadmap'  => SmartAnalytics::getAIRoadmap(),
        ];

        $this->render('customer.smart', $data, 'main');
    }

    // ─────────────────────────────────────────────────────────────
    //  API: Chatbot
    // ─────────────────────────────────────────────────────────────

    public function chatbot(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $message = trim($_POST['message'] ?? '');

        if (empty($message)) {
            echo json_encode(['reply' => 'Ketik pertanyaan Anda ya! 😊', 'quick_replies' => ['Jam Buka', 'Harga', 'Promo']]);
            return;
        }

        $response = SmartAnalytics::getChatbotResponse($message);
        echo json_encode($response);
    }

    // ─────────────────────────────────────────────────────────────
    //  API: Rekomendasi Layanan (JSON)
    // ─────────────────────────────────────────────────────────────

    public function recommendations(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $user   = Auth::user();
        $userId = $user ? (int)($user['id'] ?? 0) : 0;

        if (!$userId) {
            echo json_encode(['error' => 'Login diperlukan']);
            return;
        }

        $recs = SmartAnalytics::getServiceRecommendations($userId);
        echo json_encode(['recommendations' => $recs]);
    }
}

