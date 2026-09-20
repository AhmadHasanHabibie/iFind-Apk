<<<<<<< HEAD
﻿# i-Find — Platform Reservasi & LBS (Location Based Service)

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
=======
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

You may also try the [Laravel Bootcamp](https://bootcamp.laravel.com), where you will be guided through building a modern Laravel application from scratch.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains thousands of video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the [Laravel Partners program](https://partners.laravel.com).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[WebReinvent](https://webreinvent.com/)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Curotec](https://www.curotec.com/services/technologies/laravel/)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[DevSquad](https://devsquad.com/hire-laravel-developers)**
- **[Jump24](https://jump24.co.uk)**
- **[Redberry](https://redberry.international/laravel/)**
- **[Active Logic](https://activelogic.com)**
- **[byte5](https://byte5.de)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
>>>>>>> a30346de2a442db245cd6dcb6351f792b19d0f3d
