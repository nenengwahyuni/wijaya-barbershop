<?php
use App\Core\Auth;
$user = Auth::user();
$currentUri = $_SERVER['REQUEST_URI'] ?? '/admin';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Admin Dashboard - Wijaya Barbershop') ?></title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: {
                            500: '#10b981',
                            600: '#059669',
                            700: '#047857',
                            900: '#064e3b'
                        }
                    }
                }
            }
        }
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="relative min-h-screen text-slate-100 flex flex-row overflow-hidden bg-slate-950" style="display: flex !important; flex-direction: row !important; height: 100vh; width: 100vw; overflow: hidden; margin: 0;">

    <!-- Interactive HTML5 Barbershop Particle Canvas (Ember & Stardust Engine) -->
    <canvas id="barber-particle-canvas" class="fixed inset-0 pointer-events-none z-0 opacity-60"></canvas>

    <!-- 1. Authentic Barbershop Studio Background (Sama dengan Web Utama) -->
    <div id="admin-bg-image" class="fixed inset-0 z-0 bg-cover bg-center pointer-events-none opacity-35 scale-105 transition-all duration-700" style="background-image: url('https://images.unsplash.com/photo-1585747860715-2ba37e788b70?auto=format&fit=crop&w=1920&q=80');"></div>
    
    <!-- 2. Dark Atmospheric Ambient Vignette -->
    <div class="fixed inset-0 bg-gradient-to-tr from-slate-950/90 via-slate-900/75 to-emerald-950/40 z-0 pointer-events-none"></div>

    <!-- 3. Moving Ambient Decorative Light Orbs (Animasi Bergerak) -->
    <div class="fixed -top-36 -left-36 w-96 h-96 bg-emerald-500/20 rounded-full blur-3xl animate-pulse-glow pointer-events-none z-0"></div>
    <div class="fixed -bottom-36 -right-36 w-96 h-96 bg-teal-500/15 rounded-full blur-3xl animate-float pointer-events-none z-0"></div>
    <div class="fixed top-1/2 left-1/3 w-80 h-80 bg-amber-500/10 rounded-full blur-3xl animate-float-reverse pointer-events-none z-0"></div>

    <!-- Sidebar (Sesuai Gambar 34 Skripsi: Wijaya, Dashboard, Kelola Pesanan, Daftar Item, Users, Kelola Role, Categories) -->
    <aside class="w-64 bg-slate-900/90 backdrop-blur-xl border-r border-white/10 flex flex-col flex-shrink-0 z-20 h-screen text-slate-200" style="width: 16rem; flex-shrink: 0; height: 100vh; max-height: 100vh; overflow: hidden;">
        <!-- Sidebar Header / Logo with Animated Barber Pole (Pinned Top) -->
        <div class="h-16 px-5 border-b border-white/10 flex items-center justify-between flex-shrink-0 bg-slate-900/80">
            <a href="/admin" class="flex items-center space-x-2.5 group">
                <span class="w-2.5 h-6 rounded-full barber-pole border border-slate-700 shadow-md"></span>
                <span class="text-xl font-black text-white tracking-tight">Wijaya</span>
                <span class="text-[10px] px-2 py-0.5 rounded-md bg-gradient-to-r from-emerald-600 to-teal-500 text-white font-extrabold uppercase tracking-wider shadow-sm">Staff</span>
            </a>
            <div class="flex items-center space-x-1">
                <button type="button" onclick="toggleAdminBgBrightness()" class="w-7 h-7 rounded-lg bg-white/10 hover:bg-emerald-600 text-amber-300 flex items-center justify-center transition" title="Kecerahan Background Studio">
                    <i class="fas fa-sun text-xs"></i>
                </button>
            </div>
        </div>

        <!-- Navigation Links (Scrollable Tengah: Bebas di-Scroll) -->
        <nav class="flex-1 min-h-0 overflow-y-auto p-3 space-y-1 text-[13px] font-semibold custom-scrollbar" style="flex: 1 1 0%; min-height: 0; overflow-y: auto;">
            <!-- Dashboard -->
            <a href="/admin" 
                class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition <?= ($currentUri === '/admin') ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white shadow-lg shadow-emerald-600/35 font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white' ?>">
                <i class="fas fa-th-large w-5 text-center text-sm"></i>
                <span>Dashboard</span>
            </a>

            <!-- Kelola Pesanan -->
            <a href="/admin/orders" 
                class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition <?= str_starts_with($currentUri, '/admin/orders') ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white shadow-lg shadow-emerald-600/35 font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white' ?>">
                <i class="fas fa-shopping-cart w-5 text-center text-sm"></i>
                <span>Kelola Pesanan</span>
            </a>

            <!-- Daftar Item -->
            <a href="/admin/items" 
                class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition <?= str_starts_with($currentUri, '/admin/items') ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white shadow-lg shadow-emerald-600/35 font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white' ?>">
                <i class="fas fa-scissors w-5 text-center text-sm"></i>
                <span>Daftar Item</span>
            </a>

            <!-- Users -->
            <a href="/admin/users" 
                class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition <?= str_starts_with($currentUri, '/admin/users') ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white shadow-lg shadow-emerald-600/35 font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white' ?>">
                <i class="fas fa-user-friends w-5 text-center text-sm"></i>
                <span>Users & Barber</span>
            </a>

            <!-- Kelola Role -->
            <a href="/admin/roles" 
                class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition <?= str_starts_with($currentUri, '/admin/roles') ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white shadow-lg shadow-emerald-600/35 font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white' ?>">
                <i class="fas fa-shield-alt w-5 text-center text-sm"></i>
                <span>Kelola Role</span>
            </a>

            <!-- Categories -->
            <a href="/admin/categories" 
                class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition <?= str_starts_with($currentUri, '/admin/categories') ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white shadow-lg shadow-emerald-600/35 font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white' ?>">
                <i class="fas fa-tags w-5 text-center text-sm"></i>
                <span>Categories</span>
            </a>

            <!-- Voucher & Promo -->
            <a href="/admin/vouchers" 
                class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition <?= str_starts_with($currentUri, '/admin/vouchers') ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white shadow-lg shadow-emerald-600/35 font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white' ?>">
                <i class="fas fa-ticket-alt w-5 text-center text-sm"></i>
                <span>Voucher Promo</span>
            </a>

            <!-- Loyalitas & Poin VIP -->
            <a href="/admin/loyalty" 
                class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition <?= str_starts_with($currentUri, '/admin/loyalty') ? 'bg-gradient-to-r from-emerald-600 to-emerald-500 text-white shadow-lg shadow-emerald-600/35 font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white' ?>">
                <i class="fas fa-crown w-5 text-center text-sm text-amber-400"></i>
                <span>Loyalitas & Poin</span>
            </a>

            <!-- Analitik Cerdas (AI) -->
            <a href="/admin/analytics" 
                class="flex items-center space-x-3 px-3.5 py-2.5 rounded-xl transition <?= str_starts_with($currentUri, '/admin/analytics') ? 'bg-gradient-to-r from-purple-700 to-indigo-600 text-white shadow-lg shadow-purple-600/35 font-bold' : 'text-slate-300 hover:bg-white/10 hover:text-white' ?>">
                <i class="fas fa-brain w-5 text-center text-sm text-purple-400"></i>
                <span>Analitik Cerdas</span>
            </a>
        </nav>

        <!-- Sidebar Footer / User Info (Pinned Bottom: Selalu Terlihat Penuh & Tidak Terpotong) -->
        <div class="p-3 border-t border-white/10 flex-shrink-0 bg-slate-900/95 backdrop-blur-md">
            <div class="p-2.5 bg-white/5 border border-white/10 rounded-xl flex items-center justify-between mb-2">
                <div class="flex items-center space-x-2.5 truncate">
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-tr from-emerald-600 to-teal-400 text-white font-black flex items-center justify-center flex-shrink-0 text-xs shadow-sm">
                        <?= substr($user['fullname'] ?? 'A', 0, 1) ?>
                    </div>
                    <div class="truncate">
                        <span class="block text-xs font-bold text-white truncate"><?= htmlspecialchars($user['fullname'] ?? 'Admin') ?></span>
                        <span class="block text-[10px] text-emerald-400 uppercase font-extrabold tracking-wider"><?= htmlspecialchars($user['role_name'] ?? 'Staff') ?></span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2 text-center text-xs">
                <a href="/" target="_blank" class="py-2 px-2 rounded-xl bg-white/10 hover:bg-white/20 text-slate-200 font-semibold transition flex items-center justify-center space-x-1 border border-white/5 shadow-sm">
                    <i class="fas fa-external-link-alt text-[10px]"></i>
                    <span>Web Tamu</span>
                </a>
                <a href="/logout" class="py-2 px-2 rounded-xl bg-rose-500/20 hover:bg-rose-500 text-rose-300 hover:text-white font-semibold transition flex items-center justify-center space-x-1 border border-rose-500/30 shadow-sm">
                    <i class="fas fa-sign-out-alt text-[10px]"></i>
                    <span>Keluar</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex-1 flex flex-col h-full overflow-hidden relative z-10" style="flex: 1 1 0%; min-width: 0; height: 100vh; display: flex; flex-direction: column;">
        
        <!-- Top Navbar with Frosted Glass & Live Radar Indicator -->
        <header class="h-20 bg-slate-900/80 backdrop-blur-xl border-b border-white/10 px-8 flex items-center justify-between flex-shrink-0 text-white relative z-10 shadow-sm" style="height: 5rem; flex-shrink: 0;">
            <div class="flex items-center space-x-4">
                <div class="sonar-radar-box">
                    <div class="sonar-sweep-hand"></div>
                    <div class="sonar-ring"></div>
                </div>
                <div>
                    <div class="flex items-center space-x-2">
                        <h2 class="text-xl font-black text-white tracking-tight"><?= htmlspecialchars($title ?? 'Dashboard') ?></h2>
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping" title="Sistem Aktif Real-Time"></span>
                    </div>
                    <p class="text-xs text-slate-400">Sistem Informasi Manajemen Wijaya Barbershop &bull; Kota Metro</p>
                </div>
            </div>
            
            <div class="flex items-center space-x-5">
                <!-- Soundwave Equalizer & Live Running Clock -->
                <div class="text-right hidden sm:flex items-center space-x-3">
                    <div class="eq-container">
                        <span class="eq-bar"></span>
                        <span class="eq-bar"></span>
                        <span class="eq-bar"></span>
                        <span class="eq-bar"></span>
                    </div>
                    <div>
                        <span id="live-admin-clock" class="text-xs font-black text-white block font-mono"><?= date('l, d F Y') ?></span>
                        <span class="text-[11px] text-emerald-400 font-semibold flex items-center justify-end space-x-1.5">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Sistem Aktif Real-Time (Live)</span>
                        </span>
                    </div>
                </div>
                
                <!-- Background Music Toggle Button in Admin Header -->
                <button type="button" onclick="toggleBarberMusic()" id="navbar-music-toggle" class="px-3.5 py-1.5 rounded-xl bg-white/10 hover:bg-emerald-600/30 text-emerald-400 border border-emerald-500/30 text-xs font-bold transition flex items-center space-x-1.5 shadow-sm btn-magnetic" title="Putar / Jeda Musik Barbershop">
                    <i class="fas fa-music text-xs text-emerald-400"></i>
                    <span id="navbar-music-label" class="hidden md:inline font-bold">Musik: OFF</span>
                </button>

                <!-- Web Tamu shortcut -->
                <a href="/" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-white/10 hover:bg-emerald-600 text-white text-xs font-bold transition flex items-center space-x-1.5 border border-white/10 shadow-sm btn-magnetic" title="Buka Halaman Tamu / Pelanggan">
                    <i class="fas fa-external-link-alt text-[10px]"></i>
                    <span class="hidden md:inline">Web Tamu</span>
                </a>
            </div>
        </header>

        <!-- Dynamic Body View -->
        <main class="flex-1 p-6 sm:p-8 overflow-y-auto relative z-10" style="flex: 1 1 0%; overflow-y: auto;">
            <!-- Flash Alert Messages -->
            <?php if (!empty($success)): ?>
            <div class="bg-emerald-500/20 border border-emerald-500/40 text-emerald-200 px-4 py-3 rounded-2xl flex items-center justify-between mb-6 shadow-lg backdrop-blur-md auto-dismiss">
                <div class="flex items-center space-x-2.5">
                    <i class="fas fa-check-circle text-emerald-400 text-lg"></i>
                    <span class="text-sm font-semibold"><?= htmlspecialchars($success) ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white">&times;</button>
            </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
            <div class="bg-rose-500/20 border border-rose-500/40 text-rose-200 px-4 py-3 rounded-2xl flex items-center justify-between mb-6 shadow-lg backdrop-blur-md auto-dismiss">
                <div class="flex items-center space-x-2.5">
                    <i class="fas fa-exclamation-triangle text-rose-400 text-lg"></i>
                    <span class="text-sm font-semibold"><?= htmlspecialchars($error) ?></span>
                </div>
                <button onclick="this.parentElement.remove()" class="text-rose-400 hover:text-white">&times;</button>
            </div>
            <?php endif; ?>

            <?= $content ?>
        </main>
    </div>

    <!-- ═══ ASISTEN PINTAR AI KHUSUS ADMIN / KASIR / DIREKTUR ═══ -->
    <div id="admin-ai-assistant" class="fixed bottom-6 right-6 z-50 no-print" style="font-family:'Plus Jakarta Sans',sans-serif;">
        <!-- Bubble Toggle Button -->
        <button id="admin-ai-toggle"
            onclick="toggleAdminAIChat()"
            class="px-4 py-3 rounded-2xl bg-gradient-to-tr from-purple-700 via-purple-600 to-indigo-600 hover:from-purple-600 hover:to-indigo-500 text-white flex items-center space-x-2 text-xs font-bold shadow-2xl hover:scale-105 transition-all duration-200 border border-purple-400/30 group"
            title="Asisten Pintar Staf / Admin">
            <i class="fas fa-robot text-sm group-hover:animate-bounce" id="admin-ai-icon"></i>
            <span class="hidden sm:inline">Asisten Pintar AI</span>
            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
        </button>

        <!-- Chat Window -->
        <div id="admin-ai-window"
            class="hidden absolute bottom-14 right-0 w-80 sm:w-96 bg-slate-900 border border-purple-500/40 rounded-3xl shadow-2xl overflow-hidden flex flex-col"
            style="height: 480px; display: none; flex-direction: column;">

            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-800 to-indigo-700 px-4 py-3.5 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center">
                        <i class="fas fa-brain text-white text-sm"></i>
                    </div>
                    <div>
                        <p class="text-white font-black text-sm">Asisten Pintar Staf</p>
                        <p class="text-purple-200 text-[10px] font-medium flex items-center space-x-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse inline-block"></span>
                            <span>Wijaya AI Management Engine</span>
                        </p>
                    </div>
                </div>
                <button onclick="toggleAdminAIChat()" class="text-white/70 hover:text-white text-lg transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Messages Area -->
            <div id="admin-ai-messages" class="flex-1 overflow-y-auto p-4 space-y-3" style="flex:1; overflow-y:auto;">
                <!-- Welcome message -->
                <div class="flex items-start space-x-2">
                    <div class="w-7 h-7 rounded-lg bg-purple-600/50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-robot text-purple-300 text-xs"></i>
                    </div>
                    <div class="bg-slate-800 rounded-2xl rounded-tl-sm px-3.5 py-2.5 max-w-[85%] border border-white/5">
                        <p class="text-white text-xs leading-relaxed">Halo Admin / Kasir! 👋 Saya <strong>Asisten Pintar</strong> Wijaya Barbershop siap membantu analisis pendapatan, rencana kedepannya, atau info operasional.</p>
                    </div>
                </div>
                <!-- Quick replies default -->
                <div id="admin-ai-quick-default" class="flex flex-wrap gap-1.5 pl-9">
                    <button onclick="sendAdminAIQuickReply('Rencana kedepannya')" class="chatbot-qr-btn">Rencana Kedepannya</button>
                    <button onclick="sendAdminAIQuickReply('Jam Buka')" class="chatbot-qr-btn">Jam Operasional</button>
                    <button onclick="sendAdminAIQuickReply('Promo hari ini')" class="chatbot-qr-btn">Promo & Diskon</button>
                    <button onclick="sendAdminAIQuickReply('Harga Layanan')" class="chatbot-qr-btn">Daftar Harga</button>
                </div>
            </div>

            <!-- Input Area -->
            <div class="flex-shrink-0 p-3 border-t border-white/10 bg-slate-900">
                <div class="flex items-center space-x-2">
                    <input type="text" id="admin-ai-input"
                        placeholder="Tanya asisten pintar..."
                        class="flex-1 bg-slate-800 text-white text-xs rounded-xl px-3 py-2.5 outline-none border border-white/10 focus:border-purple-500/50 placeholder-slate-500 transition"
                        onkeypress="if(event.key==='Enter') sendAdminAIChat()">
                    <button onclick="sendAdminAIChat()"
                        class="w-9 h-9 rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-500 text-white flex items-center justify-center hover:scale-105 transition flex-shrink-0">
                        <i class="fas fa-paper-plane text-xs"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .chatbot-qr-btn {
            font-size: 11px;
            padding: 4px 10px;
            border-radius: 20px;
            background: rgba(139,92,246,0.15);
            border: 1px solid rgba(139,92,246,0.35);
            color: #c4b5fd;
            font-weight: 700;
            cursor: pointer;
            transition: all .15s;
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .chatbot-qr-btn:hover { background: rgba(139,92,246,0.35); color: #fff; }
        #admin-ai-messages::-webkit-scrollbar { width: 4px; }
        #admin-ai-messages::-webkit-scrollbar-track { background: transparent; }
        #admin-ai-messages::-webkit-scrollbar-thumb { background: rgba(139,92,246,0.3); border-radius: 4px; }
    </style>

    <script>
    let adminAIOpen = false;
    let adminAITyping = false;

    function toggleAdminAIChat() {
        adminAIOpen = !adminAIOpen;
        const win = document.getElementById('admin-ai-window');
        const icon = document.getElementById('admin-ai-icon');
        if (adminAIOpen) {
            win.style.display = 'flex';
            win.classList.remove('hidden');
            if (icon) { icon.classList.remove('fa-robot'); icon.classList.add('fa-times'); }
            setTimeout(() => { document.getElementById('admin-ai-input').focus(); }, 100);
        } else {
            win.style.display = 'none';
            win.classList.add('hidden');
            if (icon) { icon.classList.remove('fa-times'); icon.classList.add('fa-robot'); }
        }
    }

    function sendAdminAIQuickReply(text) {
        document.getElementById('admin-ai-input').value = text;
        sendAdminAIChat();
    }

    function appendAdminAIMessage(text, isUser) {
        const container = document.getElementById('admin-ai-messages');
        const wrapper = document.createElement('div');
        wrapper.className = isUser ? 'flex justify-end' : 'flex items-start space-x-2';

        if (isUser) {
            wrapper.innerHTML = `<div class="bg-gradient-to-tr from-purple-700 to-indigo-600 rounded-2xl rounded-tr-sm px-3.5 py-2.5 max-w-[85%] border border-purple-500/30">
                <p class="text-white text-xs leading-relaxed">${escHtmlAdmin(text)}</p></div>`;
        } else {
            const formatted = escHtmlAdmin(text).replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
            wrapper.innerHTML = `<div class="w-7 h-7 rounded-lg bg-purple-600/50 flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="fas fa-robot text-purple-300 text-xs"></i></div>
                <div class="bg-slate-800 rounded-2xl rounded-tl-sm px-3.5 py-2.5 max-w-[85%] border border-white/5">
                <p class="text-white text-xs leading-relaxed">${formatted}</p></div>`;
        }
        container.appendChild(wrapper);
        container.scrollTop = container.scrollHeight;
        return wrapper;
    }

    function appendAdminAIQuickReplies(replies) {
        if (!replies || !replies.length) return;
        const container = document.getElementById('admin-ai-messages');
        const wrapper = document.createElement('div');
        wrapper.className = 'flex flex-wrap gap-1.5 pl-9';
        replies.forEach(r => {
            const btn = document.createElement('button');
            btn.className = 'chatbot-qr-btn';
            btn.textContent = r;
            btn.onclick = () => sendAdminAIQuickReply(r);
            wrapper.appendChild(btn);
        });
        container.appendChild(wrapper);
        container.scrollTop = container.scrollHeight;
    }

    function showAdminAITyping() {
        const container = document.getElementById('admin-ai-messages');
        const el = document.createElement('div');
        el.id = 'admin-ai-typing';
        el.className = 'flex items-start space-x-2';
        el.innerHTML = `<div class="w-7 h-7 rounded-lg bg-purple-600/50 flex items-center justify-center flex-shrink-0 mt-0.5">
            <i class="fas fa-robot text-purple-300 text-xs"></i></div>
            <div class="bg-slate-800 rounded-2xl rounded-tl-sm px-3.5 py-2.5">
            <div class="flex space-x-1"><span class="w-1.5 h-1.5 bg-purple-400 rounded-full animate-bounce" style="animation-delay:0s"></span>
            <span class="w-1.5 h-1.5 bg-purple-400 rounded-full animate-bounce" style="animation-delay:.15s"></span>
            <span class="w-1.5 h-1.5 bg-purple-400 rounded-full animate-bounce" style="animation-delay:.3s"></span></div></div>`;
        container.appendChild(el);
        container.scrollTop = container.scrollHeight;
    }

    function removeAdminAITyping() {
        const el = document.getElementById('admin-ai-typing');
        if (el) el.remove();
    }

    function escHtmlAdmin(s) {
        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function sendAdminAIChat() {
        if (adminAITyping) return;
        const input = document.getElementById('admin-ai-input');
        const message = (input.value || '').trim();
        if (!message) return;

        const defQR = document.getElementById('admin-ai-quick-default');
        if (defQR) defQR.remove();

        input.value = '';
        appendAdminAIMessage(message, true);
        showAdminAITyping();
        adminAITyping = true;

        fetch('/api/smart/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'message=' + encodeURIComponent(message)
        })
        .then(r => r.json())
        .then(data => {
            removeAdminAITyping();
            appendAdminAIMessage(data.reply || 'Maaf, ada kesalahan respon AI.', false);
            appendAdminAIQuickReplies(data.quick_replies || []);
        })
        .catch(() => {
            removeAdminAITyping();
            appendAdminAIMessage('Koneksi asisten bermasalah. Coba lagi ya!', false);
        })
        .finally(() => { adminAITyping = false; });
    }
    </script>

    <script src="/js/app.js"></script>
    <script>
        // Admin Background Brightness Controller
        let adminBgState = 0;
        function toggleAdminBgBrightness() {
            const bg = document.getElementById('admin-bg-image');
            if (!bg) return;
            adminBgState = (adminBgState + 1) % 3;
            if (adminBgState === 1) {
                bg.style.opacity = '0.45'; // Extra Jelas
            } else if (adminBgState === 2) {
                bg.style.opacity = '0.08'; // Sangat Halus
            } else {
                bg.style.opacity = '0.22'; // Normal Cantik
            }
        }
    </script>
</body>
</html>

