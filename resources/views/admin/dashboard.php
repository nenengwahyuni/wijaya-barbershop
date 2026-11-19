<div class="space-y-8">
    <!-- Stat Cards Header (HUD Laser Border Beam & 3D Specular Tilt) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Pendapatan -->
        <div class="border-beam-card hud-card tilt-card-luxury p-6 shadow-xl shadow-black/25 flex items-center justify-between group overflow-hidden">
            <span class="hud-corner hud-corner-tl"></span>
            <span class="hud-corner hud-corner-tr"></span>
            <span class="hud-corner hud-corner-bl"></span>
            <span class="hud-corner hud-corner-br"></span>
            <div class="relative z-10">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Total Pendapatan</span>
                <span class="text-2xl font-black text-white block tracking-tight">Rp <span data-counter="<?= $totalRevenue ?>"><?= number_format($totalRevenue, 0, ',', '.') ?></span></span>
                <span class="text-[11px] text-emerald-400 font-semibold mt-1.5 inline-flex items-center space-x-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 beacon-pulse"></span>
                    <span>Terverifikasi Lunas</span>
                </span>
            </div>
            <div class="relative z-10 w-14 h-14 rounded-2xl bg-emerald-500/15 border border-emerald-500/30 text-emerald-400 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-emerald-500 group-hover:text-white transition duration-300 shadow-lg shadow-emerald-500/20">
                <i class="fas fa-wallet"></i>
            </div>
        </div>

        <!-- Pesanan Hari Ini -->
        <div class="border-beam-card hud-card tilt-card-luxury p-6 shadow-xl shadow-black/25 flex items-center justify-between group overflow-hidden">
            <span class="hud-corner hud-corner-tl"></span>
            <span class="hud-corner hud-corner-tr"></span>
            <span class="hud-corner hud-corner-bl"></span>
            <span class="hud-corner hud-corner-br"></span>
            <div class="relative z-10">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Pesanan Hari Ini</span>
                <span class="text-2xl font-black text-white block tracking-tight"><span data-counter="<?= $ordersToday ?>"><?= $ordersToday ?></span> Transaksi</span>
                <span class="text-[11px] text-teal-300 font-semibold mt-1.5 inline-flex items-center space-x-1">
                    <i class="fas fa-calendar-day mr-0.5"></i>
                    <span>Antrean aktif</span>
                </span>
            </div>
            <div class="relative z-10 w-14 h-14 rounded-2xl bg-teal-500/15 border border-teal-500/30 text-teal-400 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-teal-500 group-hover:text-white transition duration-300 shadow-lg shadow-teal-500/20">
                <i class="fas fa-calendar-check"></i>
            </div>
        </div>

        <!-- Antrean Menunggu -->
        <div class="border-beam-card hud-card tilt-card-luxury p-6 shadow-xl shadow-black/25 flex items-center justify-between group overflow-hidden">
            <span class="hud-corner hud-corner-tl"></span>
            <span class="hud-corner hud-corner-tr"></span>
            <span class="hud-corner hud-corner-bl"></span>
            <span class="hud-corner hud-corner-br"></span>
            <div class="relative z-10">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Antrean Pending</span>
                <span class="text-2xl font-black text-amber-400 block tracking-tight"><span data-counter="<?= $pendingOrders ?>"><?= $pendingOrders ?></span> Menunggu</span>
                <span class="text-[11px] text-amber-300 font-semibold mt-1.5 inline-flex items-center space-x-1">
                    <i class="fas fa-clock mr-0.5"></i>
                    <span>Menunggu dilayani</span>
                </span>
            </div>
            <div class="relative z-10 w-14 h-14 rounded-2xl bg-amber-500/15 border border-amber-500/30 text-amber-400 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-amber-500 group-hover:text-slate-950 transition duration-300 shadow-lg shadow-amber-500/20">
                <i class="fas fa-user-clock"></i>
            </div>
        </div>

        <!-- Barber On Duty -->
        <div class="border-beam-card hud-card tilt-card-luxury p-6 shadow-xl shadow-black/25 flex items-center justify-between group overflow-hidden">
            <span class="hud-corner hud-corner-tl"></span>
            <span class="hud-corner hud-corner-tr"></span>
            <span class="hud-corner hud-corner-bl"></span>
            <span class="hud-corner hud-corner-br"></span>
            <div class="relative z-10">
                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider block mb-1">Barber On Duty</span>
                <span class="text-2xl font-black text-white block tracking-tight"><span data-counter="<?= count($barbersOnDuty) ?>"><?= count($barbersOnDuty) ?></span> Capster</span>
                <span class="text-[11px] text-emerald-400 font-semibold mt-1.5 inline-flex items-center space-x-1.5">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span>Standby di kursi</span>
                </span>
            </div>
            <div class="relative z-10 w-14 h-14 rounded-2xl bg-purple-500/15 border border-purple-500/30 text-purple-400 flex items-center justify-center text-2xl group-hover:scale-110 group-hover:bg-purple-500 group-hover:text-white transition duration-300 shadow-lg shadow-purple-500/20">
                <i class="fas fa-cut"></i>
            </div>
        </div>
    </div>

    <!-- Rekap Penghasilan Simpel (Hari Ini & 7 Hari + Tombol Atur Bulan + Daftar Siapa Saja yang Order) -->
    <?php
    $dashPeriods = $revenueRecap['periods'] ?? [];
    $dashOrdersList = $revenueRecap['orders_list'] ?? [];
    if (!empty($dashPeriods)):
        $pToday = $dashPeriods['today'] ?? ['revenue' => 0, 'orders_count' => 0, 'sublabel' => date('d M Y')];
        $p7d    = $dashPeriods['last_7_days'] ?? ['revenue' => 0, 'orders_count' => 0, 'sublabel' => ''];
    ?>
    <div class="bg-slate-900/85 backdrop-blur-xl rounded-3xl border border-white/10 shadow-xl shadow-black/25 p-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between pb-4 mb-5 border-b border-white/10 gap-3">
            <div>
                <div class="flex items-center space-x-2.5">
                    <span class="w-2.5 h-6 rounded-full barber-pole border border-slate-700 shadow-sm"></span>
                    <h3 class="font-black text-white text-base tracking-tight">Rekap Penghasilan & Daftar Pelanggan Order</h3>
                </div>
            </div>

            <!-- Tombol Utama: Hari Ini, 7 Hari, & Atur Bulan -->
            <div class="flex flex-wrap items-center gap-2">
                <button type="button" id="dash-btn-today" onclick="applyDashRecap('today')"
                    class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer bg-slate-800 text-slate-200 hover:bg-slate-700 border border-white/10">
                    Hari Ini (Rp <?= number_format($pToday['revenue'], 0, ',', '.') ?>)
                </button>
                <button type="button" id="dash-btn-7d" onclick="applyDashRecap('7d')"
                    class="px-3.5 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer bg-emerald-500 text-slate-950 shadow-md shadow-emerald-500/25">
                    7 Hari Terakhir (Rp <?= number_format($p7d['revenue'], 0, ',', '.') ?>)
                </button>
                <button type="button" onclick="document.getElementById('dash-custom-filter-box').classList.toggle('hidden')"
                    class="px-3.5 py-2 rounded-xl text-xs font-bold bg-amber-500/20 hover:bg-amber-500/30 text-amber-300 border border-amber-500/40 transition flex items-center space-x-1.5 cursor-pointer">
                    <i class="fas fa-sliders-h text-xs"></i>
                    <span>Atur Bulan / Periode Lain</span>
                </button>
            </div>
        </div>

        <!-- Panel Atur Bulan / Periode Lain (Tersembunyi Default Agar Tidak Pusing) -->
        <div id="dash-custom-filter-box" class="hidden mb-5 p-4 rounded-2xl bg-slate-950/90 border border-amber-500/30 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-1.5">
                    <span class="text-xs font-bold text-slate-300 mr-1">Pilihan Cepat:</span>
                    <button type="button" onclick="applyDashRecap('prev7d')" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-emerald-600 text-white text-xs font-semibold transition cursor-pointer">7 Hari Lalu (Minggu Kemarin)</button>
                    <button type="button" onclick="applyDashRecap('this_month')" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-emerald-600 text-white text-xs font-semibold transition cursor-pointer">Bulan Ini</button>
                    <button type="button" onclick="applyDashRecap('last_month')" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-emerald-600 text-white text-xs font-semibold transition cursor-pointer">Bulan Lalu / Kemarin</button>
                    <button type="button" onclick="applyDashRecap('last_2_months')" class="px-2.5 py-1.5 rounded-lg bg-slate-800 hover:bg-emerald-600 text-white text-xs font-semibold transition cursor-pointer">2 Bulan Terakhir</button>
                </div>

                <div class="flex flex-wrap items-center gap-2 pt-2 sm:pt-0 border-t sm:border-t-0 border-white/10">
                    <span class="text-xs font-bold text-amber-300">Pilih Bulan & Tahun:</span>
                    <select id="dash-select-month" class="bg-slate-800 text-white text-xs rounded-lg px-2.5 py-1.5 border border-white/15 outline-none">
                        <option value="all">-- Semua Bulan (1 Tahun) --</option>
                        <?php
                        $mNames = [1=>'Januari',2=>'Februari',3=>'Maret',4=>'April',5=>'Mei',6=>'Juni',7=>'Juli',8=>'Agustus',9=>'September',10=>'Oktober',11=>'November',12=>'Desember'];
                        $lastM = (int)date('n') - 1; if ($lastM < 1) $lastM = 12;
                        foreach ($mNames as $mIdx => $mName):
                        ?>
                        <option value="<?= $mIdx ?>" <?= $mIdx === $lastM ? 'selected' : '' ?>><?= $mName ?></option>
                        <?php endforeach; ?>
                    </select>
                    <select id="dash-select-year" class="bg-slate-800 text-white text-xs rounded-lg px-2.5 py-1.5 border border-white/15 outline-none">
                        <option value="<?= date('Y') ?>" selected>Tahun <?= date('Y') ?></option>
                        <option value="<?= (int)date('Y') - 1 ?>">Tahun <?= (int)date('Y') - 1 ?></option>
                    </select>
                    <button type="button" onclick="applyDashCustomMonth()" class="px-3 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-400 text-slate-950 font-extrabold text-xs transition cursor-pointer">
                        Tampilkan
                    </button>
                </div>
            </div>
        </div>

        <!-- Ringkasan Periode Terpilih (2 Kartu Bersih) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-5">
            <div class="p-4 rounded-2xl bg-slate-950/75 border border-emerald-500/30">
                <span id="dash-recap-label" class="text-[11px] font-extrabold text-emerald-400 uppercase tracking-wider block">Rekap 7 Hari Terakhir</span>
                <p id="dash-recap-revenue" class="text-2xl font-black text-white font-mono mt-1">Rp <?= number_format($p7d['revenue'], 0, ',', '.') ?></p>
                <span id="dash-recap-sublabel" class="text-[11px] text-slate-400 block mt-0.5"><?= htmlspecialchars($p7d['sublabel']) ?></span>
            </div>
            <div class="p-4 rounded-2xl bg-slate-950/75 border border-white/10">
                <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block">Jumlah Transaksi & Pelanggan</span>
                <p id="dash-recap-count" class="text-2xl font-black text-amber-400 font-mono mt-1"><?= $p7d['orders_count'] ?> Pesanan</p>
                <span id="dash-recap-type" class="text-[11px] text-slate-400 block mt-0.5">Terverifikasi Lunas</span>
            </div>
            <div class="p-4 rounded-2xl bg-slate-950/75 border border-white/10 flex flex-col justify-between">
                <div>
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block">Rincian Metode Pembayaran</span>
                    <p id="dash-recap-method" class="text-sm font-bold text-slate-200 mt-1.5">QRIS & Tunai</p>
                </div>
                <a href="/admin/analytics" class="text-xs font-bold text-emerald-400 hover:text-emerald-300 inline-flex items-center space-x-1 mt-2">
                    <span>Buka Grafik Diagram Batang</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- Tabel Siapa Saja yang Order pada Periode Ini -->
        <div class="rounded-2xl border border-white/10 overflow-hidden bg-slate-950/50">
            <div class="px-4 py-3 bg-slate-950/80 border-b border-white/10 flex items-center justify-between">
                <span class="text-xs font-extrabold text-white flex items-center space-x-2">
                    <i class="fas fa-users text-emerald-400"></i>
                    <span id="dash-orders-table-title">Daftar Pelanggan yang Order (7 Hari Terakhir)</span>
                </span>
            </div>
            <div class="overflow-x-auto max-h-72 overflow-y-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-900/90 text-slate-400 font-bold border-b border-white/10 uppercase">
                        <tr>
                            <th class="p-3 pl-4">Tanggal & Jam</th>
                            <th class="p-3">Nama Pelanggan (Siapa yang Order)</th>
                            <th class="p-3">Layanan Dipesan</th>
                            <th class="p-3 text-center">Metode</th>
                            <th class="p-3 pr-4 text-right">Nominal Bayar</th>
                        </tr>
                    </thead>
                    <tbody id="dash-recap-orders-tbody" class="divide-y divide-white/5 text-slate-200"></tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
    const DASH_RECAP_META = {
        today_ymd: <?= json_encode($revenueRecap['today_ymd'] ?? date('Y-m-d')) ?>,
        last_7d_start: <?= json_encode($revenueRecap['last_7d_start'] ?? date('Y-m-d', strtotime('-6 days'))) ?>,
        prev_7d_start: <?= json_encode($revenueRecap['prev_7d_start'] ?? date('Y-m-d', strtotime('-13 days'))) ?>,
        prev_7d_end: <?= json_encode($revenueRecap['prev_7d_end'] ?? date('Y-m-d', strtotime('-7 days'))) ?>,
        this_ym: <?= json_encode($revenueRecap['this_ym'] ?? date('Y-m')) ?>,
        last_ym: <?= json_encode($revenueRecap['last_ym'] ?? date('Y-m', strtotime('first day of last month'))) ?>,
        orders: <?= json_encode($dashOrdersList) ?>
    };
    const DASH_MONTH_NAMES = {1:'Januari',2:'Februari',3:'Maret',4:'April',5:'Mei',6:'Juni',7:'Juli',8:'Agustus',9:'September',10:'Oktober',11:'November',12:'Desember'};

    function renderDashRecapOrders(filteredOrders, periodTitle, subLabel) {
        let totalRev = 0, qrisRev = 0, tunaiRev = 0, studioCnt = 0, homeCnt = 0;
        filteredOrders.forEach(o => {
            const g = Number(o.grand_total) || 0;
            totalRev += g;
            if ((o.payment_method || '').toLowerCase() === 'qris') qrisRev += g; else tunaiRev += g;
            if (o.service_type === 'home_service') homeCnt++; else studioCnt++;
        });

        document.getElementById('dash-recap-label').textContent = periodTitle;
        document.getElementById('dash-recap-revenue').textContent = 'Rp ' + Math.round(totalRev).toLocaleString('id-ID');
        document.getElementById('dash-recap-sublabel').textContent = subLabel;
        document.getElementById('dash-recap-count').textContent = filteredOrders.length + ' Pesanan';
        document.getElementById('dash-recap-type').textContent = `${studioCnt} Studio · ${homeCnt} Home Service`;
        document.getElementById('dash-recap-method').textContent = `QRIS: Rp ${Math.round(qrisRev).toLocaleString('id-ID')} | Tunai: Rp ${Math.round(tunaiRev).toLocaleString('id-ID')}`;
        document.getElementById('dash-orders-table-title').textContent = `Daftar Pelanggan yang Order — ${periodTitle} (${filteredOrders.length} Orang)`;

        const tbody = document.getElementById('dash-recap-orders-tbody');
        if (!tbody) return;
        if (filteredOrders.length === 0) {
            tbody.innerHTML = `<tr><td colspan="5" class="p-6 text-center text-slate-400">Belum ada transaksi pada periode ini.</td></tr>`;
            return;
        }
        tbody.innerHTML = filteredOrders.map(o => `
            <tr class="hover:bg-white/5 transition">
                <td class="p-3 pl-4 font-mono text-[11px] text-slate-300">
                    <a href="/admin/orders/${o.id}" class="text-emerald-400 font-bold hover:underline block">#${o.order_code}</a>
                    <span>${o.formatted_dt}</span>
                </td>
                <td class="p-3">
                    <strong class="text-white font-bold block">${o.customer_name}</strong>
                    <span class="text-[11px] text-slate-400 font-mono"><i class="fab fa-whatsapp text-emerald-400 mr-1"></i>${o.customer_phone}</span>
                </td>
                <td class="p-3 text-slate-200">
                    <span>${o.items_text}</span>
                    ${o.service_type === 'home_service' ? '<span class="ml-1.5 px-1.5 py-0.5 rounded bg-amber-500/20 text-amber-300 text-[10px] font-bold">Home Service</span>' : ''}
                </td>
                <td class="p-3 text-center">
                    <span class="px-2 py-0.5 rounded bg-white/10 text-slate-200 font-bold text-[10px]">${o.payment_method}</span>
                </td>
                <td class="p-3 pr-4 text-right font-mono font-black text-emerald-400">
                    Rp ${Math.round(Number(o.grand_total) || 0).toLocaleString('id-ID')}
                </td>
            </tr>
        `).join('');
    }

    function applyDashRecap(mode) {
        const btnToday = document.getElementById('dash-btn-today');
        const btn7d = document.getElementById('dash-btn-7d');
        const idleCls = 'px-3.5 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer bg-slate-800 text-slate-200 hover:bg-slate-700 border border-white/10';
        const actCls = 'px-3.5 py-2 rounded-xl text-xs font-extrabold transition cursor-pointer bg-emerald-500 text-slate-950 shadow-md shadow-emerald-500/25';
        if (btnToday) btnToday.className = (mode === 'today') ? actCls : idleCls;
        if (btn7d) btn7d.className = (mode === '7d') ? actCls : idleCls;

        const all = DASH_RECAP_META.orders || [];
        let filtered = [];
        let title = '';
        let sub = '';

        if (mode === 'today') {
            filtered = all.filter(o => o.date_ymd === DASH_RECAP_META.today_ymd);
            title = 'Rekap Hari Ini';
            sub = 'Tanggal ' + DASH_RECAP_META.today_ymd;
        } else if (mode === '7d') {
            filtered = all.filter(o => o.date_ymd >= DASH_RECAP_META.last_7d_start && o.date_ymd <= DASH_RECAP_META.today_ymd);
            title = 'Rekap 7 Hari Terakhir';
            sub = DASH_RECAP_META.last_7d_start + ' s/d ' + DASH_RECAP_META.today_ymd;
        } else if (mode === 'prev7d') {
            filtered = all.filter(o => o.date_ymd >= DASH_RECAP_META.prev_7d_start && o.date_ymd <= DASH_RECAP_META.prev_7d_end);
            title = 'Rekap 7 Hari yang Lalu (Minggu Kemarin)';
            sub = DASH_RECAP_META.prev_7d_start + ' s/d ' + DASH_RECAP_META.prev_7d_end;
        } else if (mode === 'this_month') {
            filtered = all.filter(o => o.ym === DASH_RECAP_META.this_ym);
            title = 'Rekap Bulan Ini';
            sub = 'Periode ' + DASH_RECAP_META.this_ym;
        } else if (mode === 'last_month') {
            filtered = all.filter(o => o.ym === DASH_RECAP_META.last_ym);
            title = 'Rekap Bulan Lalu / Kemarin';
            sub = 'Periode ' + DASH_RECAP_META.last_ym;
        } else if (mode === 'last_2_months') {
            filtered = all.filter(o => o.ym === DASH_RECAP_META.last_ym || o.ym === DASH_RECAP_META.this_ym);
            title = 'Rekap 2 Bulan Terakhir';
            sub = DASH_RECAP_META.last_ym + ' & ' + DASH_RECAP_META.this_ym;
        }
        renderDashRecapOrders(filtered, title, sub);
    }

    function applyDashCustomMonth() {
        const mVal = document.getElementById('dash-select-month').value;
        const yVal = Number(document.getElementById('dash-select-year').value);
        const all = DASH_RECAP_META.orders || [];
        let filtered = [];
        let title = '';
        let sub = '';

        if (mVal === 'all') {
            filtered = all.filter(o => Number(o.year) === yVal);
            title = 'Rekap Tahunan ' + yVal;
            sub = 'Januari – Desember ' + yVal;
        } else {
            const mNum = Number(mVal);
            filtered = all.filter(o => Number(o.year) === yVal && Number(o.month) === mNum);
            title = 'Rekap Bulan ' + (DASH_MONTH_NAMES[mNum] || mNum) + ' ' + yVal;
            sub = 'Seluruh transaksi bulan ' + (DASH_MONTH_NAMES[mNum] || mNum) + ' ' + yVal;
        }
        renderDashRecapOrders(filtered, title, sub);
    }

    document.addEventListener('DOMContentLoaded', function() {
        applyDashRecap('today');
    });
    </script>
    <?php endif; ?>

    <!-- Main Grid: Pesanan Terbaru & Capster On-Duty -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        <!-- Tabel Pesanan Masuk Terbaru (8 Kolom) -->
        <div class="lg:col-span-8 bg-slate-900/80 backdrop-blur-xl rounded-3xl border border-white/10 shadow-xl shadow-black/25 overflow-hidden">
            <div class="p-6 border-b border-white/10 flex items-center justify-between bg-slate-950/40">
                <div>
                    <div class="flex items-center space-x-2.5">
                        <span class="w-2.5 h-6 rounded-full barber-pole border border-slate-700 shadow-sm"></span>
                        <h3 class="font-black text-white text-base tracking-tight">Antrean & Transaksi Terbaru</h3>
                    </div>
                    <p class="text-xs text-slate-400 mt-1">Ringkasan pesanan pelanggan paling update di Wijaya Barbershop.</p>
                </div>
                <a href="/admin/orders" class="text-xs font-bold text-emerald-400 hover:text-emerald-300 flex items-center space-x-1 px-3 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 border border-white/10 transition">
                    <span>Lihat Semua</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-950/70 text-slate-300 font-bold border-b border-white/10 uppercase tracking-wider">
                        <tr>
                            <th class="p-4 pl-6">Kode Order</th>
                            <th class="p-4 text-center">Antrean</th>
                            <th class="p-4">Customer</th>
                            <th class="p-4">Metode</th>
                            <th class="p-4 text-right">Total</th>
                            <th class="p-4 text-center">Status</th>
                            <th class="p-4 pr-6 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-slate-200">
                        <?php if (empty($recentOrders)): ?>
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400">
                                Belum ada pesanan terbaru.
                            </td>
                        </tr>
                        <?php endif; ?>

                        <?php foreach ($recentOrders as $ro): ?>
                        <?php $isRoHs = ($ro['service_type'] ?? 'barbershop') === 'home_service'; ?>
                        <tr class="hover:bg-white/5 transition">
                            <td class="p-4 pl-6 font-mono font-bold text-white">
                                <a href="/admin/orders/<?= $ro['id'] ?>" class="hover:text-emerald-400 transition">
                                    <?= htmlspecialchars($ro['order_code']) ?>
                                </a>
                                <?php if ($isRoHs): ?>
                                <span class="block text-[10px] font-sans font-extrabold text-amber-400 mt-0.5">
                                    <i class="fas fa-motorcycle mr-0.5"></i> Home Service
                                </span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4 text-center font-black text-emerald-400 text-sm">
                                #<?= str_pad($ro['queue_number'], 2, '0', STR_PAD_LEFT) ?>
                            </td>
                            <td class="p-4 font-semibold text-slate-200">
                                <?= htmlspecialchars($ro['customer_name'] ?? 'Pelanggan') ?>
                            </td>
                            <td class="p-4 uppercase font-bold text-[10px] text-slate-300">
                                <span class="px-2 py-0.5 rounded-md bg-white/10 border border-white/10">
                                    <?= htmlspecialchars($ro['payment_method']) ?>
                                </span>
                            </td>
                            <td class="p-4 text-right font-black text-white">
                                Rp <?= number_format($ro['grand_total'], 0, ',', '.') ?>
                            </td>
                            <td class="p-4 text-center">
                                <?php
                                $badgeClass = match($ro['status']) {
                                    'settlement' => 'bg-emerald-500/20 text-emerald-300 border-emerald-500/40',
                                    'completed'  => 'bg-blue-500/20 text-blue-300 border-blue-500/40',
                                    'cancelled'  => 'bg-rose-500/20 text-rose-300 border-rose-500/40',
                                    default      => 'bg-amber-500/20 text-amber-300 border-amber-500/40'
                                };
                                ?>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase border <?= $badgeClass ?>">
                                    <?= htmlspecialchars($ro['status']) ?>
                                </span>
                            </td>
                            <td class="p-4 pr-6 text-center">
                                <a href="/admin/orders/<?= $ro['id'] ?>" class="w-8 h-8 rounded-xl bg-white/10 hover:bg-emerald-600 text-white inline-flex items-center justify-center transition border border-white/10 shadow-sm" title="Lihat Detail Pesanan">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Capster Bertugas Card (4 Kolom) -->
        <div class="lg:col-span-4 bg-slate-900/80 backdrop-blur-xl rounded-3xl border border-white/10 shadow-xl shadow-black/25 p-6">
            <div class="flex items-center justify-between mb-4 pb-4 border-b border-white/10">
                <div>
                    <h3 class="font-black text-white text-base tracking-tight">Capster Bertugas</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Status kehadiran staf capster hari ini.</p>
                </div>
                <a href="/admin/users" class="text-xs font-bold text-emerald-400 hover:text-emerald-300" title="Kelola Kehadiran">
                    <i class="fas fa-cog"></i>
                </a>
            </div>

            <div class="space-y-3">
                <?php if (empty($barbersOnDuty)): ?>
                <div class="p-4 rounded-2xl bg-white/5 border border-white/10 text-center text-xs text-slate-400">
                    Belum ada capster yang standby (on-duty).
                </div>
                <?php endif; ?>

                <?php foreach ($barbersOnDuty as $b): ?>
                <div class="p-3.5 rounded-2xl bg-slate-950/60 border border-white/10 flex items-center justify-between hover:border-emerald-500/40 transition group">
                    <div class="flex items-center space-x-3 truncate">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-emerald-600 to-teal-400 text-white font-black flex items-center justify-center flex-shrink-0 text-sm shadow-md group-hover:scale-105 transition">
                            <?= substr($b['fullname'], 0, 1) ?>
                        </div>
                        <div class="truncate">
                            <strong class="font-bold text-white text-xs block truncate"><?= htmlspecialchars($b['fullname']) ?></strong>
                            <span class="text-[10px] text-slate-400 block truncate">
                                <?= !empty($b['phone']) ? htmlspecialchars($b['phone']) : 'Capster Studio' ?>
                            </span>
                        </div>
                    </div>
                    <span class="flex items-center space-x-1.5 px-2.5 py-1 rounded-full text-[10px] font-extrabold uppercase bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                        <span>Aktif</span>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Quick Action Links -->
            <div class="mt-6 pt-5 border-t border-white/10 grid grid-cols-2 gap-2 text-center text-xs font-bold">
                <a href="/admin/orders" class="p-2.5 rounded-xl bg-emerald-600/30 hover:bg-emerald-600 text-emerald-200 hover:text-white border border-emerald-500/30 transition flex items-center justify-center space-x-1.5">
                    <i class="fas fa-clipboard-list text-xs"></i>
                    <span>Semua Antrean</span>
                </a>
                <a href="/admin/items" class="p-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white border border-white/10 transition flex items-center justify-center space-x-1.5">
                    <i class="fas fa-cut text-xs"></i>
                    <span>Katalog Item</span>
                </a>
            </div>
        </div>
    </div>
</div>
