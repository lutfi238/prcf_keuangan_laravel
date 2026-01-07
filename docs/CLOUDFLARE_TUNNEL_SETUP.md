# Panduan Setup Cloudflare Tunnel dari XAMPP Windows

## 📋 Informasi Domain

Berdasarkan gambar yang diberikan:
- **Domain**: `prcf-test.indevs.in`
- **Status**: ACTIVE ✅
- **Provider**: Stackryze Domains
- **Nameserver**: n1.stackryze.com, n2.stackryze.com
- **DNS Access**: Ada (bisa tambah record via Stackryze)

---

## 🎯 Rencana Setup

**Fase 1**: Quick Tunnel dengan `*.trycloudflare.com` (testing instan)
**Fase 2**: Setup tunnel permanen dengan domain `prcf-test.indevs.in`
**Fase 3**: Migrasi ke server production (VPS/Shared Hosting)

---

## ✅ Checklist Persiapan XAMPP/Windows

### 1. Cek Konfigurasi XAMPP

```powershell
# Buka PowerShell sebagai Administrator

# Cek XAMPP berjalan
netstat -an | findstr :80
netstat -an | findstr :443

# Pastikan Apache running di port 80 (atau 8080)
```

**Yang perlu dicek:**
- [ ] Apache running (ikon XAMPP Control Panel hijau)
- [ ] MySQL running
- [ ] Port 80 atau 8080 aktif
- [ ] Document root: `C:\xampp\htdocs\prcf_laravel\public`

### 2. Konfigurasi Virtual Host (Opsional tapi Recommended)

Edit `C:\xampp\apache\conf\extra\httpd-vhosts.conf`:

```apache
<VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/prcf_laravel/public"
    ServerName prcf.local
    ServerAlias prcf-test.indevs.in
    
    <Directory "C:/xampp/htdocs/prcf_laravel/public">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog "logs/prcf-error.log"
    CustomLog "logs/prcf-access.log" common
</VirtualHost>
```

Lalu edit `C:\Windows\System32\drivers\etc\hosts` (sebagai Admin):
```
127.0.0.1   prcf.local
```

Restart Apache di XAMPP Control Panel.

### 3. Cek Aplikasi Laravel Berjalan

```powershell
cd C:\xampp\htdocs\prcf_laravel

# Test akses via browser
start http://localhost/prcf_laravel/public
# atau jika pakai virtual host:
start http://prcf.local
```

---

## 🚀 Fase 1: Quick Tunnel (Testing Instan)

### Step 1: Download cloudflared

1. Buka: https://developers.cloudflare.com/cloudflare-one/connections/connect-apps/install-and-setup/installation/
2. Download **Windows 64-bit** installer
3. Atau via PowerShell:

```powershell
# Download langsung
Invoke-WebRequest -Uri "https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-windows-amd64.exe" -OutFile "C:\cloudflared\cloudflared.exe"

# Buat folder dan tambahkan ke PATH
mkdir C:\cloudflared -ErrorAction SilentlyContinue
$env:Path += ";C:\cloudflared"
```

### Step 2: Jalankan Quick Tunnel

```powershell
# Dari folder cloudflared
cd C:\cloudflared

# Jalankan tunnel ke port 80 (default XAMPP)
.\cloudflared.exe tunnel --url http://localhost:80

# ATAU jika pakai virtual host prcf.local
.\cloudflared.exe tunnel --url http://prcf.local:80
```

**Output yang diharapkan:**
```
INF Thank you for trying Cloudflare Tunnel. ...
INF Your quick Tunnel has been created! ...
INF +-----------------------------------------------------------+
INF |  Your URL is: https://random-words-here.trycloudflare.com |
INF +-----------------------------------------------------------+
```

### Step 3: Test Akses

1. Copy URL `https://xxx.trycloudflare.com`
2. Buka di browser
3. Aplikasi Laravel seharusnya tampil dengan HTTPS! ✅

**⚠️ Peringatan Keamanan Quick Tunnel:**
- URL bersifat publik - siapa saja bisa akses
- Jangan gunakan untuk data sensitif
- Matikan tunnel saat tidak dipakai (Ctrl+C di terminal)
- Cocok untuk: demo, testing, development

---

## 🔧 Fase 2: Tunnel Permanen dengan Domain Sendiri

### Step 1: Login ke Cloudflare

```powershell
cd C:\cloudflared
.\cloudflared.exe tunnel login
```

Browser akan terbuka - login dengan akun Cloudflare Anda.
Jika belum punya akun, daftar gratis di https://dash.cloudflare.com/sign-up

### Step 2: Buat Tunnel Baru

```powershell
# Buat tunnel dengan nama
.\cloudflared.exe tunnel create prcf-tunnel

# Output: Created tunnel prcf-tunnel with id <TUNNEL-ID>
# Catat TUNNEL-ID ini!
```

### Step 3: Konfigurasi Tunnel

Buat file `C:\cloudflared\config.yml`:

```yaml
tunnel: <TUNNEL-ID>
credentials-file: C:\Users\<USERNAME>\.cloudflared\<TUNNEL-ID>.json

ingress:
  - hostname: prcf-test.indevs.in
    service: http://localhost:80
  - service: http_status:404
```

**Ganti:**
- `<TUNNEL-ID>` dengan ID dari step 2
- `<USERNAME>` dengan username Windows Anda

### Step 4: Setup DNS di Stackryze

Karena domain di-manage Stackryze (bukan Cloudflare), ada 2 opsi:

**Opsi A: CNAME Record (Recommended)**

1. Login ke Stackryze: https://domain.stackryze.com
2. Klik "DNS Records" di sidebar
3. Tambahkan record:
   ```
   Type: CNAME
   Name: @ (atau kosong untuk root)
   Target: <TUNNEL-ID>.cfargotunnel.com
   TTL: 300
   ```

**Opsi B: Pindahkan Nameserver ke Cloudflare**

1. Di Cloudflare Dashboard, tambahkan site `prcf-test.indevs.in` (atau parent domain jika perlu)
2. Copy nameserver yang diberikan Cloudflare
3. Di Stackryze, ubah nameserver ke Cloudflare
4. Tunggu propagasi (1-48 jam)

**Untuk sementara, pakai Opsi A dulu (CNAME).**

### Step 5: Jalankan Tunnel

```powershell
# Test dengan config
.\cloudflared.exe tunnel --config C:\cloudflared\config.yml run

# Atau jalankan tunnel by name
.\cloudflared.exe tunnel run prcf-tunnel
```

### Step 6: Verifikasi

```powershell
# Cek status tunnel
.\cloudflared.exe tunnel info prcf-tunnel

# Test akses
start https://prcf-test.indevs.in
```

---

## ⚙️ Konfigurasi Environment Laravel

### Update .env untuk Tunnel

```env
# Untuk development via tunnel
APP_NAME="PRCF Keuangan"
APP_ENV=local
APP_DEBUG=true

# PENTING: Sesuaikan dengan URL tunnel
# Quick Tunnel:
APP_URL=https://xxx.trycloudflare.com
# atau Domain sendiri:
APP_URL=https://prcf-test.indevs.in

# Trusted Proxies untuk Cloudflare
TRUSTED_PROXIES=*

# Session settings untuk HTTPS
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax

# Database - tetap lokal
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prcf_keuangan
DB_USERNAME=root
DB_PASSWORD=

# Queue & Cache - file untuk lokal
QUEUE_CONNECTION=sync
CACHE_DRIVER=file
SESSION_DRIVER=file
```

### Pastikan TrustProxies Middleware

Cek file `app/Http/Middleware/TrustProxies.php` (Laravel 10-) atau konfirmasi di `bootstrap/app.php` (Laravel 11+):

```php
// Untuk Laravel 11 di bootstrap/app.php (biasanya sudah default)
// Tidak perlu edit jika sudah ada TRUSTED_PROXIES=* di .env
```

### Clear Cache Setelah Update .env

```powershell
cd C:\xampp\htdocs\prcf_laravel

php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

---

## 🧪 Testing Setelah Tunnel Hidup

### Checklist Verifikasi

```markdown
[ ] 1. Akses via HTTPS berhasil
      URL: https://xxx.trycloudflare.com atau https://prcf-test.indevs.in
      
[ ] 2. Halaman login tampil normal
      Tidak ada error, CSS/JS loading

[ ] 3. Login berhasil (session works)
      - Login dengan test account
      - Refresh halaman - tetap login
      - Logout - session cleared
      
[ ] 4. Register flow (jika enabled)
      - Link register tampil/tidak sesuai setting
      - Bisa register dan verify OTP
      
[ ] 5. Upload file berfungsi
      - Test attach dokumen (proposal/report)
      - File tersimpan di storage
      
[ ] 6. Mixed content tidak ada
      - Buka Developer Tools (F12) > Console
      - Tidak ada warning "Mixed Content"
      
[ ] 7. Cookie secure
      - Di DevTools > Application > Cookies
      - Session cookie punya flag "Secure"
```

### Troubleshooting Umum

| Masalah | Penyebab | Solusi |
|---------|----------|--------|
| ERR_TOO_MANY_REDIRECTS | Loop HTTP/HTTPS | Set `TRUSTED_PROXIES=*` di .env |
| 502 Bad Gateway | Tunnel tidak connect ke Apache | Cek Apache running, port benar |
| Mixed Content | Asset masih HTTP | Set `APP_URL` dengan https:// |
| Session tidak tersimpan | Cookie tidak secure | Set `SESSION_SECURE_COOKIE=true` |
| CSS/JS tidak loading | Path salah | Cek APP_URL dan asset() helper |

---

## 📦 Persiapan Migrasi ke Server Production

### Yang Perlu Disiapkan

1. **Export Database**
   ```powershell
   cd C:\xampp\mysql\bin
   mysqldump -u root prcf_keuangan > C:\backup\prcf_db_backup.sql
   ```

2. **Daftar Dependencies**
   ```powershell
   cd C:\xampp\htdocs\prcf_laravel
   composer show --installed > dependencies.txt
   ```

3. **Storage Files**
   - Backup folder `storage/app/public/`
   - Ini berisi file upload (ToR, budget, receipts)

4. **Environment Variables**
   - Catat semua variable di `.env`
   - Siapkan versi production (tanpa credentials di Git!)

### Struktur File untuk Deploy

```
prcf_laravel/
├── .env.production.example    # Template untuk production
├── docs/
│   ├── DEPLOYMENT_GUIDE.md    # Panduan deployment lengkap
│   └── CLOUDFLARE_TUNNEL_SETUP.md  # File ini
├── storage/
│   └── app/public/            # Perlu dicopy/backup
└── ...
```

### Checklist Sebelum Migrasi

```markdown
[ ] Backup database terbaru
[ ] Backup storage files
[ ] .env production sudah disiapkan
[ ] APP_DEBUG=false untuk production
[ ] APP_ENV=production
[ ] Log error ke file (bukan debug bar)
[ ] Queue worker dikonfigurasi
[ ] Cron/Scheduler dikonfigurasi
[ ] SSL certificate (Let's Encrypt atau Cloudflare Origin)
```

---

## 🛡️ Keamanan Minimum

### Untuk Tunnel Development

1. **Jangan share URL Quick Tunnel sembarangan**
   - URL publik, siapa saja bisa akses
   - Matikan tunnel saat tidak develop

2. **Firewall Lokal**
   - Tidak perlu buka port di router
   - Cloudflare Tunnel = outbound connection

3. **Jangan expose database**
   - MySQL tetap di localhost
   - Jangan buka port 3306 ke internet

4. **Review data test**
   - Jangan pakai data produksi asli untuk testing
   - Gunakan seeder dengan data dummy

### Untuk Domain Production Nanti

1. **Rate Limiting** - aktifkan di Cloudflare
2. **WAF Rules** - proteksi basic
3. **Block Countries** - jika tidak perlu akses global
4. **Bot Protection** - aktifkan challenge

---

## 🚀 Langkah Selanjutnya

### Sekarang (Quick Start)

1. Download cloudflared.exe
2. Jalankan `cloudflared tunnel --url http://localhost:80`
3. Test akses via URL trycloudflare.com
4. Pastikan login/register berfungsi

### Nanti (Setup Permanen)

1. Setup tunnel permanen dengan nama
2. Konfigurasi DNS di Stackryze (CNAME)
3. Test domain prcf-test.indevs.in

### Production Ready

1. Pilih hosting (VPS/Shared)
2. Setup server dengan panduan DEPLOYMENT_GUIDE.md
3. Migrasi database dan files
4. Point domain ke server production

---

## ❓ Pertanyaan Klarifikasi

Sebelum lanjut, saya perlu tahu:

1. **Port Apache di XAMPP**: Port 80 atau 8080? (cek XAMPP Control Panel)

2. **Path aplikasi**: 
   - `http://localhost/prcf_laravel/public` 
   - atau sudah pakai Virtual Host?

3. **Database name**: Apa nama database MySQL yang dipakai?

4. **Akun Cloudflare**: Sudah punya atau perlu daftar baru?

---

*Dokumen ini untuk setup development PRCF Keuangan*
*Last Updated: 2026-01-07*