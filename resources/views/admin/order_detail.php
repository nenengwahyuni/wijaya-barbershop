<?php
$isHomeService = ($order['service_type'] ?? 'barbershop') === 'home_service';
$waNumber = preg_replace('/[^0-9]/', '', $order['customer_phone'] ?? '');
if (str_starts_with($waNumber, '0')) {
    $waNumber = '62' . substr($waNumber, 1);
}
$mapsQuery = urlencode($order['customer_address'] ?? 'Kota Metro Lampung');
?>
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Top Action Bar -->
    <div class="flex items-center justify-between">
        <a href="/admin/orders" class="text-xs font-bold text-slate-400 hover:text-emerald-400 flex items-center space-x-1.5 transition">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Data Order</span>
        </a>
        <div class="flex items-center space-x-2">
            <a href="/receipt?code=<?= $order['order_code'] ?>" target="_blank" class="px-4 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-xs font-bold transition flex items-center space-x-1.5 border border-white/10">
                <i class="fas fa-print"></i>
                <span>Cetak Nota Pelanggan</span>
            </a>
        </div>
    </div>

    <!-- Main Detail Card (Dark Luxury Frosted Glass) -->
    <div class="bg-slate-900/80 backdrop-blur-xl rounded-3xl border border-white/10 shadow-xl shadow-black/30 p-8 text-white">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-6 border-b border-white/10 gap-4">
            <div>
                <div class="flex items-center space-x-2 mb-1.5">
                    <?php if ($isHomeService): ?>
                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase bg-amber-500 text-slate-950 shadow-sm">
                        <i class="fas fa-motorcycle"></i>
                        <span>Cukur Panggilan (Home Service)</span>
                    </span>
                    <?php else: ?>
                    <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        <i class="fas fa-store"></i>
                        <span>Layanan Studio Barbershop</span>
                    </span>
                    <?php endif; ?>
                </div>
                <h1 class="text-2xl font-black text-white tracking-tight">Order #<?= htmlspecialchars($order['order_code']) ?></h1>
                <span class="text-xs text-slate-400">Dibuat pada: <?= date('d M Y, H:i', strtotime($order['created_at'])) ?> WIB</span>
            </div>

            <!-- Update Status Form -->
            <form action="/admin/orders/status" method="POST" class="flex items-center space-x-2">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <select name="status" class="px-3 py-2 rounded-xl border border-slate-700 bg-slate-950/80 text-xs font-bold text-white outline-none focus:border-emerald-500">
                    <option value="pending" <?= ($order['status'] === 'pending') ? 'selected' : '' ?> class="bg-slate-900 text-white">Pending (Menunggu)</option>
                    <option value="settlement" <?= ($order['status'] === 'settlement') ? 'selected' : '' ?> class="bg-slate-900 text-white">Settlement (Lunas)</option>
                    <option value="completed" <?= ($order['status'] === 'completed') ? 'selected' : '' ?> class="bg-slate-900 text-white">Completed (Selesai)</option>
                    <option value="cancelled" <?= ($order['status'] === 'cancelled') ? 'selected' : '' ?> class="bg-slate-900 text-white">Cancelled (Batal)</option>
                </select>
                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition shadow-md shadow-emerald-600/30">
                    Simpan Status
                </button>
            </form>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6 py-6 border-b border-white/10 text-xs">
            <div class="p-4 rounded-2xl bg-slate-950/60 border border-white/10">
                <span class="text-slate-400 block font-semibold mb-1">Data Pelanggan</span>
                <strong class="text-sm font-bold text-white block"><?= htmlspecialchars($order['customer_name'] ?? 'Pelanggan') ?></strong>
                <span class="text-emerald-400 block mt-1"><i class="fab fa-whatsapp mr-1"></i> <?= htmlspecialchars($order['customer_phone'] ?? '-') ?></span>
                <span class="text-slate-400 block truncate"><?= htmlspecialchars($order['customer_email'] ?? '-') ?></span>
            </div>

            <div class="p-4 rounded-2xl <?= $isHomeService ? 'bg-amber-500/10 border border-amber-500/30' : 'bg-emerald-500/10 border border-emerald-500/30' ?>">
                <span class="<?= $isHomeService ? 'text-amber-300' : 'text-emerald-300' ?> block font-semibold mb-1">
                    <?= $isHomeService ? 'Jadwal Kunjungan Home Service' : 'Nomor Antrean Pelanggan' ?>
                </span>
                <strong class="text-2xl font-black <?= $isHomeService ? 'text-amber-400' : 'text-emerald-400' ?> block">
                    #<?= str_pad($order['queue_number'], 2, '0', STR_PAD_LEFT) ?> &bull; <?= date('H:i', strtotime($order['scheduled_at'] ?: $order['queue_time'])) ?> WIB
                </strong>
                <span class="text-[11px] <?= $isHomeService ? 'text-amber-300' : 'text-emerald-300' ?> block mt-1 font-semibold">
                    <i class="fas fa-calendar-alt mr-1"></i> <?= date('d F Y', strtotime($order['scheduled_at'] ?: $order['queue_time'])) ?>
                </span>
            </div>

            <div class="p-4 rounded-2xl bg-slate-950/60 border border-white/10">
                <span class="text-slate-400 block font-semibold mb-1">Metode & Status Bayar</span>
                <strong class="text-sm font-bold text-white block uppercase"><?= htmlspecialchars($order['payment_method']) ?></strong>
                <span class="inline-block mt-2 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase <?= ($order['status'] === 'settlement' || $order['status'] === 'completed') ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-300 border border-amber-500/30' ?>">
                    <?= htmlspecialchars($order['status']) ?>
                </span>
            </div>
        </div>

        <!-- Action & Notifikasi WhatsApp Manual Kasir/Admin -->
        <div class="py-4 border-b border-white/10 flex flex-wrap items-center justify-between gap-3 text-xs">
            <span class="font-bold text-slate-300 flex items-center space-x-1.5">
                <i class="fab fa-whatsapp text-emerald-400 text-sm"></i>
                <span>Kirim Notifikasi WhatsApp Cepat ke Customer:</span>
            </span>
            <div class="flex flex-wrap items-center gap-2">
                <a href="<?= $waConfirmLink ?>" target="_blank" class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold transition flex items-center space-x-1 shadow-sm">
                    <i class="fas fa-check-circle"></i>
                    <span>Kirim Bukti Lunas (WA)</span>
                </a>
                <a href="<?= $waPendingLink ?>" target="_blank" class="px-3 py-1.5 bg-amber-500 hover:bg-amber-400 text-slate-950 rounded-xl font-bold transition flex items-center space-x-1 shadow-sm">
                    <i class="fas fa-clock"></i>
                    <span>Kirim Pengingat Jadwal</span>
                </a>
                <a href="<?= $waCompletedLink ?>" target="_blank" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold transition flex items-center space-x-1 shadow-sm">
                    <i class="fas fa-scissors"></i>
                    <span>Kirim Ucapan Selesai</span>
                </a>
            </div>
        </div>

        <!-- Panel Penugasan Capster & Lokasi Home Service -->
        <div class="py-6 border-b border-white/10 space-y-4">
            <div class="p-5 rounded-2xl <?= $isHomeService ? 'bg-amber-500/10 border border-amber-500/30' : 'bg-slate-950/60 border border-white/10' ?>">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 block mb-1">
                            <i class="fas fa-user-tie text-emerald-400 mr-1"></i> Capster / Barber Bertugas
                        </span>
                        <p class="text-sm font-extrabold text-white">
                            <?= htmlspecialchars($order['barber_name'] ?? 'Belum Ditentukan') ?>
                            <?php if (!empty($order['barber_phone'])): ?>
                            <span class="text-xs font-normal text-slate-400 ml-1">(<?= htmlspecialchars($order['barber_phone']) ?>)</span>
                            <?php endif; ?>
                        </p>
                    </div>

                    <!-- Form Assign / Ganti Barber -->
                    <form action="/admin/orders/assign-barber" method="POST" class="flex items-center space-x-2">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <select name="barber_id" class="px-3 py-2 rounded-xl border border-slate-700 bg-slate-950/80 text-xs font-bold text-white outline-none focus:border-emerald-500">
                            <option value="" class="bg-slate-900 text-slate-400">-- Pilih Capster Bertugas --</option>
                            <?php if (!empty($barbers)): foreach ($barbers as $b): ?>
                            <option value="<?= $b['id'] ?>" <?= ((int)($order['barber_id'] ?? 0) === (int)$b['id']) ? 'selected' : '' ?> class="bg-slate-900 text-white">
                                <?= htmlspecialchars($b['fullname']) ?>
                            </option>
                            <?php endforeach; endif; ?>
                        </select>
                        <button type="submit" class="px-3.5 py-2 bg-white/10 hover:bg-emerald-600 text-white rounded-xl text-xs font-bold transition border border-white/10">
                            Simpan Barber
                        </button>
                    </form>
                </div>

                <?php if ($isHomeService): ?>
                <div class="mt-4 pt-4 border-t border-amber-500/20 grid grid-cols-1 sm:grid-cols-12 gap-4 items-center text-xs">
                    <div class="sm:col-span-8">
                        <span class="font-bold text-amber-300 block mb-1">
                            <i class="fas fa-map-marker-alt text-rose-400 mr-1"></i> Alamat Tujuan Cukur Panggilan (Zona: <?= htmlspecialchars($order['area_zone'] ?? '-') ?>):
                        </span>
                        <p class="text-slate-200 font-semibold leading-relaxed whitespace-pre-line bg-slate-950/80 p-3 rounded-xl border border-amber-500/30">
                            <?= htmlspecialchars($order['customer_address'] ?? '-') ?>
                        </p>
                    </div>
                    <div class="sm:col-span-4 flex flex-col space-y-2">
                        <a href="https://www.google.com/maps/search/?api=1&query=<?= $mapsQuery ?>" target="_blank"
                            class="w-full px-3.5 py-2.5 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-bold text-xs text-center transition flex items-center justify-center space-x-1.5 shadow-sm">
                            <i class="fas fa-map-location-dot"></i>
                            <span>Buka di Google Maps</span>
                        </a>
                        <?php if (!empty($waNumber)): ?>
                        <a href="https://wa.me/<?= $waNumber ?>?text=<?= urlencode("Halo Kak {$order['customer_name']}, kami dari Wijaya Barbershop mengonfirmasi pesanan Cukur Panggilan #{$order['order_code']} untuk jadwal " . date('d M Y H:i', strtotime($order['scheduled_at'] ?: $order['queue_time'])) . " WIB.") ?>" target="_blank"
                            class="w-full px-3.5 py-2.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold text-xs text-center transition flex items-center justify-center space-x-1.5 shadow-sm">
                            <i class="fab fa-whatsapp"></i>
                            <span>Hubungi WhatsApp Customer</span>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Catatan Pemesan -->
        <?php if (!empty($order['notes'])): ?>
        <div class="py-4 border-b border-white/10 text-xs">
            <span class="font-bold text-slate-300 block mb-1">Catatan Khusus dari Pelanggan:</span>
            <p class="text-amber-300 italic bg-amber-500/10 p-3 rounded-xl border border-amber-500/20">"<?= htmlspecialchars($order['notes']) ?>"</p>
        </div>
        <?php endif; ?>

        <!-- Item Table -->
        <div class="py-6">
            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Item Layanan & Produk</h4>
            <div class="overflow-x-auto rounded-2xl border border-white/10 bg-slate-950/60">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950 text-slate-300 font-bold border-b border-white/10">
                        <tr>
                            <th class="p-3 pl-4">Nama Layanan</th>
                            <th class="p-3 text-center">Qty</th>
                            <th class="p-3 text-right">Harga</th>
                            <th class="p-3 text-right">Pajak</th>
                            <th class="p-3 pr-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-slate-200">
                        <?php foreach ($order['items'] as $item): ?>
                        <?php $admImg = \App\Models\Item::getImageUrl(null, $item['item_name'] ?? ''); ?>
                        <tr>
                            <td class="p-3 pl-4 font-bold text-white flex items-center space-x-2.5">
                                <img src="<?= htmlspecialchars($admImg) ?>" alt="<?= htmlspecialchars($item['item_name']) ?>" class="w-8 h-8 rounded-lg object-cover border border-white/10 shadow-sm flex-shrink-0">
                                <span><?= htmlspecialchars($item['item_name']) ?></span>
                            </td>
                            <td class="p-3 text-center"><?= $item['quantity'] ?></td>
                            <td class="p-3 text-right">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                            <td class="p-3 text-right">Rp <?= number_format($item['tax'], 0, ',', '.') ?></td>
                            <td class="p-3 pr-4 text-right font-black text-emerald-400">Rp <?= number_format($item['total_price'], 0, ',', '.') ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Financial Recapitulation -->
        <div class="pt-4 border-t border-white/10 space-y-2 text-xs text-slate-400 max-w-xs ml-auto">
            <div class="flex justify-between">
                <span>Subtotal:</span>
                <span class="font-bold text-white">Rp <?= number_format((int)$order['subtotal'], 0, ',', '.') ?></span>
            </div>
            <div class="flex justify-between">
                <span>Pajak (10%):</span>
                <span class="font-bold text-white">Rp <?= number_format((int)$order['tax'], 0, ',', '.') ?></span>
            </div>
            <?php if (!empty($order['delivery_fee']) && (int)$order['delivery_fee'] > 0): ?>
            <div class="flex justify-between text-amber-300 font-bold">
                <span>Transport Home Service:</span>
                <span>Rp <?= number_format((int)$order['delivery_fee'], 0, ',', '.') ?></span>
            </div>
            <?php endif; ?>
            <div class="flex justify-between items-center pt-2 border-t border-white/10 text-sm font-black text-white">
                <span>Grand Total:</span>
                <span class="text-xl text-emerald-400 font-black">Rp <?= number_format((int)$order['grand_total'], 0, ',', '.') ?></span>
            </div>
        </div>
    </div>
</div>
