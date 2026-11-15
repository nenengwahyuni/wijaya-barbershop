<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;

class QueueService
{
    /**
     * Hitung nomor antrean dan estimasi jam giliran
     */
    public static function generateQueueInfo(): array
    {
        $queueNumber = Order::getTodayQueueNumber();
        $barbersOnDuty = count(User::getBarbersOnDuty()) ?: 3; // Minimal 3 barber sesuai struktur organisasi (Doni, Lana, Yoyo)
        $serviceDuration = (int) (getenv('ESTIMATED_SERVICE_MINUTES') ?: 30); // 30 menit per pelanggan

        // Menghitung putaran antrean berdasarkan jumlah barber
        $round = (int) ceil($queueNumber / $barbersOnDuty);
        $minutesOffset = ($round - 1) * $serviceDuration;

        $now = time();
        $openingTime = strtotime(date('Y-m-d') . ' ' . (getenv('OPENING_HOUR') ?: '09:00'));

        $baseTime = max($now, $openingTime);
        $estimatedTime = $baseTime + ($minutesOffset * 60);

        return [
            'queue_number' => $queueNumber,
            'queue_time' => date('Y-m-d H:i:s', $estimatedTime),
            'formatted_time' => date('H:i', $estimatedTime) . ' WIB',
            'barbers_count' => $barbersOnDuty
        ];
    }
}

