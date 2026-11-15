<div class="max-w-4xl space-y-6">
    <div class="bg-slate-900/80 backdrop-blur-xl rounded-3xl p-6 border border-white/10 shadow-xl shadow-black/25">
        <div class="flex items-center space-x-2.5 mb-1">
            <span class="w-2.5 h-6 rounded-full barber-pole border border-slate-700 shadow-sm"></span>
            <h1 class="text-2xl font-black text-white tracking-tight">Kelola Role & Hak Akses</h1>
        </div>
        <p class="text-xs text-slate-400">Struktur peran dan izin pengguna sistem Wijaya Barbershop.</p>
    </div>

    <div class="bg-slate-900/80 backdrop-blur-xl rounded-3xl border border-white/10 shadow-xl shadow-black/25 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/70 text-slate-300 font-bold border-b border-white/10 uppercase tracking-wider">
                    <tr>
                        <th class="p-4 pl-6 w-16 text-center">ID</th>
                        <th class="p-4">Nama Role</th>
                        <th class="p-4">Deskripsi Peran</th>
                        <th class="p-4 pr-6 text-center">Tingkat Izin</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-slate-200">
                    <?php foreach ($roles as $role): ?>
                    <tr class="hover:bg-white/5 transition">
                        <td class="p-4 pl-6 text-center font-bold text-slate-400"><?= $role['id'] ?></td>
                        <td class="p-4 font-mono font-bold text-white text-sm">
                            <?= htmlspecialchars($role['role_name']) ?>
                        </td>
                        <td class="p-4 text-slate-400">
                            <?= htmlspecialchars($role['description'] ?? '-') ?>
                        </td>
                        <td class="p-4 pr-6 text-center">
                            <?php if ($role['role_name'] === 'admin'): ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-purple-500/20 text-purple-300 border border-purple-500/30">Akses Penuh (Full Access)</span>
                            <?php elseif ($role['role_name'] === 'kasir'): ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-500/20 text-blue-300 border border-blue-500/30">Transaksi & Order</span>
                            <?php elseif ($role['role_name'] === 'barber'): ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Layanan Antrean</span>
                            <?php else: ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-white/10 text-slate-300 border border-white/10">Pemesanan & Riwayat</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
