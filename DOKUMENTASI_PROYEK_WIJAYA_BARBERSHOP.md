# DOKUMENTASI DAN RANCANGAN SISTEM INFORMASI WIJAYA BARBERSHOP
**Berdasarkan Laporan Skripsi: Rancang Bangun Sistem Informasi Pemesanan dan Pembayaran Jasa Pangkas Rambut Berbasis Web pada Wijaya Barbershop**

---

## 1. Identitas Proyek & Referensi Skripsi
* **Judul**: Rancang Bangun Sistem Informasi Pemesanan dan Pembayaran Jasa Pangkas Rambut Berbasis Web pada Wijaya Barbershop
* **Penyusun**: Juni Sanjaya (NPM: 22430116)
* **Institusi**: Program Studi S1 Ilmu Komputer, Fakultas Ilmu Komputer, Universitas Muhammadiyah Metro (2026)
* **Dosen Pembimbing**:
  1. Dani Anggoro, S.Kom., M.Kom.
  2. Gunayanti Kemalasari Siregar, S.Kom., MTI.
* **Referensi Repository**: `junisanjaya2026/skripsi-wijayabarbershop`

---

## 2. Latar Belakang & Tujuan
### Latar Belakang Masalah
1. Wijaya Barbershop melayani rata-rata hingga 50 pelanggan per hari.
2. Proses pemesanan masih manual (pelanggan harus datang langsung ke lokasi untuk mendaftar antrean).
3. Pembayaran hanya tunai di tempat (*on the spot*).
4. Sering terjadi antrean panjang yang membuat pelanggan bosan menunggu, membuang waktu, atau beralih ke barbershop lain.
5. Kasir mencatat pesanan dan pendapatan harian secara konvensional di buku laporan, menyulitkan rekapitulasi data.

### Solusi yang Dibangun
Sistem informasi berbasis web yang memungkinkan:
* **Pelanggan**: Melakukan pemesanan jasa pangkas rambut & produk perawatan secara online, memilih metode pembayaran (tunai atau digital via QRIS / Midtrans), mendapatkan nomor antrean serta resi digital tanpa perlu antre fisik terlebih dahulu.
* **Karyawan / Kasir**: Mengelola katalog layanan/produk, memantau pesanan masuk secara real-time, mengonfirmasi pembayaran tunai/non-tunai, mengelola antrean, dan melihat laporan transaksi.
* **Pemilik / Direktur**: Memantau perkembangan operasional dan laporan keuangan bisnis secara transparan.

---

## 3. Struktur Organisasi Wijaya Barbershop
* **Direktur**: Agung Wijaya
* **Kasir**: Mely, Riles
* **Barber / Capster**: Doni, Lana, Yoyo

---

## 4. Arsitektur & Teknologi (Tech Stack)
* **Bahasa Pemrograman**: PHP (Framework Laravel)
* **Database**: MySQL / MariaDB (dengan fallback SQLite otomatis)
* **Metode Pengembangan**: Waterfall Model (Requirements Definition, System & Software Design, Implementation & Unit Testing, Integration & System Testing, Operation & Maintenance)
* **Paradigma**: Object-Oriented Programming (OOP) & Arsitektur MVC (Model-View-Controller)
* **Frontend**: Responsive Blade Views, HTML5, CSS3, JavaScript, TailwindCSS / Bootstrap
* **Payment Gateway**: Midtrans Snap API (QRIS, E-Wallet ShopeePay/GoPay, Virtual Account Bank Transfer) + Pembayaran Tunai (Cash)
* **Alat Pendukung**: Visual Studio Code, Git, XAMPP / Laragon, Composer

---

## 5. Aktor & Peran (Role & Permissions)
Sistem memiliki 2 aktor utama dalam diagram use-case skripsi:
1. **Pelanggan (Customer)**:
   * Registrasi akun baru & Login
   * Menjelajahi Beranda (Landing page profil Wijaya Barbershop, statistik, slider)
   * Melihat katalog layanan pangkas rambut dan produk rambut (*View Produk dan Jasa*)
   * Menambahkan item layanan/produk ke Keranjang (*Cart*)
   * Melakukan Pemesanan / Checkout (*Checkout*)
   * Melakukan Pembayaran (*Payment* - Cash atau QRIS/Midtrans Snap)
   * Mendapatkan Resi / Bukti Pemesanan & Nomor Antrean (*Resi*)
   * Memantau daftar riwayat pesanan (*My Orders*)
2. **Karyawan / Kasir / Admin**:
   * Login Karyawan
   * Dashboard Ringkasan Bisnis
   * Kelola Pesanan (*Management Pesanan*): Verifikasi, ubah status pesanan (`pending`, `settlement`, `completed`, `cancelled`)
   * Kelola Produk & Jasa (*Input Item Produk dan Jasa*): CRUD Item
   * Kelola Kategori (*Input Kategori*): CRUD Categories
   * Kelola Akun & Role (*Management Akun & Kelola Role*)
   * Rekapitulasi Data Transaksi & Laporan Penjualan

---

## 6. Struktur Basis Data (ERD & Tabel Relasi)
Sesuai dengan **Gambar 24 (Relasi Tabel Sistem Wijaya Barbershop)** pada buku skripsi:

### 1. Tabel `roles`
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | bigint unsigned (PK, AI) | ID Role |
| `role_name` | varchar(255) | Nama role (admin, kasir, barber, customer) |
| `description` | varchar(255) | Deskripsi peran |
| `created_at` | timestamp | Waktu dibuat |
| `updated_at` | timestamp | Waktu diperbarui |
| `deleted_at` | timestamp | Soft delete |

### 2. Tabel `users`
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | bigint unsigned (PK, AI) | ID Pengguna |
| `username` | varchar(255) | Username login |
| `password` | varchar(255) | Password terenkripsi (Bcrypt) |
| `fullname` | varchar(255) | Nama lengkap user |
| `email` | varchar(255) | Alamat email (unik) |
| `phone` | varchar(255) | Nomor HP / WhatsApp |
| `on_duty` | tinyint(1) | Status bertugas (untuk barber/kasir: 1 = bertugas, 0 = libur) |
| `role_id` | bigint unsigned (FK) | Relasi ke `roles.id` |
| `created_at` | timestamp | Waktu pendaftaran |
| `updated_at` | timestamp | Waktu update |
| `deleted_at` | timestamp | Soft delete |

### 3. Tabel `categories`
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | bigint unsigned (PK, AI) | ID Kategori |
| `category_name` | varchar(255) | Nama kategori (Jasa Pangkas Rambut, Perawatan & Treatment Rambut, Produk Rambut & Styling) |
| `description` | varchar(255) | Penjelasan kategori |
| `created_at` | timestamp | Waktu dibuat |
| `updated_at` | timestamp | Waktu update |
| `deleted_at` | timestamp | Soft delete |

### 4. Tabel `items`
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | bigint unsigned (PK, AI) | ID Produk / Jasa |
| `item_name` | varchar(255) | Nama layanan / produk |
| `description` | text | Deskripsi lengkap layanan / produk |
| `price` | int | Harga item (IDR) |
| `category_id` | bigint unsigned (FK) | Relasi ke `categories.id` |
| `image_path` | varchar(255) | Path / URL foto item |
| `is_active` | tinyint(1) | Status ketersediaan (1 = aktif, 0 = nonaktif) |
| `created_at` | timestamp | Waktu dibuat |
| `updated_at` | timestamp | Waktu update |
| `deleted_at` | timestamp | Soft delete |

#### Master Data Produk & Layanan (Sesuai Skripsi & Profil Wijaya Barbershop):
1. **Jasa Pangkas Rambut**:
   * `Potong Rambut Dewasa`: Layanan pangkas rambut pria dewasa rapi presisi sesuai bentuk wajah + pembersihan rambut & styling pomade (Rp 35.000).
   * `Paket Potong Rambut Lengkap (Cuci + Pijat)`: Pangkas rambut eksklusif + keramas cuci air hangat + pijat relaksasi kepala + handuk dingin & hair tonic (Rp 50.000).
   * `Potong Rambut Anak & Balita`: Layanan potong rambut khusus anak-anak dan balita dengan pelayanan ramah, sabar, dan rapi (Rp 30.000).
   * `Cukur Kumis & Jenggot (Shaving)`: Cukur jenggot dan kumis presisi dengan busa hangat, pisau cukur steril, dan aftershave menyegarkan (Rp 25.000).
   * `Hair Tattoo / Ukir Rambut Artistik`: Seni ukir garis pola dan motif artistik pada rambut pria (keahlian khusus Capster Lana) (Rp 40.000).
2. **Perawatan & Treatment Rambut**:
   * `Semir Rambut / Pewarnaan (Hair Color)`: Layanan pewarnaan rambut pria dengan warna natural (hitam pekat, cokelat tua) maupun fashion bleach aman bagi kulit kepala (Rp 75.000).
   * `Cuci Rambut & Creambath (Hair Spa)`: Perawatan nutrisi akar rambut dengan krim creambath, pijatan relaksasi kepala melancarkan sirkulasi darah dan basmi ketombe (Rp 45.000).
3. **Produk Rambut & Styling**:
   * `Pomade Rambut Wijaya (Water Based)`: Produk minyak rambut pomade water based aroma segar maskulin, daya rekat tinggi (strong hold), berkilau alami, dan mudah dibilas (Rp 65.000).
   * `Clay Rambut (Matte Clay Hold)`: Produk penata rambut dengan hasil akhir tanpa kilau (matte finish), memberikan volume dan tekstur alami untuk gaya rambut modern (Rp 70.000).
   * `Hair Tonic Ginseng (Vitamin Rambut)`: Tonik rambut dengan ekstrak ginseng alami untuk menguatkan akar rambut, merangsang pertumbuhan rambut sehat, dan sensasi dingin (Rp 40.000).

### 5. Tabel `orders`
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | bigint unsigned (PK, AI) | ID Pesanan |
| `order_code` | varchar(255) | Kode pesanan unik (contoh: `ORD1778413577`) |
| `user_id` | bigint unsigned (FK) | Relasi ke `users.id` (pemesan) |
| `subtotal` | varchar(255) / int | Total harga sebelum diskon & pajak |
| `discount` | varchar(255) / int | Nilai potongan / diskon (default 0) |
| `tax` | varchar(255) / int | Nilai pajak (misal PPN 10%) |
| `grand_total` | varchar(255) / int | Total akhir yang harus dibayar |
| `status` | enum('pending', 'settlement', 'completed', 'cancelled') | Status progres pemesanan |
| `payment_method`| enum('tunai', 'bank_transfer', 'qris') | Metode pembayaran |
| `notes` | text | Catatan khusus dari pelanggan |
| `queue_number` | int | Nomor antrean pelanggan pada hari tersebut |
| `queue_time` | datetime | Waktu jadwal / perkiraan layanan antrean |
| `created_at` | timestamp | Waktu pembuatan transaksi |
| `updated_at` | timestamp | Waktu update pesanan |
| `deleted_at` | timestamp | Soft delete |

### 6. Tabel `order_items`
| Kolom | Tipe Data | Keterangan |
| :--- | :--- | :--- |
| `id` | bigint unsigned (PK, AI) | ID Detail Item Pesanan |
| `order_id` | bigint unsigned (FK) | Relasi ke `orders.id` |
| `item_id` | bigint unsigned (FK) | Relasi ke `items.id` |
| `quantity` | int | Jumlah kuantitas yang dipesan |
| `price` | int | Harga satuan saat dipesan |
| `tax` | int | Pajak per item |
| `total_price` | int | Subtotal item (`price * quantity`) |
| `created_at` | timestamp | Waktu dibuat |
| `updated_at` | timestamp | Waktu update |
| `deleted_at` | timestamp | Soft delete |

---

## 7. Desain Antarmuka & Halaman (UI/UX Mockup & Implementation)
Berdasarkan screenshot implementasi (Gambar 31 s/d 37):
1. **Halaman Autentikasi**:
   * Split-screen modern: Sisi kiri hijau/hitam elegan berlabel *"PREMIUM BARBERSHOP EXPERIENCE"* dengan statistik (*5K+ Pelanggan, 12 Barber Profesional, 8 Tahun Berdiri*).
   * Sisi kanan: Form Register Akun (*Nama Lengkap, Email, Username, No WhatsApp, Password, Konfirmasi Password*) & Form Login (*Email/Username, Password*).
2. **Landing Page Pelanggan**:
   * Header / Navbar: Logo Wijaya Barber, Navigasi (*Home, Layanan, My Orders, Kontak*), Keranjang Belanja (*Cart*), Menu Profil.
   * Hero Section: Slider foto barbershop dengan teks *"Potong Rambut Berkualitas di Wijaya Barber - Gaya modern untuk pria yang percaya diri"* + Tombol CTA *"Booking Sekarang"*.
   * Bagian Layanan: Card item pangkas rambut & produk dengan foto, harga, deskripsi, dan tombol *"Tambah ke Keranjang"*.
3. **Keranjang & Checkout Form**:
   * Form pengisian data pemesan: Nama Lengkap, Nomor WhatsApp, Catatan Pesanan (opsional).
   * Rincian Pesanan: Tabel item yang dipilih, kuantitas, harga, subtotal.
   * Ringkasan Biaya: Subtotal, Pajak 10%, Grand Total.
   * Opsi Metode Pembayaran: Radio button pilihan `QRIS` atau `Tunai`.
   * Tombol *"KONFIRMASI PESANAN"*.
4. **Integrasi Midtrans Snap**:
   * Modal popup Midtrans untuk pembayaran QRIS (GoPay, ShopeePay), Transfer Virtual Account Bank (BCA, Mandiri, BNI, BRI), dan Kartu Kredit.
   * Callback otomatis merubah status pesanan menjadi `settlement`.
5. **Halaman Resi & Antrean**:
   * Menampilkan nomor antrean (`queue_number`), waktu antrean, kode order, status pembayaran, dan rincian pesanan yang dapat dicetak/disimpan.
6. **Halaman My Orders (Riwayat Pesanan Pelanggan)**:
   * Daftar pesanan yang pernah dibuat beserta status terkini.
7. **Dashboard Admin / Karyawan**:
   * Sidebar navigasi: Dashboard, Kelola Pesanan, Daftar Item, Users, Kelola Role, Categories.
   * Tabel Data Order: No, Kode Order, Customer, WhatsApp, Subtotal, Diskon, Pajak, Grand Total, Status Badge, Metode Pembayaran, Tanggal, Aksi (Lihat Detail, Konfirmasi Tunai, Selesai, Batalkan).
   * Form Tambah / Edit Item: Input Nama Item, Dropdown Kategori, Deskripsi, Harga, Upload Foto, Toggle Switch Aktif/Nonaktif.

---

## 8. Alur Kerja Sistem (Flowchart & Activity Diagram)
1. **Alur Pemesanan & Pembayaran Pelanggan**:
   `Mulai` -> `Kunjungi Website Wijaya Barbershop` -> `Pilih Jasa / Produk` -> `Masukkan ke Keranjang` -> `Isi Formulir (WhatsApp & Catatan)` -> `Pilih Pembayaran (Tunai / QRIS Midtrans)` -> `Bayar` -> `Dapatkan Nomor Antrean & Resi` -> `Datang ke Barbershop` -> `Diverifikasi Karyawan` -> `Dilayani Capster` -> `Selesai`.
2. **Alur Karyawan / Kasir**:
   `Login Karyawan` -> `Buka Dashboard Order` -> `Cek Pesanan Masuk` -> `Jika Tunai: Terima Uang & Ubah Status Jadi Settlement` -> `Jika QRIS/Online: Otomatis Settlement dari Midtrans Callback` -> `Arahkan ke Capster Bertugas` -> `Ubah Status Pesanan ke Completed`.

---

## 9. Rekomendasi Fitur Tambahan (Saran Pengembangan dari Skripsi Bab 5)
* Notifikasi WhatsApp otomatis menggunakan WhatsApp Gateway API (Fonnte / Wablas) saat booking berhasil dan saat antrean hampir tiba.
* Export data laporan penjualan ke PDF / Excel.
* Tampilan responsif mobile-friendly.
* Layanan Cukur Panggilan (Home Service).

---

## 10. Modernisasi Visual & Animasi Bergerak Bespoke (Exclusive Barbershop Engine)
Sistem animasi telah dirombak total dari animasi generik/pasaran menjadi **animasi eksklusif, interaktif, dan berkelas tinggi (bespoke luxury)** yang dirancang khusus untuk tema barbershop modern:

1. **Interactive HTML5 Canvas Particle Engine (`#barber-particle-canvas`)**:
   - Menghasilkan partikel kabut uap aromatik emas (*golden embers*), stardust emerald, dan gunting mikro pangkas rambut (*micro shears*).
   - **Interaktif terhadap kursor mouse**: Partikel merespons gerakan mouse secara dinamis (efek *repel* dan *sparkle trail* 60 FPS).
   - Diterapkan di seluruh portal: Web Tamu, Halaman Login/Register, serta Portal Staf Admin/Kasir.
2. **Laser Border Beam Tracer (`.border-beam-card`)**:
   - Efek garis laser neon berputar secara terus-menerus (`conic-gradient`) mengitari bingkai kartu login, live status antrean, ringkasan booking home service, dan kartu statistik HUD admin.
3. **Realistic 3D Perspective Tilt & Specular Cursor Glare (`.tilt-card-luxury`)**:
   - Kartu katalog item pangkas rambut dan dashboard memiringkan perspektif 3D mengikuti posisi mouse, dilengkapi sorot kilau kaca cermin (*specular glare radial gradient*) yang mengikuti kursor secara presisi.
4. **Realistic Dual-Blade Snipping Scissors (`.scissor-snip-container`)**:
   - Animasi gunting SVG dengan gerakan membuka-menutup bilah realistis (*snip-snip motion*) pada logo brand, header login, dan tombol dial.
5. **Rotating Sonar Sweep Radar (`.sonar-radar-box`)**:
   - Tampilan radar sweeping 360° dengan jarum sapuan gelombang dan ring berdenyut pada widget antrean live dan status server admin real-time.
6. **Barbershop Lounge Soundwave Audio Equalizer (`.eq-container`)**:
   - Bar gelombang audio bergerak acak dinamis di samping logo dan header staf mewakili atmosfer lounge barbershop yang hidup.
7. **Magnetic Button Hover (`.btn-magnetic`) & Wave Ripple Click (`.ripple-circle`)**:
   - Tombol utama tertarik secara magnetis ke arah kursor saat didekati, dan memancarkan gelombang ripple melingkar saat diklik.
8. **Asynchronous Organic Floating Badges (`.float-async-1`, `.float-async-2`, `.float-async-3`)**:
   - Kartu capster bertugas (Doni, Lana, Yoyo) mengambang dengan ritme frekuensi berbeda sehingga tidak kaku atau monoton.
9. **Cyber-Luxury HUD Stat Cards (`.hud-card`, `.hud-corner`)**:
   - Kartu metrik pendapatan, pesanan, dan antrean di dashboard staf memiliki bracket sudut HUD taktis dan hitungan angka naik dinamis (*data-counter*).
