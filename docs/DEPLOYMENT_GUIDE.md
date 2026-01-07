# Panduan Deployment PRCF Keuangan

## Ringkasan Situasi
- **Status**: Aplikasi masih di lokal XAMPP (Windows)
- **Domain Testing**: `prcf-test.indevs.in` (subdomain pihak lain, akses DNS tidak jelas)
- **Hosting**: Belum ditentukan
- **Kebutuhan**: Development sekarang + bisa migrasi ke staging/production nanti

---

## 🎯 Rekomendasi Utama

**Untuk kondisi Anda, saya rekomendasikan: VPS Kecil + Cloudflare**

Alasan:
- Fleksibilitas tinggi untuk development dan production
- Biaya terjangkau (~$5-6/bulan)
- Full kontrol (root access)
- Mudah migrasi ke provider manapun
- SSL/HTTPS otomatis via Cloudflare
- Bisa pakai Git-based deployment

---

## 📊 Perbandingan 3 Opsi

### Opsi 1: Cloudflare Tunnel (Quick Testing)

**Deskripsi**: Expose XAMPP lokal ke internet via Cloudflare Tunnel

| Aspek | Detail |
|-------|--------|
| **Biaya** | GRATIS |
| **Kesulitan** | ⭐ Mudah |
| **Setup Time** | 10-15 menit |
| **Cocok Untuk** | Quick demo, testing sementara |

**Pro:**
- ✅ Gratis 100%
- ✅ Tidak perlu server tambahan
- ✅ HTTPS otomatis
- ✅ Bisa pakai domain sendiri ATAU domain gratis `*.trycloudflare.com`

**Kontra:**
- ❌ Tergantung PC lokal nyala
- ❌ Performa tidak konsisten (internet rumah)
- ❌ Tidak cocok untuk production
- ❌ Untuk domain `prcf-test.indevs.in`: perlu akses DNS atau minta admin

**Kapan Pilih**: Testing cepat, demo ke klien, development remote

---

### Opsi 2: VPS Kecil (⭐ REKOMENDASI)

**Deskripsi**: Server virtual dengan Nginx + PHP-FPM + MySQL

| Aspek | Detail |
|-------|--------|
| **Biaya** | $4-6/bulan (DigitalOcean, Vultr, Linode) |
| **Kesulitan** | ⭐⭐ Sedang |
| **Setup Time** | 30-60 menit |
| **Cocok Untuk** | Staging & Production |

**Pro:**
- ✅ Full kontrol (root access)
- ✅ Skalabel (upgrade RAM/CPU kapan saja)
- ✅ Performa konsisten
- ✅ Bisa multiple domain/project
- ✅ Belajar DevOps skills

**Kontra:**
- ❌ Perlu bayar bulanan
- ❌ Perlu kelola sendiri (security updates, backup)
- ❌ Butuh basic Linux knowledge

**Provider Rekomendasi:**
| Provider | Harga Mulai | Lokasi Singapore |
|----------|-------------|------------------|
| DigitalOcean | $4/bulan | ✅ |
| Vultr | $5/bulan | ✅ |
| Linode | $5/bulan | ✅ |
| IDCloudHost | Rp 50rb/bulan | ✅ Indonesia |

**Kapan Pilih**: Staging serius, production, belajar server management

---

### Opsi 3: Shared Hosting cPanel

**Deskripsi**: Hosting tradisional dengan kontrol panel

| Aspek | Detail |
|-------|--------|
| **Biaya** | Rp 20-100rb/bulan |
| **Kesulitan** | ⭐ Mudah |
| **Setup Time** | 15-30 menit |
| **Cocok Untuk** | Aplikasi sederhana, non-teknikal |

**Pro:**
- ✅ User-friendly (GUI)
- ✅ Email, database, SSL sudah include
- ✅ Tidak perlu kelola server

**Kontra:**
- ❌ Limited resources (shared)
- ❌ Tidak bisa kustomisasi server
- ❌ PHP version/extension terbatas
- ❌ Tidak cocok untuk queue/scheduler intensive
- ❌ Artisan commands terbatas

**Kapan Pilih**: Budget sangat terbatas, tidak mau ribet server

---

## 🌐 Skenario DNS untuk `prcf-test.indevs.in`

### Skenario A: Anda Punya Akses DNS

Jika Anda bisa login ke panel DNS `indevs.in`:

```
# Untuk VPS dengan IP 123.45.67.89
Type: A
Name: prcf-test
Value: 123.45.67.89
TTL: 300 (5 menit, untuk testing)

# Untuk Cloudflare Tunnel
Type: CNAME
Name: prcf-test
Value: <tunnel-id>.cfargotunnel.com
TTL: Auto
```

### Skenario B: Tidak Punya Akses DNS

**Alternatif 1: Minta Admin Domain**
- Hubungi admin `indevs.in`
- Minta dibuatkan A record atau CNAME untuk `prcf-test`

**Alternatif 2: Gunakan Domain Gratis**
| Service | Format Domain | Kebutuhan |
|---------|---------------|-----------|
| Cloudflare Quick Tunnel | `xxx-xxx.trycloudflare.com` | Tidak perlu apa-apa |
| FreeDNS | `xxx.mooo.com`, dll | Daftar akun |
| DuckDNS | `xxx.duckdns.org` | Daftar akun |

**Alternatif 3: Beli Domain Murah**
- Domain `.my.id`: Rp 12.000/tahun
- Domain `.site`: $1-2/tahun
- Domain `.xyz`: $1/tahun pertama

---

## 🚀 Langkah Setup VPS (Rekomendasi Utama)

### Tahap 1: Buat VPS

```bash
# Pilih:
# - OS: Ubuntu 24.04 LTS
# - RAM: 1GB (minimum), 2GB (rekomendasi)
# - Storage: 25GB SSD
# - Region: Singapore (dekat Indonesia)
```

### Tahap 2: Setup Server (SSH ke VPS)

```bash
# 1. Update sistem
sudo apt update && sudo apt upgrade -y

# 2. Install Nginx, PHP 8.2, MySQL
sudo apt install -y nginx mysql-server php8.2-fpm \
    php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl \
    php8.2-zip php8.2-gd php8.2-bcmath php8.2-intl \
    unzip git composer

# 3. Konfigurasi MySQL
sudo mysql_secure_installation
sudo mysql -e "CREATE DATABASE prcf_keuangan;"
sudo mysql -e "CREATE USER 'prcf_user'@'localhost' IDENTIFIED BY 'password_kuat_123';"
sudo mysql -e "GRANT ALL PRIVILEGES ON prcf_keuangan.* TO 'prcf_user'@'localhost';"

# 4. Setup folder aplikasi
sudo mkdir -p /var/www/prcf
sudo chown -R $USER:www-data /var/www/prcf
```

### Tahap 3: Konfigurasi Nginx

```bash
sudo nano /etc/nginx/sites-available/prcf
```

Isi file:
```nginx
server {
    listen 80;
    server_name prcf-test.indevs.in;  # Ganti dengan domain Anda
    root /var/www/prcf/public;
    
    index index.php;
    
    # Laravel routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    # PHP-FPM
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    # Deny .ht* files
    location ~ /\.ht {
        deny all;
    }
    
    # Cache static files
    location ~* \.(jpg|jpeg|png|gif|ico|css|js|woff2)$ {
        expires 7d;
        add_header Cache-Control "public, immutable";
    }
}
```

Aktifkan site:
```bash
sudo ln -s /etc/nginx/sites-available/prcf /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### Tahap 4: Deploy Aplikasi

**Opsi A: Git Clone (Rekomendasi)**
```bash
cd /var/www/prcf
git clone https://github.com/username/prcf_laravel.git .
```

**Opsi B: Upload Manual**
```bash
# Dari Windows, gunakan WinSCP atau:
scp -r c:\xampp\htdocs\prcf_laravel\* user@server-ip:/var/www/prcf/
```

### Tahap 5: Konfigurasi Laravel

```bash
cd /var/www/prcf

# Install dependencies
composer install --optimize-autoloader --no-dev

# Setup environment
cp .env.example .env
nano .env
```

Edit `.env`:
```env
APP_NAME="PRCF Keuangan"
APP_ENV=staging           # atau production
APP_DEBUG=false           # PENTING: false untuk production
APP_URL=https://prcf-test.indevs.in

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=prcf_keuangan
DB_USERNAME=prcf_user
DB_PASSWORD=password_kuat_123

# Session & Cache
SESSION_DRIVER=database   # atau redis
CACHE_DRIVER=file         # atau redis
QUEUE_CONNECTION=database # atau redis

# Untuk Cloudflare (penting!)
TRUSTED_PROXIES=*
```

Lanjutkan setup:
```bash
# Generate key
php artisan key:generate

# Optimize
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage link
php artisan storage:link

# Database
php artisan migrate --force
php artisan db:seed --force  # jika perlu

# Permissions
sudo chown -R www-data:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### Tahap 6: SSL via Cloudflare

1. **Tambahkan domain ke Cloudflare** (jika punya akses)
2. **Set DNS record** (A → IP VPS)
3. **Enable Proxy** (orange cloud ON)
4. **SSL/TLS Setting**:
   - Mode: **Full** (jika VPS punya SSL)
   - Mode: **Flexible** (jika VPS tidak punya SSL) - tidak rekomendasi
   
**Rekomendasi: Full (Strict)**
```bash
# Install Cloudflare Origin Certificate atau Let's Encrypt
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d prcf-test.indevs.in
```

### Tahap 7: Setup Scheduler & Queue

```bash
# Crontab untuk scheduler
crontab -e
```

Tambahkan:
```
* * * * * cd /var/www/prcf && php artisan schedule:run >> /dev/null 2>&1
```

Queue worker (menggunakan Supervisor):
```bash
sudo apt install supervisor
sudo nano /etc/supervisor/conf.d/prcf-worker.conf
```

Isi:
```ini
[program:prcf-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/prcf/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/var/www/prcf/storage/logs/worker.log
```

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start prcf-worker:*
```

---

## 🔧 Troubleshooting Umum

### 1. Redirect Loop (ERR_TOO_MANY_REDIRECTS)

**Penyebab**: Mismatch SSL antara Cloudflare dan server

**Solusi**:
```php
// Di app/Http/Middleware/TrustProxies.php
protected $proxies = '*';
protected $headers = Request::HEADER_X_FORWARDED_FOR | 
                     Request::HEADER_X_FORWARDED_HOST | 
                     Request::HEADER_X_FORWARDED_PORT | 
                     Request::HEADER_X_FORWARDED_PROTO;
```

Atau di `.env`:
```env
TRUSTED_PROXIES=*
```

### 2. Error 525/526 (SSL Handshake Failed)

**Penyebab**: Cloudflare mode Full/Strict tapi server tidak punya SSL

**Solusi**:
- Set Cloudflare SSL ke **Flexible** (sementara)
- Atau install SSL di server:
```bash
sudo certbot --nginx -d prcf-test.indevs.in
```

### 3. Mixed Content

**Penyebab**: Asset (CSS/JS/gambar) dimuat via HTTP

**Solusi**:
```php
// Di AppServiceProvider.php boot()
if (config('app.env') === 'production') {
    \URL::forceScheme('https');
}
```

### 4. Session/Login Tidak Berfungsi

**Penyebab**: Cloudflare caching cookies

**Solusi di Cloudflare Dashboard**:
- Page Rules → Add Rule
- URL: `prcf-test.indevs.in/*`
- Setting: Cache Level = Bypass

Atau di `.env`:
```env
SESSION_SECURE_COOKIE=true
SESSION_SAME_SITE=lax
```

### 5. Storage/Upload Tidak Bisa

```bash
sudo chown -R www-data:www-data /var/www/prcf/storage
sudo chmod -R 775 /var/www/prcf/storage
php artisan storage:link
```

---

## ✅ Checklist Verifikasi Post-Deploy

```markdown
[ ] Website bisa diakses via HTTPS
[ ] Login berfungsi (session OK)
[ ] Redirect HTTP → HTTPS bekerja
[ ] Upload file berfungsi
[ ] Storage link bekerja (gambar/dokumen tampil)
[ ] Database terkoneksi (cek dashboard data)
[ ] Email/notifikasi terkirim (jika ada)
[ ] Queue berjalan (cek `php artisan queue:work`)
[ ] Scheduler berjalan (cek log)
[ ] Error logging aktif (cek storage/logs)
[ ] APP_DEBUG=false di production
[ ] APP_ENV=production
```

**Test Commands:**
```bash
# Cek aplikasi
curl -I https://prcf-test.indevs.in

# Cek SSL
openssl s_client -connect prcf-test.indevs.in:443 -servername prcf-test.indevs.in

# Cek dari lokal
php artisan tinker
> \App\Models\User::count()
```

---

## 📦 Quick Start: Cloudflare Tunnel (Alternatif Cepat)

Jika ingin testing cepat tanpa VPS:

### Windows (XAMPP)

```powershell
# 1. Download cloudflared
# https://developers.cloudflare.com/cloudflare-one/connections/connect-apps/install-and-setup/installation/

# 2. Jalankan Quick Tunnel (domain random gratis)
cloudflared tunnel --url http://localhost:80

# Output: https://xxx-xxx-xxx.trycloudflare.com
```

### Dengan Domain Sendiri

```bash
# 1. Login Cloudflare
cloudflared tunnel login

# 2. Buat tunnel
cloudflared tunnel create prcf-tunnel

# 3. Konfigurasi (buat file config.yml)
tunnel: <TUNNEL-ID>
credentials-file: ~/.cloudflared/<TUNNEL-ID>.json

ingress:
  - hostname: prcf-test.indevs.in
    service: http://localhost:80
  - service: http_status:404

# 4. Buat DNS record di Cloudflare
cloudflared tunnel route dns prcf-tunnel prcf-test.indevs.in

# 5. Jalankan
cloudflared tunnel run prcf-tunnel
```

---

## ❓ Pertanyaan Klarifikasi

Untuk melanjutkan, saya perlu tahu:

1. **Akses DNS**: Apakah Anda bisa mengelola DNS untuk `indevs.in` atau `prcf-test.indevs.in`? (Bisa login ke panel DNS-nya?)

2. **Budget**: Berapa budget maksimal per bulan untuk hosting? (Rp 0 / < Rp 100rb / < Rp 300rb / fleksibel)

3. **Timeline**: Kapan harus online? (Hari ini untuk demo / Minggu depan / Tidak urgent)

4. **Pilihan**: Mau langsung coba mana dulu?
   - A) Cloudflare Tunnel (gratis, testing dari XAMPP)
   - B) Setup VPS (bayar, lebih proper)
   - C) Mau pakai domain gratis dulu (`*.trycloudflare.com`)

5. **Tech Stack di Server**: Prefer Docker atau native install?

---

*Dokumen ini dibuat untuk PRCF Keuangan Laravel*  
*Last Updated: 2026-01-07*