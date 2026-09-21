# i-Find — Platform Reservasi & LBS (Location Based Service)

Platform direktori dan reservasi tempat nongkrong, coworking space, dan kafe berbasis Laravel 10 yang dirancang khusus untuk kemudahan reservasi kursi, pencarian berbasis GPS akurat (Haversine Formula), sistem pembayaran DP/Lunas, E-Ticket QR Code, chat interaktif, dan pusat bantuan tiket multi-role.

---

## 🚀 Fitur Utama

### 1. Multi-Role Architecture
- **Admin**: Dashboard analitik, verifikasi akun staf, moderasi toko (Approve/Reject/Suspend), manajemen pengguna (Blokir/Aktifkan), CRUD Kategori & Fasilitas tempat, pusat tiket bantuan, dan chat staf.
- **Staff (Pemilik / Pengelola Toko)**: Manajemen profil toko, upload foto & QRIS, pengaturan slot waktu (bulk generate & status closed), konfirmasi/penolakan booking, proses refund manual, scanner QR check-in pelanggan di tempat, tiket bantuan ke admin, dan chat pelanggan.
- **User (Customer / Pelajar)**: Eksplorasi spot populer dengan filter kategori, rating, fasilitas, dan jarak terdekat (GPS Location-Based Service), reservasi kursi dengan proteksi *pessimistic lock* (bebas *overbooking*), upload bukti pembayaran, E-Ticket QR dinamis, ulasan/rating tempat, live chat dengan toko, dan tiket pengaduan ke admin.

### 2. Fitur Unggulan
- **Integritas Transaksi Anti-Overbooking**: Menggunakan *Pessimistic Locking* (`lockForUpdate`) di database untuk menjamin sisa kursi tidak pernah bernilai minus meskipun dibooking bersamaan di detik yang sama.
- **Location-Based Service (LBS)**: Perhitungan jarak real-time dari koordinat pengguna ke toko menggunakan formula Haversine dengan sorting jarak dan filter radius.
- **QR Code Check-in System**: Generate QR unik untuk setiap pesanan yang telah dikonfirmasi dan dapat divalidasi oleh kamera / scanner staf di lokasi.
- **End-to-End Support Helpdesk**: Modul tiket bantuan dua arah antara Pelanggan/Staf dengan Administrator.
- **Auto Cancellation Scheduler**: Pembatalan otomatis dan pengembalian kapasitas kursi untuk booking yang melewati batas waktu pembayaran (*payment deadline*).

---

## 🛠️ Panduan Instalasi

### 1. Kloning & Dependensi
```bash
# Install PHP dependencies
composer install

# Install JS dependencies
npm install
```

### 2. Konfigurasi Lingkungan (.env)
```bash
# Salin file environment
cp .env.example .env

# Generate application key
php artisan key:generate
```

Pastikan konfigurasi database di `.env` sudah sesuai (MySQL):
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ifind_db
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Migrasi & Seeding Data
```bash
# Jalankan migrasi dan seeder awal
php artisan migrate:fresh --seed

# Buat symbolic link untuk file uploads (bukti bayar, foto toko, QRIS)
php artisan storage:link
```

### 4. Menjalankan Aplikasi
```bash
# Jalankan development server
php artisan serve

# Jalankan vite asset compiler di terminal terpisah
npm run dev

# (Opsional) Jalankan task scheduler untuk auto-cancel booking kadaluarsa
php artisan schedule:work
```

---

## 👥 Akun Login Demo (Default Seeder)

| Role | Email | Password | Keterangan |
|:---|:---|:---|:---|
| **Admin** | `admin@ifind.com` | `password` | Super Admin Platform |
| **Staf Toko** | `staff@ifind.com` | `password` | Pengelola Toko / Kafe Terverifikasi |
| **Customer** | `user@ifind.com` | `password` | Pelanggan / Pelajar |

---

## 📄 Lisensi
Proyek ini dikembangkan di bawah lisensi [MIT](LICENSE).
