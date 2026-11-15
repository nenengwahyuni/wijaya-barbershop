# Wijaya Barbershop - Sistem Informasi Pemesanan & Pembayaran Berbasis Web

Proyek implementasi sistem informasi berdasarkan buku skripsi:
> **"Rancang Bangun Sistem Informasi Pemesanan dan Pembayaran Jasa Pangkas Rambut Berbasis Web pada Wijaya Barbershop"**  
> **Penulis**: Juni Sanjaya (NPM: 22430116)  
> **Program Studi**: S1 Ilmu Komputer, Universitas Muhammadiyah Metro (2026)  
> **Dosen Pembimbing**: Dani Anggoro, S.Kom., M.Kom. & Gunayanti Kemalasari Siregar, S.Kom., MTI.  

---

## 🌟 Fitur Utama Sistem

### 1. Pelanggan (Customer)
* **Landing Page Interaktif (Gambar 33)**: Hero banner, slider promosi, statistik barbershop (*5K+ Pelanggan, 12 Barber, 8 Tahun*).
* **Katalog Layanan & Produk**: Filter kategori (*Pangkas Rambut, Treatment, Styling Products*), deskripsi & harga.
* **Keranjang Belanja (Cart)**: Tambah item, atur kuantitas, kalkulasi harga otomatis.
* **Formulir Checkout & Pemesanan (Gambar 30 & 36)**:
  * Pengisian Nama Lengkap, No WhatsApp, Catatan Khusus.
  * Perhitungan Subtotal, Pajak PPN 10%, dan Grand Total.
  * Pilihan metode pembayaran: **QRIS / Online (Midtrans)** atau **Tunai (di Kasir)**.
* **Resi Digital & Tiket Antrean (Gambar 36 & 37)**:
  * Nomor Antrean Pelanggan (`queue_number`) & Perkiraan Jam Layanan (`queue_time`).
  * Kode Pesanan Unik (contoh: `ORD1778413577`).
  * Fitur Cetak Resi / Tiket Antrean.
* **Riwayat Pesanan Saya (Gambar 22)**: Melacak status seluruh transaksi yang pernah dilakukan.
* **Notifikasi WhatsApp Otomatis**:
  * Mengirimkan pesan WhatsApp otomatis berisi rincian pesanan baru, kode order, nomor antrean, dan jadwal ke nomor WhatsApp pelanggan.
  * Notifikasi otomatis **Konfirmasi Pembayaran Lunas (Settlement)** saat pembayaran Midtrans terverifikasi atau diverifikasi kasir.
  * Notifikasi otomatis saat pesanan dinyatakan **Selesai (Completed)**.
  * Dilengkapi tombol pintas **Kirim Bukti ke WhatsApp** dan integrasi API WhatsApp Gateway (Fonnte) dengan log sistem di `storage/logs/whatsapp.log`.
* **Layanan Cukur Panggilan / Home Service (Pengembangan Bab V Saran Skripsi)**:
  * Halaman khusus pemesanan cukur panggilan (`/home-service`) maupun pilihan langsung di halaman `/checkout`.
  * Penentuan **Alamat Lengkap Pelanggan**, **Patokan Lokasi**, dan pendeteksi **Titik Koordinat GPS Google Maps** otomatis.
  * Pemilihan **Jadwal Kunjungan** (Tanggal & Jam kedatangan barber) serta **Pilihan Capster / Barber Favorit** (*Doni, Lana, Yoyo*).
  * Kalkulasi otomatis **Ongkos Transport Zona Area** (*Metro Pusat Rp 10.000*, *Metro Timur/Barat/Utara/Selatan Rp 15.000*, *Sekitar Kota Metro Rp 25.000*).
* **Desain Modern, Animasi Bergerak & Interaktif**:
  * **Barber Pole Animation**: Indikator spiral berputar khas barbershop klasik pada logo navigasi.
  * **Running Announcement Marquee Ticker**: Teks berjalan real-time di bagian atas website memuat status operasional barbershop.
  * **Smooth Scroll Reveal (IntersectionObserver)**: Animasi masuk bertahap (*fade-up, fade-left, fade-right, zoom*) saat halaman digulir.
  * **Animated Number Counters**: Efek angka statistik (*5.000+ Pelanggan, 12 Capster, 8 Tahun*) yang menghitung bertambah dinamis secara otomatis.
  * **Interactive 3D Tilt Card**: Kartu preview antrean live di Hero Section yang bergerak dinamis mengikuti kursor mouse (3D Parallax Tilt).
  * **Floating Quick Action Widget**: Tombol aksi mengambang di pojok kanan bawah dengan efek *beacon pulse* untuk akses instan ke WhatsApp CS, Booking Studio, dan Cukur Panggilan.
  * **Shimmer Hover & Glassmorphism**: Efek pantulan cahaya (*light sweep shimmer*) pada tombol dan kartu layanan.

### 2. Karyawan / Kasir / Admin
* **Dashboard Karyawan**: Ringkasan total omset, transaksi hari ini, antrean pending, dan barber bertugas.
* **Data Order / Kelola Pesanan (Gambar 29 & 34)**:
  * Tabel Data Order: No, Kode Order, Customer, WhatsApp, Subtotal, Diskon, Pajak, Grand Total, Status Badge, Metode Bayar, Tanggal, Aksi.
  * Konfirmasi instan pembayaran tunai (`pending` -> `settlement`).
  * Penyelesaian pesanan saat selesai dicukur (`settlement` -> `completed`).
* **Kelola Data Items (Gambar 35)**:
  * Tambah & edit layanan/produk: Nama Item, Harga, Dropdown Kategori, Upload Foto, Deskripsi, Switch Aktifkan Item.
* **Kelola Kategori**: Pengelompokan layanan & produk.
* **Kelola User & Barber**: Mengatur jadwal capster bertugas (*On Duty* / *Off*).
* **Kelola Role**: Struktur hak akses (*admin, kasir, barber, customer*).

### 3. Integrasi Pembayaran Midtrans Snap
* Mendukung pembayaran non-tunai via **Midtrans Snap** (QRIS GoPay, ShopeePay, Virtual Account Bank Transfer).
* Dilengkapi **Simulator Pembayaran Mock** untuk kemudahan testing tanpa hambatan koneksi saat ujian/sidang skripsi.
* Webhook API Callback untuk auto-update status pesanan.

---

## 🚀 Cara Menjalankan Aplikasi

### Cara 1: Sekali Klik (Rekomendasi di Windows)
Cukup klik ganda file **`jalankan_server.bat`** di folder proyek ini.
Skrip akan otomatis:
1. Mendeteksi PHP XAMPP yang terpasang di komputer Anda.
2. Memeriksa migrasi 6 tabel basis data & data seeder.
3. Membuka browser ke `http://localhost:8000`.

### Cara 2: Melalui Terminal / Command Prompt
```bash
# Jalankan server
php -S 127.0.0.1:8000 -t public
```
Buka browser di: **`http://localhost:8000`**

---

## 🔑 Akun Demo Pengujian

Semua akun menggunakan kata sandi (password): **`password123`**

| Role / Jabatan | Username | Email | Keterangan |
| :--- | :--- | :--- | :--- |
| **Direktur / Admin** | `agung` | direktur@wijayabarber.com | Akses penuh seluruh dashboard staf |
| **Kasir 1** | `mely` | mely@wijayabarber.com | Kasir penerima pembayaran & antrean |
| **Kasir 2** | `riles` | riles@wijayabarber.com | Kasir operasional |
| **Barber / Capster** | `doni` | doni@wijayabarber.com | Capster Senior bertugas (*On Duty*) |
| **Pelanggan (Penulis)**| `juni` | junisanjaya2026@gmail.com | Akun customer untuk pengujian pemesanan |
| **Pelanggan Umum** | `budi` | budi@gmail.com | Akun customer pengujian |

---

## 🗄️ Konfigurasi Database

Sistem secara cerdas mendukung **Dual-Driver Mode**:
1. **MySQL / MariaDB (XAMPP)**:
   * File schema SQL murni tersedia di: `database/schema.sql`
   * File data awal tersedia di: `database/seed.sql`
   * Anda bisa langsung mengimpor file `database/schema.sql` lalu `database/seed.sql` ke **phpMyAdmin**.
2. **SQLite (Zero Configuration Local)**:
   * Otomatis aktif saat MySQL XAMPP belum dijalankan, sehingga aplikasi langsung bisa digunakan tanpa konfigurasi rumit.

