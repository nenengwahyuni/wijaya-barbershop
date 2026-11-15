<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2 mb-1">
                <span class="w-2.5 h-5 rounded-full barber-pole border border-slate-700 shadow-sm"></span>
                <span class="text-xs font-black uppercase tracking-widest text-emerald-400">Riwayat Transaksi</span>
            </div>
            <h1 class="text-3xl font-black text-white tracking-tight">Pesanan & Antrean Saya</h1>
            <p class="text-xs sm:text-sm text-slate-400 mt-1">Daftar semua antrean studio dan pemesanan Cukur Panggilan (Home Service) Anda.</p>
        </div>
        <div class="flex items-center space-x-3">
            <a href="/home-service" class="px-4 py-2.5 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl text-xs font-black transition shadow-lg shadow-amber-500/25 flex items-center space-x-2 btn-magnetic shimmer-btn">
                <i class="fas fa-motorcycle"></i>
                <span>Cukur Panggilan</span>
            </a>
            <a href="/services" class="px-5 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white rounded-xl text-xs font-black transition shadow-lg shadow-emerald-600/30 flex items-center space-x-2 btn-magnetic shimmer-btn">
                <i class="fas fa-plus"></i>
                <span>Pesan di Studio</span>
            </a>
        </div>
    </div>

    <?php if (empty($orders)): ?>
    <div class="border-beam-card tilt-card-luxury p-12 text-center max-w-lg mx-auto shadow-2xl overflow-hidden text-white">
        <div class="w-20 h-20 bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl shadow-lg">
            <i class="fas fa-receipt"></i>
        </div>
        <h3 class="text-xl font-black text-white mb-2">Belum Ada Riwayat Pesanan</h3>
        <p class="text-xs sm:text-sm text-slate-400 mb-6">Anda belum pernah melakukan pemesanan pangkas rambut di Wijaya Barbershop.</p>
        <a href="/services" class="inline-flex items-center space-x-2 px-6 py-3.5 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-black rounded-2xl transition shadow-xl shadow-emerald-600/30 text-xs uppercase tracking-wider shimmer-btn btn-magnetic">
            <span>Mulai Pesan Sekarang</span>
        </a>
    </div>
    <?php else: ?>
    <div class="bg-slate-900/80 backdrop-blur-xl rounded-3xl border border-white/10 shadow-xl shadow-black/25 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-950/70 text-slate-300 font-bold border-b border-white/10 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="p-4 pl-6">Kode Order</th>
                        <th class="p-4">Tipe Layanan</th>
                        <th class="p-4 text-center">No Antrean</th>
                        <th class="p-4">Jadwal / Waktu</th>
                        <th class="p-4">Metode</th>
                        <th class="p-4 text-right">Total</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 pr-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-slate-200 text-xs">
                    <?php foreach ($orders as $order): ?>
                    <?php $isHs = ($order['service_type'] ?? 'barbershop') === 'home_service'; ?>
                    <tr class="hover:bg-white/5 transition">
                        <td class="p-4 pl-6 font-mono font-bold text-white">
                            <?= htmlspecialchars($order['order_code']) ?>
                        </td>
                        <td class="p-4">
                            <?php if ($isHs): ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-md text-[10px] font-black bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                <i class="fas fa-motorcycle"></i>
                                <span>Home Service</span>
                            </span>
                            <?php if (!empty($order['barber_name'])): ?>
                            <span class="block text-[10px] text-slate-400 mt-1">Barber: <?= htmlspecialchars($order['barber_name']) ?></span>
                            <?php endif; ?>
                            <?php else: ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-white/10 text-slate-200 border border-white/10">
                                <i class="fas fa-store"></i>
                                <span>Di Studio</span>
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-center font-black text-emerald-400 text-sm">
                            #<?= str_pad($order['queue_number'], 2, '0', STR_PAD_LEFT) ?>
                        </td>
                        <td class="p-4">
                            <span class="block font-bold text-white"><?= date('d M Y', strtotime($order['scheduled_at'] ?: $order['queue_time'])) ?></span>
                            <span class="text-[11px] text-slate-400"><?= date('H:i', strtotime($order['scheduled_at'] ?: $order['queue_time'])) ?> WIB</span>
                        </td>
                        <td class="p-4">
                            <span class="font-mono font-bold uppercase text-[10px] px-2 py-0.5 rounded bg-white/10 text-slate-300 border border-white/10">
                                <?= htmlspecialchars($order['payment_method']) ?>
                            </span>
                        </td>
                        <td class="p-4 text-right font-black text-white text-sm">
                            Rp <?= number_format((int)$order['grand_total'], 0, ',', '.') ?>
                        </td>
                        <td class="p-4 text-center">
                            <?php if ($order['status'] === 'settlement'): ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                                Lunas (Settlement)
                            </span>
                            <?php elseif ($order['status'] === 'completed'): ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-blue-500/20 text-blue-300 border border-blue-500/30">
                                Selesai Dicukur
                            </span>
                            <?php elseif ($order['status'] === 'pending'): ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                Menunggu
                            </span>
                            <?php else: ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                Dibatalkan
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 pr-6 text-center">
                            <a href="/receipt?code=<?= $order['order_code'] ?>" 
                                class="px-3.5 py-1.5 bg-white/10 hover:bg-emerald-600 text-white rounded-xl font-bold text-[11px] transition shadow-sm inline-flex items-center space-x-1 border border-white/10 btn-magnetic">
                                <i class="fas fa-eye text-[10px]"></i>
                                <span>Lihat Resi</span>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</div>
