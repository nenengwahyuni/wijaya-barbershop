<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <div class="mb-8">
        <div class="inline-flex items-center space-x-2 px-4 py-1.5 rounded-full bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 text-xs font-black uppercase tracking-widest mb-3 backdrop-blur-md shadow-sm">
            <span class="w-2 h-2 rounded-full bg-emerald-400 beacon-pulse"></span>
            <span>Tahap Akhir Reservasi</span>
        </div>
        <h1 class="text-3xl sm:text-4xl font-black text-white tracking-tight leading-tight">
            Formulir <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-300 to-amber-300 animate-gradient">Pemesanan & Pembayaran</span>
        </h1>
        <p class="text-sm text-slate-300 mt-2">Pilih jenis layanan (Datang ke Barbershop atau Cukur Panggilan ke Rumah), lengkapi data, dan tentukan pembayaran.</p>
    </div>

    <form action="/checkout" method="POST" id="checkout-form">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Kolom Kiri: Pilihan Layanan, Detail Pemesan & Pembayaran -->
            <div class="lg:col-span-7 space-y-6">

                <!-- Card 0: Pilih Jenis Layanan (Barbershop vs Cukur Panggilan / Home Service) -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
                    <h3 class="text-lg font-bold text-slate-900 mb-5 flex items-center space-x-2.5 pb-4 border-b border-slate-100">
                        <span class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm font-bold">1</span>
                        <span>Pilih Tipe Layanan Cukur</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Datang ke Barbershop -->
                        <label class="cursor-pointer border-2 rounded-2xl p-4 transition flex flex-col justify-between border-slate-200 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/30">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-extrabold text-slate-900 text-sm flex items-center space-x-2">
                                    <i class="fas fa-store text-emerald-600 text-base"></i>
                                    <span>Datang ke Barbershop</span>
                                </span>
                                <input type="radio" name="service_type" value="barbershop" 
                                    <?= ($selectedType ?? 'barbershop') === 'barbershop' ? 'checked' : '' ?>
                                    onchange="toggleCheckoutServiceType()"
                                    class="text-emerald-600 focus:ring-emerald-500">
                            </div>
                            <p class="text-xs text-slate-500">
                                Ambil nomor antrean online dan datang langsung ke studio Wijaya Barbershop.
                            </p>
                            <span class="mt-2.5 text-[10px] font-bold uppercase tracking-wider text-slate-600 bg-slate-100 px-2.5 py-0.5 rounded w-max">
                                Bebas Biaya Transport
                            </span>
                        </label>

                        <!-- Cukur Panggilan (Home Service) -->
                        <label class="cursor-pointer border-2 rounded-2xl p-4 transition flex flex-col justify-between border-slate-200 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/30">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-extrabold text-slate-900 text-sm flex items-center space-x-2">
                                    <i class="fas fa-motorcycle text-amber-500 text-base"></i>
                                    <span>Cukur Panggilan (Home Service)</span>
                                </span>
                                <input type="radio" name="service_type" value="home_service"
                                    <?= ($selectedType ?? 'barbershop') === 'home_service' ? 'checked' : '' ?>
                                    onchange="toggleCheckoutServiceType()"
                                    class="text-emerald-600 focus:ring-emerald-500">
                            </div>
                            <p class="text-xs text-slate-500">
                                Barber kami datang ke rumah, kos, atau kantor Anda sesuai alamat & jadwal pilihan.
                            </p>
                            <span class="mt-2.5 text-[10px] font-bold uppercase tracking-wider text-amber-800 bg-amber-100 px-2.5 py-0.5 rounded w-max">
                                Layanan Panggilan ke Lokasi
                            </span>
                        </label>
                    </div>

                    <!-- Pilihan Barber / Capster -->
                    <div class="mt-5 pt-5 border-t border-slate-100">
                        <label for="barber_id" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                            <i class="fas fa-user-scissors text-emerald-600 mr-1"></i> Pilih Capster / Barber (Opsional)
                        </label>
                        <select id="barber_id" name="barber_id" class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-emerald-500 text-slate-900 text-sm font-medium outline-none bg-white">
                            <option value="">-- Bebas / Rekomendasi Capster yang Tersedia --</option>
                            <?php if (!empty($barbers)): foreach ($barbers as $b): ?>
                            <option value="<?= $b['id'] ?>"><?= htmlspecialchars($b['fullname']) ?> (Standby On-Duty)</option>
                            <?php endforeach; endif; ?>
                        </select>
                    </div>
                </div>

                <!-- Card Khusus Home Service (Tampil saat Cukur Panggilan dipilih) -->
                <div id="home-service-fields" class="bg-amber-50/40 rounded-3xl border-2 border-amber-200 shadow-sm p-6 sm:p-8 hidden">
                    <h3 class="text-lg font-bold text-slate-900 mb-5 flex items-center space-x-2.5 pb-4 border-b border-amber-200/70">
                        <span class="w-8 h-8 rounded-xl bg-amber-500 text-white flex items-center justify-center text-sm font-bold">
                            <i class="fas fa-map-marker-alt"></i>
                        </span>
                        <span>Detail Lokasi & Jadwal Cukur Panggilan</span>
                    </h3>

                    <div class="space-y-4">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="scheduled_date" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Tanggal Kunjungan *
                                </label>
                                <input type="date" id="scheduled_date" name="scheduled_date"
                                    min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-white text-slate-900 text-sm font-semibold outline-none">
                            </div>
                            <div>
                                <label for="scheduled_time" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                    Jam Kedatangan Barber *
                                </label>
                                <select id="scheduled_time" name="scheduled_time"
                                    class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-white text-slate-900 text-sm font-semibold outline-none">
                                    <option value="09:00">09:00 WIB (Pagi)</option>
                                    <option value="10:30">10:30 WIB (Pagi)</option>
                                    <option value="13:00">13:00 WIB (Siang)</option>
                                    <option value="14:30" selected>14:30 WIB (Siang)</option>
                                    <option value="16:00">16:00 WIB (Sore)</option>
                                    <option value="18:30">18:30 WIB (Malam)</option>
                                    <option value="20:00">20:00 WIB (Malam)</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="area_zone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Zona Wilayah Kunjungan (Biaya Transport) *
                            </label>
                            <select id="area_zone" name="area_zone" onchange="toggleCheckoutServiceType()"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-white text-slate-900 text-sm font-semibold outline-none">
                                <?php if (!empty($areaZones)): foreach ($areaZones as $zKey => $zInfo): ?>
                                <option value="<?= htmlspecialchars($zKey) ?>" data-fee="<?= $zInfo['fee'] ?>">
                                    <?= htmlspecialchars($zInfo['label']) ?> (+Rp <?= number_format($zInfo['fee'], 0, ',', '.') ?>)
                                </option>
                                <?php endforeach; endif; ?>
                            </select>
                        </div>

                        <div>
                            <label for="customer_address" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Alamat Lengkap Tujuan Cukur Panggilan *
                            </label>
                            <textarea id="customer_address" name="customer_address" rows="3"
                                placeholder="Masukkan Nama Jalan, Nomor Rumah, RT/RW, Kelurahan, dan Patokan Lokasi..."
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 bg-white text-slate-900 text-sm outline-none"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Card 1: Data Diri Pemesan -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
                    <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center space-x-2.5 pb-4 border-b border-slate-100">
                        <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">2</span>
                        <span>Detail Pemesan & Kontak</span>
                    </h3>

                    <div class="space-y-4">
                        <div>
                            <label for="fullname" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Nama Lengkap *
                            </label>
                            <input type="text" id="fullname" name="fullname" required 
                                value="<?= htmlspecialchars($user['fullname'] ?? '') ?>" 
                                placeholder="Masukkan nama lengkap Anda"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-slate-900 text-sm transition outline-none">
                        </div>

                        <div>
                            <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Nomor WhatsApp *
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                                    <i class="fab fa-whatsapp text-emerald-600 text-base"></i>
                                </span>
                                <input type="tel" id="phone" name="phone" required 
                                    value="<?= htmlspecialchars($user['phone'] ?? '') ?>" 
                                    placeholder="0812xxxxxxxx"
                                    class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-slate-900 text-sm transition outline-none">
                            </div>
                            <span class="text-[11px] text-slate-400 mt-1 block">Nomor aktif untuk konfirmasi jadwal antrean / kunjungan barber.</span>
                        </div>

                        <div>
                            <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Catatan Pesanan (Opsional)
                            </label>
                            <textarea id="notes" name="notes" rows="2" 
                                placeholder="Contoh: Model rambut fade samping tipis, atau permintaan khusus lainnya..."
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-slate-900 text-sm transition outline-none"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Card 2: Metode Pembayaran (Sesuai Gambar 36) -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
                    <h3 class="text-lg font-bold text-slate-900 mb-6 flex items-center space-x-2.5 pb-4 border-b border-slate-100">
                        <span class="w-8 h-8 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-sm font-bold">3</span>
                        <span>Metode Pembayaran</span>
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Opsi QRIS -->
                        <label class="relative flex flex-col p-5 border-2 rounded-2xl cursor-pointer hover:border-emerald-500 transition group border-slate-200 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/20">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-extrabold text-slate-900 text-sm flex items-center space-x-2">
                                    <i class="fas fa-qrcode text-emerald-600 text-lg"></i>
                                    <span>QRIS</span>
                                </span>
                                <input type="radio" name="payment_method" value="qris" checked class="text-emerald-600 focus:ring-emerald-500">
                            </div>
                            <p class="text-xs text-slate-500">
                                Scan instan QR Code Nasional via GoPay, OVO, Dana, ShopeePay, BCA, Mandiri, BRI, BNI.
                            </p>
                            <span class="mt-3 text-[10px] font-bold uppercase tracking-wider text-emerald-700 bg-emerald-100 px-2 py-0.5 rounded w-max">
                                Rekomendasi Bebas Antre
                            </span>
                        </label>

                        <!-- Opsi Tunai (Cash) -->
                        <label class="relative flex flex-col p-5 border-2 rounded-2xl cursor-pointer hover:border-emerald-500 transition group border-slate-200 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/20">
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-extrabold text-slate-900 text-sm flex items-center space-x-2">
                                    <i class="fas fa-money-bill-wave text-slate-600 text-lg"></i>
                                    <span>Tunai (Cash / COD)</span>
                                </span>
                                <input type="radio" name="payment_method" value="tunai" class="text-emerald-600 focus:ring-emerald-500">
                            </div>
                            <p class="text-xs text-slate-500">
                                Bayar langsung secara tunai ke kasir Barbershop atau ke Capster saat tiba di rumah Anda.
                            </p>
                            <span class="mt-3 text-[10px] font-bold uppercase tracking-wider text-slate-600 bg-slate-100 px-2 py-0.5 rounded w-max">
                                Bayar di Tempat
                            </span>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Rincian Pesanan & Konfirmasi with Laser Border Beam -->
            <div class="lg:col-span-5 border-beam-card tilt-card-luxury text-white p-6 sm:p-8 sticky top-28 shadow-2xl overflow-hidden">
                <h3 class="text-lg font-black text-white mb-4 pb-3 border-b border-white/10 flex items-center justify-between relative z-10">
                    <span>Detail Pesanan</span>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-white/10 rounded-lg text-emerald-400 border border-white/10"><?= count($cart) ?> Layanan</span>
                </h3>

                <!-- Estimasi Antrean Info -->
                <div class="mb-5 p-4 rounded-2xl bg-gradient-to-r from-emerald-950/90 to-slate-900 border border-emerald-500/30 text-white shadow-md relative z-10">
                    <div class="flex items-center justify-between">
                        <div>
                            <span id="badge-queue-title" class="text-[10px] uppercase font-bold tracking-widest text-emerald-400 block mb-0.5">Estimasi Tiket Antrean</span>
                            <span class="text-xl font-black">Nomor Antrean #<?= $queueInfo['queue_number'] ?></span>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] text-slate-400 block mb-0.5">Perkiraan Waktu:</span>
                            <span id="badge-queue-time" class="text-sm font-bold text-amber-400"><?= $queueInfo['formatted_time'] ?></span>
                        </div>
                    </div>
                </div>

                <!-- Table Item Pesanan (Gambar 36) -->
                <div class="divide-y divide-white/10 mb-5 max-h-56 overflow-y-auto pr-1 relative z-10">
                    <?php foreach ($cart as $item): ?>
                    <?php $ckImg = \App\Models\Item::getImageUrl($item['image_path'] ?? null, $item['name'] ?? ''); ?>
                    <div class="py-2.5 flex items-center justify-between text-sm">
                        <div class="flex items-center space-x-3">
                            <img src="<?= htmlspecialchars($ckImg) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-10 h-10 rounded-xl object-cover border border-white/10 shadow-sm flex-shrink-0">
                            <div>
                                <h4 class="font-bold text-white text-xs sm:text-sm leading-tight"><?= htmlspecialchars($item['name']) ?></h4>
                                <span class="text-[11px] text-slate-400"><?= $item['quantity'] ?>x @ Rp <?= number_format($item['price'], 0, ',', '.') ?></span>
                            </div>
                        </div>
                        <span class="font-black text-emerald-400 text-xs sm:text-sm ml-2">
                            Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Bagian Kupon Promo & Diskon Voucher -->
                <div class="mb-5 p-4 rounded-2xl bg-white/5 border border-white/10 relative z-10">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2 flex items-center justify-between">
                        <span class="flex items-center space-x-1.5">
                            <i class="fas fa-ticket-alt text-emerald-400"></i>
                            <span>Punya Kode Voucher Promo?</span>
                        </span>
                        <a href="/promotions" target="_blank" class="text-[10px] text-emerald-400 hover:underline">Lihat Katalog &rarr;</a>
                    </label>
                    <div class="flex items-center space-x-2">
                        <input type="text" id="voucher_code_input" name="voucher_code" placeholder="Ketik kode kupon..." class="flex-1 px-3.5 py-2.5 rounded-xl bg-slate-900 border border-slate-700 focus:border-emerald-500 font-mono text-xs uppercase tracking-wider text-white outline-none">
                        <button type="button" onclick="applyVoucher()" id="btn-apply-voucher" class="px-4 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs uppercase tracking-wider transition shadow-md shadow-emerald-600/30 flex-shrink-0">
                            Terapkan
                        </button>
                    </div>
                    <div id="voucher-feedback" class="text-[11px] mt-2 hidden font-semibold"></div>

                    <!-- Quick Voucher Chips -->
                    <?php if (!empty($vouchers)): ?>
                    <div class="mt-3 pt-2.5 border-t border-white/10">
                        <span class="text-[10px] text-slate-400 block mb-1.5 uppercase font-bold">Kupon Rekomendasi:</span>
                        <div class="flex flex-wrap gap-1.5">
                            <?php foreach (array_slice($vouchers, 0, 3) as $vc): ?>
                            <button type="button" onclick="selectVoucherChip('<?= htmlspecialchars($vc['code']) ?>')" class="px-2.5 py-1 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[10px] font-mono font-bold transition flex items-center space-x-1">
                                <span><?= htmlspecialchars($vc['code']) ?></span>
                                <span class="text-[9px] text-slate-400">(<?= ($vc['discount_type'] === 'percentage') ? $vc['discount_value'].'%' : 'Rp '.number_format($vc['discount_value']/1000).'k' ?>)</span>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Bagian Diskon Member VIP & Penukaran Poin -->
                <?php if ($loyalty): ?>
                <div class="mb-5 p-4 rounded-2xl bg-gradient-to-r from-amber-950/40 to-slate-900 border border-amber-500/30 text-white relative z-10 space-y-3">
                    <!-- Status Member Tier -->
                    <div class="flex items-center justify-between pb-2.5 border-b border-white/10">
                        <div class="flex items-center space-x-2">
                            <span class="w-6 h-6 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-xs font-bold">
                                <i class="fas fa-crown"></i>
                            </span>
                            <span class="text-xs font-bold text-amber-300">Tier: <?= htmlspecialchars($loyalty['benefits']['name']) ?></span>
                        </div>
                        <?php if (!empty($loyalty['benefits']['discount_percent'])): ?>
                        <span class="text-[11px] font-black text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-lg">
                            Diskon <?= $loyalty['benefits']['discount_percent'] ?>% Aktif
                        </span>
                        <?php else: ?>
                        <span class="text-[10px] text-slate-400">1x Poin Tiap Transaksi</span>
                        <?php endif; ?>
                    </div>

                    <!-- Penukaran Poin Loyalitas -->
                    <?php if ($loyalty['points'] > 0): ?>
                    <?php 
                        $canRedeemPoints = min($loyalty['points'], 500);
                        $canRedeemRupiah = $canRedeemPoints * 50;
                    ?>
                    <div class="flex items-start justify-between">
                        <div>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" id="use_points_toggle" onchange="togglePointsRedeem()" class="w-4 h-4 rounded text-amber-500 focus:ring-amber-400 bg-slate-800 border-slate-700">
                                <span class="text-xs font-bold text-slate-200">Gunakan Poin Loyalitas</span>
                            </label>
                            <span class="text-[10px] text-slate-400 block ml-6">
                                Saldo: <?= number_format($loyalty['points']) ?> Poin (Maks. <?= $canRedeemPoints ?> Poin = Rp <?= number_format($canRedeemRupiah, 0, ',', '.') ?>)
                            </span>
                        </div>
                        <input type="hidden" id="redeem_points" name="redeem_points" value="0" data-max="<?= $canRedeemPoints ?>">
                        <span id="points-discount-badge" class="text-xs font-black text-amber-400 hidden">-Rp <?= number_format($canRedeemRupiah, 0, ',', '.') ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Kalkulasi Biaya (Subtotal, Diskon Member, Diskon Voucher, Diskon Poin, Pajak 10%, Transport, Total) -->
                <div class="space-y-2 text-xs sm:text-sm text-slate-300 pt-3 border-t border-white/10 mb-6 relative z-10">
                    <div class="flex justify-between">
                        <span>Subtotal:</span>
                        <span class="font-semibold text-white" id="val-subtotal" data-subtotal="<?= $subtotal ?>">Rp <?= number_format($subtotal, 0, ',', '.') ?></span>
                    </div>

                    <!-- Row Diskon Member -->
                    <div id="row-member-discount" class="flex justify-between text-emerald-400 <?= empty($loyalty['benefits']['discount_percent']) ? 'hidden' : '' ?>">
                        <span><i class="fas fa-crown text-amber-400 mr-1"></i> Diskon Member (<?= $loyalty['benefits']['discount_percent'] ?? 0 ?>%):</span>
                        <span class="font-bold" id="val-member-discount">-Rp 0</span>
                    </div>

                    <!-- Row Diskon Voucher -->
                    <div id="row-voucher-discount" class="flex justify-between text-emerald-400 hidden">
                        <span><i class="fas fa-ticket mr-1"></i> Diskon Kupon (<span id="label-voucher-code">-</span>):</span>
                        <span class="font-bold" id="val-voucher-discount">-Rp 0</span>
                    </div>

                    <!-- Row Diskon Poin -->
                    <div id="row-points-discount" class="flex justify-between text-amber-300 hidden">
                        <span><i class="fas fa-coins mr-1"></i> Potongan Poin VIP:</span>
                        <span class="font-bold" id="val-points-discount">-Rp 0</span>
                    </div>

                    <div class="flex justify-between">
                        <span>Pajak (10%):</span>
                        <span class="font-semibold text-white" id="val-tax">Rp <?= number_format($tax, 0, ',', '.') ?></span>
                    </div>

                    <div id="row-delivery-fee" class="flex justify-between text-amber-300 bg-amber-500/10 border border-amber-500/30 px-3 py-1.5 rounded-xl hidden">
                        <span class="font-semibold"><i class="fas fa-motorcycle mr-1"></i> Biaya Transport Home Service:</span>
                        <span id="checkout-delivery-fee" class="font-black">Rp 0</span>
                    </div>

                    <div class="flex justify-between items-center pt-3 border-t border-white/10">
                        <span class="text-base font-bold text-white">Total Pembayaran:</span>
                        <span id="checkout-grand-total" data-base="<?= $grandTotal ?>" class="text-2xl font-black text-emerald-400">
                            Rp <?= number_format($grandTotal, 0, ',', '.') ?>
                        </span>
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-amber-300/90 pt-1">
                        <span><i class="fas fa-star mr-1 text-amber-400"></i> Estimasi Poin Diperoleh:</span>
                        <span class="font-bold" id="val-points-earned">+0 Poin VIP</span>
                    </div>
                </div>

                <!-- Tombol Konfirmasi Pesanan (Gambar 36) -->
                <button type="submit" 
                    class="w-full py-4 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-black rounded-2xl transition duration-300 shadow-xl shadow-emerald-600/40 text-center text-sm uppercase tracking-wider flex items-center justify-center space-x-2 shimmer-btn btn-magnetic relative z-10">
                    <i class="fas fa-check-circle"></i>
                    <span>KONFIRMASI PESANAN</span>
                </button>

                <p class="text-center text-[11px] text-slate-400 mt-3 relative z-10">
                    Dengan menekan tombol, Anda menyetujui reservasi di Wijaya Barbershop.
                </p>
            </div>
        </div>
    </form>
</div>

<script>
const subtotalBase = <?= (int) $subtotal ?>;
const memberDiscountPercent = <?= (int) ($loyalty['benefits']['discount_percent'] ?? 0) ?>;
const pointMultiplier = <?= (float) ($loyalty['benefits']['point_multiplier'] ?? 1.0) ?>;

let currentVoucherDiscount = 0;
let currentPointsDiscount = 0;
let currentDeliveryFee = 0;

function formatRpCheckout(num) {
    return 'Rp ' + Math.max(0, num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function selectVoucherChip(code) {
    document.getElementById('voucher_code_input').value = code;
    applyVoucher();
}

function applyVoucher() {
    const code = document.getElementById('voucher_code_input').value.trim();
    const fb = document.getElementById('voucher-feedback');
    const serviceType = document.querySelector('input[name="service_type"]:checked') ? document.querySelector('input[name="service_type"]:checked').value : 'barbershop';

    if (!code) {
        fb.textContent = 'Silakan ketikkan kode kupon.';
        fb.className = 'text-[11px] mt-2 font-semibold text-rose-400 block';
        currentVoucherDiscount = 0;
        document.getElementById('row-voucher-discount').classList.add('hidden');
        recalculateTotals();
        return;
    }

    fetch('/api/vouchers/apply', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'code=' + encodeURIComponent(code) + '&subtotal=' + subtotalBase + '&service_type=' + serviceType
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            currentVoucherDiscount = data.voucher.discount;
            fb.textContent = '✓ ' + data.message;
            fb.className = 'text-[11px] mt-2 font-semibold text-emerald-400 block';

            document.getElementById('row-voucher-discount').classList.remove('hidden');
            document.getElementById('label-voucher-code').textContent = data.voucher.code;
            document.getElementById('val-voucher-discount').textContent = '-Rp ' + currentVoucherDiscount.toLocaleString('id-ID');
        } else {
            currentVoucherDiscount = 0;
            fb.textContent = '✗ ' + data.message;
            fb.className = 'text-[11px] mt-2 font-semibold text-rose-400 block';
            document.getElementById('row-voucher-discount').classList.add('hidden');
        }
        recalculateTotals();
    })
    .catch(() => {
        fb.textContent = 'Gagal memverifikasi voucher.';
        fb.className = 'text-[11px] mt-2 font-semibold text-rose-400 block';
    });
}

function togglePointsRedeem() {
    const cb = document.getElementById('use_points_toggle');
    const input = document.getElementById('redeem_points');
    const badge = document.getElementById('points-discount-badge');
    const row = document.getElementById('row-points-discount');

    if (cb && cb.checked) {
        const maxPts = parseInt(input.getAttribute('data-max') || '0', 10);
        input.value = maxPts;
        currentPointsDiscount = maxPts * 50;
        badge.classList.remove('hidden');
        row.classList.remove('hidden');
        document.getElementById('val-points-discount').textContent = '-Rp ' + currentPointsDiscount.toLocaleString('id-ID');
    } else {
        if (input) input.value = 0;
        currentPointsDiscount = 0;
        if (badge) badge.classList.add('hidden');
        if (row) row.classList.add('hidden');
    }
    recalculateTotals();
}

function recalculateTotals() {
    // 1. Diskon Member
    let memberDiscount = 0;
    if (memberDiscountPercent > 0) {
        memberDiscount = Math.round((subtotalBase * memberDiscountPercent) / 100);
        const rowMem = document.getElementById('row-member-discount');
        if (rowMem) {
            rowMem.classList.remove('hidden');
            document.getElementById('val-member-discount').textContent = '-Rp ' + memberDiscount.toLocaleString('id-ID');
        }
    }

    // 2. Total Diskon (dibatasi maksimal subtotal)
    const totalDiscount = Math.min(subtotalBase, memberDiscount + currentVoucherDiscount + currentPointsDiscount);

    // 3. Dasar Pengenaan Pajak
    const taxableAmount = Math.max(0, subtotalBase - totalDiscount);
    const tax = Math.round(taxableAmount * 0.10);

    // 4. Grand Total
    const grandTotal = Math.max(0, taxableAmount + tax + currentDeliveryFee);

    // 5. Estimasi Poin yang Didapat
    const pointsEarned = Math.round(Math.floor(grandTotal / 1000) * pointMultiplier);

    // Update DOM
    document.getElementById('val-tax').textContent = formatRpCheckout(tax);
    document.getElementById('checkout-grand-total').textContent = formatRpCheckout(grandTotal);
    document.getElementById('val-points-earned').textContent = '+' + pointsEarned.toLocaleString('id-ID') + ' Poin VIP';
}

function toggleCheckoutServiceType() {
    const selected = document.querySelector('input[name="service_type"]:checked');
    const hsFields = document.getElementById('home-service-fields');
    const addrInput = document.getElementById('customer_address');
    const rowDelivery = document.getElementById('row-delivery-fee');
    const deliveryFeeText = document.getElementById('checkout-delivery-fee');

    if (selected && selected.value === 'home_service') {
        if (hsFields) hsFields.classList.remove('hidden');
        if (addrInput) addrInput.setAttribute('required', 'required');
        if (rowDelivery) rowDelivery.classList.remove('hidden');

        const zoneSelect = document.getElementById('area_zone');
        const selectedOption = zoneSelect ? zoneSelect.options[zoneSelect.selectedIndex] : null;
        currentDeliveryFee = parseInt(selectedOption ? selectedOption.getAttribute('data-fee') : '10000', 10);

        if (deliveryFeeText) deliveryFeeText.textContent = formatRpCheckout(currentDeliveryFee);
    } else {
        if (hsFields) hsFields.classList.add('hidden');
        if (addrInput) addrInput.removeAttribute('required');
        if (rowDelivery) rowDelivery.classList.add('hidden');
        currentDeliveryFee = 0;
    }

    recalculateTotals();
}

document.addEventListener('DOMContentLoaded', () => {
    toggleCheckoutServiceType();
    recalculateTotals();
});
</script>
