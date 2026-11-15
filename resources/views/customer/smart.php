<?php
/**
 * resources/views/customer/smart.php
 * Halaman Asisten Pintar Pelanggan — Rekomendasi & Smart Info
 */

$user       = $user       ?? null;
$recs       = $recs       ?? [];
$favorites  = $favorites  ?? [];
$busyHours  = $busy_hours ?? [];
$topServices= $top_services ?? [];
$aiRoadmap  = $ai_roadmap ?? [];

// Jam paling sibuk
$peakHour = 0;
$peakCount = 0;
foreach ($busyHours as $h => $c) {
    if ($c > $peakCount) { $peakCount = $c; $peakHour = $h; }
}
// Jam paling sepi
$quietHour = 0;
$quietCount = PHP_INT_MAX;
foreach ($busyHours as $h => $c) {
    if ($h >= 8 && $h <= 20 && $c < $quietCount) { $quietCount = $c; $quietHour = $h; }
}
?>

<!-- Hero Section -->
<div class="relative bg-gradient-to-br from-slate-950 via-slate-900 to-purple-950/40 overflow-hidden">
    <!-- BG decorations -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="absolute -top-20 -left-20 w-72 h-72 rounded-full bg-purple-600/15 blur-3xl"></div>
        <div class="absolute -bottom-20 -right-20 w-72 h-72 rounded-full bg-indigo-600/10 blur-3xl"></div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 relative z-10">
        <div class="text-center max-w-3xl mx-auto">
            <div class="inline-flex items-center space-x-2 bg-purple-500/10 border border-purple-500/30 rounded-full px-4 py-1.5 mb-6">
                <span class="w-2 h-2 rounded-full bg-purple-400 animate-pulse"></span>
                <span class="text-purple-300 text-xs font-bold uppercase tracking-wider">AI · Sistem Cerdas Aktif</span>
            </div>
            <h1 class="text-4xl sm:text-5xl font-black text-white mb-4 leading-tight">
                Asisten <span class="text-transparent bg-clip-text bg-gradient-to-r from-purple-400 to-indigo-400">Pintar</span>
            </h1>
            <p class="text-slate-400 text-base sm:text-lg leading-relaxed mb-8">
                Rekomendasi layanan personal, waktu terbaik berkunjung, dan asisten chatbot AI — semua untuk pengalaman barbershop terbaik Anda.
            </p>
            <button onclick="
                if(typeof toggleChatbot === 'function') {
                    if(!chatbotOpen) toggleChatbot();
                    document.getElementById('chatbot-input').focus();
                }
            " class="inline-flex items-center space-x-2.5 bg-gradient-to-r from-purple-600 to-indigo-500 hover:from-purple-500 hover:to-indigo-400 text-white font-black px-7 py-3.5 rounded-2xl shadow-xl shadow-purple-600/30 transition hover:scale-105">
                <i class="fas fa-robot text-lg"></i>
                <span>Mulai Chat dengan AI</span>
            </button>
        </div>
    </div>
</div>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 space-y-10">

    <!-- ═══ SMART TIME ADVISOR ═══ -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <!-- Waktu Terbaik Berkunjung -->
        <div class="bg-slate-800/60 backdrop-blur-md border border-emerald-500/20 rounded-3xl p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 rounded-2xl bg-emerald-500/20 flex items-center justify-center">
                    <i class="fas fa-clock text-emerald-400 text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Rekomendasi AI</p>
                    <p class="text-sm font-black text-white">Waktu Terbaik</p>
                </div>
            </div>
            <p class="text-3xl font-black text-emerald-400"><?= sprintf('%02d:00', $quietHour) ?></p>
            <p class="text-xs text-slate-400 mt-1">Jam paling sepi · antrean minimal</p>
            <div class="mt-3 text-xs text-emerald-300 bg-emerald-500/10 rounded-xl px-3 py-2 font-semibold">
                💡 Datang jam <?= sprintf('%02d.00', $quietHour) ?>–<?= sprintf('%02d.00', $quietHour + 1) ?> untuk pengalaman terbaik!
            </div>
        </div>

        <!-- Jam Tersibuk -->
        <div class="bg-slate-800/60 backdrop-blur-md border border-amber-500/20 rounded-3xl p-6">
            <div class="flex items-center space-x-3 mb-4">
                <div class="w-10 h-10 rounded-2xl bg-amber-500/20 flex items-center justify-center">
                    <i class="fas fa-fire text-amber-400 text-lg"></i>
                </div>
                <div>
                    <p class="text-xs text-slate-400 font-semibold uppercase tracking-wider">Info AI</p>
                    <p class="text-sm font-black text-white">Jam Tersibuk</p>
                </div>
            </div>
            <p class="text-3xl font-black text-amber-400"><?= sprintf('%02d:00', $peakHour) ?></p>
            <p class="text-xs text-slate-400 mt-1">Paling ramai · antrean panjang</p>
            <div class="mt-3 text-xs text-amber-300 bg-amber-500/10 rounded-xl px-3 py-2 font-semibold">
                ⚠️ Hindari jam <?= sprintf('%02d.00', $peakHour) ?>–<?= sprintf('%02d.00', $peakHour + 1) ?> jika ingin cepat!
            </div>
        </div>

        <!-- Booking Langsung -->
        <div class="bg-gradient-to-br from-purple-700/40 to-indigo-700/30 backdrop-blur-md border border-purple-500/30 rounded-3xl p-6 flex flex-col justify-between">
            <div>
                <div class="w-10 h-10 rounded-2xl bg-purple-500/30 flex items-center justify-center mb-4">
                    <i class="fas fa-calendar-check text-purple-300 text-lg"></i>
                </div>
                <p class="text-sm font-black text-white mb-1">Pesan Sekarang</p>
                <p class="text-xs text-purple-200/80">Booking layanan barbershop atau cukur panggilan ke lokasi Anda.</p>
            </div>
            <div class="mt-5 space-y-2">
                <a href="/services" class="block w-full text-center bg-purple-600 hover:bg-purple-500 text-white text-xs font-black py-2.5 rounded-xl transition">
                    <i class="fas fa-scissors mr-1.5"></i>Booking Studio
                </a>
                <a href="/home-service" class="block w-full text-center bg-amber-500/20 hover:bg-amber-500/40 text-amber-300 text-xs font-bold py-2.5 rounded-xl border border-amber-500/30 transition">
                    <i class="fas fa-motorcycle mr-1.5"></i>Home Service
                </a>
            </div>
        </div>
    </div>

    <!-- ═══ REKOMENDASI LAYANAN PERSONAL ═══ -->
    <?php if ($user): ?>
    <div class="bg-slate-800/60 backdrop-blur-md border border-white/10 rounded-3xl p-7">
        <div class="flex items-center justify-between mb-6">
            <div>
                <div class="flex items-center space-x-2 mb-1">
                    <i class="fas fa-star text-amber-400 text-sm"></i>
                    <h2 class="text-lg font-black text-white">Rekomendasi Khusus untuk Anda</h2>
                </div>
                <p class="text-xs text-slate-400">Dipilih AI berdasarkan riwayat pelanggan serupa dengan Anda</p>
            </div>
            <span class="hidden sm:block text-xs font-bold px-3 py-1 rounded-full bg-purple-500/20 border border-purple-500/30 text-purple-300">
                <i class="fas fa-brain mr-1"></i>AI Powered
            </span>
        </div>

        <?php if (empty($recs)): ?>
        <div class="text-center py-8 text-slate-500">
            <i class="fas fa-magic text-3xl mb-3 opacity-30"></i>
            <p class="text-sm">Belum ada rekomendasi personal.</p>
            <p class="text-xs mt-1">Coba pesan layanan pertama Anda untuk mendapatkan rekomendasi!</p>
            <a href="/services" class="inline-block mt-4 px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition">Lihat Semua Layanan</a>
        </div>
        <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php foreach ($recs as $i => $rec): ?>
            <div class="bg-slate-900/60 border border-white/10 rounded-2xl p-4 hover:border-purple-500/40 transition group">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-purple-600/50 to-indigo-500/30 flex items-center justify-center mb-3">
                    <i class="fas fa-scissors text-purple-300 text-sm group-hover:animate-pulse"></i>
                </div>
                <h3 class="text-sm font-bold text-white mb-1 leading-tight"><?= htmlspecialchars($rec['name'] ?? '-') ?></h3>
                <?php if (!empty($rec['avg_price']) && $rec['avg_price'] > 0): ?>
                <p class="text-xs text-emerald-400 font-bold mb-3">Rp <?= number_format($rec['avg_price'], 0, ',', '.') ?></p>
                <?php endif; ?>
                <a href="/services" class="text-xs font-bold text-purple-400 hover:text-purple-300 transition">
                    Pesan Sekarang <i class="fas fa-arrow-right text-[10px] ml-1"></i>
                </a>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Layanan Favorit User -->
    <?php if (!empty($favorites)): ?>
    <div class="bg-slate-800/60 backdrop-blur-md border border-white/10 rounded-3xl p-7">
        <div class="flex items-center space-x-2 mb-5">
            <i class="fas fa-heart text-rose-400 text-sm"></i>
            <h2 class="text-base font-black text-white">Favorit Anda</h2>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <?php foreach ($favorites as $i => $fav): ?>
            <div class="flex items-center space-x-3 bg-slate-900/60 border border-white/10 rounded-2xl p-4">
                <div class="w-9 h-9 rounded-xl bg-rose-500/20 flex items-center justify-center flex-shrink-0 font-black text-rose-400">
                    <?= ['🥇','🥈','🥉'][$i] ?? '·' ?>
                </div>
                <div class="min-w-0">
                    <p class="text-sm font-bold text-white truncate"><?= htmlspecialchars($fav['name'] ?? '-') ?></p>
                    <p class="text-xs text-slate-400"><?= $fav['cnt'] ?>x dipesan · Rp <?= number_format($fav['spend'] ?? 0, 0, ',', '.') ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <!-- ═══ TOP LAYANAN POPULER ═══ -->
    <div class="bg-slate-800/60 backdrop-blur-md border border-white/10 rounded-3xl p-7">
        <div class="flex items-center space-x-2 mb-5">
            <i class="fas fa-trophy text-amber-400 text-sm"></i>
            <h2 class="text-base font-black text-white">Layanan Paling Diminati</h2>
        </div>
        <?php if (empty($topServices)): ?>
        <p class="text-slate-500 text-sm text-center py-6">Belum ada data layanan.</p>
        <?php else: ?>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <?php $medals = ['🥇','🥈','🥉','4️⃣']; ?>
            <?php foreach ($topServices as $i => $svc): ?>
            <div class="bg-slate-900/60 border border-white/10 rounded-2xl p-4 hover:border-emerald-500/30 transition">
                <div class="text-2xl mb-2"><?= $medals[$i] ?? '·' ?></div>
                <h3 class="text-sm font-bold text-white mb-1 leading-tight"><?= htmlspecialchars($svc['name'] ?? '-') ?></h3>
                <p class="text-xs text-emerald-400 font-bold"><?= $svc['orders'] ?> pesanan</p>
                <div class="mt-2 bg-slate-800 rounded-full h-1.5 overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-emerald-500 to-teal-400" style="width:<?= $svc['pct'] ?? 0 ?>%"></div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- ═══ INOVASI & RENCANA MASA DEPAN KEDEPANNYA ═══ -->
    <div class="bg-slate-800/60 backdrop-blur-md border border-purple-500/30 rounded-3xl p-7 sm:p-8 relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-80 h-80 bg-purple-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-8">
            <div>
                <div class="flex items-center space-x-2.5 mb-1.5">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-400 animate-ping"></span>
                    <span class="px-2.5 py-0.5 rounded-md bg-purple-500/20 text-purple-300 text-[10px] font-black uppercase tracking-wider border border-purple-500/30">Next-Gen Barbershop</span>
                </div>
                <h2 class="text-2xl font-black text-white">🔮 Rencana Inovasi AI Masa Depan</h2>
                <p class="text-xs text-slate-400 mt-1">Teknologi mutakhir yang sedang dikembangkan untuk menyempurnakan pengalaman cukur Anda</p>
            </div>
            <span class="text-xs text-slate-400 font-mono hidden sm:block">Roadmap 2026 - 2027</span>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
            <!-- 1. AI Face Shape & Hairstyle Matcher -->
            <div class="bg-slate-900/80 border border-purple-500/30 hover:border-purple-500/60 rounded-2xl p-6 transition group relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-purple-500/20 text-purple-400 flex items-center justify-center text-xl group-hover:scale-110 transition shadow-inner">
                        <i class="fas fa-camera"></i>
                    </div>
                    <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full bg-purple-500/20 text-purple-300 border border-purple-500/30">
                        Dalam Riset
                    </span>
                </div>
                <h3 class="text-base font-black text-white mb-2 group-hover:text-purple-300 transition">AI Face Shape Matcher</h3>
                <p class="text-xs text-slate-300 leading-relaxed mb-4">
                    Pindai wajah Anda menggunakan kamera smartphone. Algoritma Vision AI akan mendeteksi bentuk proporsi wajah (oval, persegi, bulat) dan otomatis menampilkan 3 model rambut paling cocok secara visual.
                </p>
                <div class="text-[11px] font-semibold text-purple-400 flex items-center space-x-1.5">
                    <i class="fas fa-microchip"></i>
                    <span>MediaPipe FaceMesh & Augmented Reality</span>
                </div>
            </div>

            <!-- 2. Dynamic Happy-Hour Discounts -->
            <div class="bg-slate-900/80 border border-amber-500/30 hover:border-amber-500/60 rounded-2xl p-6 transition group relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-xl group-hover:scale-110 transition shadow-inner">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">
                        Segera Rilis
                    </span>
                </div>
                <h3 class="text-base font-black text-white mb-2 group-hover:text-amber-300 transition">Smart Happy-Hour Auto Discount</h3>
                <p class="text-xs text-slate-300 leading-relaxed mb-4">
                    Sistem mendeteksi jam-jam santai secara otomatis dan memberikan diskon instan khusus bagi pelanggan yang memesan di jam tersebut. Potong rambut lebih hemat tanpa harus antre!
                </p>
                <div class="text-[11px] font-semibold text-amber-400 flex items-center space-x-1.5">
                    <i class="fas fa-tags"></i>
                    <span>Load Balancing Antrean Otomatis</span>
                </div>
            </div>

            <!-- 3. IoT Smart Mirror Studio -->
            <div class="bg-slate-900/80 border border-teal-500/30 hover:border-teal-500/60 rounded-2xl p-6 transition group relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 rounded-2xl bg-teal-500/20 text-teal-400 flex items-center justify-center text-xl group-hover:scale-110 transition shadow-inner">
                        <i class="fas fa-tv"></i>
                    </div>
                    <span class="text-[10px] font-extrabold uppercase px-2.5 py-1 rounded-full bg-teal-500/20 text-teal-300 border border-teal-500/30">
                        Visi Jangka Panjang
                    </span>
                </div>
                <h3 class="text-base font-black text-white mb-2 group-hover:text-teal-300 transition">IoT Smart Mirror Barbershop</h3>
                <p class="text-xs text-slate-300 leading-relaxed mb-4">
                    Cermin pintar di setiap kursi potong yang menampilkan informasi nomor antrean berikutnya, status poin VIP Anda, rekomendasi produk pomade, serta kontrol musik favorit Anda.
                </p>
                <div class="text-[11px] font-semibold text-teal-400 flex items-center space-x-1.5">
                    <i class="fas fa-wifi"></i>
                    <span>Smart Display & IoT Raspberry Pi</span>
                </div>
            </div>
        </div>

        <!-- Interactive Customer Wishlist / Voting Widget -->
        <div class="bg-purple-950/30 border border-purple-500/20 rounded-2xl p-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <p class="text-sm font-bold text-white mb-1">💬 Fitur masa depan mana yang paling Anda tunggu?</p>
                    <p class="text-xs text-slate-400">Pilih salah satu untuk memberi masukan prioritas pengembangan tim kami:</p>
                </div>
                <div class="flex flex-wrap gap-2" id="wishlist-buttons">
                    <button onclick="voteWishlist('AI Face Shape Matcher')" class="px-3.5 py-2 rounded-xl bg-purple-600/30 hover:bg-purple-600 text-purple-200 hover:text-white text-xs font-bold transition border border-purple-500/30 flex items-center space-x-1.5">
                        <i class="fas fa-camera text-xs"></i>
                        <span>AI Face Matcher</span>
                    </button>
                    <button onclick="voteWishlist('Diskon Happy Hour Otomatis')" class="px-3.5 py-2 rounded-xl bg-amber-500/20 hover:bg-amber-500 text-amber-200 hover:text-slate-950 text-xs font-bold transition border border-amber-500/30 flex items-center space-x-1.5">
                        <i class="fas fa-bolt text-xs"></i>
                        <span>Diskon Happy Hour</span>
                    </button>
                    <button onclick="voteWishlist('IoT Cermin Pintar')" class="px-3.5 py-2 rounded-xl bg-teal-500/20 hover:bg-teal-500 text-teal-200 hover:text-white text-xs font-bold transition border border-teal-500/30 flex items-center space-x-1.5">
                        <i class="fas fa-tv text-xs"></i>
                        <span>Cermin Pintar</span>
                    </button>
                </div>
            </div>
            <div id="wishlist-feedback" class="hidden mt-3 p-3 bg-emerald-500/20 border border-emerald-500/40 rounded-xl text-emerald-300 text-xs font-semibold flex items-center space-x-2">
                <i class="fas fa-check-circle"></i>
                <span id="wishlist-feedback-text">Terima kasih atas voting Anda! Pilihan Anda telah dicatat oleh sistem AI.</span>
            </div>
        </div>
    </div>

    <script>
    function voteWishlist(featureName) {
        const fb = document.getElementById('wishlist-feedback');
        const fbText = document.getElementById('wishlist-feedback-text');
        if (fb && fbText) {
            fbText.textContent = 'Terima kasih atas voting Anda! AI mencatat ketertarikan Anda pada: "' + featureName + '". Masukan ini akan memprioritaskan jadwal rilis fitur.';
            fb.classList.remove('hidden');
        }
    }
    </script>

    <!-- ═══ CHATBOT CTA ═══ -->
    <div class="bg-gradient-to-r from-purple-900/50 to-indigo-900/40 border border-purple-500/20 rounded-3xl p-8 text-center">
        <div class="w-16 h-16 rounded-3xl bg-purple-600/30 flex items-center justify-center mx-auto mb-5">
            <i class="fas fa-robot text-purple-300 text-3xl"></i>
        </div>
        <h2 class="text-xl font-black text-white mb-2">Punya Pertanyaan?</h2>
        <p class="text-slate-400 text-sm mb-6 max-w-md mx-auto">
            Asisten Pintar kami siap menjawab pertanyaan seputar harga, jam buka, promo, home service, dan lainnya — 24 jam, langsung!
        </p>
        <button onclick="if(typeof toggleChatbot==='function'){if(!chatbotOpen)toggleChatbot();}" 
            class="inline-flex items-center space-x-2.5 bg-gradient-to-r from-purple-600 to-indigo-500 hover:from-purple-500 hover:to-indigo-400 text-white font-black px-7 py-3.5 rounded-2xl shadow-lg shadow-purple-600/25 transition hover:scale-105">
            <i class="fas fa-comment-dots text-lg"></i>
            <span>Buka Chat AI Sekarang</span>
        </button>
        <p class="text-xs text-slate-500 mt-4">💡 Musik latar tetap berjalan saat chat — tidak ada gangguan!</p>
    </div>

</div>

