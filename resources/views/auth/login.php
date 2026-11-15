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
                    <i class="fas fa-user"></i>
                </span>
                <input type="text" id="login" name="login" required placeholder="Masukkan username atau email Anda" 
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

        <button type="submit" id="loginSubmitBtn"
            class="w-full py-4 px-4 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-black rounded-xl transition duration-300 shadow-xl shadow-emerald-600/35 text-xs uppercase tracking-wider shimmer-btn flex items-center justify-center space-x-2">
            <span id="loginBtnText">MASUK SEKARANG</span>
            <i id="loginBtnIcon" class="fas fa-arrow-right text-xs"></i>
        </button>

        <script>
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

        <div class="text-center pt-2 space-y-3">
            <p class="text-sm text-slate-500">
                Belum memiliki akun? 
                <a href="/register" class="font-bold text-emerald-600 hover:text-emerald-500 hover:underline">Daftar Sekarang</a>
            </p>
            <div class="pt-2 border-t border-slate-200/80">
                <a href="/" class="w-full py-3 px-4 rounded-xl bg-slate-100 hover:bg-emerald-50 text-slate-700 hover:text-emerald-700 border border-slate-200 hover:border-emerald-300 font-bold text-xs inline-flex items-center justify-center space-x-2 transition">
                    <i class="fas fa-home text-emerald-600"></i>
                    <span>Buka Web Tamu / Dashboard Utama (Tanpa Login)</span>
                </a>
            </div>
        </div>
    </form>
</div>

