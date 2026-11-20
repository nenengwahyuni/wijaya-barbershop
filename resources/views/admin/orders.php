<div class="space-y-6">
    <!-- Header Page & Filter Bar (Dark Frosted Glass Bar) -->
    <div class="bg-slate-900/80 backdrop-blur-xl rounded-3xl p-6 border border-white/10 shadow-xl shadow-black/25 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center space-x-2.5 mb-1">
                <span class="w-2.5 h-6 rounded-full barber-pole border border-slate-700 shadow-sm"></span>
                <h1 class="text-2xl font-black text-white tracking-tight">Data Order & Antrean</h1>
            </div>
            <p class="text-xs text-slate-400">Daftar pesanan studio barbershop maupun Cukur Panggilan (Home Service) Wijaya Barbershop.</p>
        </div>

        <!-- Filter Tipe Layanan & Status Buttons -->
        <div class="flex flex-wrap items-center gap-2">
            <!-- Filter Tipe Layanan -->
            <a href="/admin/orders<?= $selectedStatus ? '?status=' . urlencode($selectedStatus) : '' ?>" 
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition <?= empty($selectedServiceType) ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'bg-white/10 text-slate-300 hover:bg-white/20' ?>">
                Semua Tipe
            </a>
            <a href="/admin/orders?service_type=home_service<?= $selectedStatus ? '&status=' . urlencode($selectedStatus) : '' ?>" 
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 <?= ($selectedServiceType ?? '') === 'home_service' ? 'bg-amber-500 text-slate-950 font-black shadow-lg shadow-amber-500/30' : 'bg-amber-500/20 text-amber-300 hover:bg-amber-500/30 border border-amber-500/30' ?>">
                <i class="fas fa-motorcycle"></i>
                <span>Home Service</span>
            </a>
            <a href="/admin/orders?service_type=barbershop<?= $selectedStatus ? '&status=' . urlencode($selectedStatus) : '' ?>" 
                class="px-3.5 py-1.5 rounded-xl text-xs font-bold transition flex items-center space-x-1.5 <?= ($selectedServiceType ?? '') === 'barbershop' ? 'bg-emerald-600 text-white shadow-lg shadow-emerald-600/30' : 'bg-white/10 text-slate-300 hover:bg-white/20' ?>">
                <i class="fas fa-store"></i>
                <span>Studio Barbershop</span>
            </a>

            <span class="text-slate-600 mx-1">|</span>

            <!-- Filter Status -->
            <a href="/admin/orders<?= !empty($selectedServiceType) ? '?service_type=' . urlencode($selectedServiceType) : '' ?>" 
                class="px-3 py-1.5 rounded-xl text-xs font-bold transition <?= empty($selectedStatus) ? 'bg-white/20 text-white' : 'bg-white/10 text-slate-400 hover:bg-white/20 hover:text-white' ?>">
                Semua
            </a>
            <a href="/admin/orders?status=pending<?= !empty($selectedServiceType) ? '&service_type=' . urlencode($selectedServiceType) : '' ?>" 
                class="px-3 py-1.5 rounded-xl text-xs font-bold transition <?= ($selectedStatus === 'pending') ? 'bg-amber-500 text-slate-950 font-black' : 'bg-white/10 text-slate-400 hover:bg-white/20' ?>">
                Pending
            </a>
            <a href="/admin/orders?status=settlement<?= !empty($selectedServiceType) ? '&service_type=' . urlencode($selectedServiceType) : '' ?>" 
                class="px-3 py-1.5 rounded-xl text-xs font-bold transition <?= ($selectedStatus === 'settlement') ? 'bg-emerald-600 text-white font-black' : 'bg-white/10 text-slate-400 hover:bg-white/20' ?>">
                Settlement
            </a>
            <a href="/admin/orders?status=completed<?= !empty($selectedServiceType) ? '&service_type=' . urlencode($selectedServiceType) : '' ?>" 
                class="px-3 py-1.5 rounded-xl text-xs font-bold transition <?= ($selectedStatus === 'completed') ? 'bg-blue-600 text-white font-black' : 'bg-white/10 text-slate-400 hover:bg-white/20' ?>">
                Completed
            </a>
        </div>
    </div>

    <!-- Toggle Mode Tampilan Khusus HP (md:hidden — Tidak muncul di Laptop!) -->
    <div class="md:hidden flex items-center justify-between bg-slate-900/90 border border-white/10 rounded-2xl px-3.5 py-2.5 text-xs">
        <span class="text-slate-300 font-bold flex items-center space-x-1.5">
            <i class="fas fa-mobile-alt text-emerald-400"></i>
            <span>Mode Tampilan HP:</span>
        </span>
        <div class="flex items-center space-x-1.5">
            <button type="button" id="btn-order-view-card" onclick="switchMobileOrderView('card')" class="px-3 py-1.5 rounded-xl font-extrabold bg-emerald-500 text-slate-950 shadow-sm transition">
                Kartu Lengkap
            </button>
            <button type="button" id="btn-order-view-table" onclick="switchMobileOrderView('table')" class="px-3 py-1.5 rounded-xl font-bold bg-white/10 text-slate-300 transition">
                Tabel Geser
            </button>
        </div>
    </div>

    <!-- TAMPILAN KARTU LENGKAP KHUSUS HP (md:hidden — Menampilkan SEMUA 12 kolom informasi tanpa terpotong!) -->
    <div id="orders-mobile-cards-wrap" class="md:hidden space-y-3.5">
        <?php if (empty($orders)): ?>
        <div class="bg-slate-900/80 rounded-3xl border border-white/10 p-8 text-center text-slate-400 text-xs">
            Tidak ada data pesanan ditemukan.
        </div>
        <?php endif; ?>

        <?php $mNo = 1; foreach ($orders as $order): ?>
        <?php
            $isHs = ($order['service_type'] ?? 'barbershop') === 'home_service';
            $waQuickMobile = \App\Services\WhatsAppService::createWaLink(
                $order['customer_phone'] ?? '',
                \App\Services\WhatsAppService::buildNewOrderMessage($order)
            );
        ?>
        <div class="bg-slate-900/90 backdrop-blur-xl rounded-2xl border <?= $isHs ? 'border-amber-500/30' : 'border-white/10' ?> p-4 shadow-lg text-xs text-slate-200 space-y-3">
            <!-- Baris 1: No, Kode Order, Antrean, Status & Metode -->
            <div class="flex items-start justify-between gap-2 pb-2.5 border-b border-white/10">
                <div>
                    <div class="flex items-center space-x-2">
                        <span class="px-2 py-0.5 rounded-md bg-white/10 text-slate-300 font-mono font-bold text-[10px]">#<?= $mNo++ ?></span>
                        <a href="/admin/orders/<?= $order['id'] ?>" class="font-mono font-black text-white text-sm hover:text-emerald-400">
                            <?= htmlspecialchars($order['order_code']) ?>
                        </a>
                    </div>
                    <span class="inline-block mt-1 text-[11px] text-emerald-400 font-extrabold">
                        Antrean #<?= str_pad($order['queue_number'], 2, '0', STR_PAD_LEFT) ?> &bull; <?= date('d-m-Y', strtotime($order['created_at'])) ?>
                    </span>
                </div>
                <div class="flex flex-col items-end space-y-1">
                    <?php if ($order['status'] === 'settlement'): ?>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Settlement</span>
                    <?php elseif ($order['status'] === 'completed'): ?>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-blue-500/20 text-blue-300 border border-blue-500/30">Completed</span>
                    <?php elseif ($order['status'] === 'pending'): ?>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30">Pending</span>
                    <?php else: ?>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase bg-rose-500/20 text-rose-300 border border-rose-500/30">Cancelled</span>
                    <?php endif; ?>

                    <span class="px-2 py-0.5 rounded font-mono font-bold text-[10px] uppercase <?= ($order['payment_method'] === 'qris') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'bg-white/10 text-slate-300 border border-white/10' ?>">
                        <?= htmlspecialchars($order['payment_method']) ?>
                    </span>
                </div>
            </div>

            <!-- Baris 2: Info Customer, Kontak, Tipe Layanan & Barber -->
            <div class="grid grid-cols-2 gap-3">
                <div class="bg-slate-950/60 p-2.5 rounded-xl border border-white/5">
                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Customer & Kontak</span>
                    <strong class="text-white font-bold block mt-0.5 truncate"><?= htmlspecialchars($order['customer_name'] ?? 'Pelanggan') ?></strong>
                    <span class="font-mono text-[11px] text-emerald-400 block mt-0.5">
                        <i class="fab fa-whatsapp mr-1"></i><?= htmlspecialchars($order['customer_phone'] ?? '-') ?>
                    </span>
                </div>
                <div class="bg-slate-950/60 p-2.5 rounded-xl border border-white/5">
                    <span class="text-[10px] text-slate-400 uppercase font-bold block">Tipe Layanan & Barber</span>
                    <?php if ($isHs): ?>
                    <span class="inline-flex items-center space-x-1 text-[11px] font-black text-amber-300 mt-0.5">
                        <i class="fas fa-motorcycle"></i>
                        <span>Home Service</span>
                    </span>
                    <?php else: ?>
                    <span class="inline-flex items-center space-x-1 text-[11px] font-bold text-slate-200 mt-0.5">
                        <i class="fas fa-store text-emerald-400"></i>
                        <span>Studio Barbershop</span>
                    </span>
                    <?php endif; ?>
                    <span class="block text-[11px] text-slate-300 mt-0.5 truncate">
                        Barber: <strong><?= htmlspecialchars($order['barber_name'] ?? 'Belum dipilih') ?></strong>
                    </span>
                </div>
            </div>

            <?php if ($isHs && !empty($order['customer_address'])): ?>
            <div class="bg-amber-500/10 border border-amber-500/25 rounded-xl p-2.5 text-[11px] text-amber-200">
                <i class="fas fa-map-pin text-amber-400 mr-1"></i>
                <strong>Alamat Home Service:</strong> <?= htmlspecialchars($order['customer_address']) ?>
            </div>
            <?php endif; ?>

            <!-- Baris 3: Rincian Biaya Lengkap (Subtotal, Pajak, Transport, Grand Total) -->
            <div class="bg-slate-950/80 rounded-xl p-3 border border-white/10 grid grid-cols-4 gap-2 text-center">
                <div>
                    <span class="text-[10px] text-slate-400 block">Subtotal</span>
                    <span class="font-mono font-bold text-slate-200 text-[11px]">Rp <?= number_format((int)$order['subtotal'], 0, ',', '.') ?></span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block">Pajak</span>
                    <span class="font-mono font-bold text-slate-300 text-[11px]">Rp <?= number_format((int)$order['tax'], 0, ',', '.') ?></span>
                </div>
                <div>
                    <span class="text-[10px] text-slate-400 block">Transport</span>
                    <span class="font-mono font-bold text-amber-300 text-[11px]">
                        <?= (!empty($order['delivery_fee']) && (int)$order['delivery_fee'] > 0) ? 'Rp ' . number_format((int)$order['delivery_fee'], 0, ',', '.') : '-' ?>
                    </span>
                </div>
                <div>
                    <span class="text-[10px] text-emerald-400 font-extrabold block">Grand Total</span>
                    <span class="font-mono font-black text-emerald-400 text-xs">Rp <?= number_format((int)$order['grand_total'], 0, ',', '.') ?></span>
                </div>
            </div>

            <!-- Baris 4: Tombol Aksi Lengkap -->
            <div class="flex flex-wrap items-center gap-2 pt-1">
                <a href="/admin/orders/<?= $order['id'] ?>" class="flex-1 py-2 px-3 bg-white/10 hover:bg-emerald-600 text-white rounded-xl font-bold text-center transition border border-white/10 flex items-center justify-center space-x-1.5">
                    <i class="fas fa-eye text-xs"></i>
                    <span>Detail</span>
                </a>

                <?php if ($order['status'] === 'pending'): ?>
                <form action="/admin/orders/status" method="POST" class="flex-1" onsubmit="if(window.playTransactionSuccessSound) window.playTransactionSuccessSound(); return confirm('Konfirmasi pembayaran telah lunas (Settlement) & kirim notifikasi WhatsApp?')">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <input type="hidden" name="status" value="settlement">
                    <button type="submit" class="w-full py-2 px-3 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-extrabold transition flex items-center justify-center space-x-1.5 shadow-sm">
                        <i class="fas fa-check-double text-xs"></i>
                        <span>Verifikasi Lunas</span>
                    </button>
                </form>
                <?php elseif ($order['status'] === 'settlement'): ?>
                <form action="/admin/orders/status" method="POST" class="flex-1" onsubmit="if(window.playTransactionSuccessSound) window.playTransactionSuccessSound(); return confirm('Tandai pesanan & pangkas rambut telah selesai & kirim ucapan via WhatsApp?')">
                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                    <input type="hidden" name="status" value="completed">
                    <button type="submit" class="w-full py-2 px-3 bg-blue-600 hover:bg-blue-500 text-white rounded-xl font-extrabold transition flex items-center justify-center space-x-1.5 shadow-sm">
                        <i class="fas fa-scissors text-xs"></i>
                        <span>Selesai Cukur</span>
                    </button>
                </form>
                <?php endif; ?>

                <a href="<?= $waQuickMobile ?>" target="_blank" class="py-2 px-3 bg-emerald-500/15 hover:bg-emerald-600 text-emerald-300 hover:text-white rounded-xl font-bold transition border border-emerald-500/30 flex items-center justify-center space-x-1">
                    <i class="fab fa-whatsapp text-sm"></i>
                    <span>WA</span>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Data Order Table (Dark Luxury Glassmorphic Table — Tampil Penuh di Laptop, atau saat pilih Tabel Geser di HP) -->
    <div id="orders-desktop-table-wrap" class="hidden md:block bg-slate-900/80 backdrop-blur-xl rounded-3xl border border-white/10 shadow-xl shadow-black/25 overflow-hidden">
        <div class="overflow-x-auto" style="-webkit-overflow-scrolling: touch;">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/70 text-slate-300 font-bold border-b border-white/10 uppercase tracking-wider">
                    <tr>
                        <th class="p-4 pl-6 text-center w-12">No</th>
                        <th class="p-4">Kode Order</th>
                        <th class="p-4">Tipe Layanan</th>
                        <th class="p-4">Customer & Kontak</th>
                        <th class="p-4 text-right">Subtotal</th>
                        <th class="p-4 text-right">Pajak</th>
                        <th class="p-4 text-right">Transport</th>
                        <th class="p-4 text-right">Grand Total</th>
                        <th class="p-4 text-center">Status</th>
                        <th class="p-4 text-center">Pembayaran</th>
                        <th class="p-4">Jadwal</th>
                        <th class="p-4 pr-6 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-slate-200">
                    <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="12" class="p-8 text-center text-slate-400">
                            Tidak ada data pesanan ditemukan.
                        </td>
                    </tr>
                    <?php endif; ?>

                    <?php $no = 1; foreach ($orders as $order): ?>
                    <?php $isHs = ($order['service_type'] ?? 'barbershop') === 'home_service'; ?>
                    <tr class="hover:bg-white/5 transition <?= $isHs ? 'bg-amber-500/5' : '' ?>">
                        <td class="p-4 pl-6 text-center font-bold text-slate-400"><?= $no++ ?></td>
                        <td class="p-4 font-mono font-bold text-white">
                            <a href="/admin/orders/<?= $order['id'] ?>" class="hover:text-emerald-400 transition">
                                <?= htmlspecialchars($order['order_code']) ?>
                            </a>
                            <span class="block text-[10px] text-emerald-400 font-bold font-sans">Antrean #<?= str_pad($order['queue_number'], 2, '0', STR_PAD_LEFT) ?></span>
                        </td>
                        <td class="p-4">
                            <?php if ($isHs): ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-md text-[10px] font-black bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                <i class="fas fa-motorcycle"></i>
                                <span>Home Service</span>
                            </span>
                            <span class="block text-[10px] text-slate-300 mt-1 font-medium">
                                Barber: <strong><?= htmlspecialchars($order['barber_name'] ?? 'Belum dipilih') ?></strong>
                            </span>
                            <?php else: ?>
                            <span class="inline-flex items-center space-x-1 px-2.5 py-0.5 rounded-md text-[10px] font-bold bg-white/10 text-slate-200 border border-white/10">
                                <i class="fas fa-store"></i>
                                <span>Studio</span>
                            </span>
                            <?php if (!empty($order['barber_name'])): ?>
                            <span class="block text-[10px] text-slate-400 mt-1"><?= htmlspecialchars($order['barber_name']) ?></span>
                            <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td class="p-4">
                            <span class="font-bold text-white block"><?= htmlspecialchars($order['customer_name'] ?? 'Pelanggan') ?></span>
                            <span class="font-mono text-[11px] text-slate-400 block"><?= htmlspecialchars($order['customer_phone'] ?? '-') ?></span>
                            <?php if ($isHs && !empty($order['customer_address'])): ?>
                            <span class="text-[10px] text-amber-300 block mt-0.5 max-w-[200px] truncate" title="<?= htmlspecialchars($order['customer_address']) ?>">
                                <i class="fas fa-map-pin mr-0.5"></i> <?= htmlspecialchars($order['customer_address']) ?>
                            </span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-right text-slate-300">
                            Rp <?= number_format((int)$order['subtotal'], 0, ',', '.') ?>
                        </td>
                        <td class="p-4 text-right text-slate-400">
                            Rp <?= number_format((int)$order['tax'], 0, ',', '.') ?>
                        </td>
                        <td class="p-4 text-right">
                            <?php if (!empty($order['delivery_fee']) && (int)$order['delivery_fee'] > 0): ?>
                            <span class="font-bold text-amber-300">Rp <?= number_format((int)$order['delivery_fee'], 0, ',', '.') ?></span>
                            <?php else: ?>
                            <span class="text-slate-500">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-right font-black text-white">
                            Rp <?= number_format((int)$order['grand_total'], 0, ',', '.') ?>
                        </td>
                        <!-- Status Badge Gambar 34 -->
                        <td class="p-4 text-center">
                            <?php if ($order['status'] === 'settlement'): ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 shadow-sm">
                                Settlement
                            </span>
                            <?php elseif ($order['status'] === 'completed'): ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-blue-500/20 text-blue-300 border border-blue-500/30 shadow-sm">
                                Completed
                            </span>
                            <?php elseif ($order['status'] === 'pending'): ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-amber-500/20 text-amber-300 border border-amber-500/30 shadow-sm">
                                Pending
                            </span>
                            <?php else: ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-rose-500/20 text-rose-300 border border-rose-500/30 shadow-sm">
                                Cancelled
                            </span>
                            <?php endif; ?>
                        </td>
                        <!-- Pembayaran (QRIS / TUNAI) -->
                        <td class="p-4 text-center">
                            <span class="px-2 py-0.5 rounded font-mono font-bold text-[10px] uppercase <?= ($order['payment_method'] === 'qris') ? 'bg-purple-500/20 text-purple-300 border border-purple-500/30' : 'bg-white/10 text-slate-300 border border-white/10' ?>">
                                <?= htmlspecialchars($order['payment_method']) ?>
                            </span>
                        </td>
                        <td class="p-4 whitespace-nowrap text-slate-400">
                            <?= date('d-m-Y', strtotime($order['created_at'])) ?>
                        </td>
                        <!-- Aksi Dropdown / Tombol Cepat -->
                        <td class="p-4 pr-6 text-center">
                            <div class="flex items-center justify-center space-x-1.5">
                                <a href="/admin/orders/<?= $order['id'] ?>" class="p-2 bg-white/10 hover:bg-emerald-600 text-white rounded-xl transition border border-white/10" title="Lihat Detail">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>

                                <!-- Update Status Modal / Form Form Cepat -->
                                <?php if ($order['status'] === 'pending'): ?>
                                <form action="/admin/orders/status" method="POST" class="inline" onsubmit="if(window.playTransactionSuccessSound) window.playTransactionSuccessSound(); return confirm('Konfirmasi pembayaran telah lunas (Settlement) & kirim notifikasi WhatsApp?')">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <input type="hidden" name="status" value="settlement">
                                    <button type="submit" class="p-2 bg-emerald-500/20 hover:bg-emerald-600 text-emerald-300 hover:text-white rounded-xl transition border border-emerald-500/30" title="Verifikasi Bayar Lunas & Kirim Notif WA">
                                        <i class="fas fa-check-double text-xs"></i>
                                    </button>
                                </form>
                                <?php elseif ($order['status'] === 'settlement'): ?>
                                <form action="/admin/orders/status" method="POST" class="inline" onsubmit="if(window.playTransactionSuccessSound) window.playTransactionSuccessSound(); return confirm('Tandai pesanan & pangkas rambut telah selesai & kirim ucapan via WhatsApp?')">
                                    <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                                    <input type="hidden" name="status" value="completed">
                                    <button type="submit" class="p-2 bg-blue-500/20 hover:bg-blue-600 text-blue-300 hover:text-white rounded-xl transition border border-blue-500/30" title="Tandai Selesai Dicukur">
                                        <i class="fas fa-scissors text-xs"></i>
                                    </button>
                                </form>
                                <?php endif; ?>

                                <?php 
                                $waQuick = \App\Services\WhatsAppService::createWaLink(
                                    $order['customer_phone'] ?? '', 
                                    \App\Services\WhatsAppService::buildNewOrderMessage($order)
                                );
                                ?>
                                <a href="<?= $waQuick ?>" target="_blank" class="p-2 bg-emerald-500/10 hover:bg-emerald-600 text-emerald-300 hover:text-white rounded-xl transition border border-emerald-500/20" title="Kirim Pesan WhatsApp">
                                    <i class="fab fa-whatsapp text-xs"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
    function switchMobileOrderView(mode) {
        const cardWrap = document.getElementById('orders-mobile-cards-wrap');
        const tableWrap = document.getElementById('orders-desktop-table-wrap');
        const btnCard = document.getElementById('btn-order-view-card');
        const btnTable = document.getElementById('btn-order-view-table');
        if (!cardWrap || !tableWrap) return;

        if (mode === 'table') {
            cardWrap.classList.add('hidden');
            tableWrap.classList.remove('hidden');
            if (btnTable) btnTable.className = 'px-3 py-1.5 rounded-xl font-extrabold bg-emerald-500 text-slate-950 shadow-sm transition';
            if (btnCard) btnCard.className = 'px-3 py-1.5 rounded-xl font-bold bg-white/10 text-slate-300 transition';
        } else {
            cardWrap.classList.remove('hidden');
            tableWrap.classList.add('hidden');
            if (btnCard) btnCard.className = 'px-3 py-1.5 rounded-xl font-extrabold bg-emerald-500 text-slate-950 shadow-sm transition';
            if (btnTable) btnTable.className = 'px-3 py-1.5 rounded-xl font-bold bg-white/10 text-slate-300 transition';
        }
    }
    </script>
</div>
