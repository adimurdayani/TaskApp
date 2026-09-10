# Task App

Task App adalah aplikasi manajemen tugas berbasis Laravel, Livewire, dan Flux UI. Aplikasi ini menyediakan autentikasi pengguna, dashboard, pengelolaan tugas, kategori tugas, serta manajemen akun pengguna berdasarkan role.

## Teknologi

- PHP 8.3
- Laravel 13
- Livewire 4
- Flux UI 2
- Laravel Fortify
- Tailwind CSS 4
- Vite Plus
- SQLite sebagai database default

## Fitur Utama

- Autentikasi login, register, reset password, two-factor authentication, dan passkey.
- Dashboard statistik tugas.
- CRUD tugas dengan kategori, prioritas, status, dan tanggal jatuh tempo.
- CRUD kategori tugas khusus admin.
- CRUD akun pengguna khusus admin.
- Pengaturan profil, tampilan, keamanan, 2FA, dan passkey.
- Role pengguna: `admin` dan `user`.

## Kebutuhan Sistem

Pastikan perangkat sudah memiliki:

- PHP 8.3 atau lebih baru
- Composer
- Node.js dan npm
- SQLite atau database lain yang didukung Laravel

## Instalasi

Clone repository project:

```bash
git clone <url-repository>
cd taskapp
```

Install dependency PHP:

```bash
composer install
```

Install dependency JavaScript:

```bash
npm install
```

Salin file environment:

```bash
cp .env.example .env
```

Generate application key:

```bash
php artisan key:generate
```

## Konfigurasi Database

Secara default project menggunakan SQLite:

```env
DB_CONNECTION=sqlite
```

Buat file database SQLite jika belum ada:

```bash
touch database/database.sqlite
```

Jalankan migrasi:

```bash
php artisan migrate
```

Untuk mengisi data awal, jalankan seeder:

```bash
php artisan db:seed
```

Seeder akan membuat akun demo, kategori, dan tugas dummy.

## Akun Demo

Setelah menjalankan seeder, gunakan akun berikut untuk login:

| Role | Email | Password |
| --- | --- | --- |
| Admin | `admin@admin.com` | `password` |
| User | `user1@user.com` | `password` |
| User | `user2@user.com` | `password` |

## Cara Cepat Setup

Project ini juga menyediakan script setup bawaan:

```bash
composer setup
```

Script ini akan menjalankan instalasi dependency, membuat `.env`, generate key, menjalankan migrasi, install npm package, dan build asset.

## Menjalankan Project

Jalankan server development Laravel dan Vite:

```bash
composer dev
```

Atau jalankan secara terpisah:

```bash
php artisan serve
npm run dev
```

Buka aplikasi di browser:

```text
http://localhost:8000
```

## Build Asset

Untuk membuat asset production:

```bash
npm run build
```

## Testing dan Quality Check

Jalankan test:

```bash
php artisan test
```

Jalankan semua pengecekan project:

```bash
composer test
```

Format kode PHP:

```bash
composer lint
```

Cek format tanpa mengubah file:

```bash
composer lint:check
```

Cek tipe statis:

```bash
composer types:check
```

## Struktur Penting

```text
app/Models                 Model User, Task, dan Category
app/Enum                   Enum role, status, prioritas, dan status kategori
resources/views/pages      Halaman Livewire Volt
resources/views/layouts    Layout aplikasi dan autentikasi
routes/web.php             Route utama aplikasi
routes/settings.php        Route halaman pengaturan
database/migrations        Struktur tabel database
database/seeders           Data awal aplikasi
database/factories         Factory data testing/seeding
```

## Route Utama

- `/` halaman awal
- `/dashboard` dashboard pengguna
- `/task` halaman pengelolaan tugas
- `/category` halaman kategori tugas khusus admin
- `/user` halaman akun pengguna khusus admin
- `/settings/profile` pengaturan profil
- `/settings/appearance` pengaturan tampilan
- `/settings/security` pengaturan keamanan

## Catatan Role

User biasa hanya dapat mengelola dan melihat tugas miliknya sendiri. Admin dapat mengakses fitur kategori, akun pengguna, dan melihat data tugas secara lebih luas sesuai logic aplikasi.
