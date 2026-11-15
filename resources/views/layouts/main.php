<?php
use App\Core\Auth;
Auth::initSession();
$cartCount = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $c) {
        $cartCount += $c['quantity'];
    }
}
$currentUser = Auth::user();
$currentUri = $_SERVER['REQUEST_URI'] ?? '/';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Wijaya Barbershop - Modern Haircut & Grooming') ?></title>
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
    <!-- Font Awesome 6 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 flex flex-col min-h-screen relative selection:bg-emerald-500 selection:text-white">

    <!-- Interactive HTML5 Barbershop Particle Canvas (Ember & Stardust Engine) -->
    <canvas id="barber-particle-canvas" class="fixed inset-0 pointer-events-none z-0 opacity-75"></canvas>

    <!-- Top Animated Announcement Marquee Ticker -->
    <div class="bg-slate-950 text-slate-300 text-[11px] py-1.5 px-4 border-b border-slate-800/80 overflow-hidden marquee-wrapper z-50">
        <div class="marquee-content font-medium flex items-center space-x-8">
            <span class="flex items-center space-x-1.5 text-emerald-400 font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span>WIJAYA BARBERSHOP METRO</span>
            </span>
            <span>&bull;</span>
            <span class="flex items-center space-x-1 text-slate-300">
                <i class="fas fa-scissors text-emerald-400 text-xs"></i>
                <span>3 Capster Senior On-Duty Standby Melayani</span>
            </span>
            <span>&bull;</span>
            <span class="flex items-center space-x-1 text-amber-300 font-semibold">
                <i class="fas fa-motorcycle text-amber-400 text-xs"></i>
                <span>Fitur Baru: Layanan Cukur Panggilan (Home Service) ke Rumah / Kos / Kantor</span>
            </span>
            <span>&bull;</span>
            <span class="flex items-center space-x-1 text-slate-300">
                <i class="fas fa-qrcode text-emerald-400 text-xs"></i>
                <span>Bayar Instan QRIS & Notifikasi WhatsApp Otomatis</span>
            </span>
            <span>&bull;</span>
            <span class="flex items-center space-x-1 text-slate-400">
                <i class="fas fa-clock text-emerald-400 text-xs"></i>
                <span>Buka Setiap Hari 09.00 - 21.00 WIB</span>
            </span>
            <span>&bull;</span>
            <!-- Repeat for seamless marquee loop -->
            <span class="flex items-center space-x-1.5 text-emerald-400 font-bold">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span>WIJAYA BARBERSHOP METRO</span>
            </span>
            <span>&bull;</span>
            <span class="flex items-center space-x-1 text-slate-300">
                <i class="fas fa-scissors text-emerald-400 text-xs"></i>
                <span>3 Capster Senior On-Duty Standby Melayani</span>
            </span>
            <span>&bull;</span>
            <span class="flex items-center space-x-1 text-amber-300 font-semibold">
                <i class="fas fa-motorcycle text-amber-400 text-xs"></i>
                <span>Fitur Baru: Layanan Cukur Panggilan (Home Service) ke Rumah</span>
            </span>
        </div>
    </div>

    <!-- Top Navigation Bar (Sesuai Gambar 33 Skripsi + Efek Glass & Barber Pole) -->
    <header class="bg-slate-900 border-b border-slate-800/90 sticky top-0 z-40 text-white transition-all duration-300">
        <div class="w-full max-w-[1536px] mx-auto px-3 sm:px-5 lg:px-6">
            <div class="flex items-center justify-between h-20 gap-2 lg:gap-4">
                <!-- Brand / Logo with Animated Barber Pole & Snipping Shears -->
                <a href="/" class="flex items-center space-x-2.5 sm:space-x-3.5 group flex-shrink-0">
                    <div class="relative w-11 h-11 sm:w-12 sm:h-12 rounded-2xl bg-gradient-to-tr from-emerald-600 via-emerald-500 to-teal-400 flex items-center justify-center text-white shadow-lg shadow-emerald-500/35 group-hover:scale-105 transition duration-300 scissor-snip-container flex-shrink-0">
                        <!-- Dual Blade Animated SVG Scissors -->
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 text-white transform -rotate-45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="6" cy="6" r="3"/>
                            <path class="scissor-top" d="M8.12 8.12 12 12l7.5-7.5"/>
                            <circle cx="6" cy="18" r="3"/>
                            <path class="scissor-bottom" d="M8.12 15.88 12 12l7.5 7.5"/>
                        </svg>
                        <!-- Animated Barber Pole Indicator Pill -->
                        <span class="absolute -bottom-1 -right-1 w-3.5 h-6 rounded-full barber-pole border border-slate-900 shadow-md"></span>
                    </div>
                    <div>
                        <div class="flex items-center space-x-1.5 sm:space-x-2">
                            <span class="text-lg sm:text-xl font-black tracking-tight text-white block group-hover:text-emerald-400 transition whitespace-nowrap">
                                Wijaya <span class="text-emerald-400">Barber</span>
                            </span>
                            <!-- Lounge Soundwave Equalizer (Tampil di Layar Lebar) -->
                            <div class="eq-container hidden 2xl:inline-flex" title="Lounge Atmosfir">
                                <span class="eq-bar"></span>
                                <span class="eq-bar"></span>
                                <span class="eq-bar"></span>
                                <span class="eq-bar"></span>
                            </div>
                        </div>
                        <span class="text-[9px] sm:text-[10px] uppercase tracking-widest text-emerald-400/90 font-bold block -mt-0.5 whitespace-nowrap">
                            Modern & Grooming &bull; Metro
                        </span>
                    </div>
                </a>

                <!-- Desktop Nav Links (Spacious, Rapih & Bebas Menumpuk di Layar Luas) -->
                <nav class="hidden xl:flex items-center space-x-1.5 2xl:space-x-2.5 text-xs 2xl:text-[13px] font-semibold text-slate-300 flex-shrink-0">
                    <a href="/" class="whitespace-nowrap px-2.5 py-1.5 rounded-xl hover:bg-white/5 hover:text-emerald-400 transition flex items-center space-x-1.5 <?= ($currentUri === '/' || $currentUri === '') ? 'text-emerald-400 font-bold bg-white/5' : '' ?>">
                        <i class="fas fa-home text-xs"></i>
                        <span>Home</span>
                    </a>
                    <a href="/services" class="whitespace-nowrap px-2.5 py-1.5 rounded-xl hover:bg-white/5 hover:text-emerald-400 transition flex items-center space-x-1.5 <?= str_starts_with($currentUri, '/services') ? 'text-emerald-400 font-bold bg-white/5' : '' ?>">
                        <i class="fas fa-cut text-xs"></i>
                        <span>Layanan</span>
                    </a>
                    <a href="/home-service" class="whitespace-nowrap px-2.5 py-1.5 rounded-xl hover:bg-amber-500/10 hover:text-amber-300 transition flex items-center space-x-1.5 text-amber-300 font-bold <?= str_starts_with($currentUri, '/home-service') ? 'bg-amber-500/15 font-black text-amber-400' : '' ?>">
                        <i class="fas fa-motorcycle text-xs"></i>
                        <span>Home Service</span>
                    </a>
                    <a href="/promotions" class="whitespace-nowrap px-2.5 py-1.5 rounded-xl hover:bg-white/5 hover:text-emerald-400 transition flex items-center space-x-1.5 <?= str_starts_with($currentUri, '/promotions') ? 'text-emerald-400 font-bold bg-white/5' : '' ?>">
                        <i class="fas fa-tags text-xs text-emerald-400"></i>
                        <span>Promo</span>
                    </a>
                    <a href="/membership" class="whitespace-nowrap px-2.5 py-1.5 rounded-xl hover:bg-amber-500/10 hover:text-amber-300 transition flex items-center space-x-1.5 text-amber-300/90 font-bold <?= str_starts_with($currentUri, '/membership') ? 'bg-amber-500/15 font-black text-amber-400' : '' ?>">
                        <i class="fas fa-crown text-xs text-amber-400"></i>
                        <span>VIP & Poin</span>
                    </a>
                    <a href="/smart" class="whitespace-nowrap px-2.5 py-1.5 rounded-xl hover:bg-purple-500/10 hover:text-purple-300 transition flex items-center space-x-1.5 text-purple-300/90 font-bold <?= str_starts_with($currentUri, '/smart') ? 'bg-purple-500/15 font-black text-purple-300' : '' ?>">
                        <i class="fas fa-robot text-xs text-purple-400"></i>
                        <span>Asisten Pintar</span>
                    </a>
                    <?php if (Auth::check()): ?>
                    <a href="/my-orders" class="whitespace-nowrap px-2.5 py-1.5 rounded-xl hover:bg-white/5 hover:text-emerald-400 transition flex items-center space-x-1.5 <?= str_starts_with($currentUri, '/my-orders') ? 'text-emerald-400 font-bold bg-white/5' : '' ?>">
                        <i class="fas fa-receipt text-xs"></i>
                        <span>Pesanan Saya</span>
                    </a>
                    <?php endif; ?>
                </nav>

                <!-- Actions: Cart & Profile / Login & Mobile Toggle -->
                <div class="flex items-center space-x-2 sm:space-x-3 flex-shrink-0">
                    <!-- Background Music Toggle Button in Navbar (Wijaya Lounge Music) -->
                    <button type="button" onclick="toggleBarberMusic()" id="navbar-music-toggle" class="relative flex items-center space-x-1.5 px-2.5 py-2 rounded-2xl bg-slate-800/80 hover:bg-emerald-600/25 text-emerald-400 border border-emerald-500/30 text-xs font-bold transition shadow-inner btn-magnetic whitespace-nowrap" title="Putar / Jeda Musik Santai Barbershop">
                        <i class="fas fa-music text-xs text-emerald-400"></i>
                        <span id="navbar-music-label" class="hidden 2xl:inline text-[11px] font-black">Musik: OFF</span>
                    </button>

                    <!-- Sound FX Audio Demo & Preview (Tampil khusus layar ultra lebar 2XL agar tidak menumpuk) -->
                    <button type="button" onclick="toggleAudioPreview(this)" class="relative hidden 2xl:flex p-2.5 rounded-2xl bg-slate-800/80 hover:bg-emerald-600/30 text-emerald-400 hover:text-emerald-300 border border-emerald-500/30 transition shadow-inner hover:scale-105 btn-magnetic" title="Tes Efek Suara Pesanan & Transaksi">
                        <i class="fas fa-volume-up text-sm"></i>
                        <span class="absolute -bottom-1 -right-1 w-2.5 h-2.5 rounded-full bg-emerald-400 border-2 border-slate-900 animate-pulse"></span>
                    </button>

                    <!-- Cart Icon with badge -->
                    <a href="/cart" class="relative p-2 sm:p-2.5 rounded-2xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 transition shadow-inner hover:scale-105" title="Keranjang Pesanan">
                        <i class="fas fa-shopping-bag text-base sm:text-lg"></i>
                        <?php if ($cartCount > 0): ?>
                        <span class="absolute -top-1.5 -right-1.5 bg-emerald-500 text-white font-black text-xs w-5 h-5 rounded-full flex items-center justify-center ring-2 ring-slate-900 animate-pulse">
                            <?= $cartCount ?>
                        </span>
                        <?php endif; ?>
                    </a>

                    <!-- User Auth Button -->
                    <?php if (Auth::check()): ?>
                    <div class="relative group" id="user-dropdown-wrapper">
                        <button type="button" onclick="const m=document.getElementById('user-dropdown-menu'); if(m) m.classList.toggle('hidden');" class="flex items-center space-x-2 bg-slate-800/80 hover:bg-slate-700 px-2.5 sm:px-3.5 py-2 rounded-2xl transition text-xs sm:text-sm font-medium border border-slate-700/80 shadow-sm cursor-pointer">
                            <div class="w-6 h-6 sm:w-7 sm:h-7 rounded-xl bg-gradient-to-tr from-emerald-600 to-emerald-400 text-white flex items-center justify-center font-bold text-xs uppercase shadow-sm">
                                <?= substr($currentUser['fullname'], 0, 1) ?>
                            </div>
                            <span class="max-w-[70px] sm:max-w-[100px] xl:max-w-[120px] truncate text-slate-200 font-bold whitespace-nowrap"><?= htmlspecialchars($currentUser['fullname']) ?></span>
                            <i class="fas fa-chevron-down text-[10px] text-slate-400 group-hover:rotate-180 transition duration-200"></i>
                        </button>
                        <div id="user-dropdown-menu" class="absolute right-0 top-full pt-2 w-64 hidden group-hover:block z-50 animate-fade-in">
                            <div class="bg-white rounded-2xl shadow-2xl py-2 border border-slate-100 text-slate-800">
                                <div class="px-4 py-2.5 border-b border-slate-100">
                                    <p class="text-[11px] text-slate-400">Masuk sebagai:</p>
                                    <p class="text-sm font-bold text-slate-800 truncate"><?= htmlspecialchars($currentUser['fullname']) ?></p>
                                    <span class="inline-block mt-1 text-[10px] font-bold uppercase px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded-md">
                                        <?= htmlspecialchars($currentUser['role_name'] ?? 'Pelanggan') ?>
                                    </span>
                                </div>
                                <?php if (Auth::isStaff()): ?>
                                <a href="/admin" class="flex items-center space-x-2 px-4 py-2 text-sm text-emerald-600 hover:bg-emerald-50 font-bold transition">
                                    <i class="fas fa-tachometer-alt text-xs w-4"></i>
                                    <span>Dashboard Staf</span>
                                </a>
                                <?php endif; ?>
                                <a href="/membership" class="flex items-center space-x-2 px-4 py-2 text-sm hover:bg-slate-50 font-bold transition text-amber-600">
                                    <i class="fas fa-crown text-xs text-amber-500 w-4"></i>
                                    <span>VIP Club & Poin Saya</span>
                                </a>
                                <a href="/promotions" class="flex items-center space-x-2 px-4 py-2 text-sm hover:bg-slate-50 font-semibold transition">
                                    <i class="fas fa-tags text-xs text-slate-400 w-4"></i>
                                    <span>Kupon Promo Diskon</span>
                                </a>
                                <a href="/my-orders" class="flex items-center space-x-2 px-4 py-2 text-sm hover:bg-slate-50 font-semibold transition">
                                    <i class="fas fa-list-ul text-xs text-slate-400 w-4"></i>
                                    <span>Pesanan Saya</span>
                                </a>
                                <hr class="my-1 border-slate-100">
                                <a href="/logout" class="flex items-center space-x-2 px-4 py-2.5 text-sm text-rose-600 hover:bg-rose-50 font-bold transition">
                                    <i class="fas fa-sign-out-alt text-xs w-4"></i>
                                    <span>Keluar (Logout)</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php else: ?>
                    <a href="/login" class="inline-flex items-center space-x-1.5 sm:space-x-2 bg-gradient-to-r from-emerald-600 to-emerald-500 hover:from-emerald-500 hover:to-emerald-400 text-white font-extrabold text-xs sm:text-sm px-3.5 sm:px-5 py-2.5 rounded-2xl transition shadow-lg shadow-emerald-600/25 shimmer-btn whitespace-nowrap">
                        <i class="fas fa-user-circle"></i>
                        <span>Masuk</span>
                    </a>
                    <?php endif; ?>

                    <!-- Mobile / Tablet Hamburger Toggle Button (Tampil di Layar di bawah XL) -->
                    <button type="button" onclick="toggleMobileNav()" class="xl:hidden p-2.5 rounded-2xl bg-slate-800/80 hover:bg-slate-700 text-slate-200 border border-slate-700/80 transition" title="Menu Navigasi">
                        <i class="fas fa-bars text-lg" id="mobile-menu-icon"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile / Tablet Nav Drawer (Ketika Layar Lebih Kecil dari XL) -->
            <div id="mobile-nav-menu" class="hidden xl:hidden pb-4 pt-3 border-t border-slate-800 text-sm space-y-1.5 animate-fade-in">
                <a href="/" class="flex items-center space-x-2.5 px-3.5 py-2.5 rounded-xl hover:bg-white/5 transition <?= ($currentUri === '/' || $currentUri === '') ? 'text-emerald-400 font-bold bg-white/5' : 'text-slate-300' ?>">
                    <i class="fas fa-home w-5 text-center text-xs"></i>
                    <span>Home</span>
                </a>
                <a href="/services" class="flex items-center space-x-2.5 px-3.5 py-2.5 rounded-xl hover:bg-white/5 transition <?= str_starts_with($currentUri, '/services') ? 'text-emerald-400 font-bold bg-white/5' : 'text-slate-300' ?>">
                    <i class="fas fa-cut w-5 text-center text-xs"></i>
                    <span>Layanan</span>
                </a>
                <a href="/home-service" class="flex items-center space-x-2.5 px-3.5 py-2.5 rounded-xl hover:bg-amber-500/10 text-amber-300 font-bold transition <?= str_starts_with($currentUri, '/home-service') ? 'bg-amber-500/15 text-amber-400' : '' ?>">
                    <i class="fas fa-motorcycle w-5 text-center text-xs"></i>
                    <span>Home Service (Cukur Panggilan)</span>
                </a>
                <a href="/promotions" class="flex items-center space-x-2.5 px-3.5 py-2.5 rounded-xl hover:bg-white/5 transition <?= str_starts_with($currentUri, '/promotions') ? 'text-emerald-400 font-bold bg-white/5' : 'text-slate-300' ?>">
                    <i class="fas fa-tags w-5 text-center text-xs text-emerald-400"></i>
                    <span>Promo & Voucher</span>
                </a>
                <a href="/membership" class="flex items-center space-x-2.5 px-3.5 py-2.5 rounded-xl hover:bg-amber-500/10 text-amber-300 font-bold transition <?= str_starts_with($currentUri, '/membership') ? 'bg-amber-500/15 text-amber-400' : '' ?>">
                    <i class="fas fa-crown w-5 text-center text-xs text-amber-400"></i>
                    <span>VIP Club & Poin Loyalitas</span>
                </a>
                <a href="/smart" class="flex items-center space-x-2.5 px-3.5 py-2.5 rounded-xl hover:bg-purple-500/10 text-purple-300 font-bold transition <?= str_starts_with($currentUri, '/smart') ? 'bg-purple-500/15 text-purple-300' : '' ?>">
                    <i class="fas fa-robot w-5 text-center text-xs text-purple-400"></i>
                    <span>Asisten Pintar AI</span>
                </a>
                <?php if (Auth::check()): ?>
                <a href="/my-orders" class="flex items-center space-x-2.5 px-3.5 py-2.5 rounded-xl hover:bg-white/5 transition <?= str_starts_with($currentUri, '/my-orders') ? 'text-emerald-400 font-bold bg-white/5' : 'text-slate-300' ?>">
                    <i class="fas fa-receipt w-5 text-center text-xs"></i>
                    <span>Pesanan Saya</span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <script>
    function toggleMobileNav() {
        const menu = document.getElementById('mobile-nav-menu');
        const icon = document.getElementById('mobile-menu-icon');
        if (menu) {
            menu.classList.toggle('hidden');
            if (icon) {
                if (menu.classList.contains('hidden')) {
                    icon.classList.remove('fa-times');
                    icon.classList.add('fa-bars');
                } else {
                    icon.classList.remove('fa-bars');
                    icon.classList.add('fa-times');
                }
            }
        }
    }
    </script>
        </div>
    </header>

    <!-- Flash Alert Messages -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4 w-full">
        <?php if (!empty($success)): ?>
        <div class="bg-emerald-50/90 border border-emerald-200 text-emerald-800 px-5 py-3.5 rounded-2xl flex items-center justify-between mb-4 shadow-sm auto-dismiss backdrop-blur-sm">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-sm font-bold shadow-sm">
                    <i class="fas fa-check"></i>
                </div>
                <span class="text-sm font-bold"><?= htmlspecialchars($success) ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-800 text-xl font-bold">&times;</button>
        </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
        <div class="bg-rose-50/90 border border-rose-200 text-rose-800 px-5 py-3.5 rounded-2xl flex items-center justify-between mb-4 shadow-sm auto-dismiss backdrop-blur-sm">
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center text-sm font-bold shadow-sm">
                    <i class="fas fa-exclamation"></i>
                </div>
                <span class="text-sm font-bold"><?= htmlspecialchars($error) ?></span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-rose-500 hover:text-rose-800 text-xl font-bold">&times;</button>
        </div>
        <?php endif; ?>
    </div>

    <!-- Main Content Area -->
    <main class="flex-grow">
        <?= $content ?>
    </main>

    <!-- Floating Quick Action Speed Dial Widget (Pojok Kanan Bawah) -->
    <div class="fixed bottom-6 right-6 z-50 flex flex-col items-end space-y-3 no-print">
        <!-- Expanded Menu Options -->
        <div id="floating-dial-menu" class="hidden flex flex-col space-y-2.5 items-end mb-1 transition-all duration-300">
            <a href="https://wa.me/6281278412312?text=Halo%20Wijaya%20Barbershop,%20saya%20ingin%20tanya%20informasi%20layanan%20cukur" target="_blank"
                class="px-4 py-2.5 rounded-2xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-xl flex items-center space-x-2 hover:scale-105 transition">
                <i class="fab fa-whatsapp text-sm"></i>
                <span>Chat CS WhatsApp</span>
            </a>
            <a href="/home-service"
                class="px-4 py-2.5 rounded-2xl bg-amber-500 hover:bg-amber-400 text-slate-950 text-xs font-black shadow-xl flex items-center space-x-2 hover:scale-105 transition">
                <i class="fas fa-motorcycle text-sm"></i>
                <span>Cukur Panggilan (Home Service)</span>
            </a>
            <a href="/services"
                class="px-4 py-2.5 rounded-2xl bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold shadow-xl flex items-center space-x-2 hover:scale-105 transition border border-slate-700">
                <i class="fas fa-calendar-check text-emerald-400 text-sm"></i>
                <span>Booking Antrean Studio</span>
            </a>
        </div>

        <!-- Main Trigger Button with Pulse Beacon -->
        <button id="floating-dial-toggle" aria-label="Quick Actions"
            class="w-14 h-14 rounded-full bg-gradient-to-tr from-emerald-600 to-emerald-400 text-white flex items-center justify-center text-xl font-bold floating-dial-btn beacon-pulse focus:outline-none">
            <i class="fas fa-scissors transition-transform duration-300"></i>
        </button>
    </div>

    <!-- Footer Modern -->
    <footer id="kontak" class="bg-slate-950 border-t border-slate-800 text-slate-400 py-14 mt-20 relative overflow-hidden">
        <!-- Ambient decorative glow in footer -->
        <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-96 h-96 bg-emerald-600/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-10">
                <div class="md:col-span-2">
                    <div class="flex items-center space-x-3 mb-4">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-600 to-emerald-400 flex items-center justify-center text-white shadow-md">
                            <i class="fas fa-scissors"></i>
                        </div>
                        <span class="text-xl font-black text-white tracking-tight">Wijaya <span class="text-emerald-400">Barbershop</span></span>
                    </div>
                    <p class="text-sm text-slate-400 leading-relaxed mb-5 max-w-md">
                        Sistem Informasi Pemesanan & Pembayaran Jasa Pangkas Rambut Berbasis Web modern di Kota Metro. Dilengkapi antrean digital, pembayaran instan QRIS, serta layanan Cukur Panggilan langsung ke lokasi pelanggan.
                    </p>
                    <div class="flex space-x-3 text-slate-300">
                        <a href="#" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center hover:text-emerald-400 hover:border-emerald-500/50 transition"><i class="fab fa-instagram"></i></a>
                        <a href="https://wa.me/6281278412312" target="_blank" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center hover:text-emerald-400 hover:border-emerald-500/50 transition"><i class="fab fa-whatsapp"></i></a>
                        <a href="#" class="w-9 h-9 rounded-xl bg-slate-900 border border-slate-800 flex items-center justify-center hover:text-emerald-400 hover:border-emerald-500/50 transition"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>

                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4 flex items-center space-x-2">
                        <i class="fas fa-clock text-emerald-400 text-xs"></i>
                        <span>Jam Operasional</span>
                    </h4>
                    <ul class="text-sm space-y-2.5 text-slate-400">
                        <li class="flex justify-between"><span>Senin - Sabtu:</span> <span class="font-bold text-slate-200">09:00 - 21:00</span></li>
                        <li class="flex justify-between"><span>Minggu:</span> <span class="font-bold text-slate-200">10:00 - 20:00</span></li>
                        <li class="text-xs text-emerald-400 mt-2 font-semibold flex items-center space-x-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                            <span>Buka Setiap Hari (Melayani Studio & Panggilan)</span>
                        </li>
                    </ul>
                </div>

                <div>
                    <h4 class="text-white font-bold text-sm uppercase tracking-wider mb-4 flex items-center space-x-2">
                        <i class="fas fa-map-marker-alt text-emerald-400 text-xs"></i>
                        <span>Lokasi & Kontak</span>
                    </h4>
                    <p class="text-sm text-slate-400 leading-relaxed mb-2.5">
                        <i class="fas fa-location-dot text-emerald-400 mr-2"></i> Kota Metro, Lampung
                    </p>
                    <p class="text-sm text-slate-400 mb-2.5">
                        <i class="fab fa-whatsapp text-emerald-400 mr-2"></i> +62 812-7841-2312
                    </p>
                    <p class="text-sm text-slate-400">
                        <i class="fas fa-envelope text-emerald-400 mr-2"></i> kontak@wijayabarber.com
                    </p>
                </div>
            </div>

            <div class="pt-8 border-t border-slate-800 text-center text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
                <p>&copy; <?= date('Y') ?> Wijaya Barbershop. Rancang Bangun Sistem Informasi Pemesanan & Pembayaran Berbasis Web.</p>
                <p class="font-semibold text-slate-400">Skripsi Ilmu Komputer &bull; Universitas Muhammadiyah Metro</p>
            </div>
        </div>
    </footer>

    <!-- ═══ FLOATING CHATBOT WIDGET (Asisten Pintar AI) ═══ -->
    <div id="chatbot-widget" class="fixed bottom-24 right-6 z-[9999] no-print" style="font-family:'Plus Jakarta Sans',sans-serif;">
        <!-- Bubble Toggle Button -->
        <button id="chatbot-toggle"
            onclick="toggleChatbot()"
            class="w-14 h-14 rounded-full bg-gradient-to-tr from-purple-600 to-indigo-500 text-white flex items-center justify-center text-xl shadow-2xl hover:scale-110 transition-transform duration-200 relative"
            title="Asisten Pintar Wijaya Barbershop">
            <i class="fas fa-robot" id="chatbot-icon"></i>
            <span class="absolute -top-1 -right-1 w-3.5 h-3.5 rounded-full bg-emerald-400 border-2 border-slate-900 animate-pulse"></span>
        </button>

        <!-- Chat Window -->
        <div id="chatbot-window"
            class="hidden absolute bottom-16 right-0 w-80 sm:w-96 bg-slate-900 border border-purple-500/30 rounded-3xl shadow-2xl overflow-hidden flex flex-col"
            style="height: 480px; display: none; flex-direction: column;">

            <!-- Header -->
            <div class="bg-gradient-to-r from-purple-700 to-indigo-600 px-4 py-3.5 flex items-center justify-between flex-shrink-0">
                <div class="flex items-center space-x-2.5">
                    <div class="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center">
                        <i class="fas fa-robot text-white text-sm"></i>
                    </div>
                    <div>
                        <p class="text-white font-black text-sm">Asisten Pintar</p>
                        <p class="text-purple-200 text-[10px] font-medium flex items-center space-x-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse inline-block"></span>
                            <span>Wijaya Barbershop AI</span>
                        </p>
                    </div>
                </div>
                <button onclick="toggleChatbot()" class="text-white/70 hover:text-white text-lg transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <!-- Messages Area -->
            <div id="chatbot-messages" class="flex-1 overflow-y-auto p-4 space-y-3" style="flex:1; overflow-y:auto;">
                <!-- Welcome message -->
                <div class="flex items-start space-x-2">
                    <div class="w-7 h-7 rounded-lg bg-purple-600/50 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas fa-robot text-purple-300 text-xs"></i>
                    </div>
                    <div class="bg-slate-800 rounded-2xl rounded-tl-sm px-3.5 py-2.5 max-w-[85%]">
                        <p class="text-white text-xs leading-relaxed">Halo! 👋 Saya Asisten Pintar Wijaya Barbershop.<br>Ada yang bisa saya bantu?</p>
                    </div>
                </div>
                <!-- Quick replies default -->
                <div id="chatbot-quick-default" class="flex flex-wrap gap-1.5 pl-9">
                    <button onclick="sendQuickReply('Rencana kedepannya')" class="chatbot-qr-btn">Rencana Kedepannya</button>
                    <button onclick="sendQuickReply('Jam Buka')" class="chatbot-qr-btn">Jam Buka</button>
                    <button onclick="sendQuickReply('Harga Layanan')" class="chatbot-qr-btn">Harga</button>
                    <button onclick="sendQuickReply('Promo diskon')" class="chatbot-qr-btn">Promo</button>
                </div>
            </div>

            <!-- Input Area -->
            <div class="flex-shrink-0 p-3 border-t border-white/10 bg-slate-900">
                <div class="flex items-center space-x-2">
                    <input type="text" id="chatbot-input"
                        placeholder="Ketik pertanyaan Anda..."
                        class="flex-1 bg-slate-800 text-white text-xs rounded-xl px-3 py-2.5 outline-none border border-white/10 focus:border-purple-500/50 placeholder-slate-500 transition"
                        onkeypress="if(event.key==='Enter') sendChatMessage()">
                    <button onclick="sendChatMessage()"
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
        #chatbot-messages::-webkit-scrollbar { width: 4px; }
        #chatbot-messages::-webkit-scrollbar-track { background: transparent; }
        #chatbot-messages::-webkit-scrollbar-thumb { background: rgba(139,92,246,0.3); border-radius: 4px; }
    </style>

    <script>
    let chatbotOpen = false;
    let chatTyping = false;

    function toggleChatbot() {
        chatbotOpen = !chatbotOpen;
        const win = document.getElementById('chatbot-window');
        const icon = document.getElementById('chatbot-icon');
        if (chatbotOpen) {
            win.style.display = 'flex';
            win.classList.remove('hidden');
            if (icon) { icon.classList.remove('fa-robot'); icon.classList.add('fa-times'); }
            setTimeout(() => { document.getElementById('chatbot-input').focus(); }, 100);
        } else {
            win.style.display = 'none';
            win.classList.add('hidden');
            if (icon) { icon.classList.remove('fa-times'); icon.classList.add('fa-robot'); }
        }
    }

    function sendQuickReply(text) {
        document.getElementById('chatbot-input').value = text;
        sendChatMessage();
    }

    function appendMessage(text, isUser) {
        const container = document.getElementById('chatbot-messages');
        const wrapper = document.createElement('div');
        wrapper.className = isUser ? 'flex justify-end' : 'flex items-start space-x-2';

        if (isUser) {
            wrapper.innerHTML = `<div class="bg-gradient-to-tr from-purple-600 to-indigo-500 rounded-2xl rounded-tr-sm px-3.5 py-2.5 max-w-[85%]">
                <p class="text-white text-xs leading-relaxed">${escHtml(text)}</p></div>`;
        } else {
            const formatted = escHtml(text).replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>').replace(/\n/g, '<br>');
            wrapper.innerHTML = `<div class="w-7 h-7 rounded-lg bg-purple-600/50 flex items-center justify-center flex-shrink-0 mt-0.5">
                <i class="fas fa-robot text-purple-300 text-xs"></i></div>
                <div class="bg-slate-800 rounded-2xl rounded-tl-sm px-3.5 py-2.5 max-w-[85%]">
                <p class="text-white text-xs leading-relaxed">${formatted}</p></div>`;
        }
        container.appendChild(wrapper);
        container.scrollTop = container.scrollHeight;
        return wrapper;
    }

    function appendQuickReplies(replies) {
        if (!replies || !replies.length) return;
        const container = document.getElementById('chatbot-messages');
        const wrapper = document.createElement('div');
        wrapper.className = 'flex flex-wrap gap-1.5 pl-9';
        replies.forEach(r => {
            const btn = document.createElement('button');
            btn.className = 'chatbot-qr-btn';
            btn.textContent = r;
            btn.onclick = () => sendQuickReply(r);
            wrapper.appendChild(btn);
        });
        container.appendChild(wrapper);
        container.scrollTop = container.scrollHeight;
    }

    function showTyping() {
        const container = document.getElementById('chatbot-messages');
        const el = document.createElement('div');
        el.id = 'chatbot-typing';
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

    function removeTyping() {
        const el = document.getElementById('chatbot-typing');
        if (el) el.remove();
    }

    function escHtml(s) {
        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    function sendChatMessage() {
        if (chatTyping) return;
        const input = document.getElementById('chatbot-input');
        const message = (input.value || '').trim();
        if (!message) return;

        const defQR = document.getElementById('chatbot-quick-default');
        if (defQR) defQR.remove();

        input.value = '';
        appendMessage(message, true);
        showTyping();
        chatTyping = true;

        fetch('/api/smart/chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'message=' + encodeURIComponent(message)
        })
        .then(r => r.json())
        .then(data => {
            removeTyping();
            appendMessage(data.reply || 'Maaf, ada kesalahan.', false);
            appendQuickReplies(data.quick_replies || []);
        })
        .catch(() => {
            removeTyping();
            appendMessage('Maaf, koneksi bermasalah. Coba lagi ya! 🙏', false);
        })
        .finally(() => { chatTyping = false; });
    }
    </script>

    <!-- App JS with Animation Engine -->
    <script src="/js/app.js"></script>
</body>
</html>
