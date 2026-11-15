<div class="max-w-md w-full mx-auto">
    <div class="mb-6">
        <span class="text-xs font-black uppercase tracking-widest text-emerald-400 block mb-1.5 flex items-center space-x-2">
            <span class="w-2 h-2 rounded-full bg-emerald-400 beacon-pulse"></span>
            <span>Mulai Reservasi Digital</span>
        </span>
        <h2 class="text-3xl font-black text-white tracking-tight">REGISTER AKUN</h2>
        <p class="text-xs sm:text-sm text-slate-400 mt-1.5 leading-relaxed">Daftarkan akun Anda untuk kemudahan booking antrean dan pemesanan cukur panggilan.</p>
    </div>

    <?php if (!empty($error)): ?>
    <div class="mb-5 p-4 rounded-2xl bg-rose-500/20 border border-rose-500/40 text-rose-200 text-xs sm:text-sm flex items-center space-x-3 backdrop-blur-md">
        <i class="fas fa-exclamation-circle text-rose-400 text-lg flex-shrink-0"></i>
        <span><?= htmlspecialchars($error) ?></span>
    </div>
    <?php endif; ?>

    <form action="/register" method="POST" class="space-y-3.5">
        <div>
            <label for="fullname" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                Nama Lengkap *
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                    <i class="fas fa-user"></i>
                </span>
                <input type="text" id="fullname" name="fullname" required value="<?= htmlspecialchars($old['fullname'] ?? '') ?>" placeholder="Nama lengkap Anda" 
                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-slate-950/70 border border-slate-700/80 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/25 text-white placeholder-slate-500 text-sm transition outline-none shadow-inner">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label for="username" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    Username *
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                        <i class="fas fa-at"></i>
                    </span>
                    <input type="text" id="username" name="username" required value="<?= htmlspecialchars($old['username'] ?? '') ?>" placeholder="username" 
                        class="w-full pl-10 pr-3 py-2.5 rounded-2xl bg-slate-950/70 border border-slate-700/80 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/25 text-white placeholder-slate-500 text-sm transition outline-none shadow-inner">
                </div>
            </div>

            <div>
                <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    No WhatsApp *
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                        <i class="fab fa-whatsapp"></i>
                    </span>
                    <input type="tel" id="phone" name="phone" required value="<?= htmlspecialchars($old['phone'] ?? '') ?>" placeholder="0812xxxxxxxx" 
                        class="w-full pl-10 pr-3 py-2.5 rounded-2xl bg-slate-950/70 border border-slate-700/80 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/25 text-white placeholder-slate-500 text-sm transition outline-none shadow-inner">
                </div>
            </div>
        </div>

        <div>
            <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                Alamat Email *
            </label>
            <div class="relative">
                <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                    <i class="fas fa-envelope"></i>
                </span>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($old['email'] ?? '') ?>" placeholder="nama@email.com" 
                    class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-slate-950/70 border border-slate-700/80 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/25 text-white placeholder-slate-500 text-sm transition outline-none shadow-inner">
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
            <div>
                <label for="password" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    Password *
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input type="password" id="password" name="password" required placeholder="Min 6 karakter" 
                        class="w-full pl-10 pr-3 py-2.5 rounded-2xl bg-slate-950/70 border border-slate-700/80 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/25 text-white placeholder-slate-500 text-sm transition outline-none shadow-inner">
                </div>
            </div>

            <div>
                <label for="password_confirmation" class="block text-xs font-bold uppercase tracking-wider text-slate-300 mb-1.5">
                    Konfirmasi *
                </label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 pointer-events-none">
                        <i class="fas fa-check-double"></i>
                    </span>
                    <input type="password" id="password_confirmation" name="password_confirmation" required placeholder="Ulangi password" 
                        class="w-full pl-10 pr-3 py-2.5 rounded-2xl bg-slate-950/70 border border-slate-700/80 focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/25 text-white placeholder-slate-500 text-sm transition outline-none shadow-inner">
                </div>
            </div>
        </div>

        <button type="submit" 
            class="w-full mt-3 py-3.5 px-4 bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 text-white font-black rounded-2xl transition duration-300 shadow-xl shadow-emerald-600/35 text-xs uppercase tracking-wider shimmer-btn flex items-center justify-center space-x-2">
            <span>DAFTAR SEKARANG</span>
            <i class="fas fa-user-plus text-xs"></i>
        </button>
    </form>

    <div class="mt-5 text-center text-xs text-slate-400">
        Sudah memiliki akun? 
        <a href="/login" class="text-emerald-400 font-extrabold hover:text-emerald-300 transition underline underline-offset-4">
            Masuk Sekarang
        </a>
    </div>
</div>
