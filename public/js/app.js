// Wijaya Barbershop Modern Interactive & Animation Engine

document.addEventListener('DOMContentLoaded', function () {
    initAlerts();
    initScrollReveal();
    initCounters();
    initNavbarScroll();
    init3DTilt();
    initQuickDial();
});

// 1. Auto-dismiss alerts
function initAlerts() {
    const alerts = document.querySelectorAll('.auto-dismiss');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            alert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-10px)';
            setTimeout(function () { alert.remove(); }, 500);
        }, 5000);
    });
}

// 2. Scroll Reveal Animations with IntersectionObserver
function initScrollReveal() {
    const reveals = document.querySelectorAll('.reveal, .reveal-left, .reveal-right, .reveal-zoom');
    if (!reveals.length) return;

    const observerOptions = {
        root: null,
        rootMargin: '0px 0px -40px 0px',
        threshold: 0.12
    };

    const revealObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('active');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    reveals.forEach(el => revealObserver.observe(el));
}

// 3. Smooth Number Counters on Scroll
function initCounters() {
    const counters = document.querySelectorAll('[data-counter]');
    if (!counters.length) return;

    const counterObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                animateCounter(entry.target);
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.2 });

    counters.forEach(counter => counterObserver.observe(counter));
}

function animateCounter(el) {
    const target = parseInt(el.getAttribute('data-counter'), 10);
    const suffix = el.getAttribute('data-suffix') || '';
    const duration = 1800; // ms
    const startTime = performance.now();

    function updateCount(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        // Easing out cubic
        const easeOut = 1 - Math.pow(1 - progress, 3);
        const currentCount = Math.floor(easeOut * target);

        el.textContent = currentCount.toLocaleString('id-ID') + suffix;

        if (progress < 1) {
            requestAnimationFrame(updateCount);
        } else {
            el.textContent = target.toLocaleString('id-ID') + suffix;
        }
    }

    requestAnimationFrame(updateCount);
}

// 4. Navbar Sticky Glass Transition
function initNavbarScroll() {
    const header = document.querySelector('header');
    if (!header) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 25) {
            header.classList.add('bg-slate-900/90', 'backdrop-blur-md', 'shadow-xl');
            header.classList.remove('bg-slate-900');
        } else {
            header.classList.remove('bg-slate-900/90', 'backdrop-blur-md', 'shadow-xl');
            header.classList.add('bg-slate-900');
        }
    }, { passive: true });
}

// 5. Interactive 3D Card Tilt
function init3DTilt() {
    const tiltCards = document.querySelectorAll('.tilt-3d');
    tiltCards.forEach(card => {
        card.addEventListener('mousemove', e => {
            const rect = card.getBoundingClientRect();
            const x = e.clientX - rect.left;
            const y = e.clientY - rect.top;
            const centerX = rect.width / 2;
            const centerY = rect.height / 2;
            const rotateX = ((y - centerY) / centerY) * -7;
            const rotateY = ((x - centerX) / centerX) * 7;

            card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)';
        });
    });
}

// 6. Floating Action Widget (Speed Dial)
function initQuickDial() {
    const dialBtn = document.getElementById('floating-dial-toggle');
    const dialMenu = document.getElementById('floating-dial-menu');
    if (!dialBtn || !dialMenu) return;

    dialBtn.addEventListener('click', () => {
        dialMenu.classList.toggle('hidden');
        dialBtn.querySelector('i').classList.toggle('rotate-45');
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (!dialBtn.contains(e.target) && !dialMenu.contains(e.target)) {
            dialMenu.classList.add('hidden');
            const icon = dialBtn.querySelector('i');
            if (icon) icon.classList.remove('rotate-45');
        }
    });
}

// 7. Midtrans Snap Trigger Function
function payWithMidtrans(snapToken, orderCode) {
    if (typeof playOrderSound === 'function') playOrderSound();
    if (typeof window.snap !== 'undefined' && snapToken && !snapToken.startsWith('SNAP-MOCK')) {
        window.snap.pay(snapToken, {
            onSuccess: function (result) {
                if (typeof playTransactionSuccessSound === 'function') playTransactionSuccessSound();
                setTimeout(() => {
                    window.location.href = '/receipt?code=' + orderCode + '&payment_status=success';
                }, 900);
            },
            onPending: function (result) {
                window.location.href = '/receipt?code=' + orderCode + '&payment_status=pending';
            },
            onError: function (result) {
                alert('Pembayaran gagal atau dibatalkan.');
            },
            onClose: function () {
                alert('Anda menutup jendela pembayaran sebelum menyelesaikan transaksi.');
            }
        });
    } else {
        openMockPaymentModal(orderCode);
    }
}

// 8. Simulasi Modal Pembayaran Midtrans (Testing Offline / Ujian Skripsi)
function openMockPaymentModal(orderCode) {
    if (typeof playOrderSound === 'function') playOrderSound();
    const modalHtml = `
    <div id="mock-midtrans-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/75 backdrop-blur-md p-4 animate-fade-in">
        <div class="bg-white rounded-3xl shadow-2xl max-w-md w-full overflow-hidden border border-slate-100">
            <div class="bg-gradient-to-r from-slate-950 via-slate-900 to-emerald-950 px-6 py-5 flex items-center justify-between text-white border-b border-slate-800">
                <div class="flex items-center space-x-2.5">
                    <span class="w-3 h-3 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span class="font-extrabold tracking-wide text-sm text-emerald-400">Midtrans Snap Simulator</span>
                </div>
                <button onclick="document.getElementById('mock-midtrans-modal').remove()" class="text-slate-400 hover:text-white text-2xl leading-none">&times;</button>
            </div>
            <div class="p-6 text-center">
                <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-2xl flex items-center justify-center mx-auto mb-4 text-3xl font-bold shadow-md">
                    <i class="fas fa-qrcode"></i>
                </div>
                <h3 class="text-xl font-black text-slate-800 mb-1">Simulasi Pembayaran QRIS</h3>
                <p class="text-xs text-slate-500 mb-5">Kode Transaksi: <span class="font-mono font-bold text-slate-900">${orderCode}</span></p>
                
                <div class="bg-slate-50 border border-slate-200/80 rounded-2xl p-4 mb-6 text-left">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-xs font-bold text-slate-600 uppercase tracking-wider">Metode Virtual Account:</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Auto Verify</span>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                        <div class="p-2 bg-white rounded-xl border border-slate-200 font-bold text-slate-800">GoPay QRIS</div>
                        <div class="p-2 bg-white rounded-xl border border-slate-200 font-bold text-slate-800">ShopeePay</div>
                        <div class="p-2 bg-white rounded-xl border border-slate-200 font-bold text-slate-800">BCA VA</div>
                    </div>
                </div>

                <button onclick="executeSimulatedPayment('${orderCode}')" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white font-black rounded-xl transition shadow-xl shadow-emerald-600/30 text-xs uppercase tracking-wider shimmer-btn btn-magnetic">
                    <i class="fas fa-check-circle mr-1.5"></i>
                    <span>Konfirmasi Pembayaran Lunas (Settlement)</span>
                </button>
                <button onclick="document.getElementById('mock-midtrans-modal').remove()" class="w-full mt-2.5 py-2 text-xs text-slate-400 hover:text-slate-600 font-semibold">
                    Tutup / Bayar Nanti
                </button>
            </div>
        </div>
    </div>`;

    document.body.insertAdjacentHTML('beforeend', modalHtml);
}

function executeSimulatedPayment(orderCode) {
    if (typeof playOrderSound === 'function') playOrderSound();
    fetch('/api/midtrans/simulate-success', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'order_code=' + encodeURIComponent(orderCode)
    })
    .then(res => res.json())
    .then(data => {
        if (data.status === 'success') {
            if (typeof playTransactionSuccessSound === 'function') {
                playTransactionSuccessSound();
            }
            if (data.wa_desktop_link) {
                try {
                    // Coba buka langsung aplikasi WhatsApp Desktop di Windows
                    const wAppFrame = document.createElement('iframe');
                    wAppFrame.style.display = 'none';
                    wAppFrame.src = data.wa_desktop_link;
                    document.body.appendChild(wAppFrame);
                    setTimeout(() => wAppFrame.remove(), 2500);
                } catch (e) {
                    try { window.open(data.wa_link || data.wa_desktop_link, '_blank'); } catch (err) {}
                }
            } else if (data.wa_link) {
                try {
                    window.open(data.wa_link, '_blank');
                } catch (e) {}
            }
            const modal = document.getElementById('mock-midtrans-modal');
            if (modal) {
                modal.innerHTML = `
                <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/85 backdrop-blur-md p-4 animate-fade-in">
                    <div class="bg-slate-900 border border-emerald-500/40 rounded-3xl shadow-2xl max-w-md w-full p-8 text-center text-white">
                        <div class="w-20 h-20 bg-emerald-500/20 border border-emerald-500/40 text-emerald-400 rounded-3xl flex items-center justify-center mx-auto mb-4 text-4xl shadow-lg shadow-emerald-500/30 animate-bounce">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <h3 class="text-2xl font-black text-white mb-2">Pembayaran Berhasil!</h3>
                        <p class="text-xs text-slate-300 mb-4">Status transaksi: <strong class="text-emerald-400">LUNAS (Settlement)</strong>. Notifikasi bukti lunas terhubung ke aplikasi WhatsApp Anda.</p>
                        <div class="space-y-2 mb-5">
                            ${data.wa_desktop_link ? `
                            <a href="${data.wa_desktop_link}" class="w-full py-3.5 px-4 bg-gradient-to-r from-emerald-500 to-teal-400 hover:from-emerald-400 text-slate-950 font-black rounded-xl text-xs uppercase tracking-wider flex items-center justify-center space-x-2 shadow-lg shadow-emerald-500/30 transition">
                                <i class="fab fa-whatsapp text-lg"></i>
                                <span>Buka Aplikasi WhatsApp Laptop 💻</span>
                            </a>
                            ` : ''}
                            ${data.wa_link ? `
                            <a href="${data.wa_link}" target="_blank" class="w-full py-2.5 px-4 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl text-xs uppercase tracking-wider flex items-center justify-center space-x-2 border border-slate-700 transition">
                                <i class="fas fa-globe text-sm"></i>
                                <span>Buka WhatsApp Web 🌐</span>
                            </a>
                            ` : ''}
                        </div>
                        <div class="inline-flex items-center space-x-2 px-4 py-2 rounded-xl bg-white/5 border border-white/10 text-xs text-emerald-300 font-bold">
                            <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
                            <span>Memperbarui bukti resi...</span>
                        </div>
                    </div>
                </div>`;
            }
            setTimeout(() => {
                window.location.href = '/receipt?code=' + encodeURIComponent(orderCode) + '&payment_status=success&auto_wa=1';
            }, 1500);
        } else {
            alert(data.message || 'Gagal memproses simulasi');
        }
    })
    .catch(err => {
        alert('Terjadi kesalahan koneksi.');
    });
}

// 7. Hero Background Visibility & Studio Atmosphere Controller
let heroBrightnessState = 0; // 0: Terang (90%), 1: Ultra Terang (100%), 2: Redup Cinematic (60%)

function toggleHeroBgBrightness() {
    const bgImage = document.getElementById('hero-bg-image');
    const bgOverlay = document.getElementById('hero-bg-overlay');
    const btnText = document.getElementById('btn-brightness-text');
    if (!bgImage || !bgOverlay) return;

    heroBrightnessState = (heroBrightnessState + 1) % 3;

    if (heroBrightnessState === 1) {
        // Mode 1: ULTRA TERANG (100% Terlihat Jelas)
        bgImage.style.opacity = '1.0';
        bgOverlay.className = 'absolute inset-0 bg-gradient-to-r from-slate-950/60 via-slate-950/20 to-transparent transition-all duration-500 z-0';
        if (btnText) btnText.textContent = 'Mode: Ultra Terang (100%)';
    } else if (heroBrightnessState === 2) {
        // Mode 2: REDUP CINEMATIC (60%)
        bgImage.style.opacity = '0.6';
        bgOverlay.className = 'absolute inset-0 bg-gradient-to-r from-slate-950/90 via-slate-950/65 to-slate-950/40 transition-all duration-500 z-0';
        if (btnText) btnText.textContent = 'Mode: Redup (60%)';
    } else {
        // Mode 0: NORMAL JERNIH (90%)
        bgImage.style.opacity = '0.9';
        bgOverlay.className = 'absolute inset-0 bg-gradient-to-r from-slate-950/75 via-slate-950/40 to-slate-950/10 transition-all duration-500 z-0';
        if (btnText) btnText.textContent = 'Mode: Jernih (90%)';
    }
}

function changeHeroBg(imageUrl) {
    const bgImage = document.getElementById('hero-bg-image');
    if (!bgImage) return;

    bgImage.style.transition = 'opacity 0.4s ease';
    bgImage.style.opacity = '0.2';
    setTimeout(() => {
        bgImage.style.backgroundImage = `url('${imageUrl}')`;
        bgImage.style.opacity = heroBrightnessState === 1 ? '1.0' : (heroBrightnessState === 2 ? '0.6' : '0.9');
        try {
            localStorage.setItem('wijaya_hero_bg', imageUrl);
        } catch (e) {}
    }, 400);
}

// Restore saved hero background on load
(function restoreSavedHeroBg() {
    try {
        const saved = localStorage.getItem('wijaya_hero_bg');
        if (saved) {
            const bgImage = document.getElementById('hero-bg-image');
            if (bgImage) bgImage.style.backgroundImage = `url('${saved}')`;
        }
    } catch (e) {}
})();



// ----------------------------------------------------
// 8. BESPOKE INTERACTIVE CANVAS PARTICLE ENGINE (Wijaya Barbershop Embers & Scissor Sparks)
// ----------------------------------------------------
function initBarberCanvasParticles() {
    const canvas = document.getElementById('barber-particle-canvas');
    if (!canvas) return;

    const ctx = canvas.getContext('2d');
    if (!ctx) return;

    let width = (canvas.width = window.innerWidth);
    let height = (canvas.height = window.innerHeight);

    window.addEventListener('resize', () => {
        width = canvas.width = window.innerWidth;
        height = canvas.height = window.innerHeight;
    }, { passive: true });

    // Particle Types: Golden Embers, Emerald Stardust, Micro Scissors
    const particles = [];
    const particleCount = Math.min(Math.floor(width / 32), 48); // Adaptive to screen width

    const mouse = {
        x: -9999,
        y: -9999,
        radius: 130
    };

    window.addEventListener('mousemove', (e) => {
        mouse.x = e.clientX;
        mouse.y = e.clientY;
    }, { passive: true });

    window.addEventListener('mouseleave', () => {
        mouse.x = -9999;
        mouse.y = -9999;
    }, { passive: true });

    class Particle {
        constructor() {
            this.reset(true);
        }

        reset(initial = false) {
            this.x = Math.random() * width;
            this.y = initial ? Math.random() * height : height + 10;
            this.vx = (Math.random() - 0.5) * 0.7;
            this.vy = -(Math.random() * 0.9 + 0.3); // Gentle upward floating
            this.size = Math.random() * 2.8 + 1.2;
            this.alpha = Math.random() * 0.55 + 0.2;
            this.colorType = Math.random() > 0.4 ? 'emerald' : 'gold';
            this.isScissor = Math.random() < 0.12; // 12% are micro shears
            this.rotation = Math.random() * Math.PI * 2;
            this.rotSpeed = (Math.random() - 0.5) * 0.04;
        }

        update() {
            // Mouse interaction (repel gently)
            const dx = mouse.x - this.x;
            const dy = mouse.y - this.y;
            const dist = Math.sqrt(dx * dx + dy * dy);

            if (dist < mouse.radius) {
                const force = (mouse.radius - dist) / mouse.radius;
                const angle = Math.atan2(dy, dx);
                this.x -= Math.cos(angle) * force * 3.5;
                this.y -= Math.sin(angle) * force * 3.5;
            }

            this.x += this.vx;
            this.y += this.vy;
            this.rotation += this.rotSpeed;

            // Fade in and out
            if (this.y < height * 0.15) {
                this.alpha -= 0.008;
            }

            if (this.y < -20 || this.x < -20 || this.x > width + 20 || this.alpha <= 0.02) {
                this.reset();
            }
        }

        draw() {
            ctx.save();
            ctx.translate(this.x, this.y);
            ctx.rotate(this.rotation);

            if (this.isScissor) {
                // Draw Micro Scissor
                ctx.strokeStyle = this.colorType === 'emerald' ? `rgba(16, 185, 129, ${this.alpha})` : `rgba(245, 158, 11, ${this.alpha})`;
                ctx.lineWidth = 1.2;
                ctx.beginPath();
                ctx.moveTo(-6, -6);
                ctx.lineTo(6, 6);
                ctx.moveTo(-6, 6);
                ctx.lineTo(6, -6);
                ctx.stroke();

                // Center pivot point
                ctx.fillStyle = '#ffffff';
                ctx.beginPath();
                ctx.arc(0, 0, 1.2, 0, Math.PI * 2);
                ctx.fill();
            } else {
                // Draw Glowing Ember Particle
                const grad = ctx.createRadialGradient(0, 0, 0, 0, 0, this.size * 2);
                if (this.colorType === 'emerald') {
                    grad.addColorStop(0, `rgba(52, 211, 153, ${this.alpha})`);
                    grad.addColorStop(0.6, `rgba(16, 185, 129, ${this.alpha * 0.6})`);
                    grad.addColorStop(1, 'rgba(6, 78, 59, 0)');
                } else {
                    grad.addColorStop(0, `rgba(251, 191, 36, ${this.alpha})`);
                    grad.addColorStop(0.6, `rgba(245, 158, 11, ${this.alpha * 0.6})`);
                    grad.addColorStop(1, 'rgba(180, 83, 9, 0)');
                }

                ctx.fillStyle = grad;
                ctx.beginPath();
                ctx.arc(0, 0, this.size * 2, 0, Math.PI * 2);
                ctx.fill();
            }

            ctx.restore();
        }
    }

    for (let i = 0; i < particleCount; i++) {
        particles.push(new Particle());
    }

    let isPaused = false;
    document.addEventListener('visibilitychange', () => {
        isPaused = document.hidden;
    });

    function loop() {
        if (!isPaused) {
            ctx.clearRect(0, 0, width, height);
            for (let i = 0; i < particles.length; i++) {
                particles[i].update();
                particles[i].draw();
            }
        }
        requestAnimationFrame(loop);
    }

    loop();
}

// ----------------------------------------------------
// 9. HIGH-END 3D TILT WITH CURSOR SPECULAR GLARE
// ----------------------------------------------------
function initAdvancedTiltAndGlare() {
    const cards = document.querySelectorAll('.tilt-card-luxury, .card-premium, .border-beam-card, .tilt-3d');
    
    cards.forEach(card => {
        // Inject specular glare overlay if not present
        if (!card.querySelector('.specular-glare')) {
            const glare = document.createElement('div');
            glare.className = 'specular-glare';
            card.appendChild(glare);
        }

        let bounds;
        function updateBounds() {
            bounds = card.getBoundingClientRect();
        }

        card.addEventListener('mouseenter', updateBounds);
        window.addEventListener('resize', updateBounds, { passive: true });

        card.addEventListener('mousemove', (e) => {
            if (!bounds) updateBounds();
            const mouseX = e.clientX - bounds.left;
            const mouseY = e.clientY - bounds.top;
            
            const pctX = (mouseX / bounds.width) * 100;
            const pctY = (mouseY / bounds.height) * 100;

            card.style.setProperty('--mouse-x', `${pctX}%`);
            card.style.setProperty('--mouse-y', `${pctY}%`);

            const centerX = bounds.width / 2;
            const centerY = bounds.height / 2;

            const rotateX = ((mouseY - centerY) / centerY) * -9;
            const rotateY = ((mouseX - centerX) / centerX) * 9;

            card.style.transform = `perspective(1100px) rotateX(${rotateX.toFixed(2)}deg) rotateY(${rotateY.toFixed(2)}deg) translateZ(6px)`;
        });

        card.addEventListener('mouseleave', () => {
            card.style.transform = 'perspective(1100px) rotateX(0deg) rotateY(0deg) translateZ(0px)';
        });
    });
}

// ----------------------------------------------------
// 10. MAGNETIC BUTTON INTERACTION
// ----------------------------------------------------
function initMagneticButtons() {
    const magneticBtns = document.querySelectorAll('.btn-magnetic, .shimmer-btn');

    magneticBtns.forEach(btn => {
        btn.addEventListener('mousemove', (e) => {
            const rect = btn.getBoundingClientRect();
            const x = e.clientX - rect.left - rect.width / 2;
            const y = e.clientY - rect.top - rect.height / 2;

            btn.style.transform = `translate(${x * 0.22}px, ${y * 0.22}px)`;
        });

        btn.addEventListener('mouseleave', () => {
            btn.style.transform = 'translate(0px, 0px)';
        });
    });
}

// ----------------------------------------------------
// 11. CLICK RIPPLE WAVE EFFECT
// ----------------------------------------------------
function initRippleClicks() {
    document.addEventListener('click', (e) => {
        const btn = e.target.closest('.btn-magnetic, .shimmer-btn, button, .shimmer-pill');
        if (!btn) return;

        const rect = btn.getBoundingClientRect();
        const circle = document.createElement('span');
        const diameter = Math.max(rect.width, rect.height);
        const radius = diameter / 2;

        circle.style.width = circle.style.height = `${diameter}px`;
        circle.style.left = `${e.clientX - rect.left - radius}px`;
        circle.style.top = `${e.clientY - rect.top - radius}px`;
        circle.classList.add('ripple-circle');

        const existing = btn.querySelector('.ripple-circle');
        if (existing) existing.remove();

        btn.appendChild(circle);
        setTimeout(() => circle.remove(), 600);
    });
}

// ----------------------------------------------------
// 12. LIVE SONAR RADAR INTERACTION
// ----------------------------------------------------
function initLiveSonarQueue() {
    const queueCards = document.querySelectorAll('.live-queue-radar');
    if (!queueCards.length) return;

    // Subtle audio / pulse ping simulation
    setInterval(() => {
        queueCards.forEach(c => {
            const ping = c.querySelector('.sonar-ring');
            if (ping) {
                ping.style.animation = 'none';
                void ping.offsetWidth;
                ping.style.animation = 'sonarRingPulseAnim 2.2s cubic-bezier(0, 0, 0.2, 1) infinite';
            }
        });
    }, 4500);
}

// ----------------------------------------------------
// 13. BARBERSHOP SOUND FX ENGINE (Web Audio API Synthesizer)
// ----------------------------------------------------
let barberAudioCtx = null;

function getBarberAudioContext() {
    try {
        if (window.top && window.top !== window && window.top.location.origin === window.location.origin) {
            if (!window.top.__wijayaSharedAudioCtx) {
                const TopAudioContext = window.top.AudioContext || window.top.webkitAudioContext;
                if (TopAudioContext) {
                    window.top.__wijayaSharedAudioCtx = new TopAudioContext();
                }
            }
            if (window.top.__wijayaSharedAudioCtx) {
                barberAudioCtx = window.top.__wijayaSharedAudioCtx;
                if (barberAudioCtx.state === 'suspended') {
                    barberAudioCtx.resume();
                }
                return barberAudioCtx;
            }
        }
    } catch (e) {}

    if (!barberAudioCtx) {
        const AudioContextClass = window.AudioContext || window.webkitAudioContext;
        if (AudioContextClass) {
            barberAudioCtx = new AudioContextClass();
            try { window.__wijayaSharedAudioCtx = barberAudioCtx; } catch (e) {}
        }
    }
    if (barberAudioCtx && barberAudioCtx.state === 'suspended') {
        barberAudioCtx.resume();
    }
    return barberAudioCtx;
}

// 1. Suara Ketukan Tunggal (1 Single Tactile Knock) untuk SEMUA Fitur, Tombol, Menu & Link
function playGeneralClickSound() {
    try {
        const ctx = getBarberAudioContext();
        if (!ctx) return;

        const now = ctx.currentTime;

        // 1 Osilator Tunggal: Suara Ketukan Bersih (1x "Tok / Tap", Tidak Ganda)
        const knockOsc = ctx.createOscillator();
        const knockGain = ctx.createGain();
        knockOsc.type = 'sine';
        knockOsc.frequency.setValueAtTime(640, now);
        knockOsc.frequency.exponentialRampToValueAtTime(160, now + 0.045);

        knockGain.gain.setValueAtTime(0.42, now);
        knockGain.gain.exponentialRampToValueAtTime(0.001, now + 0.048);

        knockOsc.connect(knockGain);
        knockGain.connect(ctx.destination);
        knockOsc.start(now);
        knockOsc.stop(now + 0.052);
    } catch (e) {
        console.warn('Audio play error:', e);
    }
}

// 2. Suara KHUSUS Klik Keranjang Pemesanan (Shopping Cart Bubble Drop + Bell Chime)
function playCartSound() {
    try {
        const ctx = getBarberAudioContext();
        if (!ctx) return;

        const now = ctx.currentTime;

        // Komponen 1: "Plop / Bubble" item masuk ke dalam keranjang
        const popOsc = ctx.createOscillator();
        const popGain = ctx.createGain();
        popOsc.type = 'sine';
        popOsc.frequency.setValueAtTime(340, now);
        popOsc.frequency.exponentialRampToValueAtTime(640, now + 0.045);

        popGain.gain.setValueAtTime(0.24, now);
        popGain.gain.exponentialRampToValueAtTime(0.001, now + 0.05);

        popOsc.connect(popGain);
        popGain.connect(ctx.destination);
        popOsc.start(now);
        popOsc.stop(now + 0.06);

        // Komponen 2: Lonceng Belanja Ceria (G5 783.99 Hz & E6 1318.51 Hz)
        const cartNotes = [783.99, 1318.51];
        cartNotes.forEach((freq, idx) => {
            const bellOsc = ctx.createOscillator();
            const bellGain = ctx.createGain();
            bellOsc.type = 'triangle';
            bellOsc.frequency.setValueAtTime(freq, now + 0.035 + (idx * 0.03));

            bellGain.gain.setValueAtTime(0.001, now + 0.035 + (idx * 0.03));
            bellGain.gain.linearRampToValueAtTime(0.22, now + 0.045 + (idx * 0.03));
            bellGain.gain.exponentialRampToValueAtTime(0.0001, now + 0.35 + (idx * 0.03));

            bellOsc.connect(bellGain);
            bellGain.connect(ctx.destination);
            bellOsc.start(now + 0.035 + (idx * 0.03));
            bellOsc.stop(now + 0.40);
        });

        // Visual mini toast khusus keranjang
        showAudioToast('🛒 Keranjang Pemesanan', 'Item ditambahkan ke keranjang belanja');
    } catch (e) {
        console.warn('Audio play error:', e);
    }
}

// 3. Suara KHUSUS Klik Pemesanan (Acoustic Barber Scissor Double-Snip + Tactile Pop)
function playOrderSound() {
    try {
        const ctx = getBarberAudioContext();
        if (!ctx) return;

        const now = ctx.currentTime;

        // Scissor Blade 1: Friction Swoosh Slide
        createBladeSlice(ctx, now, 2800, 0.24);
        
        // Scissor Blade 2: Sharp Crisp Cut Click
        createBladeSlice(ctx, now + 0.045, 3800, 0.30);

        // Tactile UI Punch Pop (Sine sweep from 620Hz down to 140Hz)
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.type = 'sine';
        osc.frequency.setValueAtTime(620, now);
        osc.frequency.exponentialRampToValueAtTime(140, now + 0.08);

        gain.gain.setValueAtTime(0.28, now);
        gain.gain.exponentialRampToValueAtTime(0.001, now + 0.08);

        osc.connect(gain);
        gain.connect(ctx.destination);
        osc.start(now);
        osc.stop(now + 0.09);

        // Visual mini toast indikator pesanan
        showAudioToast('✂️ Pesanan Diproses', 'Efek suara klik pesanan & gunting aktif');
    } catch (e) {
        console.warn('Audio play error:', e);
    }
}

function createBladeSlice(ctx, startTime, freq, volume) {
    const bufferSize = Math.floor(ctx.sampleRate * 0.035);
    const buffer = ctx.createBuffer(1, bufferSize, ctx.sampleRate);
    const data = buffer.getChannelData(0);
    for (let i = 0; i < bufferSize; i++) {
        data[i] = (Math.random() * 2 - 1) * Math.exp(-i / (bufferSize * 0.28));
    }
    const noise = ctx.createBufferSource();
    noise.buffer = buffer;

    const filter = ctx.createBiquadFilter();
    filter.type = 'bandpass';
    filter.frequency.setValueAtTime(freq, startTime);
    filter.Q.setValueAtTime(4.2, startTime);

    const gain = ctx.createGain();
    gain.gain.setValueAtTime(volume, startTime);
    gain.gain.exponentialRampToValueAtTime(0.001, startTime + 0.035);

    noise.connect(filter);
    filter.connect(gain);
    gain.connect(ctx.destination);

    noise.start(startTime);
    noise.stop(startTime + 0.04);
}

// 4. Suara Ketika Transaksi Berhasil (Ascending Triumphant Chimes + Golden Cash Register Cha-Ching)
let hasPlayedSuccessSoundSession = false;
let lastSuccessSoundTime = 0;

function playTransactionSuccessSound(force = false) {
    try {
        const now = Date.now();
        const topWin = window.top || window;

        // Cegah bunyi berulang: Hanya boleh 1 KALI berbunyi (cooldown 15 detik kecuali tombol manual force)
        if (!force) {
            if (hasPlayedSuccessSoundSession || topWin.__wijayaSuccessSoundPlayed || (now - lastSuccessSoundTime < 15000)) {
                console.log('[Wijaya Audio] Suara transaksi sukses diabaikan karena sudah pernah berbunyi (hanya 1 kali).');
                return;
            }
        }

        hasPlayedSuccessSoundSession = true;
        try { topWin.__wijayaSuccessSoundPlayed = true; } catch (e) {}
        lastSuccessSoundTime = now;

        const ctx = getBarberAudioContext();
        if (!ctx) return;

        const nowAudio = ctx.currentTime;

        // Ascending 4-tone triumphant celebration chord:
        // C5 (523.25 Hz) -> E5 (659.25 Hz) -> G5 (783.99 Hz) -> C6 (1046.50 Hz)
        const chordNotes = [
            { freq: 523.25, time: 0.00, dur: 0.35, vol: 0.28 },
            { freq: 659.25, time: 0.09, dur: 0.35, vol: 0.30 },
            { freq: 783.99, time: 0.18, dur: 0.45, vol: 0.32 },
            { freq: 1046.50, time: 0.28, dur: 0.95, vol: 0.38 }
        ];

        chordNotes.forEach(n => {
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();

            osc.type = 'triangle';
            osc.frequency.setValueAtTime(n.freq, nowAudio + n.time);

            gain.gain.setValueAtTime(0.001, nowAudio + n.time);
            gain.gain.linearRampToValueAtTime(n.vol, nowAudio + n.time + 0.02);
            gain.gain.exponentialRampToValueAtTime(0.0001, nowAudio + n.time + n.dur);

            osc.connect(gain);
            gain.connect(ctx.destination);

            osc.start(nowAudio + n.time);
            osc.stop(nowAudio + n.time + n.dur);

            // Shimmering bell harmonic overtone
            const shimmer = ctx.createOscillator();
            const sGain = ctx.createGain();
            shimmer.type = 'sine';
            shimmer.frequency.setValueAtTime(n.freq * 2, nowAudio + n.time);
            sGain.gain.setValueAtTime(0.001, nowAudio + n.time);
            sGain.gain.linearRampToValueAtTime(n.vol * 0.45, nowAudio + n.time + 0.015);
            sGain.gain.exponentialRampToValueAtTime(0.0001, nowAudio + n.time + (n.dur * 0.7));

            shimmer.connect(sGain);
            sGain.connect(ctx.destination);

            shimmer.start(nowAudio + n.time);
            shimmer.stop(nowAudio + n.time + n.dur);
        });

        // Golden Cash Register "Cha-Ching" Bell Sparkle at t = 0.30s
        const bellFreqs = [2093.0, 2637.0, 3136.0];
        bellFreqs.forEach(bf => {
            const bOsc = ctx.createOscillator();
            const bGain = ctx.createGain();
            bOsc.type = 'sine';
            bOsc.frequency.setValueAtTime(bf, nowAudio + 0.30);
            bGain.gain.setValueAtTime(0.001, nowAudio + 0.30);
            bGain.gain.linearRampToValueAtTime(0.15, nowAudio + 0.31);
            bGain.gain.exponentialRampToValueAtTime(0.0001, nowAudio + 0.85);

            bOsc.connect(bGain);
            bGain.connect(ctx.destination);
            bOsc.start(nowAudio + 0.30);
            bOsc.stop(nowAudio + 0.90);
        });

        // Warm Confirmation Bass Foundation at t = 0.28s (C4 261.63Hz)
        const bass = ctx.createOscillator();
        const bassGain = ctx.createGain();
        bass.type = 'sine';
        bass.frequency.setValueAtTime(261.63, nowAudio + 0.28);
        bassGain.gain.setValueAtTime(0.001, nowAudio + 0.28);
        bassGain.gain.linearRampToValueAtTime(0.24, nowAudio + 0.31);
        bassGain.gain.exponentialRampToValueAtTime(0.001, nowAudio + 0.95);
        bass.connect(bassGain);
        bassGain.connect(ctx.destination);
        bass.start(nowAudio + 0.28);
        bass.stop(nowAudio + 1.0);

        // 1. Suara Pertama: Lonceng kemenangan & cha-ching berbunyi (t = 0 - 0.75s)
        // 2. Suara Kedua: Diikuti suara human perempuan mengatakan "Pembayaran berhasil terverifikasi..." (Hanya 1 kali!)
        setTimeout(() => {
            speakFemalePaymentSuccess(null, force);
        }, 750);

        // Visual Celebration Toast with Audio Equalizer Animation
        showAudioToast('🎉 Transaksi Berhasil!', 'Lonceng & suara konfirmasi pembayaran berbunyi', true);
    } catch (e) {
        console.warn('Audio play error:', e);
    }
}

// Ducking volume musik latar saat suara pengumuman aktif
function duckLoungeMusic(duck = true) {
    try {
        const audio = (window.top && window.top.barberMusicAudio) ? window.top.barberMusicAudio : window.barberMusicAudio;
        if (!audio) return;
        const targetVol = duck ? 0.08 : parseFloat(localStorage.getItem('wijaya_music_volume') || '0.35');
        audio.volume = targetVol;
    } catch (e) {}
}
window.duckLoungeMusic = duckLoungeMusic;

// Suara Human Perempuan: "Pembayaran berhasil terverifikasi. Terima kasih telah mempercayakan penampilan Anda di Wijaya Barbershop."
let isFemaleSpeaking = false;
let lastFemaleSpeakTime = 0;

function speakFemalePaymentSuccess(customText, force = false) {
    try {
        if (!('speechSynthesis' in window)) return;

        const now = Date.now();
        const topWin = window.top || window;

        // Cegah suara perempuan terulang / berbunyi berkali-kali
        if (!force) {
            if (isFemaleSpeaking || topWin.__wijayaFemaleSpoken || (now - lastFemaleSpeakTime < 15000)) {
                console.log('[Wijaya Voice] Suara perempuan diabaikan karena sudah berbicara (hanya 1 kali).');
                return;
            }
        }

        isFemaleSpeaking = true;
        try { topWin.__wijayaFemaleSpoken = true; } catch (e) {}
        lastFemaleSpeakTime = now;

        // Batalkan ucapan sebelumnya jika masih ada antrean
        window.speechSynthesis.cancel();
        duckLoungeMusic(true);

        const phrase = customText || "Pembayaran berhasil terverifikasi. Terima kasih telah mempercayakan penampilan Anda di Wijaya Barbershop.";
        const utter = new SpeechSynthesisUtterance(phrase);
        utter.lang = 'id-ID';
        utter.rate = 0.95; // Kecepatan jelas dan ramah
        utter.pitch = 1.15; // Frekuensi nada vokal perempuan Indonesia
        utter.volume = 1.0;

        let hasSpokenOnce = false;
        const setVoiceAndSpeak = () => {
            if (hasSpokenOnce) return;
            hasSpokenOnce = true;
            window.speechSynthesis.onvoiceschanged = null; // Unbind segera agar event Chrome tidak memicu ucapan ganda!

            try {
                const voices = window.speechSynthesis.getVoices();
                const idVoice = voices.find(v => 
                    (v.lang === 'id-ID' || v.lang.startsWith('id')) && 
                    (v.name.toLowerCase().includes('gadis') || v.name.toLowerCase().includes('female') || v.name.toLowerCase().includes('natural') || v.name.toLowerCase().includes('indonesia'))
                ) || voices.find(v => v.lang === 'id-ID' || v.lang.startsWith('id'));

                if (idVoice) {
                    utter.voice = idVoice;
                }
            } catch (e) {}

            utter.onend = function () {
                duckLoungeMusic(false);
                setTimeout(() => { isFemaleSpeaking = false; }, 2000);
            };
            utter.onerror = function () {
                duckLoungeMusic(false);
                isFemaleSpeaking = false;
            };

            window.speechSynthesis.cancel();
            window.speechSynthesis.speak(utter);
        };

        if (window.speechSynthesis.getVoices().length > 0) {
            setVoiceAndSpeak();
        } else {
            window.speechSynthesis.onvoiceschanged = setVoiceAndSpeak;
            setTimeout(setVoiceAndSpeak, 300);
        }
    } catch (e) {
        console.warn('Voice synthesis error:', e);
        isFemaleSpeaking = false;
    }
}
window.speakFemalePaymentSuccess = speakFemalePaymentSuccess;

// 5. Audio Toast Floating Notification
let audioToastTimeout = null;
function showAudioToast(title, subtitle, isCelebration = false) {
    let toast = document.getElementById('wijaya-audio-toast');
    if (!toast) {
        toast = document.createElement('div');
        toast.id = 'wijaya-audio-toast';
        toast.className = 'fixed bottom-6 right-6 z-50 transition-all duration-500 transform translate-y-12 opacity-0 pointer-events-none';
        document.body.appendChild(toast);
    }

    const iconHtml = isCelebration 
        ? `<div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-emerald-500 to-amber-400 text-slate-950 flex items-center justify-center font-black text-lg shadow-md animate-bounce"><i class="fas fa-bell"></i></div>`
        : `<div class="w-9 h-9 rounded-xl bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center text-sm shadow-sm"><i class="fas fa-volume-up"></i></div>`;

    toast.innerHTML = `
        <div class="px-4 py-3 rounded-2xl bg-slate-900/95 text-white border ${isCelebration ? 'border-emerald-500/40 shadow-emerald-500/25' : 'border-white/10'} shadow-2xl backdrop-blur-xl flex items-center space-x-3.5 max-w-sm pointer-events-auto">
            ${iconHtml}
            <div class="truncate">
                <div class="flex items-center space-x-2">
                    <span class="text-xs font-black text-white">${title}</span>
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span>
                </div>
                <span class="text-[11px] text-slate-300 block truncate">${subtitle}</span>
            </div>
            <div class="eq-container flex-shrink-0 scale-75">
                <span class="eq-bar"></span>
                <span class="eq-bar"></span>
                <span class="eq-bar"></span>
            </div>
        </div>
    `;

    toast.classList.remove('translate-y-12', 'opacity-0');
    toast.classList.add('translate-y-0', 'opacity-100');

    if (audioToastTimeout) clearTimeout(audioToastTimeout);
    audioToastTimeout = setTimeout(() => {
        toast.classList.remove('translate-y-0', 'opacity-100');
        toast.classList.add('translate-y-12', 'opacity-0');
    }, 2800);
}

// 6. Sound Preview Demo Function (Untuk Navbar / Ujian Skripsi)
function toggleAudioPreview(btnEl) {
    // Demo 1: Suara Klik Umum
    playGeneralClickSound();

    // Demo 2: Suara Keranjang Pemesanan
    setTimeout(() => {
        playCartSound();
    }, 300);

    // Demo 3: Suara Klik Pesanan Gunting
    setTimeout(() => {
        playOrderSound();
    }, 750);

    // Demo 4: Suara Transaksi Berhasil
    setTimeout(() => {
        playTransactionSuccessSound();
    }, 1250);
}

// 7. Universal Event Bindings for ALL Clicks, Cart, Orders & Transactions
let lastClickAudioTime = 0;

function initSoundFXBindings() {
    window.playGeneralClickSound = playGeneralClickSound;
    window.playCartSound = playCartSound;
    window.playOrderSound = playOrderSound;
    window.playTransactionSuccessSound = playTransactionSuccessSound;
    window.toggleAudioPreview = toggleAudioPreview;

    // A. GLOBAL POINTERDOWN LISTENER: HANYA 1 KETUKAN TUNGGAL di setiap klik fitur, menu, tombol, tab, dan link
    if (!window.__wijayaKnockBound) {
        window.__wijayaKnockBound = true;

        const handleInteractiveKnock = (e) => {
            const clickable = e.target.closest(
                'button, ' +
                'a, ' +
                'input, ' +
                'select, ' +
                'textarea, ' +
                'summary, ' +
                '[role="button"], ' +
                '[onclick], ' +
                '.cursor-pointer, ' +
                '.btn-magnetic, ' +
                '.card-premium, ' +
                '.period-tab-btn, ' +
                '.chatbot-qr-btn, ' +
                'label'
            );

            if (!clickable) return;

            // Pastikan HANYA 1 KETUKAN per klik (global cooldown 280ms lintas frame & elemen)
            const nowTime = Date.now();
            let lastTs = lastClickAudioTime;
            try {
                if (window.top && window.top.__wijayaGlobalKnockTs) {
                    lastTs = Math.max(lastTs, window.top.__wijayaGlobalKnockTs);
                }
            } catch (err) {}

            if (nowTime - lastTs < 280) return;

            lastClickAudioTime = nowTime;
            try {
                if (window.top) window.top.__wijayaGlobalKnockTs = nowTime;
            } catch (err) {}

            // Bunyikan tepat 1 ketukan tunggal
            playGeneralClickSound();
        };

        document.addEventListener('pointerdown', handleInteractiveKnock, true);
    }

    // B. Pemicu Suara Transaksi Sukses: HANYA saat ada parameter payment_status=success dan belum pernah berbunyi
    const isPaymentSuccessParam = window.location.search.includes('payment_status=success');
    const isInsideShellParent = (window.self === window.top && !!document.getElementById('wijaya-viewport-frame'));

    // Jangan jalankan di frame shell induk agar tidak berbunyi ganda
    if (isPaymentSuccessParam && !isInsideShellParent) {
        const orderParam = new URLSearchParams(window.location.search).get('code') || 'global';
        const sessionKey = 'wijaya_sound_played_' + orderParam;

        if (!sessionStorage.getItem(sessionKey)) {
            sessionStorage.setItem(sessionKey, '1');

            let didPlayOnce = false;
            const unlockAndPlayOnce = () => {
                if (didPlayOnce) return;
                didPlayOnce = true;
                document.removeEventListener('click', unlockAndPlayOnce, true);
                document.removeEventListener('touchstart', unlockAndPlayOnce, true);
                getBarberAudioContext();
                setTimeout(() => {
                    playTransactionSuccessSound();
                }, 200);
            };

            // Coba bunyikan segera (1 kali saja)
            try {
                getBarberAudioContext();
                setTimeout(() => {
                    didPlayOnce = true;
                    playTransactionSuccessSound();
                }, 350);
            } catch (e) {}

            // Fallback interaksi pertama HANYA jika browser memblokir autoplay awal
            document.addEventListener('click', unlockAndPlayOnce, { once: true, capture: true });
            document.addEventListener('touchstart', unlockAndPlayOnce, { once: true, capture: true });
        }
    }
}

// ----------------------------------------------------
// 15. BARBERSHOP BACKGROUND MUSIC ENGINE (Wijaya Lounge Player)
// ----------------------------------------------------
const BARBER_PLAYLIST = [
    {
        title: "Urban Fade & Modern Chill",
        artist: "hoogway — Studio Mastered Lounge Beats",
        shortTitle: "1. Urban Chill",
        src: "/audio/barbershop-modern-1.mp3"
    },
    {
        title: "Gentlemen Grooming Session",
        artist: "Cocabona — Sleek Jazzy Chillhop",
        shortTitle: "2. Grooming Lounge",
        src: "/audio/barbershop-modern-2.mp3"
    },
    {
        title: "Night Owls Barbershop Beats",
        artist: "Casioo x Sleepermane — Smooth Grooves",
        shortTitle: "3. Night Owls",
        src: "/audio/barbershop-modern-3.mp3"
    }
];

let currentTrackIndex = 0;
let barberMusicAudio = null;
let isMusicPlaying = false;
let isMusicExpanded = false;
let isNavigatingAway = false;
let isChangingTrack = false;
let barberMusicLoopMode = localStorage.getItem('wijaya_music_loop_mode') || 'all'; // 'all' (loop playlist) | 'single' (loop 1 lagu)

function initBarberMusicEngine() {
    // Jika berada di dalam iframe (child viewport), delegasikan kontrol ke window.top (Master Shell)
    if (window.self !== window.top) {
        window.toggleBarberMusic = () => { if (window.top && window.top.toggleBarberMusic) window.top.toggleBarberMusic(); };
        window.nextBarberTrack = () => { if (window.top && window.top.nextBarberTrack) window.top.nextBarberTrack(); };
        window.prevBarberTrack = () => { if (window.top && window.top.prevBarberTrack) window.top.prevBarberTrack(); };
        window.switchBarberTrack = (i) => { if (window.top && window.top.switchBarberTrack) window.top.switchBarberTrack(i); };
        window.setBarberVolume = (v) => { if (window.top && window.top.setBarberVolume) window.top.setBarberVolume(v); };
        window.toggleLoopMode = () => { if (window.top && window.top.toggleLoopMode) window.top.toggleLoopMode(); };

        // Buka audio parent saat interaksi pertama di dalam frame jika browser sempat menahan autoplay
        const unlockParentAudio = () => {
            if (window.top && window.top.barberMusicAudio && window.top.barberMusicAudio.paused) {
                if (localStorage.getItem('wijaya_music_playing') !== 'false') {
                    window.top.barberMusicAudio.play().then(() => {
                        if (window.top.updateMusicUI) window.top.updateMusicUI();
                    }).catch(() => {});
                }
            }
        };
        ['click', 'touchstart', 'pointerdown'].forEach(evt => {
            document.addEventListener(evt, unlockParentAudio, { once: true, passive: true });
        });

        // Sinkronisasi status tombol navbar di dalam frame saat dimuat
        const navLabel = document.getElementById('navbar-music-label');
        if (navLabel && window.top && window.top.isMusicPlaying !== undefined) {
            navLabel.textContent = window.top.isMusicPlaying ? 'Musik: ON 🎵' : 'Musik: OFF';
        }
        return; // Jangan buat audio ganda atau floating player kedua di dalam frame
    }

    if (barberMusicAudio) return;

    window.barberMusicAudio = barberMusicAudio;
    window.toggleBarberMusic = toggleBarberMusic;
    window.nextBarberTrack = nextBarberTrack;
    window.prevBarberTrack = prevBarberTrack;
    window.switchBarberTrack = switchBarberTrack;
    window.setBarberVolume = setBarberVolume;
    window.toggleBarberMusicExpand = toggleBarberMusicExpand;
    window.seekMusicPosition = seekMusicPosition;
    window.toggleLoopMode = toggleLoopMode;

    barberMusicAudio = new Audio();
    barberMusicAudio.preload = 'auto';
    barberMusicAudio.loop = (barberMusicLoopMode === 'single');
    window.barberMusicAudio = barberMusicAudio;

    const savedIndex = parseInt(localStorage.getItem('wijaya_music_index') || '0', 10);
    currentTrackIndex = (savedIndex >= 0 && savedIndex < BARBER_PLAYLIST.length) ? savedIndex : 0;

    const savedVolume = parseFloat(localStorage.getItem('wijaya_music_volume') || '0.35');
    barberMusicAudio.volume = Math.max(0, Math.min(1, savedVolume));

    barberMusicAudio.src = BARBER_PLAYLIST[currentTrackIndex].src;

    // KETIKA LAGU HABIS OTOMATIS MENGULANG SENDIRI DARI AWAL
    barberMusicAudio.addEventListener('ended', () => {
        isChangingTrack = true;
        if (barberMusicLoopMode === 'single') {
            // Ulang lagu ini sendiri dari awal (0:00)
            barberMusicAudio.currentTime = 0;
            const p = barberMusicAudio.play();
            if (p !== undefined) {
                p.then(() => {
                    isMusicPlaying = true;
                    updateMusicUI();
                    isChangingTrack = false;
                }).catch(() => { isChangingTrack = false; });
            }
        } else {
            // Otomatis lanjut ke lagu berikutnya secara looping (1 -> 2 -> 1 -> 2 ... berulang terus tanpa henti)
            // Ketika lagu terakhir selesai, otomatis mengulang lagi dari lagu pertama (Track 1) dari awal
            currentTrackIndex = (currentTrackIndex + 1) % BARBER_PLAYLIST.length;
            switchBarberTrack(currentTrackIndex, true);
            setTimeout(() => { isChangingTrack = false; }, 400);
        }
    });

    barberMusicAudio.addEventListener('timeupdate', () => {
        if (!barberMusicAudio.paused) {
            localStorage.setItem('wijaya_music_time', barberMusicAudio.currentTime.toString());
            localStorage.setItem('wijaya_music_leave_ts', Date.now().toString());
            updateMusicProgressBar();
        }
    });

    barberMusicAudio.addEventListener('play', () => {
        isMusicPlaying = true;
        localStorage.setItem('wijaya_music_playing', 'true');
        updateMusicUI();
        const prompt = document.getElementById('music-autoplay-prompt');
        if (prompt) prompt.remove();
    });

    barberMusicAudio.addEventListener('pause', () => {
        // Jangan ubah status ke 'false' jika pause terjadi akibat unload browser / ganti track lagu
        if (isNavigatingAway || isChangingTrack) return;
        isMusicPlaying = false;
        localStorage.setItem('wijaya_music_playing', 'false');
        updateMusicUI();
    });

    // Simpan posisi audio saat reload / navigasi keras (F5 / form submit)
    const saveStateBeforeUnload = () => {
        isNavigatingAway = true;
        if (barberMusicAudio) {
            localStorage.setItem('wijaya_music_time', barberMusicAudio.currentTime.toString());
            localStorage.setItem('wijaya_music_leave_ts', Date.now().toString());
            localStorage.setItem('wijaya_music_index', currentTrackIndex.toString());
            if (isMusicPlaying) {
                localStorage.setItem('wijaya_music_playing', 'true');
            }
        }
    };
    window.addEventListener('beforeunload', saveStateBeforeUnload);
    window.addEventListener('pagehide', saveStateBeforeUnload);

    // Render floating player UI on document
    renderMusicPlayerDOM();

    // Default ke OTOMATIS BERPUTAR (Auto-Play) kecuali jika user secara eksplisit menekan jeda
    const savedPlayingSetting = localStorage.getItem('wijaya_music_playing');
    const shouldAutoPlay = (savedPlayingSetting === null || savedPlayingSetting === 'true');

    // Hitung posisi detik lagu agar benar-benar nyambung dari halaman sebelumnya
    const savedTime = parseFloat(localStorage.getItem('wijaya_music_time') || '0');
    const savedTimestamp = parseInt(localStorage.getItem('wijaya_music_leave_ts') || '0', 10);
    let resumeTime = savedTime;

    if (savedTimestamp > 0) {
        const elapsedSec = (Date.now() - savedTimestamp) / 1000;
        if (elapsedSec > 0 && elapsedSec < 35) {
            resumeTime += elapsedSec;
        }
    }

    const applyResumeTime = () => {
        if (resumeTime > 0 && barberMusicAudio.duration) {
            try {
                if (resumeTime < barberMusicAudio.duration) {
                    barberMusicAudio.currentTime = resumeTime;
                }
            } catch (e) {}
        }
    };

    if (barberMusicAudio.readyState >= 1) {
        applyResumeTime();
    }
    barberMusicAudio.addEventListener('loadedmetadata', applyResumeTime, { once: true });
    barberMusicAudio.addEventListener('canplay', applyResumeTime, { once: true });

    if (shouldAutoPlay) {
        localStorage.setItem('wijaya_music_playing', 'true');

        const attemptPlay = () => {
            const playPromise = barberMusicAudio.play();
            if (playPromise !== undefined) {
                playPromise.then(() => {
                    applyResumeTime();
                    isMusicPlaying = true;
                    updateMusicUI();
                    removeAutoplayUnblockers();
                }).catch(() => {
                    // Browser policy memblokir autoplay dingin: tampilkan prompt halus dan buka saat ada sentuhan
                    showAutoplayPrompt();
                });
            }
        };

        attemptPlay();

        // Listener one-time yang langsung membuka suara saat user menyentuh, mengklik, atau menggerakkan kursor
        const unlockOnUserGesture = () => {
            if (localStorage.getItem('wijaya_music_playing') !== 'false') {
                barberMusicAudio.play().then(() => {
                    applyResumeTime();
                    isMusicPlaying = true;
                    updateMusicUI();
                    removeAutoplayUnblockers();
                }).catch(() => {});
            }
        };

        const gestureEvents = ['click', 'pointerdown', 'touchstart', 'scroll', 'wheel', 'keydown', 'mousemove'];
        gestureEvents.forEach(evt => {
            document.addEventListener(evt, unlockOnUserGesture, { once: true, passive: true });
        });

        function removeAutoplayUnblockers() {
            gestureEvents.forEach(evt => {
                document.removeEventListener(evt, unlockOnUserGesture);
            });
            const prompt = document.getElementById('music-autoplay-prompt');
            if (prompt) {
                prompt.style.transition = 'opacity 0.4s ease, transform 0.4s ease';
                prompt.style.opacity = '0';
                prompt.style.transform = 'translate(-50%, -12px)';
                setTimeout(() => prompt.remove(), 400);
            }
        }
    }
}

function showAutoplayPrompt() {
    if (document.getElementById('music-autoplay-prompt')) return;
    const promptHtml = `
    <div id="music-autoplay-prompt" onclick="toggleBarberMusic()" class="fixed top-5 left-1/2 -translate-x-1/2 z-50 px-4 py-2 rounded-full bg-slate-900/95 border border-emerald-500/40 text-white text-xs font-bold shadow-2xl backdrop-blur-xl flex items-center space-x-2.5 cursor-pointer hover:scale-105 transition duration-300 animate-bounce">
        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span>
        <span>🎵 Musik Santai Barbershop Aktif (Klik / Sentuh di mana saja untuk dengarkan)</span>
        <button type="button" class="w-6 h-6 rounded-full bg-emerald-500 text-slate-950 flex items-center justify-center text-[10px] ml-1 font-black">▶</button>
    </div>`;
    document.body.insertAdjacentHTML('beforeend', promptHtml);
}

function renderMusicPlayerDOM() {
    if (document.getElementById('barber-music-player')) return;

    const isAdmin = window.location.pathname.startsWith('/admin');
    const positionClass = isAdmin ? 'bottom-6 left-72 max-md:left-4 z-40' : 'bottom-6 left-6 max-md:bottom-4 max-md:left-4 z-40';

    const playerHtml = `
    <div id="barber-music-player" class="fixed ${positionClass} no-print transition-all duration-300 font-sans">
        <!-- Minimized Floating Pill -->
        <div id="music-pill-minimized" onclick="toggleBarberMusicExpand()" class="group cursor-pointer flex items-center space-x-3 px-3.5 py-2.5 rounded-full bg-slate-900/95 hover:bg-slate-900 text-white border border-emerald-500/35 backdrop-blur-2xl shadow-2xl shadow-black/80 transition-all duration-300 hover:scale-105 hover:border-emerald-400">
            <div class="vinyl-disc" id="pill-vinyl-disc"></div>
            <div class="max-w-[130px] sm:max-w-[170px] truncate">
                <span id="pill-music-title" class="text-xs font-black text-white block truncate leading-tight">${BARBER_PLAYLIST[currentTrackIndex].title}</span>
                <span id="pill-music-status" class="text-[10px] text-emerald-400 font-bold flex items-center space-x-1.5 mt-0.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span>
                    <span id="pill-status-text">Putar Musik Barbershop</span>
                </span>
            </div>
            <button type="button" onclick="event.stopPropagation(); toggleBarberMusic();" class="w-8 h-8 rounded-full bg-gradient-to-tr from-emerald-500 to-teal-400 text-slate-950 flex items-center justify-center font-bold text-xs shadow-md hover:scale-110 transition flex-shrink-0" id="pill-play-btn" title="Putar / Jeda Musik">
                <i class="fas fa-play" id="pill-play-icon"></i>
            </button>
            <button type="button" onclick="event.stopPropagation(); toggleBarberMusicExpand();" class="text-slate-400 hover:text-white text-xs pl-0.5 pr-1" title="Buka Kontrol Musik">
                <i class="fas fa-chevron-up"></i>
            </button>
        </div>

        <!-- Expanded Turntable Player Card -->
        <div id="music-card-expanded" class="hidden w-80 sm:w-96 rounded-3xl bg-slate-900/95 border border-emerald-500/40 text-white backdrop-blur-2xl shadow-2xl shadow-black/90 p-5 transition-all duration-300">
            <div class="flex items-center justify-between pb-3 mb-3.5 border-b border-white/10">
                <div class="flex items-center space-x-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-400 animate-ping"></span>
                    <span class="text-xs font-extrabold uppercase tracking-wider text-emerald-400">Wijaya Barbershop Lounge</span>
                </div>
                <div class="flex items-center space-x-1.5">
                    <button type="button" onclick="toggleLoopMode()" id="music-loop-toggle-btn" class="px-2 py-1 rounded-lg bg-white/10 hover:bg-emerald-500/20 text-emerald-400 text-[10px] font-bold flex items-center space-x-1 border border-white/10 transition" title="Ganti Mode Loop (Ulang Sendiri)">
                        <i class="fas fa-redo-alt" id="loop-mode-icon"></i>
                        <span id="loop-mode-label">Loop: Semua</span>
                    </button>
                    <button type="button" onclick="toggleBarberMusicExpand()" class="text-slate-400 hover:text-white text-xs p-1">
                        <i class="fas fa-chevron-down"></i>
                    </button>
                </div>
            </div>

            <div class="flex items-center space-x-4 mb-4">
                <div class="vinyl-disc" id="expanded-vinyl-disc" style="width: 58px; height: 58px;"></div>
                <div class="flex-1 min-w-0">
                    <h4 id="expanded-music-title" class="text-sm font-black text-white truncate">${BARBER_PLAYLIST[currentTrackIndex].title}</h4>
                    <p id="expanded-music-artist" class="text-xs text-slate-400 truncate mt-0.5">${BARBER_PLAYLIST[currentTrackIndex].artist}</p>
                    <div class="flex items-center space-x-2 mt-2">
                        <div class="eq-container" id="expanded-eq-bars">
                            <span class="eq-bar"></span>
                            <span class="eq-bar"></span>
                            <span class="eq-bar"></span>
                            <span class="eq-bar"></span>
                        </div>
                        <span id="expanded-music-state-text" class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Musik Dijeda</span>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-slate-800 rounded-full h-1.5 mb-4 overflow-hidden cursor-pointer" onclick="seekMusicPosition(event)">
                <div id="music-progress-bar" class="bg-gradient-to-r from-emerald-500 to-teal-300 h-full w-0 transition-all duration-200"></div>
            </div>

            <!-- Controls (Prev, Play/Pause, Next) & Volume -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center space-x-2">
                    <button type="button" onclick="prevBarberTrack()" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition" title="Lagu Sebelumnya">
                        <i class="fas fa-step-backward text-xs"></i>
                    </button>
                    <button type="button" onclick="toggleBarberMusic()" class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-emerald-500 via-teal-400 to-emerald-400 text-slate-950 flex items-center justify-center text-lg font-black shadow-lg shadow-emerald-500/40 hover:scale-105 transition" id="expanded-play-btn" title="Putar / Jeda Musik">
                        <i class="fas fa-play" id="expanded-play-icon"></i>
                    </button>
                    <button type="button" onclick="nextBarberTrack()" class="w-9 h-9 rounded-xl bg-white/10 hover:bg-white/20 text-white flex items-center justify-center transition" title="Lagu Selanjutnya">
                        <i class="fas fa-step-forward text-xs"></i>
                    </button>
                </div>

                <!-- Volume Control Slider -->
                <div class="flex items-center space-x-2">
                    <i class="fas fa-volume-up text-xs text-slate-400"></i>
                    <input type="range" min="0" max="1" step="0.05" value="${barberMusicAudio ? barberMusicAudio.volume : 0.35}" oninput="setBarberVolume(this.value)" class="music-volume-slider w-20 sm:w-24">
                </div>
            </div>

            <!-- Playlist Selector Pills -->
            <div class="grid grid-cols-3 gap-1.5 pt-3 border-t border-white/10 text-[10px]">
                ${BARBER_PLAYLIST.map((t, idx) => `
                <button type="button" onclick="switchBarberTrack(${idx})" id="playlist-btn-${idx}" class="px-2 py-1.5 rounded-xl ${currentTrackIndex === idx ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'bg-white/5 text-slate-300 border border-white/10'} text-center truncate font-bold transition hover:bg-white/10" title="${t.title} (${t.artist})">
                    ${t.shortTitle}
                </button>
                `).join('')}
            </div>
        </div>
    </div>`;

    document.body.insertAdjacentHTML('beforeend', playerHtml);
    updateMusicUI();
}

function toggleBarberMusic() {
    if (!barberMusicAudio) initBarberMusicEngine();

    if (barberMusicAudio.paused) {
        barberMusicAudio.play().then(() => {
            isMusicPlaying = true;
            localStorage.setItem('wijaya_music_playing', 'true');
            updateMusicUI();
        }).catch(err => {
            console.warn('Playback error:', err);
        });
    } else {
        barberMusicAudio.pause();
        isMusicPlaying = false;
        localStorage.setItem('wijaya_music_playing', 'false');
        updateMusicUI();
    }
}

function nextBarberTrack(forcePlay = false) {
    currentTrackIndex = (currentTrackIndex + 1) % BARBER_PLAYLIST.length;
    switchBarberTrack(currentTrackIndex, forcePlay);
}

function prevBarberTrack(forcePlay = false) {
    currentTrackIndex = (currentTrackIndex - 1 + BARBER_PLAYLIST.length) % BARBER_PLAYLIST.length;
    switchBarberTrack(currentTrackIndex, forcePlay);
}

function switchBarberTrack(index, forcePlay = false) {
    if (!barberMusicAudio) initBarberMusicEngine();
    currentTrackIndex = index;
    localStorage.setItem('wijaya_music_index', index.toString());
    localStorage.setItem('wijaya_music_time', '0');

    barberMusicAudio.src = BARBER_PLAYLIST[currentTrackIndex].src;
    barberMusicAudio.currentTime = 0;

    // Otomatis putar dari awal saat pindah lagu / looping
    if (isMusicPlaying || forcePlay) {
        isMusicPlaying = true;
        localStorage.setItem('wijaya_music_playing', 'true');
        const p = barberMusicAudio.play();
        if (p !== undefined) {
            p.then(() => {
                isMusicPlaying = true;
                updateMusicUI();
            }).catch(e => console.warn('Play error:', e));
        }
    }
    updateMusicUI();
}

function toggleLoopMode() {
    barberMusicLoopMode = (barberMusicLoopMode === 'all') ? 'single' : 'all';
    localStorage.setItem('wijaya_music_loop_mode', barberMusicLoopMode);
    if (barberMusicAudio) {
        barberMusicAudio.loop = (barberMusicLoopMode === 'single');
    }
    updateMusicUI();
}

function setBarberVolume(val) {
    if (!barberMusicAudio) return;
    const v = parseFloat(val);
    barberMusicAudio.volume = v;
    localStorage.setItem('wijaya_music_volume', v.toString());
}

function toggleBarberMusicExpand() {
    isMusicExpanded = !isMusicExpanded;
    const pill = document.getElementById('music-pill-minimized');
    const card = document.getElementById('music-card-expanded');
    if (!pill || !card) return;

    if (isMusicExpanded) {
        pill.classList.add('hidden');
        card.classList.remove('hidden');
    } else {
        card.classList.add('hidden');
        pill.classList.remove('hidden');
    }
}

function seekMusicPosition(e) {
    if (!barberMusicAudio || !barberMusicAudio.duration) return;
    const rect = e.currentTarget.getBoundingClientRect();
    const clickX = e.clientX - rect.left;
    const pct = Math.max(0, Math.min(1, clickX / rect.width));
    barberMusicAudio.currentTime = pct * barberMusicAudio.duration;
    updateMusicProgressBar();
}

function updateMusicProgressBar() {
    const bar = document.getElementById('music-progress-bar');
    if (!bar || !barberMusicAudio || !barberMusicAudio.duration) return;
    const pct = (barberMusicAudio.currentTime / barberMusicAudio.duration) * 100;
    bar.style.width = pct + '%';
}

function updateMusicUI() {
    const track = BARBER_PLAYLIST[currentTrackIndex];

    // Vinyl spinning
    const pillVinyl = document.getElementById('pill-vinyl-disc');
    const expVinyl = document.getElementById('expanded-vinyl-disc');
    if (pillVinyl) pillVinyl.classList.toggle('is-playing', isMusicPlaying);
    if (expVinyl) expVinyl.classList.toggle('is-playing', isMusicPlaying);

    // Play/Pause Icons
    const pillIcon = document.getElementById('pill-play-icon');
    const expIcon = document.getElementById('expanded-play-icon');
    if (pillIcon) pillIcon.className = isMusicPlaying ? 'fas fa-pause' : 'fas fa-play';
    if (expIcon) expIcon.className = isMusicPlaying ? 'fas fa-pause' : 'fas fa-play';

    // Status Texts
    const pillStatus = document.getElementById('pill-status-text');
    const expStateText = document.getElementById('expanded-music-state-text');
    if (pillStatus) {
        pillStatus.textContent = isMusicPlaying ? 'Musik Berputar 🎵' : 'Putar Musik Barbershop';
    }
    if (expStateText) {
        expStateText.textContent = isMusicPlaying ? 'Memutar Musik 🎵' : 'Musik Dijeda';
        expStateText.className = isMusicPlaying ? 'text-[10px] font-bold text-emerald-400 uppercase tracking-wider' : 'text-[10px] font-bold text-slate-400 uppercase tracking-wider';
    }

    // Title & Artist
    const pillTitle = document.getElementById('pill-music-title');
    const expTitle = document.getElementById('expanded-music-title');
    const expArtist = document.getElementById('expanded-music-artist');
    if (pillTitle) pillTitle.textContent = track.title;
    if (expTitle) expTitle.textContent = track.title;
    if (expArtist) expArtist.textContent = track.artist;

    // Loop Mode Label
    const loopLabel = document.getElementById('loop-mode-label');
    const loopIcon = document.getElementById('loop-mode-icon');
    if (loopLabel) {
        loopLabel.textContent = (barberMusicLoopMode === 'single') ? 'Loop: 1 Lagu' : 'Loop: Semua';
    }
    if (loopIcon) {
        loopIcon.className = (barberMusicLoopMode === 'single') ? 'fas fa-repeat' : 'fas fa-redo-alt';
    }

    // Playlist buttons active state
    for (let i = 0; i < BARBER_PLAYLIST.length; i++) {
        const btn = document.getElementById('playlist-btn-' + i);
        if (btn) {
            if (i === currentTrackIndex) {
                btn.className = 'px-2.5 py-1.5 rounded-xl bg-emerald-500/20 text-emerald-300 border border-emerald-500/40 text-left truncate font-bold transition';
            } else {
                btn.className = 'px-2.5 py-1.5 rounded-xl bg-white/5 text-slate-300 border border-white/10 text-left truncate font-bold transition hover:bg-white/10';
            }
        }
    }

    // Navbar Music Button (if present in top document or in iframe)
    const navLabel = document.getElementById('navbar-music-label');
    if (navLabel) {
        navLabel.textContent = isMusicPlaying ? 'Musik: ON 🎵' : 'Musik: OFF';
    }
    try {
        const frame = document.getElementById('wijaya-viewport-frame');
        if (frame && frame.contentDocument) {
            const frameNavLabel = frame.contentDocument.getElementById('navbar-music-label');
            if (frameNavLabel) {
                frameNavLabel.textContent = isMusicPlaying ? 'Musik: ON 🎵' : 'Musik: OFF';
            }
        }
    } catch (e) {}
}

// Global View Helper Functions (Available everywhere)
window.fillLoginCreds = function(u, p) {
    const loginEl = document.getElementById('login');
    const passEl = document.getElementById('password');
    if (loginEl) loginEl.value = u;
    if (passEl) passEl.value = p;
    if (loginEl) loginEl.focus();
};

window.formatRupiah = function(num) {
    return 'Rp ' + (num || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
};

window.formatRpCheckout = function(num) {
    return 'Rp ' + (num || 0).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
};

window.previewLocalFile = function(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('image-preview');
            const placeholder = document.getElementById('image-placeholder');
            if (preview) {
                preview.src = e.target.result;
                preview.classList.remove('hidden');
            }
            if (placeholder) placeholder.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
};

// Auto-run on document ready
document.addEventListener('DOMContentLoaded', () => {
    initBarberCanvasParticles();
    initAdvancedTiltAndGlare();
    initMagneticButtons();
    initRippleClicks();
    initLiveSonarQueue();
    initSoundFXBindings();
    initBarberMusicEngine();
});
