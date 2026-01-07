# PRCF Keuangan

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-8.2+-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/TailwindCSS-3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white" alt="TailwindCSS">
  <img src="https://img.shields.io/badge/Alpine.js-3.x-8BC0D0?style=for-the-badge&logo=alpine.js&logoColor=white" alt="Alpine.js">
</p>

Sistem manajemen keuangan proyek untuk **People Resources and Conservation Foundation (PRCF)** Indonesia. Aplikasi ini dirancang untuk mengotomatisasi alur kerja keuangan mulai dari pengajuan proposal, pencatatan transaksi bank, manajemen piutang, hingga pelaporan keuangan kepada donor.

---

## 📋 Daftar Isi

-   [Fitur Utama](#-fitur-utama)
-   [Tech Stack](#-tech-stack)
-   [Struktur Role & Akses](#-struktur-role--akses)
-   [Workflow Sistem](#-workflow-sistem)
-   [Instalasi](#-instalasi)
-   [Konfigurasi](#-konfigurasi)
-   [Menjalankan Aplikasi](#-menjalankan-aplikasi)
-   [Test Accounts](#-test-accounts)
-   [Struktur Database](#-struktur-database)
-   [API Endpoints](#-api-endpoints)
-   [Deployment](#-deployment)

---

## ✨ Fitur Utama

### 📄 Manajemen Proposal

-   Pembuatan proposal dengan detail budget per village dan expense code
-   Upload dokumen TOR (Terms of Reference) dan file budget
-   Workflow approval: Draft → Submitted → Approved/Rejected
-   Dual currency support (USD & IDR) dengan exchange rate

### 💰 Buku Bank (Bank Book)

-   Pencatatan transaksi debit/kredit per proyek
-   Tracking saldo bulanan dengan carry-forward otomatis
-   Export ke Excel untuk pelaporan

### 📊 Buku Piutang (Receivables)

-   Tracking dana unliquidated per Project Manager
-   Manajemen penyelesaian piutang
-   Aging analysis untuk monitoring

### 📈 Laporan Keuangan

-   Pembuatan laporan dengan detail pengeluaran
-   Workflow verifikasi (SA) dan approval (FM)
-   Attachment bukti pengeluaran (nota/invoice)

### 🌍 Laporan Donor

-   Laporan khusus format donor
-   Compile financial & activity summary
-   Tracking deadline pelaporan

### 👥 Manajemen User

-   Multi-role access control
-   OTP verification untuk login
-   Activity logging untuk audit trail

### ⚙️ System Control (Admin)

-   Toggle maintenance mode
-   Toggle registration
-   System health monitoring

---

## 🛠 Tech Stack

| Layer              | Technology                                          |
| ------------------ | --------------------------------------------------- |
| **Backend**        | Laravel 11.x, PHP 8.2+                              |
| **Database**       | SQLite (development), MySQL/PostgreSQL (production) |
| **Frontend**       | Blade Templates, Tailwind CSS (CDN), Alpine.js      |
| **Authentication** | Laravel Auth dengan OTP verification                |
| **File Storage**   | Laravel Storage (local)                             |

---

## 👥 Struktur Role & Akses

| Role                 | Kode  | Akses Utama                                                       |
| -------------------- | ----- | ----------------------------------------------------------------- |
| **Administrator**    | Admin | Kelola user, activity log, system control                         |
| **Finance Manager**  | FM    | Approve proposal & laporan, kelola master data, budget management |
| **Staff Accountant** | SA    | Verifikasi laporan, akses buku bank & piutang                     |
| **Project Manager**  | PM    | Buat proposal & laporan kegiatan                                  |
| **Direktur**         | DIR   | Dashboard eksekutif & oversight                                   |

### Matriks Akses Fitur

| Fitur                | Admin | FM  | SA  | PM  | DIR |
| -------------------- | :---: | :-: | :-: | :-: | :-: |
| Dashboard            |  ✅   | ✅  | ✅  | ✅  | ✅  |
| Proposal (Buat)      |  ❌   | ❌  | ❌  | ✅  | ❌  |
| Proposal (Approve)   |  ❌   | ✅  | ❌  | ❌  | ❌  |
| Laporan (Buat)       |  ❌   | ❌  | ❌  | ✅  | ❌  |
| Laporan (Verifikasi) |  ❌   | ❌  | ✅  | ❌  | ❌  |
| Laporan (Approve)    |  ❌   | ✅  | ❌  | ❌  | ❌  |
| Buku Bank            |  ❌   | ✅  | ✅  | ❌  | ❌  |
| Buku Piutang         |  ❌   | ✅  | ✅  | ❌  | ❌  |
| Master Data          |  ❌   | ✅  | ❌  | ❌  | ❌  |
| Kelola User          |  ✅   | ❌  | ❌  | ❌  | ❌  |
| System Control       |  ✅   | ❌  | ❌  | ❌  | ❌  |

---

## 🔄 Workflow Sistem

### Proposal Workflow

```
┌─────────┐     ┌───────────┐     ┌────────────┐     ┌──────────┐
│  Draft  │────▶│ Submitted │────▶│ ApprovedFM │ or  │ Rejected │
└─────────┘     └───────────┘     └────────────┘     └──────────┘
     │               │                   │
     │               │                   ▼
   [PM]            [PM]          [Budget Allocated]
  Create          Submit              [Bank Debit]
                                  [Piutang Created]
```

### Laporan Keuangan Workflow

```
┌─────────┐     ┌───────────┐     ┌──────────┐     ┌──────────┐
│  Draft  │────▶│ Submitted │────▶│ Verified │────▶│ Approved │
└─────────┘     └───────────┘     └──────────┘     └──────────┘
     │               │                 │                │
   [PM]            [PM]              [SA]             [FM]
  Create          Submit           Verify           Approve
                                      │
                                      ▼
                              ┌────────────────┐
                              │Revision Request│
                              └────────────────┘
```

---

## 🚀 Instalasi

### Prasyarat

-   PHP >= 8.2
-   Composer
-   Node.js & NPM (untuk asset compilation, opsional)
-   SQLite / MySQL / PostgreSQL

### Langkah Instalasi

```bash
# 1. Clone repository
git clone <repository-url>
cd prcf_laravel

# 2. Install dependencies
composer install

# 3. Copy environment file
cp .env.example .env

# 4. Generate application key
php artisan key:generate

# 5. Konfigurasi database di .env
# Untuk SQLite (default):
# DB_CONNECTION=sqlite
# DB_DATABASE=/absolute/path/to/database.sqlite

# 6. Jalankan migrasi dan seeder
php artisan migrate --seed

# 7. Buat symbolic link untuk storage
php artisan storage:link
```

---

## ⚙️ Konfigurasi

### Environment Variables (.env)

```env
APP_NAME="PRCF Keuangan"
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8001

# Database
DB_CONNECTION=sqlite
DB_DATABASE=/path/to/database/database.sqlite

# Mail (untuk OTP)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
```

---

## ▶️ Menjalankan Aplikasi

### Development Server

```bash
# Jalankan server Laravel
php artisan serve --port=8001

# Akses di browser
http://localhost:8001
```

### Dengan Cloudflare Tunnel (Remote Access)

```bash
# 1. Jalankan Laravel server
php artisan serve --port=8001

# 2. Jalankan Cloudflare Tunnel
cloudflared tunnel run prcf-tunnel

# 3. Akses via domain
https://prcf-test.indevs.in
```

---

## 🔐 Test Accounts

| Email           | Password      | Role             |
| --------------- | ------------- | ---------------- |
| `admin@prcf.id` | `password123` | Administrator    |
| `fm@prcf.id`    | `password123` | Finance Manager  |
| `sa@prcf.id`    | `password123` | Staff Accountant |
| `pm@prcf.id`    | `password123` | Project Manager  |
| `dir@prcf.id`   | `password123` | Direktur         |

> **Note:** Semua akun test sudah di-seed dan siap digunakan.

---

## 🗄️ Struktur Database

### Entity Relationship Overview

```
┌────────────┐     ┌──────────────┐     ┌─────────────────────┐
│   Users    │     │    Proyek    │     │ ProjectCodeBudget   │
├────────────┤     ├──────────────┤     ├─────────────────────┤
│ id_user    │     │ kode_proyek  │◄───▶│ kode_proyek         │
│ nama       │     │ nama_proyek  │     │ id_village          │
│ role       │     │ donor        │     │ exp_code            │
│ email      │     │ status       │     │ budget_usd/idr      │
│ status     │     │ periode      │     │ used_usd/idr        │
└────────────┘     └──────────────┘     └─────────────────────┘
      │                   │
      │                   ▼
      │            ┌──────────────┐     ┌─────────────────────┐
      │            │   Proposal   │────▶│ ProposalBudgetDetail│
      │            ├──────────────┤     ├─────────────────────┤
      └───────────▶│ id_proposal  │     │ id_village          │
                   │ kode_proyek  │     │ exp_code            │
                   │ status       │     │ amount_usd/idr      │
                   │ approved_by  │     └─────────────────────┘
                   └──────────────┘
```

### Models

| Model                   | Table                   | Primary Key       | Description            |
| ----------------------- | ----------------------- | ----------------- | ---------------------- |
| `User`                  | users                   | id_user           | Data pengguna sistem   |
| `Proyek`                | proyek                  | kode_proyek       | Data proyek            |
| `Village`               | villages                | id                | Data desa/lokasi       |
| `Donor`                 | donors                  | id_donor          | Data donor             |
| `ExpenseCode`           | expense_codes           | id                | Kode pengeluaran       |
| `ProjectCodeBudget`     | project_code_budgets    | id                | Alokasi budget         |
| `Proposal`              | proposals               | id_proposal       | Proposal pengajuan     |
| `ProposalBudgetDetail`  | proposal_budget_details | id                | Detail budget proposal |
| `BukuBankHeader`        | buku_bank_header        | id_bank_header    | Header buku bank       |
| `BukuBankDetail`        | buku_bank_detail        | id_bank_detail    | Detail transaksi bank  |
| `BukuPiutangHeader`     | buku_piutang_header     | id_piutang_header | Header buku piutang    |
| `BukuPiutangDetail`     | buku_piutang_detail     | id_piutang_detail | Detail piutang         |
| `LaporanKeuanganHeader` | laporan_keuangan_header | id                | Header laporan         |
| `LaporanKeuanganDetail` | laporan_keuangan_detail | id                | Detail laporan         |
| `LaporanDonor`          | laporan_donor           | id                | Laporan donor          |
| `Notification`          | notifications           | id                | Notifikasi user        |
| `ActivityLog`           | activity_logs           | id                | Log aktivitas          |
| `SystemSetting`         | system_settings         | id                | Pengaturan sistem      |

---

## 📁 Struktur Direktori

```
prcf_laravel/
├── app/
│   ├── Enums/              # Status enums (UserRole, ProposalStatus, etc.)
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Admin/      # Admin controllers
│   │   │   ├── Auth/       # Authentication controllers
│   │   │   └── Finance/    # Finance manager controllers
│   │   └── Middleware/     # Custom middleware
│   ├── Models/             # Eloquent models
│   └── Services/           # Business logic services
├── database/
│   ├── migrations/         # Database migrations
│   ├── seeders/           # Database seeders
│   └── database.sqlite    # SQLite database
├── resources/
│   └── views/
│       ├── layouts/       # Layout templates
│       ├── auth/          # Authentication views
│       ├── proposals/     # Proposal views
│       ├── reports/       # Report views
│       └── admin/         # Admin views
├── routes/
│   └── web.php           # Web routes
└── docs/                  # Documentation
```

---

## 🌐 Deployment

### Cloudflare Tunnel Setup

Lihat dokumentasi lengkap di [docs/CLOUDFLARE_TUNNEL_SETUP.md](docs/CLOUDFLARE_TUNNEL_SETUP.md)

### Production Checklist

-   [ ] Set `APP_ENV=production`
-   [ ] Set `APP_DEBUG=false`
-   [ ] Configure production database (MySQL/PostgreSQL)
-   [ ] Set up proper mail configuration
-   [ ] Configure file storage (S3/cloud storage)
-   [ ] Run `php artisan config:cache`
-   [ ] Run `php artisan route:cache`
-   [ ] Run `php artisan view:cache`

---

## 📚 Dokumentasi Tambahan

-   [Cloudflare Tunnel Setup](docs/CLOUDFLARE_TUNNEL_SETUP.md)
-   [Deployment Guide](docs/DEPLOYMENT_GUIDE.md)
-   [Multi-Agent Architecture](agents.md)

---

## 📄 License

This project is proprietary software for PRCF Indonesia.

---

## 👨‍💻 Development

Dibuat dengan ❤️ untuk PRCF Indonesia
