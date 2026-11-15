<div class="max-w-md w-full mx-auto">
    <div class="mb-8">
        <span class="text-xs font-bold uppercase tracking-widest text-emerald-600 block mb-1">Selamat Datang Kembali</span>
        <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight">MASUK KE AKUN ANDA</h2>
        <p class="text-sm text-slate-500 mt-2">Masuk untuk memesan antrean pangkas rambut atau mengelola transaksi.</p>
    </div>

    <?php if (!empty($error)): ?>
    <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-sm flex items-center space-x-3">
        <i class="fas fa-exclamation-circle text-rose-600 text-lg flex-shrink-0"></i>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
    <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm flex items-center space-x-3">
        <i class="fas fa-check-circle text-emerald-600 text-lg flex-shrink-0"></i>
        <span><?= htmlspecialchars($success) ?></span>
    </div>
    <?php endif; ?>

    <form action="/login" method="POST" class="space-y-5" id="loginForm">
        <div>
            <label for="login" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                Email / Username
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                    <i class="fas fa-envelope"></i>
                </span>
                <input type="text" id="login" name="login" required placeholder="admin / customer@wijayabarber.com" 
                    class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-slate-900 text-sm transition outline-none">
            </div>
        </div>

        <div>
            <div class="flex items-center justify-between mb-2">
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-700">
                    Password
                </label>
            </div>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400">
                    <i class="fas fa-lock"></i>
                </span>
                <input type="password" id="password" name="password" required placeholder="••••••••" 
                    class="w-full pl-10 pr-4 py-3 rounded-xl border border-slate-300 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 text-slate-900 text-sm transition outline-none">
            </div>
        </div>

        <!-- Akun Demo Cepat untuk Pengujian Skripsi (Klik untuk Isi Otomatis) -->
        <div class="p-3.5 bg-slate-50 border border-slate-200/90 rounded-2xl text-xs text-slate-600 shadow-sm">
            <div class="flex items-center justify-between mb-2">
                <span class="font-extrabold text-slate-800 flex items-center space-x-1.5">
                    <i class="fas fa-key text-emerald-600"></i>
                    <span>Akun Pengujian Demo (Klik untuk Isi Cepat):</span>
                </span>
                <span class="text-[10px] text-slate-400 font-mono">pass: password123</span>
            </div>
            <div class="grid grid-cols-2 gap-2 text-[11px]">
                <button type="button" onclick="fillLoginCreds('agung', 'password123')" class="p-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/50 text-left transition flex items-center space-x-2 group">
                    <span class="w-6 h-6 rounded-lg bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-xs group-hover:bg-emerald-600 group-hover:text-white transition">A</span>
                    <div>
                        <strong class="text-slate-800 block leading-tight">Admin</strong>
                        <span class="text-[10px] text-slate-400 font-mono">agung</span>
                    </div>
                </button>
                <button type="button" onclick="fillLoginCreds('mely', 'password123')" class="p-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/50 text-left transition flex items-center space-x-2 group">
                    <span class="w-6 h-6 rounded-lg bg-teal-100 text-teal-700 flex items-center justify-center font-bold text-xs group-hover:bg-teal-600 group-hover:text-white transition">K</span>
                    <div>
                        <strong class="text-slate-800 block leading-tight">Kasir</strong>
                        <span class="text-[10px] text-slate-400 font-mono">mely</span>
                    </div>
                </button>
                <button type="button" onclick="fillLoginCreds('doni', 'password123')" class="p-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/50 text-left transition flex items-center space-x-2 group">
                    <span class="w-6 h-6 rounded-lg bg-amber-100 text-amber-700 flex items-center justify-center font-bold text-xs group-hover:bg-amber-600 group-hover:text-white transition">C</span>
                    <div>
                        <strong class="text-slate-800 block leading-tight">Capster</strong>
                        <span class="text-[10px] text-slate-400 font-mono">doni</span>
                    </div>
                </button>
                <button type="button" onclick="fillLoginCreds('juni', 'password123')" class="p-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/50 text-left transition flex items-center space-x-2 group">
                    <span class="w-6 h-6 rounded-lg bg-blue-100 text-blue-700 flex items-center justify-center font-bold text-xs group-hover:bg-blue-600 group-hover:text-white transition">P</span>
                    <div>
                        <strong class="text-slate-800 block leading-tight">Pelanggan 1</strong>
                        <span class="text-[10px] text-slate-400 font-mono">juni</span>
                    </div>
                </button>
                <button type="button" onclick="fillLoginCreds('direktur', 'password123')" class="p-2 rounded-xl bg-white border border-slate-200 hover:border-purple-500 hover:bg-purple-50/50 text-left transition flex items-center space-x-2 group">
                    <span class="w-6 h-6 rounded-lg bg-purple-100 text-purple-700 flex items-center justify-center font-bold text-xs group-hover:bg-purple-600 group-hover:text-white transition">D</span>
                    <div>
                        <strong class="text-slate-800 block leading-tight">Direktur</strong>
                        <span class="text-[10px] text-slate-400 font-mono">direktur</span>
                    </div>
                </button>
                <button type="button" onclick="fillLoginCreds('reza', 'password123')" class="p-2 rounded-xl bg-white border border-slate-200 hover:border-emerald-500 hover:bg-emerald-50/50 text-left transition flex items-center space-x-2 group">
                    <span class="w-6 h-6 rounded-lg bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold text-xs group-hover:bg-indigo-600 group-hover:text-white transition">P</span>
                    <div>
                        <strong class="text-slate-800 block leading-tight">Pelanggan 3</strong>
                        <span class="text-[10px] text-slate-400 font-mono">reza</span>
                    </div>
                </button>
            </div>

            <!-- Panduan Fleksibel Login Pelanggan Bebas -->
            <div class="mt-3 p-2.5 rounded-xl bg-emerald-50/90 border border-emerald-200/80 text-[11px] text-emerald-900 flex items-start space-x-2">
                <i class="fas fa-magic text-emerald-600 mt-0.5 flex-shrink-0"></i>
                <div class="leading-relaxed">
                    <strong>Bisa Login Nama Apa Saja:</strong> Masukkan nama / username pelanggan bebas apa saja dengan password <span class="bg-white px-1.5 py-0.5 rounded text-emerald-700 font-mono font-bold border border-emerald-200">password123</span>. Sistem otomatis mengautentikasi dan langsung mengarahkan Anda ke katalog pangkas rambut.
                </div>
            </div>
        </div>

        <button type="submit" id="loginSubmitBtn"
            class="w-full py-4 px-4 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-black rounded-xl transition duration-300 shadow-xl shadow-emerald-600/35 text-xs uppercase tracking-wider shimmer-btn flex items-center justify-center space-x-2">
            <span id="loginBtnText">MASUK SEKARANG</span>
            <i id="loginBtnIcon" class="fas fa-arrow-right text-xs"></i>
        </button>

        <script>
            function fillLoginCreds(u, p) {
                const loginEl = document.getElementById('login');
                const passEl = document.getElementById('password');
                if (loginEl) loginEl.value = u;
                if (passEl) passEl.value = p;
                if (loginEl) loginEl.focus();
            }

            document.addEventListener('DOMContentLoaded', function() {
                const form = document.getElementById('loginForm');
                const submitBtn = document.getElementById('loginSubmitBtn');
                const btnText = document.getElementById('loginBtnText');
                const btnIcon = document.getElementById('loginBtnIcon');

                if (!form) return;

                form.addEventListener('submit', function(e) {
                    if (form.dataset.submitting === 'true') {
                        return; // Biarkan request submit bawaan berjalan
                    }

                    const loginVal = (document.getElementById('login')?.value || '').trim();
                    const passVal = (document.getElementById('password')?.value || '').trim();

                    if (!loginVal || !passVal) {
                        return; // Biarkan validasi HTML5 bekerja jika input kosong
                    }

                    e.preventDefault();

                    // 1. Ubah state tombol submit
                    if (submitBtn) {
                        submitBtn.disabled = true;
                        submitBtn.classList.add('opacity-85', 'cursor-not-allowed');
                        if (btnText) btnText.textContent = 'MEMVERIFIKASI...';
                        if (btnIcon) btnIcon.className = 'fas fa-circle-notch fa-spin text-xs';
                    }

                    // 2. Munculkan modal animasi loading bergerak
                    const overlay = document.getElementById('login-loading-overlay');
                    const statusText = document.getElementById('loading-status-text');
                    const progressBar = document.getElementById('loading-progress-bar');
                    const percentText = document.getElementById('loading-percentage-text');
                    const roleText = document.getElementById('loading-role-text');

                    if (roleText) {
                        roleText.textContent = 'User: ' + loginVal;
                    }

                    if (overlay) {
                        overlay.style.display = 'flex';
                        // Force DOM reflow untuk animasi fade-in
                        void overlay.offsetWidth;
                        overlay.classList.remove('opacity-0', 'pointer-events-none');
                        overlay.classList.add('opacity-100');
                    }

                    // 3. Animasi tahapan progres bergerak (Moving Progress Sequence)
                    let progress = 18;
                    const progressTimer = setInterval(() => {
                        progress += Math.floor(Math.random() * 12) + 9;
                        if (progress > 93) progress = 93;

                        if (progressBar) progressBar.style.width = progress + '%';
                        if (percentText) percentText.textContent = progress + '%';

                        if (progress >= 35 && progress < 65 && statusText) {
                            statusText.textContent = 'Mengautentikasi hak akses & sesi akun...';
                        } else if (progress >= 65 && statusText) {
                            statusText.textContent = 'Menyiapkan ruang kerja & menghubungkan ke sistem...';
                        }
                    }, 120);

                    // 4. Setelah animasi bergerak selesai (~1.1 detik), submit form secara otomatis
                    setTimeout(() => {
                        clearInterval(progressTimer);
                        if (progressBar) progressBar.style.width = '100%';
                        if (percentText) percentText.textContent = '100%';
                        if (statusText) statusText.textContent = 'Berhasil! Mengarahkan ke dashboard...';

                        form.dataset.submitting = 'true';
                        setTimeout(() => {
                            form.submit();
                        }, 250);
                    }, 1100);
                });
            });
        </script>

        <div class="text-center pt-2">
            <p class="text-sm text-slate-500">
                Belum memiliki akun? 
                <a href="/register" class="font-bold text-emerald-600 hover:text-emerald-500 hover:underline">Daftar Sekarang</a>
            </p>
        </div>
    </form>
</div>

