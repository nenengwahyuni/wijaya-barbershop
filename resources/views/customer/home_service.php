<!-- Hero Banner Cukur Panggilan (Home Service) -->
<section class="relative bg-slate-900 text-white py-14 overflow-hidden border-b border-slate-800">
    <div class="absolute inset-0 bg-gradient-to-r from-emerald-950 via-slate-900 to-slate-950 opacity-95"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-8">
            <div class="max-w-2xl">
                <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30 text-xs font-extrabold uppercase tracking-wider mb-4">
                    <i class="fas fa-motorcycle"></i>
                    <span>Fitur Layanan Baru — Wijaya Home Service</span>
                </div>
                <h1 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold tracking-tight text-white leading-tight mb-4">
                    Layanan <span class="text-emerald-400">Cukur Panggilan</span> Langsung ke Lokasi Anda
                </h1>
                <p class="text-sm sm:text-base text-slate-300 leading-relaxed">
                    Sibuk kerja, malas keluar rumah, atau ingin pangkas rambut bersama keluarga di rumah? Capster profesional Wijaya Barbershop siap datang ke rumah, kos, kantor, atau hotel Anda di wilayah Kota Metro & sekitarnya dengan peralatan lengkap dan steril.
                </p>
            </div>

            <!-- Info Keunggulan Home Service -->
            <div class="grid grid-cols-2 gap-3 sm:w-96 flex-shrink-0">
                <div class="p-4 rounded-2xl bg-slate-800/90 border border-slate-700">
                    <i class="fas fa-briefcase-medical text-emerald-400 text-lg mb-2"></i>
                    <h4 class="font-bold text-xs text-white">Alat Lengkap & Steril</h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">Clipper tanpa kabel, kain penutup, cermin & pomade.</p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-800/90 border border-slate-700">
                    <i class="fas fa-user-tie text-emerald-400 text-lg mb-2"></i>
                    <h4 class="font-bold text-xs text-white">Bebas Pilih Capster</h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">Pilih barber senior favorit Anda sesuai jadwal.</p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-800/90 border border-slate-700">
                    <i class="fas fa-map-marked-alt text-emerald-400 text-lg mb-2"></i>
                    <h4 class="font-bold text-xs text-white">Area Kota Metro</h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">Ongkos transport transparan mulai Rp 1.000.</p>
                </div>
                <div class="p-4 rounded-2xl bg-slate-800/90 border border-slate-700">
                    <i class="fas fa-qrcode text-emerald-400 text-lg mb-2"></i>
                    <h4 class="font-bold text-xs text-white">QRIS / COD Tunai</h4>
                    <p class="text-[11px] text-slate-400 mt-0.5">Bayar via QRIS Instan atau tunai di lokasi.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Form Pemesanan Home Service -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    <form action="/home-service" method="POST" id="home-service-form">
        <input type="hidden" name="service_type" value="home_service">

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Kolom Kiri (8 Kolom): Langkah-Langkah Pemesanan Home Service -->
            <div class="lg:col-span-8 space-y-8">

                <!-- LANGKAH 1: Pilih Layanan Cukur & Perawatan -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
                    <div class="flex items-center justify-between pb-4 mb-6 border-b border-slate-100">
                        <h3 class="text-lg font-extrabold text-slate-900 flex items-center space-x-3">
                            <span class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm font-black shadow-sm">1</span>
                            <span>Pilih Layanan Pangkas / Perawatan</span>
                        </h3>
                        <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full">Bisa pilih lebih dari 1</span>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <?php foreach ($items as $index => $item): ?>
                        <?php $hsImg = \App\Models\Item::getImageUrl($item['image_path'] ?? null, $item['item_name'] ?? ''); ?>
                        <div class="hs-item-card relative rounded-2xl border-2 border-slate-200 p-4 transition hover:border-emerald-500 bg-white flex flex-col justify-between" data-id="<?= $item['id'] ?>">
                            <label class="cursor-pointer flex items-start space-x-3">
                                <input type="checkbox" name="items[]" value="<?= $item['id'] ?>" 
                                    data-price="<?= (int)$item['price'] ?>"
                                    data-name="<?= htmlspecialchars($item['item_name']) ?>"
                                    <?= $index === 0 ? 'checked' : '' ?>
                                    onchange="recalculateHomeService()"
                                    class="hs-item-checkbox mt-1 w-5 h-5 rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 flex-shrink-0">
                                <img src="<?= htmlspecialchars($hsImg) ?>" alt="<?= htmlspecialchars($item['item_name']) ?>" class="w-12 h-12 rounded-xl object-cover border border-slate-200 flex-shrink-0 shadow-sm">
                                <div class="flex-1">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 block">
                                        <?= htmlspecialchars($item['category_name']) ?>
                                    </span>
                                    <h4 class="font-bold text-slate-900 text-sm"><?= htmlspecialchars($item['item_name']) ?></h4>
                                    <p class="text-xs text-slate-500 mt-1 line-clamp-2"><?= htmlspecialchars($item['description']) ?></p>
                                </div>
                            </label>

                            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between">
                                <span class="font-extrabold text-emerald-600 text-sm">
                                    Rp <?= number_format($item['price'], 0, ',', '.') ?>
                                </span>
                                <div class="flex items-center space-x-1.5">
                                    <span class="text-[11px] text-slate-400 font-semibold mr-1">Jumlah Orang/Item:</span>
                                    <button type="button" onclick="changeHsQty(<?= $item['id'] ?>, -1)" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center">-</button>
                                    <input type="number" name="qty[<?= $item['id'] ?>]" id="hs-qty-<?= $item['id'] ?>" value="1" min="1" max="10" readonly
                                        class="w-9 text-center font-bold text-xs text-slate-900 bg-transparent border-none focus:ring-0 p-0">
                                    <button type="button" onclick="changeHsQty(<?= $item['id'] ?>, 1)" class="w-7 h-7 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-xs flex items-center justify-center">+</button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- LANGKAH 2: Pilih Capster / Barber & Jadwal Kunjungan -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
                    <div class="pb-4 mb-6 border-b border-slate-100">
                        <h3 class="text-lg font-extrabold text-slate-900 flex items-center space-x-3">
                            <span class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm font-black shadow-sm">2</span>
                            <span>Pilih Capster (Barber) & Jadwal Kunjungan</span>
                        </h3>
                    </div>

                    <!-- Pilihan Barber -->
                    <div class="mb-6">
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">
                            Pilih Barber / Capster yang Akan Datang *
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                            <!-- Opsi Bebas / Rekomendasi -->
                            <label class="cursor-pointer border-2 border-slate-200 rounded-2xl p-3.5 hover:border-emerald-500 transition flex flex-col justify-between bg-slate-50/60 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/40">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="w-9 h-9 rounded-xl bg-slate-800 text-emerald-400 flex items-center justify-center font-bold text-sm">
                                        <i class="fas fa-star"></i>
                                    </div>
                                    <input type="radio" name="barber_id" value="" checked class="text-emerald-600 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 text-xs">Rekomendasi Terbaik</h4>
                                    <p class="text-[11px] text-slate-500 mt-0.5">Dipilihkan capster tercepat yang siap berangkat</p>
                                </div>
                            </label>

                            <?php foreach ($barbers as $barber): ?>
                            <label class="cursor-pointer border-2 border-slate-200 rounded-2xl p-3.5 hover:border-emerald-500 transition flex flex-col justify-between bg-white has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/40">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center font-extrabold text-sm">
                                        <?= substr($barber['fullname'], 0, 1) ?>
                                    </div>
                                    <input type="radio" name="barber_id" value="<?= $barber['id'] ?>" class="text-emerald-600 focus:ring-emerald-500">
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 text-xs"><?= htmlspecialchars($barber['fullname']) ?></h4>
                                    <span class="inline-flex items-center text-[10px] font-semibold text-emerald-700 mt-0.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-1"></span> Siap Kunjungan
                                    </span>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Tanggal & Jam Kunjungan -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-4 border-t border-slate-100">
                        <div>
                            <label for="scheduled_date" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                <i class="fas fa-calendar-alt text-emerald-600 mr-1"></i> Tanggal Kunjungan *
                            </label>
                            <input type="date" id="scheduled_date" name="scheduled_date" required
                                min="<?= date('Y-m-d') ?>"
                                value="<?= date('Y-m-d') ?>"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-slate-900 text-sm font-semibold outline-none">
                            <span class="text-[11px] text-slate-400 mt-1 block">Bisa pesan untuk hari ini atau reservasi hari berikutnya.</span>
                        </div>

                        <div>
                            <label for="scheduled_time" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                                <i class="fas fa-clock text-emerald-600 mr-1"></i> Pilih Jam Kedatangan Barber *
                            </label>
                            <select id="scheduled_time" name="scheduled_time" required
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-slate-900 text-sm font-semibold outline-none bg-white">
                                <option value="09:00">Pagi — 09:00 WIB</option>
                                <option value="10:30">Pagi — 10:30 WIB</option>
                                <option value="13:00">Siang — 13:00 WIB</option>
                                <option value="14:30" selected>Siang — 14:30 WIB</option>
                                <option value="16:00">Sore — 16:00 WIB</option>
                                <option value="18:30">Malam — 18:30 WIB</option>
                                <option value="20:00">Malam — 20:00 WIB</option>
                            </select>
                            <span class="text-[11px] text-slate-400 mt-1 block">Barber akan tiba 5-10 menit sebelum jadwal yang dipilih.</span>
                        </div>
                    </div>
                </div>

                <!-- LANGKAH 3: Alamat Lengkap & Zona Area Kunjungan -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
                    <div class="pb-4 mb-6 border-b border-slate-100">
                        <h3 class="text-lg font-extrabold text-slate-900 flex items-center space-x-3">
                            <span class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm font-black shadow-sm">3</span>
                            <span>Lokasi & Alamat Kunjungan Pelanggan</span>
                        </h3>
                    </div>

                    <div class="space-y-5">
                        <!-- Pilihan Zona Area (Biaya Transport) -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2.5">
                                Pilih Zona Wilayah Kunjungan (Biaya Transportasi Barber) *
                            </label>
                            <div class="grid grid-cols-1 gap-3">
                                <?php $zIndex = 0; foreach ($areaZones as $zoneKey => $zoneInfo): ?>
                                <label class="cursor-pointer border-2 border-slate-200 rounded-2xl p-4 hover:border-emerald-500 transition flex items-center justify-between bg-white has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/30">
                                    <div class="flex items-center space-x-3">
                                        <input type="radio" name="area_zone" value="<?= htmlspecialchars($zoneKey) ?>"
                                            data-fee="<?= $zoneInfo['fee'] ?>"
                                            <?= $zIndex === 0 ? 'checked' : '' ?>
                                            onchange="recalculateHomeService()"
                                            class="hs-zone-radio w-4 h-4 text-emerald-600 focus:ring-emerald-500">
                                        <div>
                                            <span class="font-bold text-slate-900 text-sm block"><?= htmlspecialchars($zoneInfo['label']) ?></span>
                                            <span class="text-xs text-slate-500"><i class="fas fa-route text-emerald-600 mr-1"></i> <?= htmlspecialchars($zoneInfo['distance']) ?> dari Wijaya Barbershop</span>
                                        </div>
                                    </div>
                                    <span class="font-extrabold text-emerald-700 bg-emerald-100 px-3 py-1 rounded-xl text-xs whitespace-nowrap ml-3">
                                        + Rp <?= number_format($zoneInfo['fee'], 0, ',', '.') ?>
                                    </span>
                                </label>
                                <?php $zIndex++; endforeach; ?>
                            </div>
                        </div>

                        <!-- Alamat Lengkap + Tombol Geolocation GPS -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="customer_address" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                                    Alamat Lengkap Lokasi Cukur *
                                </label>
                                <button type="button" onclick="detectMyLocation()" id="btn-gps"
                                    class="text-xs font-bold text-emerald-600 hover:text-emerald-700 flex items-center space-x-1 bg-emerald-50 hover:bg-emerald-100 px-3 py-1 rounded-lg transition">
                                    <i class="fas fa-location-crosshairs"></i>
                                    <span>Gunakan Titik GPS Saya</span>
                                </button>
                            </div>
                            <textarea id="customer_address" name="customer_address" rows="3" required
                                placeholder="Contoh: Jl. Jenderal Sudirman No. 18, Kel. Imopuro, Kec. Metro Pusat, Kota Metro"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-slate-900 text-sm transition outline-none"></textarea>
                            <p id="gps-status" class="text-[11px] text-emerald-600 font-medium mt-1 hidden"></p>
                        </div>

                        <!-- Patokan Lokasi / Detail Tambahan -->
                        <div>
                            <label for="landmark" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Patokan Rumah / Detail Lokasi (Opsional)
                            </label>
                            <input type="text" id="landmark" name="landmark"
                                placeholder="Contoh: Rumah cat putih pagar hitam, depan Masjid Nurul Iman / Kos Putra Lantai 2"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-slate-900 text-sm transition outline-none">
                        </div>
                    </div>
                </div>

                <!-- LANGKAH 4: Data Pemesan & Metode Pembayaran -->
                <div class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8">
                    <div class="pb-4 mb-6 border-b border-slate-100">
                        <h3 class="text-lg font-extrabold text-slate-900 flex items-center space-x-3">
                            <span class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center text-sm font-black shadow-sm">4</span>
                            <span>Data Kontak Pemesan & Metode Pembayaran</span>
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                        <div>
                            <label for="fullname" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Nama Lengkap Pelanggan *
                            </label>
                            <input type="text" id="fullname" name="fullname" required
                                value="<?= htmlspecialchars($user['fullname'] ?? '') ?>"
                                placeholder="Masukkan nama lengkap Anda"
                                class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-slate-900 text-sm transition outline-none">
                        </div>

                        <div>
                            <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                                Nomor WhatsApp Aktif *
                            </label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-emerald-600">
                                    <i class="fab fa-whatsapp text-base"></i>
                                </span>
                                <input type="tel" id="phone" name="phone" required
                                    value="" autocomplete="one-time-code" readonly onfocus="this.removeAttribute('readonly');"
                                    placeholder="Ketik nomor WhatsApp aktif (contoh: 0812xxxxxxxx)"
                                    class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-slate-900 text-sm transition outline-none bg-white">
                            </div>
                            <span class="text-[11px] text-slate-400 mt-1 block">Capster akan menghubungi WhatsApp ini saat menuju lokasi.</span>
                        </div>
                    </div>

                    <div class="mb-6">
                        <label for="notes" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
                            Catatan Model Rambut / Permintaan Khusus (Opsional)
                        </label>
                        <input type="text" id="notes" name="notes"
                            placeholder="Contoh: Model taper fade rapi, bawa cermin lipat & hair tonic..."
                            class="w-full px-4 py-3 rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-slate-900 text-sm transition outline-none">
                    </div>

                    <!-- Pilihan Metode Pembayaran -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-3">
                            Pilih Metode Pembayaran *
                        </label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="relative flex flex-col p-5 border-2 rounded-2xl cursor-pointer hover:border-emerald-500 transition border-slate-200 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/30">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-extrabold text-slate-900 text-sm flex items-center space-x-2">
                                        <i class="fas fa-qrcode text-emerald-600 text-lg"></i>
                                        <span>QRIS</span>
                                    </span>
                                    <input type="radio" name="payment_method" value="qris" checked class="text-emerald-600 focus:ring-emerald-500">
                                </div>
                                <p class="text-xs text-slate-500">
                                    Bayar praktis scan QR Code Nasional via GoPay, OVO, Dana, ShopeePay, BCA, Mandiri, BRI, BNI.
                                </p>
                            </label>

                            <label class="relative flex flex-col p-5 border-2 rounded-2xl cursor-pointer hover:border-emerald-500 transition border-slate-200 has-[:checked]:border-emerald-600 has-[:checked]:bg-emerald-50/30">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="font-extrabold text-slate-900 text-sm flex items-center space-x-2">
                                        <i class="fas fa-hand-holding-dollar text-amber-600 text-lg"></i>
                                        <span>Tunai di Lokasi (COD)</span>
                                    </span>
                                    <input type="radio" name="payment_method" value="tunai" class="text-emerald-600 focus:ring-emerald-500">
                                </div>
                                <p class="text-xs text-slate-500">
                                    Bayar uang tunai langsung kepada Capster Wijaya Barbershop setelah selesai pangkas rambut di rumah Anda.
                                </p>
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan (4 Kolom): Ringkasan Biaya Home Service with Laser Border Beam -->
            <div class="lg:col-span-4 border-beam-card tilt-card-luxury p-6 sm:p-8 sticky top-28 text-white shadow-2xl overflow-hidden">
                <div class="flex items-center justify-between pb-4 mb-4 border-b border-white/10 relative z-10">
                    <div>
                        <span class="text-[10px] font-extrabold uppercase tracking-widest text-emerald-400 block">Ringkasan Booking</span>
                        <h3 class="text-lg font-black text-white">Cukur Panggilan</h3>
                    </div>
                    <span class="w-10 h-10 rounded-2xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-lg shadow-sm">
                        <i class="fas fa-house-user"></i>
                    </span>
                </div>

                <!-- Daftar Item Terpilih (Live Update via JS) -->
                <div id="hs-summary-items" class="divide-y divide-white/10 mb-4 max-h-52 overflow-y-auto pr-1 text-xs relative z-10">
                    <!-- Diisi otomatis oleh JavaScript -->
                </div>

                <!-- Bagian Kupon Promo & Diskon Voucher -->
                <div class="mb-4 p-4 rounded-2xl bg-white/5 border border-white/10 relative z-10">
                    <label class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-2 flex items-center justify-between">
                        <span class="flex items-center space-x-1.5">
                            <i class="fas fa-ticket-alt text-emerald-400"></i>
                            <span>Punya Kode Voucher Promo?</span>
                        </span>
                        <a href="/promotions" target="_blank" class="text-[10px] text-emerald-400 hover:underline">Lihat Katalog &rarr;</a>
                    </label>
                    <div class="flex items-center space-x-2">
                        <input type="text" id="hs_voucher_input" name="voucher_code" placeholder="Ketik kode kupon..." class="flex-1 px-3 py-2 rounded-xl bg-slate-900 border border-slate-700 focus:border-emerald-500 font-mono text-xs uppercase tracking-wider text-white outline-none">
                        <button type="button" onclick="applyHsVoucher()" id="btn-apply-hs-voucher" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-extrabold text-xs uppercase tracking-wider transition shadow-md shadow-emerald-600/30 flex-shrink-0">
                            Terapkan
                        </button>
                    </div>
                    <div id="hs-voucher-feedback" class="text-[11px] mt-2 hidden font-semibold"></div>

                    <!-- Quick Voucher Chips -->
                    <?php if (!empty($vouchers)): ?>
                    <div class="mt-2.5 pt-2 border-t border-white/10">
                        <span class="text-[10px] text-slate-400 block mb-1 uppercase font-bold">Kupon Tersedia:</span>
                        <div class="flex flex-wrap gap-1">
                            <?php foreach (array_slice($vouchers, 0, 3) as $vc): ?>
                            <button type="button" onclick="selectHsVoucherChip('<?= htmlspecialchars($vc['code']) ?>')" class="px-2 py-0.5 rounded-lg bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 text-[9px] font-mono font-bold transition">
                                <?= htmlspecialchars($vc['code']) ?>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Bagian Diskon Member VIP & Penukaran Poin -->
                <?php if ($loyalty): ?>
                <div class="mb-4 p-3.5 rounded-2xl bg-gradient-to-r from-amber-950/40 to-slate-900 border border-amber-500/30 text-white relative z-10 space-y-2.5">
                    <div class="flex items-center justify-between pb-2 border-b border-white/10">
                        <span class="text-xs font-bold text-amber-300 flex items-center space-x-1.5">
                            <i class="fas fa-crown text-amber-400"></i>
                            <span><?= htmlspecialchars($loyalty['benefits']['name']) ?></span>
                        </span>
                        <?php if (!empty($loyalty['benefits']['discount_percent'])): ?>
                        <span class="text-[10px] font-black text-emerald-400 bg-emerald-500/10 border border-emerald-500/20 px-2 py-0.5 rounded-lg">
                            Diskon <?= $loyalty['benefits']['discount_percent'] ?>%
                        </span>
                        <?php endif; ?>
                    </div>

                    <?php if ($loyalty['points'] > 0): ?>
                    <?php 
                        $canRedeemPoints = min($loyalty['points'], 500);
                        $canRedeemRupiah = $canRedeemPoints * 50;
                    ?>
                    <div class="flex items-start justify-between">
                        <div>
                            <label class="flex items-center space-x-2 cursor-pointer">
                                <input type="checkbox" id="hs_use_points_toggle" onchange="toggleHsPointsRedeem()" class="w-4 h-4 rounded text-amber-500 focus:ring-amber-400 bg-slate-800 border-slate-700">
                                <span class="text-xs font-bold text-slate-200">Gunakan Poin Loyalitas</span>
                            </label>
                            <span class="text-[10px] text-slate-400 block ml-6">
                                <?= number_format($loyalty['points']) ?> Poin (Maks. Rp <?= number_format($canRedeemRupiah, 0, ',', '.') ?>)
                            </span>
                        </div>
                        <input type="hidden" id="hs_redeem_points" name="redeem_points" value="0" data-max="<?= $canRedeemPoints ?>">
                        <span id="hs-points-discount-badge" class="text-xs font-black text-amber-400 hidden">-Rp <?= number_format($canRedeemRupiah, 0, ',', '.') ?></span>
                    </div>
                    <?php endif; ?>
                </div>
                <?php endif; ?>

                <!-- Rincian Perhitungan Biaya -->
                <div class="space-y-2 text-xs sm:text-sm text-slate-300 pt-3 border-t border-white/10 mb-5 relative z-10">
                    <div class="flex justify-between">
                        <span>Subtotal Layanan:</span>
                        <span id="hs-subtotal" class="font-bold text-white">Rp 0</span>
                    </div>

                    <!-- Row Diskon Member -->
                    <div id="row-hs-member-discount" class="flex justify-between text-emerald-400 <?= empty($loyalty['benefits']['discount_percent']) ? 'hidden' : '' ?>">
                        <span><i class="fas fa-crown text-amber-400 mr-1"></i> Diskon Member (<?= $loyalty['benefits']['discount_percent'] ?? 0 ?>%):</span>
                        <span class="font-bold" id="val-hs-member-discount">-Rp 0</span>
                    </div>

                    <!-- Row Diskon Voucher -->
                    <div id="row-hs-voucher-discount" class="flex justify-between text-emerald-400 hidden">
                        <span><i class="fas fa-ticket mr-1"></i> Diskon Kupon (<span id="label-hs-voucher-code">-</span>):</span>
                        <span class="font-bold" id="val-hs-voucher-discount">-Rp 0</span>
                    </div>

                    <!-- Row Diskon Poin -->
                    <div id="row-hs-points-discount" class="flex justify-between text-amber-300 hidden">
                        <span><i class="fas fa-coins mr-1"></i> Potongan Poin:</span>
                        <span class="font-bold" id="val-hs-points-discount">-Rp 0</span>
                    </div>

                    <div class="flex justify-between">
                        <span>Pajak PPN (10%):</span>
                        <span id="hs-tax" class="font-bold text-white">Rp 0</span>
                    </div>

                    <div class="flex justify-between text-emerald-300 bg-emerald-950/60 border border-emerald-500/30 px-3 py-1.5 rounded-xl">
                        <span class="font-semibold flex items-center space-x-1.5">
                            <i class="fas fa-motorcycle text-xs"></i>
                            <span>Biaya Transport Area:</span>
                        </span>
                        <span id="hs-delivery" class="font-black">Rp 1.000</span>
                    </div>

                    <div class="flex justify-between items-center pt-2.5 border-t border-white/10">
                        <span class="text-base font-extrabold text-white">Total Biaya:</span>
                        <span id="hs-grandtotal" class="text-2xl font-black text-emerald-400">Rp 0</span>
                    </div>

                    <div class="flex items-center justify-between text-[11px] text-amber-300/90 pt-1">
                        <span><i class="fas fa-star mr-1 text-amber-400"></i> Estimasi Poin Didapat:</span>
                        <span class="font-bold" id="hs-points-earned">+0 Poin VIP</span>
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-4 bg-gradient-to-r from-emerald-600 via-emerald-500 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-black rounded-2xl transition duration-300 shadow-xl shadow-emerald-600/40 text-center text-sm uppercase tracking-wider flex items-center justify-center space-x-2 shimmer-btn btn-magnetic relative z-10">
                    <i class="fas fa-calendar-check"></i>
                    <span>PESAN CUKUR PANGGILAN</span>
                </button>

                <div class="mt-4 p-3.5 rounded-2xl bg-slate-950/70 border border-white/10 text-[11px] text-slate-400 leading-relaxed relative z-10">
                    <i class="fas fa-shield-halved text-emerald-400 mr-1"></i>
                    <strong>Garansi Tepat Waktu:</strong> Capster kami akan mengonfirmasi pesanan via WhatsApp sebelum berangkat menuju alamat Anda.
                </div>
            </div>
        </div>
    </form>
</div>

<script>
const memberDiscountPercent = <?= (int) ($loyalty['benefits']['discount_percent'] ?? 0) ?>;
const pointMultiplier = <?= (float) ($loyalty['benefits']['point_multiplier'] ?? 1.0) ?>;

let currentHsVoucherDiscount = 0;
let currentHsPointsDiscount = 0;
let currentHsSubtotal = 0;

function formatRupiah(num) {
    return 'Rp ' + Math.max(0, num).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
}

function selectHsVoucherChip(code) {
    document.getElementById('hs_voucher_input').value = code;
    applyHsVoucher();
}

function applyHsVoucher() {
    const code = document.getElementById('hs_voucher_input').value.trim();
    const fb = document.getElementById('hs-voucher-feedback');

    if (!code) {
        fb.textContent = 'Silakan ketikkan kode kupon.';
        fb.className = 'text-[11px] mt-2 font-semibold text-rose-400 block';
        currentHsVoucherDiscount = 0;
        document.getElementById('row-hs-voucher-discount').classList.add('hidden');
        recalculateHomeService();
        return;
    }

    fetch('/api/vouchers/apply', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'code=' + encodeURIComponent(code) + '&subtotal=' + currentHsSubtotal + '&service_type=home_service'
    })
    .then(r => r.json())
    .then(data => {
        if (data.status === 'success') {
            currentHsVoucherDiscount = data.voucher.discount;
            fb.textContent = '✓ ' + data.message;
            fb.className = 'text-[11px] mt-2 font-semibold text-emerald-400 block';

            document.getElementById('row-hs-voucher-discount').classList.remove('hidden');
            document.getElementById('label-hs-voucher-code').textContent = data.voucher.code;
            document.getElementById('val-hs-voucher-discount').textContent = '-Rp ' + currentHsVoucherDiscount.toLocaleString('id-ID');
        } else {
            currentHsVoucherDiscount = 0;
            fb.textContent = '✗ ' + data.message;
            fb.className = 'text-[11px] mt-2 font-semibold text-rose-400 block';
            document.getElementById('row-hs-voucher-discount').classList.add('hidden');
        }
        recalculateHomeService();
    })
    .catch(() => {
        fb.textContent = 'Gagal memverifikasi voucher.';
        fb.className = 'text-[11px] mt-2 font-semibold text-rose-400 block';
    });
}

function toggleHsPointsRedeem() {
    const cb = document.getElementById('hs_use_points_toggle');
    const input = document.getElementById('hs_redeem_points');
    const badge = document.getElementById('hs-points-discount-badge');
    const row = document.getElementById('row-hs-points-discount');

    if (cb && cb.checked) {
        const maxPts = parseInt(input.getAttribute('data-max') || '0', 10);
        input.value = maxPts;
        currentHsPointsDiscount = maxPts * 50;
        badge.classList.remove('hidden');
        row.classList.remove('hidden');
        document.getElementById('val-hs-points-discount').textContent = '-Rp ' + currentHsPointsDiscount.toLocaleString('id-ID');
    } else {
        if (input) input.value = 0;
        currentHsPointsDiscount = 0;
        if (badge) badge.classList.add('hidden');
        if (row) row.classList.add('hidden');
    }
    recalculateHomeService();
}

function changeHsQty(itemId, delta) {
    const input = document.getElementById('hs-qty-' + itemId);
    if (!input) return;
    let val = parseInt(input.value || '1', 10) + delta;
    if (val < 1) val = 1;
    if (val > 10) val = 10;
    input.value = val;

    // Pastikan checkbox ikut tercentang jika menambah qty
    const card = document.querySelector('.hs-item-card[data-id="' + itemId + '"]');
    if (card && delta > 0) {
        const cb = card.querySelector('.hs-item-checkbox');
        if (cb && !cb.checked) cb.checked = true;
    }
    recalculateHomeService();
}

function recalculateHomeService() {
    const checkboxes = document.querySelectorAll('.hs-item-checkbox');
    const summaryContainer = document.getElementById('hs-summary-items');
    let subtotal = 0;
    let html = '';

    checkboxes.forEach(cb => {
        const card = cb.closest('.hs-item-card');
        const id = cb.value;
        const qtyInput = document.getElementById('hs-qty-' + id);
        const qty = parseInt(qtyInput ? qtyInput.value : '1', 10);
        const price = parseInt(cb.getAttribute('data-price') || '0', 10);
        const name = cb.getAttribute('data-name') || 'Layanan';

        if (cb.checked) {
            card.classList.add('border-emerald-500', 'bg-emerald-50/20');
            card.classList.remove('border-slate-200');
            const itemTotal = price * qty;
            subtotal += itemTotal;
            html += `<div class="py-2.5 flex items-center justify-between">
                <div>
                    <span class="font-bold text-slate-800 block">${name}</span>
                    <span class="text-[11px] text-slate-400">${qty}x @ ${formatRupiah(price)}</span>
                </div>
                <span class="font-bold text-slate-900">${formatRupiah(itemTotal)}</span>
            </div>`;
        } else {
            card.classList.remove('border-emerald-500', 'bg-emerald-50/20');
            card.classList.add('border-slate-200');
        }
    });

    if (html === '') {
        html = '<div class="py-4 text-center text-slate-400 italic">Belum ada layanan dipilih</div>';
    }
    summaryContainer.innerHTML = html;
    currentHsSubtotal = subtotal;

    // 1. Diskon Member
    let memberDiscount = 0;
    if (memberDiscountPercent > 0 && subtotal > 0) {
        memberDiscount = Math.round((subtotal * memberDiscountPercent) / 100);
        const rowMem = document.getElementById('row-hs-member-discount');
        if (rowMem) {
            rowMem.classList.remove('hidden');
            document.getElementById('val-hs-member-discount').textContent = '-Rp ' + memberDiscount.toLocaleString('id-ID');
        }
    }

    // 2. Diskon Total
    const totalDiscount = Math.min(subtotal, memberDiscount + currentHsVoucherDiscount + currentHsPointsDiscount);

    // 3. Pajak
    const taxableAmount = Math.max(0, subtotal - totalDiscount);
    const tax = Math.round(taxableAmount * 0.10);

    // 4. Biaya Transport
    const selectedZone = document.querySelector('.hs-zone-radio:checked');
    const deliveryFee = selectedZone ? parseInt(selectedZone.getAttribute('data-fee') || '1000', 10) : 1000;

    // 5. Grand Total
    const grandTotal = subtotal > 0 ? (taxableAmount + tax + deliveryFee) : 0;

    // 6. Estimasi Poin
    const pointsEarned = grandTotal > 0 ? Math.round(Math.floor(grandTotal / 1000) * pointMultiplier) : 0;

    document.getElementById('hs-subtotal').textContent = formatRupiah(subtotal);
    document.getElementById('hs-tax').textContent = formatRupiah(tax);
    document.getElementById('hs-delivery').textContent = formatRupiah(deliveryFee);
    document.getElementById('hs-grandtotal').textContent = formatRupiah(grandTotal);
    document.getElementById('hs-points-earned').textContent = '+' + pointsEarned.toLocaleString('id-ID') + ' Poin VIP';
}

function detectMyLocation() {
    const statusEl = document.getElementById('gps-status');
    const addrInput = document.getElementById('customer_address');
    statusEl.classList.remove('hidden');

    if (!navigator.geolocation) {
        statusEl.textContent = 'Browser Anda tidak mendukung fitur GPS otomatis. Silakan ketik alamat secara manual.';
        return;
    }

    statusEl.textContent = 'Mendeteksi titik koordinat lokasi Anda...';
    navigator.geolocation.getCurrentPosition(
        function(pos) {
            const lat = pos.coords.latitude.toFixed(6);
            const lng = pos.coords.longitude.toFixed(6);
            const mapsUrl = `https://www.google.com/maps?q=${lat},${lng}`;
            const currentVal = addrInput.value.trim();
            if (!currentVal.includes('google.com/maps')) {
                addrInput.value = (currentVal ? currentVal + "\n" : "") + `[Titik GPS: ${mapsUrl}] `;
            }
            statusEl.innerHTML = '<i class="fas fa-check-circle mr-1"></i> Titik koordinat Google Maps berhasil ditambahkan! Silakan lengkapi nama jalan & nomor rumah.';
            addrInput.focus();
        },
        function() {
            statusEl.textContent = 'Tidak dapat mengambil GPS otomatis. Silakan ketik alamat lengkap Anda di kolom bawah.';
        }
    );
}

document.addEventListener('DOMContentLoaded', recalculateHomeService);
</script>

