<div class="space-y-6">
    <div class="bg-slate-900/80 backdrop-blur-xl rounded-3xl p-6 border border-white/10 shadow-xl shadow-black/25 flex items-center justify-between">
        <div>
            <div class="flex items-center space-x-2.5 mb-1">
                <span class="w-2.5 h-6 rounded-full barber-pole border border-slate-700 shadow-sm"></span>
                <h1 class="text-2xl font-black text-white tracking-tight">Kelola Pengguna & Barber</h1>
            </div>
            <p class="text-xs text-slate-400">Daftar akun direktur, kasir, barber/capster, dan pelanggan Wijaya Barbershop.</p>
        </div>
    </div>

    <div class="bg-slate-900/80 backdrop-blur-xl rounded-3xl border border-white/10 shadow-xl shadow-black/25 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
                <thead class="bg-slate-950/70 text-slate-300 font-bold border-b border-white/10 uppercase tracking-wider">
                    <tr>
                        <th class="p-4 pl-6 w-12 text-center">ID</th>
                        <th class="p-4">Nama Lengkap</th>
                        <th class="p-4">Username / Email</th>
                        <th class="p-4">No WhatsApp</th>
                        <th class="p-4 text-center">Role / Jabatan</th>
                        <th class="p-4 text-center">Status On Duty</th>
                        <th class="p-4 pr-6 text-center">Aksi Tugas</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5 text-slate-200">
                    <?php foreach ($users as $u): ?>
                    <tr class="hover:bg-white/5 transition">
                        <td class="p-4 pl-6 text-center font-bold text-slate-400"><?= $u['id'] ?></td>
                        <td class="p-4">
                            <strong class="font-bold text-white block text-sm"><?= htmlspecialchars($u['fullname']) ?></strong>
                        </td>
                        <td class="p-4">
                            <span class="font-mono text-emerald-400 block"><?= htmlspecialchars($u['username']) ?></span>
                            <span class="text-slate-400 text-[11px] block"><?= htmlspecialchars($u['email']) ?></span>
                        </td>
                        <td class="p-4 font-mono text-slate-300">
                            <?= htmlspecialchars($u['phone'] ?? '-') ?>
                        </td>
                        <td class="p-4 text-center">
                            <?php if ($u['role_name'] === 'admin'): ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-purple-500/20 text-purple-300 border border-purple-500/30">Direktur / Admin</span>
                            <?php elseif ($u['role_name'] === 'kasir'): ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-blue-500/20 text-blue-300 border border-blue-500/30">Kasir</span>
                            <?php elseif ($u['role_name'] === 'barber'): ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30">Barber Capster</span>
                            <?php else: ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold bg-white/10 text-slate-300 border border-white/10">Pelanggan</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 text-center">
                            <?php if ($u['role_name'] === 'barber' || $u['role_name'] === 'kasir'): ?>
                                <?php if ($u['on_duty']): ?>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-300 border border-emerald-500/30 flex items-center justify-center space-x-1.5 mx-auto w-max">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                                    <span>Bertugas</span>
                                </span>
                                <?php else: ?>
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-white/10 text-slate-400 border border-white/10 mx-auto w-max block">
                                    Libur / Off
                                </span>
                                <?php endif; ?>
                            <?php else: ?>
                            <span class="text-slate-500 text-[10px]">-</span>
                            <?php endif; ?>
                        </td>
                        <td class="p-4 pr-6 text-center">
                            <?php if ($u['role_name'] === 'barber' || $u['role_name'] === 'kasir'): ?>
                            <form action="/admin/users" method="POST" class="inline">
                                <input type="hidden" name="action" value="toggle_duty">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <button type="submit" class="px-3 py-1.5 rounded-xl text-[10px] font-bold transition <?= $u['on_duty'] ? 'bg-amber-500/20 hover:bg-amber-500 text-amber-300 hover:text-slate-950 border border-amber-500/30' : 'bg-emerald-500/20 hover:bg-emerald-500 text-emerald-300 hover:text-white border border-emerald-500/30' ?>">
                                    <?= $u['on_duty'] ? 'Set Libur' : 'Set Bertugas' ?>
                                </button>
                            </form>
                            <?php else: ?>
                            <span class="text-slate-500 text-[11px]">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
