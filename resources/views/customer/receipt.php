<?php 
$isQrisPending = ($order['payment_method'] === 'qris' && $order['status'] === 'pending');
$autoShowQris = !empty($showQris) || $isQrisPending;
$autoWa = (!$isQrisPending) && ($autoWa ?? (isset($_GET['auto_wa']) || isset($_GET['payment_status']))); 
?>

<!-- QRIS Modal Pembayaran Otomatis -->
<?php if ($isQrisPending): ?>
<div id="qris-auto-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 backdrop-blur-md p-4 transition-all duration-300 <?= $autoShowQris ? '' : 'hidden opacity-0 pointer-events-none' ?>">
    <div class="bg-slate-900 border-2 border-emerald-500/60 rounded-3xl shadow-2xl max-w-sm sm:max-w-md w-full overflow-hidden text-white relative animate-fade-in p-5 sm:p-6 text-center max-h-[92vh] overflow-y-auto">
        <!-- Header -->
        <div class="flex items-center justify-between pb-3 mb-3 border-b border-white/10">
            <div class="flex items-center space-x-2">
                <span class="w-2.5 h-6 rounded-full barber-pole"></span>
                <span class="text-xs font-black uppercase tracking-wider text-emerald-400">Pembayaran QRIS</span>
            </div>
            <div class="flex items-center space-x-2">
                <span class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-amber-500/20 text-amber-300 text-[10px] font-black uppercase tracking-wider border border-amber-500/30">
                    <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                    <span id="qris-status-pill">Menunggu Pembayaran</span>
                </span>
                <button type="button" onclick="closeQrisModal()" class="w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
        </div>

        <!-- Nominal Tag -->
        <div class="p-3 rounded-2xl bg-gradient-to-r from-emerald-950/80 via-slate-800 to-emerald-950/80 border border-emerald-500/40 mb-3">
            <span class="text-[10px] text-slate-400 uppercase tracking-widest block font-bold">Total Pembayaran:</span>
            <div class="text-3xl font-black text-emerald-400 font-mono tracking-tight my-0.5">
                Rp <?= number_format((int)$order['grand_total'], 0, ',', '.') ?>
            </div>
            <span class="text-[10px] text-amber-300 font-mono">Kode Order: #<?= htmlspecialchars($order['order_code']) ?></span>
        </div>

        <!-- QRIS Image Box -->
        <div class="p-3 bg-white rounded-2xl shadow-xl mx-auto max-w-[270px] sm:max-w-[290px] border-4 border-slate-800 relative">
            <img src="/images/qris.jpg" alt="QRIS Wijaya Barbershop" class="w-full h-auto rounded-xl object-contain mx-auto shadow-sm">
        </div>

        <!-- Info Merchant -->
        <div class="mt-3 px-3 py-2 rounded-xl bg-slate-800/80 border border-slate-700/80 text-xs text-left space-y-1">
            <div class="flex justify-between items-center text-[11px]">
                <span class="text-slate-400">Merchant:</span>
                <span class="font-bold text-white font-mono">Wijaya Barbershop</span>
            </div>
            <div class="flex justify-between items-center text-[11px]">
                <span class="text-slate-400">NMID:</span>
                <span class="font-mono text-emerald-400 font-bold">ID1025448727392</span>
            </div>
            <div class="flex justify-between items-center text-[11px]">
                <span class="text-slate-400">Metode:</span>
                <span class="text-slate-200">BCA, Mandiri, BRI, BNI, GoPay, OVO, Dana, ShopeePay</span>
            </div>
        </div>

        <!-- Status Pemantauan Pembayaran Real-Time (Tanpa Timer Hitung Mundur Palsu) -->
        <div id="qris-auto-detect-box" class="mt-3 p-3 rounded-2xl bg-gradient-to-r from-slate-800/90 via-slate-900 to-slate-800/90 border border-emerald-500/40 text-left shadow-lg">
            <div class="flex items-center justify-between text-xs mb-1">
                <span class="text-emerald-400 font-extrabold flex items-center space-x-1.5">
                    <i class="fas fa-circle-notch fa-spin text-emerald-400 text-xs"></i>
                    <span id="qris-detect-title">Menunggu Scan & Pembayaran QRIS...</span>
                </span>
                <span id="qris-countdown-text" class="font-mono text-[10px] font-black px-2 py-0.5 rounded bg-amber-500/20 text-amber-300 border border-amber-500/30">PENDING</span>
            </div>
            <p id="qris-detect-subtitle" class="text-[11px] text-slate-300 mt-1.5 leading-snug">
                Silakan scan QRIS di atas dan selesaikan pembayaran di HP Anda. Begitu pembayaran berhasil diverifikasi, <strong>suara notifikasi lunas otomatis langsung berbunyi</strong>!
            </p>
        </div>

        <!-- Actions -->
        <div class="mt-4 space-y-2">
            <button type="button" id="btn-manual-confirm-qris" onclick="executeQrisAutoConfirm(true)" 
                class="w-full py-3 bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-black rounded-xl transition duration-300 shadow-xl shadow-emerald-500/40 text-xs uppercase tracking-wider flex items-center justify-center space-x-2 shimmer-btn cursor-pointer">
                <i class="fas fa-check-circle text-base"></i>
                <span id="btn-qris-label">Saya Sudah Scan & Bayar (Verifikasi)</span>
            </button>
            <form id="form-confirm-qris" action="/receipt/confirm-qris" method="POST" class="hidden">
                <input type="hidden" name="order_code" value="<?= htmlspecialchars($order['order_code']) ?>">
            </form>

            <div class="grid grid-cols-2 gap-2">
                <a href="/images/qris.jpg" download="QRIS.jpg" 
                    class="py-2 px-3 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 hover:text-white font-bold text-[11px] transition flex items-center justify-center space-x-1.5 border border-slate-700">
                    <i class="fas fa-download text-xs"></i>
                    <span>Unduh QRIS</span>
                </a>
                <button type="button" onclick="closeQrisModal()" 
                    class="py-2 px-3 rounded-xl bg-slate-800/80 hover:bg-slate-700 text-slate-400 hover:text-slate-200 font-semibold text-[11px] transition">
                    <span>Tutup Modal</span>
                </button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Auto WhatsApp Notification Modal (Tampil Otomatis saat Pesan / Bayar) -->
<?php if ($autoWa && !empty($waLink)): ?>
<div id="wa-auto-modal" onclick="if(event.target === this) closeWaModal()" class="fixed inset-0 z-50 flex items-center justify-center bg-black/80 backdrop-blur-md p-4 transition-all duration-300">
    <div class="bg-slate-900 border border-emerald-500/50 rounded-3xl shadow-2xl max-w-lg w-full overflow-hidden text-white relative animate-fade-in p-6 sm:p-8 text-center">
        <!-- Close Button (X) -->
        <button type="button" onclick="closeWaModal()" class="absolute top-4 right-4 w-8 h-8 rounded-full bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition z-20 cursor-pointer" title="Tutup Modal">
            <i class="fas fa-times text-xs"></i>
        </button>

        <!-- Glowing WhatsApp Icon -->
        <div class="relative w-20 h-20 mx-auto mb-4">
            <div class="absolute inset-0 rounded-3xl bg-emerald-500 blur-xl opacity-60 animate-pulse"></div>
            <div class="relative w-20 h-20 rounded-3xl bg-gradient-to-tr from-emerald-500 to-teal-400 text-white flex items-center justify-center text-4xl shadow-xl shadow-emerald-500/50">
                <i class="fab fa-whatsapp"></i>
            </div>
        </div>

        <div class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-emerald-500/20 text-emerald-400 text-xs font-black uppercase tracking-wider mb-2 border border-emerald-500/30">
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
            <span>Notifikasi WhatsApp Otomatis</span>
        </div>

        <h3 class="text-2xl font-black text-white tracking-tight mb-2">
            <?= ($order['status'] === 'settlement' || $order['status'] === 'completed') ? 'Pembayaran Lunas Terverifikasi!' : 'Pesanan Sukses Dikonfirmasi!' ?>
        </h3>

        <p class="text-xs sm:text-sm text-slate-300 leading-relaxed mb-5">
            Halo <strong class="text-white"><?= htmlspecialchars($order['customer_name']) ?></strong>, bukti rincian transaksi Anda <span class="font-mono text-emerald-400 font-bold">#<?= htmlspecialchars($order['order_code']) ?></span> telah disiapkan otomatis ke nomor WhatsApp:
            <br>
            <span class="inline-block mt-2 px-4 py-1.5 bg-emerald-950/80 rounded-xl font-mono text-sm sm:text-base font-black text-emerald-300 border border-emerald-500/30">
                <i class="fab fa-whatsapp text-emerald-400 mr-1.5"></i> <?= htmlspecialchars($order['customer_phone'] ?? '-') ?>
            </span>
        </p>

        <div class="space-y-2.5">
            <?php if (!empty($waDesktopLink)): ?>
            <button type="button" id="btn-open-wa-desktop" onclick="openWhatsAppAction('desktop')" 
                class="w-full py-3.5 bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-black rounded-2xl transition duration-300 shadow-xl shadow-emerald-500/40 text-xs sm:text-sm uppercase tracking-wider flex items-center justify-center space-x-2.5 shimmer-btn btn-magnetic cursor-pointer">
                <i class="fab fa-whatsapp text-xl"></i>
                <span id="wa-countdown-text">Buka di Aplikasi WhatsApp Laptop 💻</span>
            </button>
            <?php endif; ?>
            <button type="button" id="btn-open-wa-web" onclick="openWhatsAppAction('web')" 
                class="w-full py-2.5 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl text-xs uppercase tracking-wider flex items-center justify-center space-x-2 border border-slate-700 transition cursor-pointer">
                <i class="fas fa-globe text-sm"></i>
                <span>Buka WhatsApp Web 🌐</span>
            </button>
            <button type="button" onclick="closeWaModal()" class="w-full py-2.5 text-xs text-slate-300 hover:text-white transition font-bold bg-slate-800/80 hover:bg-slate-750 rounded-xl border border-slate-700/80 cursor-pointer">
                Tutup & Lihat Resi di Halaman Ini
            </button>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <!-- Action Top Bar (No Print) -->
    <div class="mb-6 flex items-center justify-between no-print">
        <a href="/" class="text-sm font-bold text-slate-600 hover:text-emerald-600 flex items-center space-x-1">
            <i class="fas fa-arrow-left text-xs"></i>
            <span>Kembali ke Beranda</span>
        </a>
        <div class="flex items-center space-x-3">
            <?php if (!empty($waLink)): ?>
            <a href="<?= $waLink ?>" target="_blank" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shadow-sm">
                <i class="fab fa-whatsapp text-sm"></i>
                <span>Kirim Bukti ke WhatsApp</span>
            </a>
            <?php endif; ?>
            <button onclick="window.print()" class="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shadow-sm">
                <i class="fas fa-print"></i>
                <span>Cetak Tiket / Resi</span>
            </button>
            <a href="/my-orders" class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white rounded-xl text-xs font-bold transition flex items-center space-x-1.5 shadow-sm">
                <i class="fas fa-list-ul"></i>
                <span>Pesanan Saya</span>
            </a>
        </div>
    </div>

    <!-- Printable Receipt Box (Sesuai Gambar 36 & 37 Skripsi) -->
    <div id="printable-receipt" class="bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden">
        <!-- Receipt Header -->
        <div class="bg-gradient-to-r from-slate-900 via-emerald-950 to-slate-900 text-white p-8 text-center relative">
            <div class="w-14 h-14 bg-emerald-500 rounded-2xl flex items-center justify-center text-white text-2xl mx-auto mb-3 shadow-lg shadow-emerald-500/30">
                <i class="fas fa-scissors"></i>
            </div>
            <h2 class="text-2xl font-extrabold tracking-tight">Wijaya <span class="text-emerald-400">Barbershop</span></h2>
            <p class="text-xs text-slate-400 mt-1">Kota Metro, Lampung | Telp: +62 812-7841-2312</p>

            <div class="mt-6 inline-flex items-center space-x-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur text-xs font-mono font-bold tracking-wider border border-white/10">
                <span>KODE ORDER: <?= htmlspecialchars($order['order_code']) ?></span>
            </div>
        </div>

        <!-- Nomor Antrean / Info Kunjungan Highlight Card -->
        <?php $isHomeService = ($order['service_type'] ?? 'barbershop') === 'home_service'; ?>
        <?php if ($isHomeService): ?>
        <div class="bg-amber-50 border-y border-amber-200 p-6 text-center">
            <span class="inline-flex items-center space-x-1.5 px-3 py-1 rounded-full bg-amber-500 text-slate-950 text-[11px] font-black uppercase tracking-wider mb-2">
                <i class="fas fa-motorcycle"></i>
                <span>Layanan Cukur Panggilan (Home Service)</span>
            </span>
            <div class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight my-1">
                Jadwal: <?= date('H:i', strtotime($order['scheduled_at'] ?: $order['queue_time'])) ?> WIB
            </div>
            <p class="text-xs text-amber-900 font-semibold">
                <i class="fas fa-calendar-day mr-1"></i> Tanggal Kunjungan:
                <strong><?= date('d F Y', strtotime($order['scheduled_at'] ?: $order['queue_time'])) ?></strong>
                &bull; Tiket #<?= str_pad($order['queue_number'], 2, '0', STR_PAD_LEFT) ?>
            </p>
        </div>
        <?php else: ?>
        <div class="bg-emerald-50 border-y border-emerald-100 p-6 text-center">
            <span class="text-xs font-bold uppercase tracking-widest text-emerald-800 block mb-1">Tiket Antrean Studio Barbershop</span>
            <div class="text-5xl font-black text-emerald-600 tracking-tight my-2">
                #<?= str_pad($order['queue_number'], 2, '0', STR_PAD_LEFT) ?>
            </div>
            <p class="text-xs text-emerald-700 font-medium">
                <i class="fas fa-clock mr-1"></i> Perkiraan Jam Layanan: 
                <strong class="font-bold"><?= date('H:i', strtotime($order['queue_time'])) ?> WIB</strong> 
                (<?= date('d M Y', strtotime($order['queue_time'])) ?>)
            </p>
        </div>
        <?php endif; ?>

        <!-- Detail Pesanan & Pembayaran -->
        <div class="p-8 space-y-6">
            <!-- Info Status Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pb-6 border-b border-slate-100 text-xs">
                <div>
                    <span class="text-slate-400 block font-medium">Customer:</span>
                    <strong class="text-slate-800 text-sm font-bold block truncate"><?= htmlspecialchars($order['customer_name']) ?></strong>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">WhatsApp:</span>
                    <strong class="text-slate-800 text-sm font-bold block"><?= htmlspecialchars($order['customer_phone'] ?? '-') ?></strong>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">Metode Bayar:</span>
                    <span class="font-bold text-slate-800 uppercase"><?= htmlspecialchars($order['payment_method']) ?></span>
                </div>
                <div>
                    <span class="text-slate-400 block font-medium">Status Transaksi:</span>
                    <?php if ($order['status'] === 'settlement' || $order['status'] === 'completed'): ?>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-100 text-emerald-800">
                        <i class="fas fa-check-circle mr-1"></i> LUNAS (Settlement)
                    </span>
                    <?php else: ?>
                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-amber-100 text-amber-800">
                        <i class="fas fa-clock mr-1"></i> PENDING
                    </span>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Info Capster & Alamat Kunjungan (Khusus Home Service atau jika ada Barber) -->
            <?php if ($isHomeService || !empty($order['barber_name'])): ?>
            <div class="p-5 rounded-2xl <?= $isHomeService ? 'bg-amber-50/70 border border-amber-200' : 'bg-slate-50 border border-slate-200' ?> text-xs space-y-3">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                    <div>
                        <span class="text-slate-500 font-semibold block">Capster / Barber Bertugas:</span>
                        <strong class="text-sm font-extrabold text-slate-900">
                            <i class="fas fa-user-check text-emerald-600 mr-1"></i>
                            <?= htmlspecialchars($order['barber_name'] ?? 'Menunggu Penugasan Capster') ?>
                        </strong>
                    </div>
                    <?php if ($isHomeService && !empty($order['area_zone'])): ?>
                    <div class="sm:text-right">
                        <span class="text-slate-500 font-semibold block">Zona Wilayah:</span>
                        <span class="inline-block font-bold text-amber-900 bg-amber-200/70 px-2.5 py-0.5 rounded-lg mt-0.5">
                            <?= htmlspecialchars($order['area_zone']) ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>

                <?php if ($isHomeService && !empty($order['customer_address'])): ?>
                <div class="pt-3 border-t border-amber-200/60">
                    <span class="text-slate-500 font-semibold block mb-1">
                        <i class="fas fa-map-marker-alt text-rose-600 mr-1"></i> Alamat Tujuan Cukur Panggilan:
                    </span>
                    <p class="text-slate-800 font-semibold leading-relaxed whitespace-pre-line"><?= htmlspecialchars($order['customer_address']) ?></p>
                </div>
                <?php endif; ?>
            </div>
            <?php endif; ?>

            <!-- Banner Notifikasi WhatsApp Otomatis -->
            <div class="no-print p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-xs text-emerald-950 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-lg flex-shrink-0 shadow-sm">
                        <i class="fab fa-whatsapp"></i>
                    </div>
                    <div>
                        <strong class="font-bold text-slate-900 block">Notifikasi WhatsApp Otomatis:</strong>
                        <span class="text-slate-600">Rincian pesanan & status pembayaran telah disiapkan ke nomor <strong><?= htmlspecialchars($order['customer_phone'] ?? '-') ?></strong>.</span>
                    </div>
                </div>
                <?php if (!empty($waLink)): ?>
                <a href="<?= $waLink ?>" target="_blank" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white font-bold rounded-xl text-[11px] whitespace-nowrap text-center transition flex items-center justify-center space-x-1.5 shadow-sm">
                    <i class="fab fa-whatsapp text-sm"></i>
                    <span>Buka WhatsApp Sekarang</span>
                </a>
                <?php endif; ?>
            </div>

            <!-- Catatan Khusus Pelanggan -->
            <?php if (!empty($order['notes'])): ?>
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 text-xs">
                <span class="font-bold text-slate-700 block mb-1">Catatan Tambahan:</span>
                <p class="text-slate-600 italic">"<?= htmlspecialchars($order['notes']) ?>"</p>
            </div>
            <?php endif; ?>

            <!-- Tabel Item Yang Dipesan -->
            <div>
                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Rincian Layanan & Produk</h4>
                <div class="border border-slate-100 rounded-2xl overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 text-slate-600 font-bold border-b border-slate-100">
                            <tr>
                                <th class="p-3">Item Layanan / Produk</th>
                                <th class="p-3 text-center">Qty</th>
                                <th class="p-3 text-right">Harga</th>
                                <th class="p-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-slate-700">
                            <?php foreach ($order['items'] as $item): ?>
                            <?php $rcImg = \App\Models\Item::getImageUrl(null, $item['item_name'] ?? ''); ?>
                            <tr>
                                <td class="p-3 font-semibold text-slate-900 flex items-center space-x-2.5">
                                    <img src="<?= htmlspecialchars($rcImg) ?>" alt="<?= htmlspecialchars($item['item_name']) ?>" class="w-9 h-9 rounded-xl object-cover border border-slate-200 shadow-sm flex-shrink-0">
                                    <span><?= htmlspecialchars($item['item_name']) ?></span>
                                </td>
                                <td class="p-3 text-center"><?= $item['quantity'] ?></td>
                                <td class="p-3 text-right">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                <td class="p-3 text-right font-bold text-slate-900">Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Perhitungan Biaya -->
            <div class="pt-4 border-t border-slate-100 space-y-2 text-sm text-slate-600">
                <div class="flex justify-between">
                    <span>Subtotal:</span>
                    <span class="font-semibold text-slate-800">Rp <?= number_format((int)$order['subtotal'], 0, ',', '.') ?></span>
                </div>
                <?php if (!empty($order['discount']) && (int)$order['discount'] > 0): ?>
                <div class="flex justify-between text-emerald-600 font-bold">
                    <span>
                        <i class="fas fa-tags mr-1"></i> Potongan Diskon / Poin
                        <?php if (!empty($order['voucher_code'])): ?>
                            <span class="text-xs font-mono font-bold bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded border border-emerald-200">#<?= htmlspecialchars($order['voucher_code']) ?></span>
                        <?php endif; ?>:
                    </span>
                    <span>-Rp <?= number_format((int)$order['discount'], 0, ',', '.') ?></span>
                </div>
                <?php endif; ?>
                <div class="flex justify-between">
                    <span>Pajak (PPN 10%):</span>
                    <span class="font-semibold text-slate-800">Rp <?= number_format((int)$order['tax'], 0, ',', '.') ?></span>
                </div>
                <?php if (!empty($order['delivery_fee']) && (int)$order['delivery_fee'] > 0): ?>
                <div class="flex justify-between text-amber-800 font-semibold">
                    <span>Biaya Transport Home Service (<?= htmlspecialchars($order['area_zone'] ?? 'Area Metro') ?>):</span>
                    <span>Rp <?= number_format((int)$order['delivery_fee'], 0, ',', '.') ?></span>
                </div>
                <?php endif; ?>
                <div class="flex justify-between items-center pt-3 border-t border-slate-200 text-base font-extrabold text-slate-900">
                    <span>Total Pembayaran:</span>
                    <span class="text-2xl text-emerald-600 font-black">Rp <?= number_format((int)$order['grand_total'], 0, ',', '.') ?></span>
                </div>
                <?php if (!empty($order['points_earned']) && (int)$order['points_earned'] > 0): ?>
                <div class="flex justify-between items-center text-xs text-amber-600 font-bold pt-1 bg-amber-50/70 p-2 rounded-xl border border-amber-200/60">
                    <span><i class="fas fa-crown text-amber-500 mr-1"></i> Reward Poin Diperoleh:</span>
                    <span>+<?= $order['points_earned'] ?> Poin VIP Club</span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Kotak QRIS jika status masih pending -->
            <?php if ($order['status'] === 'pending' && $order['payment_method'] === 'qris'): ?>
            <div class="no-print pt-4 border-t border-slate-100">
                <div class="p-5 rounded-2xl bg-gradient-to-br from-slate-900 via-slate-850 to-slate-900 text-white border-2 border-emerald-500/50 shadow-xl">
                    <div class="flex flex-col sm:flex-row items-center gap-5">
                        <div class="w-28 h-28 bg-white p-2 rounded-2xl border-2 border-emerald-400/50 shadow-md flex-shrink-0 cursor-pointer group relative overflow-hidden" onclick="openQrisModal()" title="Klik untuk perbesar QRIS">
                            <img src="/images/qris.jpg" alt="QRIS Wijaya Barbershop" class="w-full h-full object-contain rounded-lg group-hover:scale-105 transition">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white text-[10px] font-bold">
                                <i class="fas fa-search-plus mr-1"></i> Perbesar
                            </div>
                        </div>
                        <div class="flex-1 text-center sm:text-left space-y-1">
                            <div class="inline-flex items-center space-x-1.5 px-2.5 py-0.5 rounded-full bg-emerald-500/20 text-emerald-400 text-[10px] font-black uppercase tracking-wider border border-emerald-500/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                                <span>Menunggu Pembayaran QRIS</span>
                            </div>
                            <h4 class="font-extrabold text-base text-white">QRIS <span class="text-xs font-mono text-emerald-400 font-normal">(NMID: ID1025448727392)</span></h4>
                            <p class="text-xs text-slate-300">Scan QR Code dengan aplikasi m-Banking atau E-Wallet apa saja sebesar <strong class="text-emerald-400 font-mono">Rp <?= number_format((int)$order['grand_total'], 0, ',', '.') ?></strong>.</p>
                            <div class="pt-2 flex flex-wrap gap-2 justify-center sm:justify-start">
                                <button type="button" onclick="executeQrisAutoConfirm(true)" 
                                    class="px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 hover:to-teal-300 text-slate-950 font-black rounded-xl transition shadow-lg shadow-emerald-500/30 text-xs uppercase tracking-wider flex items-center space-x-1.5 shimmer-btn cursor-pointer">
                                    <i class="fas fa-check-circle text-sm"></i>
                                    <span>Konfirmasi Sudah Bayar</span>
                                </button>
                                <button type="button" onclick="openQrisModal()" 
                                    class="px-3.5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-200 font-bold rounded-xl transition text-xs border border-slate-700 flex items-center space-x-1.5">
                                    <i class="fas fa-qrcode text-emerald-400"></i>
                                    <span>Tampilkan QRIS Penuh</span>
                                </button>
                                <a href="/images/qris.jpg" download="QRIS.jpg" 
                                    class="px-3 py-2.5 bg-slate-800/80 hover:bg-slate-700 text-slate-300 hover:text-white font-semibold rounded-xl transition text-xs border border-slate-700 flex items-center space-x-1">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php elseif ($order['status'] === 'pending' && $order['payment_method'] === 'tunai'): ?>
            <div class="no-print pt-4 border-t border-slate-100">
                <div class="p-4 rounded-2xl bg-amber-50 border border-amber-200 text-amber-900 text-xs flex items-center space-x-3">
                    <i class="fas fa-info-circle text-amber-600 text-xl flex-shrink-0"></i>
                    <div>
                        <?php if ($isHomeService): ?>
                        <strong class="font-bold block">Pembayaran Tunai di Lokasi (COD ke Barber):</strong>
                        <span>Silakan siapkan pembayaran tunai sebesar <strong>Rp <?= number_format((int)$order['grand_total'], 0, ',', '.') ?></strong> untuk dibayarkan langsung kepada Capster saat selesai pangkas rambut di lokasi Anda.</span>
                        <?php else: ?>
                        <strong class="font-bold block">Pembayaran Tunai di Kasir:</strong>
                        <span>Silakan datang ke Wijaya Barbershop tepat waktu dan tunjukkan nomor antrean ini ke kasir (Mely / Riles) untuk verifikasi pembayaran.</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- Footer Resi -->
            <div class="pt-6 border-t border-dashed border-slate-200 text-center text-[11px] text-slate-400">
                <p>Terima kasih telah mempercayakan penampilan Anda kepada Wijaya Barbershop.</p>
                <p class="mt-1 font-mono text-slate-500">Tanggal Booking: <?= date('d/m/Y H:i:s', strtotime($order['created_at'])) ?></p>
            </div>
        </div>
    </div>
</div>

<script>
let isConfirmingQris = false;

function openQrisModal() {
    const m = document.getElementById('qris-auto-modal');
    if (m) {
        m.classList.remove('hidden', 'opacity-0', 'pointer-events-none');
        m.classList.add('flex', 'opacity-100');
    }
}

function closeQrisModal() {
    const m = document.getElementById('qris-auto-modal');
    if (m) {
        m.classList.remove('opacity-100');
        m.classList.add('opacity-0', 'pointer-events-none');
        setTimeout(() => {
            m.classList.add('hidden');
            m.classList.remove('flex');
        }, 200);
    }
}

function executeQrisAutoConfirm(isManual = false) {
    if (isConfirmingQris) return;
    isConfirmingQris = true;

    const code = <?= json_encode($order['order_code'] ?? '') ?>;
    const sessionKey = 'wijaya_sound_played_' + code;
    sessionStorage.setItem(sessionKey, '1');

    // LANGSUNG BUNYIKAN SUARA LONCENG & SUARA PEREMPUAN SEKETIKA SAAT DIKLIK / TERVERIFIKASI!
    if (typeof playTransactionSuccessSound === 'function') {
        try { playTransactionSuccessSound(true); } catch (e) { console.error('Audio play error:', e); }
    }

    const statusPill = document.getElementById('qris-status-pill');
    const detectTitle = document.getElementById('qris-detect-title');
    const progressBar = document.getElementById('qris-progress-bar');
    const countdownText = document.getElementById('qris-countdown-text');
    const btnConfirm = document.getElementById('btn-manual-confirm-qris');

    if (statusPill) {
        statusPill.textContent = 'Memverifikasi...';
        statusPill.className = 'text-amber-300 font-bold';
    }
    if (detectTitle) detectTitle.textContent = 'Pembayaran Diterima, Memproses Lunas...';
    if (progressBar) {
        progressBar.style.transition = 'width 0.4s ease';
        progressBar.style.width = '100%';
    }
    if (countdownText) countdownText.textContent = '✓ LUNAS';
    if (btnConfirm) {
        btnConfirm.disabled = true;
        btnConfirm.innerHTML = '<i class="fas fa-circle-notch fa-spin mr-1.5"></i><span>Memverifikasi Lunas...</span>';
    }

    // Panggil API auto-confirm
    fetch('/api/order/qris-auto-confirm', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: 'order_code=' + encodeURIComponent(code)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            setTimeout(() => {
                closeQrisModal();
                const targetUrl = data.redirect || ('/receipt?code=' + encodeURIComponent(code) + '&auto_wa=1&payment_status=success');
                window.location.href = targetUrl;
            }, 1100);
        } else {
            isConfirmingQris = false;
        }
    })
    .catch(err => {
        console.warn('QRIS fetch auto-confirm error, fallbacking to form post:', err);
        const form = document.getElementById('form-confirm-qris');
        if (form) form.submit();
    });
}
window.executeQrisAutoConfirm = executeQrisAutoConfirm;

function closeWaModal() {
    const m = document.getElementById('wa-auto-modal');
    if (m) {
        m.classList.add('opacity-0', 'pointer-events-none');
        m.style.display = 'none';
        try { m.remove(); } catch (e) {}
    }
    // Hapus parameter auto_wa dari URL agar saat refresh tidak muncul lagi
    try {
        const u = new URL(window.location.href);
        if (u.searchParams.has('auto_wa') || u.searchParams.has('payment_status')) {
            u.searchParams.delete('auto_wa');
            u.searchParams.delete('payment_status');
            window.history.replaceState({}, document.title, u.pathname + (u.search ? u.search : ''));
        }
    } catch (e) {}
}
window.closeWaModal = closeWaModal;

function openWhatsAppAction(type) {
    const desktopUrl = <?= json_encode($waDesktopLink ?? '') ?>;
    const webUrl = <?= json_encode($waLink ?? '') ?>;

    // Tutup & buang modal seketika sehingga saat user kembali ke web, tampilan sudah bersih total!
    closeWaModal();

    if (type === 'desktop' && desktopUrl) {
        // Gunakan iframe tersembunyi agar scheme whatsapp:// tidak menyebabkan browser hang / unclickable
        try {
            const ifr = document.createElement('iframe');
            ifr.style.display = 'none';
            ifr.src = desktopUrl;
            document.body.appendChild(ifr);
            setTimeout(() => { try { ifr.remove(); } catch(e){} }, 3000);
        } catch (e) {
            try { window.open(webUrl || desktopUrl, '_blank'); } catch (err) {}
        }
    } else {
        try { window.open(webUrl || desktopUrl, '_blank'); } catch (err) {}
    }
}
window.openWhatsAppAction = openWhatsAppAction;

// Otomatis tutup modal jika pengguna berpindah jendela ke aplikasi WhatsApp dan kembali lagi ke web
window.addEventListener('blur', function () {
    closeWaModal();
});
window.addEventListener('focus', function () {
    closeWaModal();
});
document.addEventListener('visibilitychange', function () {
    if (document.hidden) {
        closeWaModal();
    }
});

document.addEventListener('DOMContentLoaded', function () {
    const isQrisPending = <?= json_encode($isQrisPending) ?>;
    const autoWa = <?= json_encode(!empty($autoWa) && (!empty($waLink) || !empty($waDesktopLink))) ?>;
    const desktopUrl = <?= json_encode($waDesktopLink ?? '') ?>;
    const webUrl = <?= json_encode($waLink ?? '') ?>;
    const orderStatus = <?= json_encode($order['status'] ?? '') ?>;
    const isLunas = (orderStatus === 'settlement' || orderStatus === 'completed');
    const orderCode = <?= json_encode($order['order_code'] ?? '') ?>;
    const sessionKey = 'wijaya_sound_played_' + orderCode;

    // Jika pembayaran QRIS masih pending:
    // Pantau status pembayaran secara real-time (tanpa timer hitung mundur palsu!)
    // Suara baru akan berbunyi otomatis ketika pembayaran benar-benar terverifikasi (oleh pelanggan setelah scan / kasir / webhook).
    if (isQrisPending) {
        const pageLoadTs = Math.floor(Date.now() / 1000);
        const pollInterval = setInterval(() => {
            if (isConfirmingQris) { clearInterval(pollInterval); return; }
            fetch('/api/order/status?code=' + encodeURIComponent(orderCode) + '&ts=' + pageLoadTs)
                .then(r => r.json())
                .then(st => {
                    if (st.is_settlement) {
                        clearInterval(pollInterval);
                        executeQrisAutoConfirm();
                    }
                }).catch(() => {});
        }, 1500);
    }

    // Bunyikan suara konfirmasi HANYA 1 KALI ketika pesanan selesai dikonfirmasi (QRIS Lunas atau Pesanan Tunai)
    if ((!isQrisPending || isLunas) && !sessionStorage.getItem(sessionKey)) {
        sessionStorage.setItem(sessionKey, '1');
        if (typeof playTransactionSuccessSound === 'function') {
            try { playTransactionSuccessSound(true); } catch (e) {}
        }
    }

    // Jika notifikasi WhatsApp otomatis aktif:
    if (autoWa && (desktopUrl || webUrl)) {
        let timer = 3;
        const countdownEl = document.getElementById('wa-countdown-text');
        const interval = setInterval(() => {
            timer--;
            if (countdownEl && timer > 0) {
                countdownEl.textContent = 'Membuka WhatsApp (' + timer + 's)...';
            } else {
                clearInterval(interval);
                // Buka WhatsApp dan otomatis tutup modal seketika
                openWhatsAppAction('desktop');
            }
        }, 1000);
    }
});
</script>

