<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'Autentikasi - Wijaya Barbershop') ?></title>
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
<body class="relative min-h-screen bg-slate-950 flex items-center justify-center p-4 sm:p-6 lg:p-8 overflow-x-hidden">

    <!-- Interactive HTML5 Barbershop Particle Canvas (Ember & Stardust Engine) -->
    <canvas id="barber-particle-canvas" class="fixed inset-0 pointer-events-none z-0 opacity-80"></canvas>

    <!-- 1. Authentic Barbershop Studio Interior Background (Sama dengan Web Utama) -->
    <div id="auth-bg-image" class="fixed inset-0 z-0 bg-cover bg-center opacity-45 scale-105 transition-transform duration-1000" style="background-image: url('https://images.unsplash.com/photo-1585747860715-2ba37e788b70?auto=format&fit=crop&w=1920&q=80');"></div>
    
    <!-- 2. Balanced Dark Vignette Gradient -->
    <div class="fixed inset-0 bg-gradient-to-tr from-slate-950/95 via-slate-950/80 to-emerald-950/40 z-0 pointer-events-none"></div>
    <div class="fixed inset-0 bg-gradient-to-b from-slate-950/70 via-transparent to-slate-950/90 z-0 pointer-events-none"></div>

    <!-- 3. Moving Ambient Decorative Light Orbs (Animasi Bergerak) -->
    <div class="fixed -top-32 -left-32 w-96 h-96 bg-emerald-500/25 rounded-full blur-3xl animate-pulse-glow pointer-events-none z-0"></div>
    <div class="fixed -bottom-32 -right-32 w-96 h-96 bg-teal-500/20 rounded-full blur-3xl animate-float pointer-events-none z-0"></div>
    <div class="fixed top-1/2 left-1/4 -translate-y-1/2 w-80 h-80 bg-amber-500/15 rounded-full blur-3xl animate-float-reverse pointer-events-none z-0"></div>

    <!-- 4. Location & Atmosphere Pill Top Bar -->
    <div class="fixed top-4 left-6 z-20 hidden sm:flex items-center space-x-3">
        <a href="/" class="flex items-center space-x-2 px-4 py-1.5 rounded-full bg-slate-900/80 hover:bg-emerald-600 text-white text-xs font-bold border border-white/10 backdrop-blur-md transition shadow-lg group btn-magnetic">
            <i class="fas fa-arrow-left text-[10px] group-hover:-translate-x-1 transition"></i>
            <span>Kembali ke Beranda Utama</span>
        </a>
    </div>

    <!-- Music Button on Auth Pages -->
    <div class="fixed top-4 right-6 z-20 hidden sm:flex items-center space-x-3">
        <button type="button" onclick="toggleBarberMusic()" id="navbar-music-toggle" class="flex items-center space-x-2 px-4 py-1.5 rounded-full bg-slate-900/80 hover:bg-emerald-600/30 text-emerald-400 text-xs font-bold border border-emerald-500/30 backdrop-blur-md transition shadow-lg btn-magnetic" title="Putar / Jeda Musik Barbershop">
            <i class="fas fa-music text-xs text-emerald-400"></i>
            <span id="navbar-music-label" class="font-bold">Musik: OFF</span>
        </button>
    </div>

    <!-- 5. Main Card Container with Modern Laser Border Beam & 3D Specular Glare -->
    <div class="max-w-5xl w-full rounded-3xl shadow-2xl overflow-hidden grid grid-cols-1 md:grid-cols-12 border-beam-card tilt-card-luxury relative z-10 transition-all duration-300 reveal-zoom active shadow-emerald-950/50">
        
        <!-- Sisi Kiri: Branding Wijaya Barbershop with Authentic Studio Background & Animated Elements -->
        <div class="md:col-span-5 bg-gradient-to-br from-slate-950 via-slate-900 to-emerald-950 p-8 sm:p-10 text-white flex flex-col justify-between relative overflow-hidden border-b md:border-b-0 md:border-r border-white/10">
            <!-- Studio Interior Background In Left Card -->
            <div class="absolute inset-0 z-0 bg-cover bg-center opacity-30" style="background-image: url('https://images.unsplash.com/photo-1585747860715-2ba37e788b70?auto=format&fit=crop&w=1200&q=80');"></div>
            <div class="absolute inset-0 bg-gradient-to-t from-slate-950 via-slate-950/70 to-emerald-950/50 z-0"></div>

            <!-- Moving Orbs Inside Card -->
            <div class="absolute -right-10 -top-10 w-44 h-44 rounded-full bg-emerald-500/20 blur-2xl animate-pulse-glow pointer-events-none z-0"></div>
            <div class="absolute -left-10 -bottom-10 w-44 h-44 rounded-full bg-teal-500/20 blur-2xl animate-float pointer-events-none z-0"></div>

            <div class="relative z-10">
                <!-- Brand Header with Animated Barber Pole & Snipping Shears -->
                <a href="/" class="flex items-center space-x-3 mb-8 inline-block group">
                    <span class="w-2.5 h-7 rounded-full barber-pole border border-slate-700 shadow-md"></span>
                    <div class="w-11 h-11 rounded-2xl bg-gradient-to-tr from-emerald-600 via-emerald-500 to-teal-400 flex items-center justify-center text-white shadow-lg shadow-emerald-500/35 group-hover:scale-110 transition duration-300 scissor-snip-container">
                        <svg class="w-6 h-6 text-white transform -rotate-45" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="6" cy="6" r="3"/>
                            <path class="scissor-top" d="M8.12 8.12 12 12l7.5-7.5"/>
                            <circle cx="6" cy="18" r="3"/>
                            <path class="scissor-bottom" d="M8.12 15.88 12 12l7.5 7.5"/>
                        </svg>
                    </div>
                    <span class="text-xl font-black tracking-tight">Wijaya <span class="text-emerald-400">Barber</span></span>
                </a>

                <!-- Badge Bergerak -->
                <div class="inline-flex items-center space-x-2 px-3.5 py-1.5 rounded-full bg-emerald-500/20 text-emerald-300 text-xs font-bold uppercase tracking-wider mb-4 border border-emerald-500/30 backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 beacon-pulse"></span>
                    <span>Sistem Reservasi & Antrean</span>
                </div>

                <!-- Animated Gradient Title -->
                <h1 class="text-3xl sm:text-4xl font-black tracking-tight leading-tight text-white mb-4">
                    PREMIUM <br>
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-300 to-amber-300 animate-gradient">BARBERSHOP</span> <br>
                    EXPERIENCE
                </h1>

                <p class="text-xs sm:text-sm text-slate-300 leading-relaxed mb-6 font-normal">
                    Layanan pangkas rambut eksklusif di Kota Metro yang menggabungkan keahlian capster profesional dan sistem digital modern untuk kenyamanan Anda.
                </p>

                <!-- Keunggulan List with Animated Icons -->
                <ul class="space-y-3.5 text-xs sm:text-sm text-slate-300">
                    <li class="flex items-start space-x-3 group">
                        <div class="w-6 h-6 rounded-xl bg-emerald-500/25 text-emerald-400 flex items-center justify-center flex-shrink-0 mt-0.5 border border-emerald-500/40 group-hover:scale-110 transition">
                            <i class="fas fa-ticket-alt text-[10px]"></i>
                        </div>
                        <div>
                            <strong class="text-white block font-bold">Booking Mudah & Cepat</strong>
                            <span class="text-slate-400 text-xs">Pesan nomor antrean tanpa buang waktu menunggu fisik.</span>
                        </div>
                    </li>
                    <li class="flex items-start space-x-3 group">
                        <div class="w-6 h-6 rounded-xl bg-emerald-500/25 text-emerald-400 flex items-center justify-center flex-shrink-0 mt-0.5 border border-emerald-500/40 group-hover:scale-110 transition">
                            <i class="fas fa-cut text-[10px]"></i>
                        </div>
                        <div>
                            <strong class="text-white block font-bold">Barber Capster Profesional</strong>
                            <span class="text-slate-400 text-xs">Doni, Lana & Yoyo siap memberikan potongan presisi.</span>
                        </div>
                    </li>
                    <li class="flex items-start space-x-3 group">
                        <div class="w-6 h-6 rounded-xl bg-emerald-500/25 text-emerald-400 flex items-center justify-center flex-shrink-0 mt-0.5 border border-emerald-500/40 group-hover:scale-110 transition">
                            <i class="fab fa-whatsapp text-xs text-emerald-300"></i>
                        </div>
                        <div>
                            <strong class="text-white block font-bold">Notifikasi WhatsApp Real-Time</strong>
                            <span class="text-slate-400 text-xs">Pemberitahuan status pesanan langsung ke ponsel Anda.</span>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Animated Stats Counter Bar (Sesuai Gambar 31 & 32 Skripsi) -->
            <div class="relative z-10 pt-6 mt-6 border-t border-slate-800/80 grid grid-cols-3 gap-2 text-center">
                <div class="p-2 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-sm">
                    <span class="block text-2xl font-black text-emerald-400" data-counter="5000" data-suffix="+">5.000+</span>
                    <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Pelanggan</span>
                </div>
                <div class="p-2 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-sm">
                    <span class="block text-2xl font-black text-white" data-counter="12" data-suffix="">12</span>
                    <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Barber</span>
                </div>
                <div class="p-2 rounded-2xl bg-white/5 border border-white/5 backdrop-blur-sm">
                    <span class="block text-2xl font-black text-amber-400" data-counter="8" data-suffix=" Thn">8 Thn</span>
                    <span class="text-[10px] text-slate-400 font-semibold uppercase tracking-wider">Berdiri</span>
                </div>
            </div>
        </div>

        <!-- Sisi Kanan: Form Register / Login with Clean Modern Frosted Styling -->
        <div class="md:col-span-7 p-8 sm:p-12 flex flex-col justify-center bg-white/95 backdrop-blur-xl relative">
            <?= $content ?>
        </div>
    </div>

    <!-- Login Loading Animation Modal Overlay (Berlaku untuk Semua Role Pengguna: Admin, Kasir, Capster, Pelanggan, Direktur) -->
    <div id="login-loading-overlay" class="fixed inset-0 z-[99999] bg-slate-950/90 backdrop-blur-xl flex-col items-center justify-center p-4 transition-all duration-300 opacity-0 pointer-events-none" style="display: none;">
        <div class="relative max-w-sm w-full p-8 rounded-3xl bg-slate-900/95 border border-emerald-500/40 shadow-2xl shadow-emerald-950 text-center flex flex-col items-center overflow-hidden">
            
            <!-- Ambient Glowing Background Orbs -->
            <div class="absolute -top-14 -left-14 w-36 h-36 bg-emerald-500/25 rounded-full blur-3xl pointer-events-none animate-pulse"></div>
            <div class="absolute -bottom-14 -right-14 w-36 h-36 bg-amber-500/20 rounded-full blur-3xl pointer-events-none animate-pulse" style="animation-delay: 1s;"></div>
            
            <!-- Animated 3D Spinning Loader Rings with Scissors & Barber Pole -->
            <div class="relative w-28 h-28 flex items-center justify-center my-2">
                <!-- Outer Ring 1: Glowing emerald dashed spinning ring -->
                <div class="absolute inset-0 rounded-full border-2 border-dashed border-emerald-400/70 animate-spin" style="animation-duration: 4s;"></div>
                
                <!-- Outer Ring 2: Counter-spinning gradient ring -->
                <div class="absolute inset-2 rounded-full border-2 border-t-emerald-400 border-r-teal-400 border-b-amber-400 border-l-transparent animate-spin" style="animation-duration: 1.6s; animation-direction: reverse;"></div>
                
                <!-- Inner Center Icon: Glowing Scissors -->
                <div class="absolute inset-5 rounded-2xl bg-gradient-to-tr from-emerald-600 via-emerald-500 to-teal-400 flex items-center justify-center text-white shadow-xl shadow-emerald-500/50">
                    <i class="fas fa-scissors text-2xl text-white transform -rotate-45 animate-bounce"></i>
                </div>
                
                <!-- Floating Mini Barber Pole Badge -->
                <div class="absolute -bottom-1 -right-1 w-6 h-10 rounded-full barber-pole border-2 border-slate-900 shadow-xl" title="Wijaya Barber Pole"></div>
            </div>

            <!-- Pill Status Badge -->
            <div class="inline-flex items-center space-x-2 px-3.5 py-1 rounded-full bg-emerald-500/15 border border-emerald-500/35 text-emerald-300 text-[11px] font-black uppercase tracking-wider mt-4 backdrop-blur-md">
                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                <span>Autentikasi Akun</span>
            </div>

            <!-- Main Title -->
            <h3 class="text-xl sm:text-2xl font-black text-white mt-3 tracking-tight">
                MEMVERIFIKASI <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 via-teal-300 to-amber-300">AKUN...</span>
            </h3>
            
            <!-- Dynamic Subtitle -->
            <p id="loading-status-text" class="text-xs text-slate-300 mt-2 font-medium min-h-[1.5rem] transition-all duration-300">
                Memeriksa kredensial pengguna...
            </p>

            <!-- Progress Bar -->
            <div class="w-full bg-slate-800/90 rounded-full h-2.5 mt-5 p-0.5 border border-slate-700/80 overflow-hidden shadow-inner">
                <div id="loading-progress-bar" class="h-full bg-gradient-to-r from-emerald-500 via-teal-400 to-amber-400 rounded-full transition-all duration-200 ease-out shadow-md shadow-emerald-500/50" style="width: 15%;"></div>
            </div>

            <!-- Percentage Counter & Target Role Indicator -->
            <div class="flex items-center justify-between w-full text-[10px] text-slate-400 font-mono mt-2.5 px-1">
                <span id="loading-role-text" class="text-emerald-400 font-bold uppercase truncate max-w-[140px]">Memproses...</span>
                <span id="loading-percentage-text" class="font-bold text-slate-200">15%</span>
            </div>
        </div>
    </div>

    <script src="/js/app.js"></script>
</body>
</html>

